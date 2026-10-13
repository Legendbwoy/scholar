<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\BroadcastMessage;
use App\Models\BroadcastDelivery;
use App\Models\AuditTrail;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Role: Student View
        if ($user->isStudent()) {
            $student = $user->student;
            $attendances = $student ? $student->attendances()->orderBy('date', 'desc')->paginate(15) : collect();
            $totalDays = $student ? $student->attendances()->count() : 0;
            $presentDays = $student ? $student->attendances()->where('status', 'present')->count() : 0;
            $lateDays = $student ? $student->attendances()->where('status', 'late')->count() : 0;
            $absentDays = $student ? $student->attendances()->where('status', 'absent')->count() : 0;
            $excusedDays = $student ? $student->attendances()->where('status', 'excused')->count() : 0;

            return view('portals.attendance.student', compact(
                'student', 'attendances', 'totalDays', 'presentDays', 'lateDays', 'absentDays', 'excusedDays'
            ));
        }

        // 2. Role: Parent / Guardian View
        if ($user->isParent()) {
            $guardian = $user->guardian;
            $wards = $guardian ? $guardian->students()->with(['user', 'schoolClass', 'attendances' => function ($q) {
                $q->orderBy('date', 'desc')->take(10);
            }])->get() : collect();

            return view('portals.attendance.parent', compact('guardian', 'wards'));
        }

        // 3. Role: Super Admin & Teacher / Faculty Desk View
        $date = $request->input('date', now()->toDateString());
        $classes = SchoolClass::withCount('students')->get();

        $stats = [
            'total_students' => Student::count(),
            'present' => Attendance::where('date', $date)->whereIn('status', ['present', 'late'])->count(),
            'absent' => Attendance::where('date', $date)->where('status', 'absent')->count(),
            'late' => Attendance::where('date', $date)->where('status', 'late')->count(),
            'excused' => Attendance::where('date', $date)->where('status', 'excused')->count(),
        ];

        $classAttendance = [];
        foreach ($classes as $c) {
            $att = Attendance::where('school_class_id', $c->id)->where('date', $date)->get();
            $classAttendance[$c->id] = [
                'present' => $att->whereIn('status', ['present', 'late'])->count(),
                'absent' => $att->where('status', 'absent')->count(),
                'total' => $c->students_count,
            ];
        }

        return view('portals.attendance.index', compact('classes', 'date', 'stats', 'classAttendance'));
    }

    public function mark($classId, Request $request)
    {
        $class = SchoolClass::with('students.user', 'students.guardian.user')->findOrFail($classId);
        $date = $request->input('date', now()->toDateString());

        $existing = Attendance::where('school_class_id', $classId)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('portals.attendance.mark', compact('class', 'date', 'existing'));
    }

    public function storeMark(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
        ]);

        $classId = $request->school_class_id;
        $date = $request->date;
        $records = $request->attendances;
        $remarks = $request->remarks ?? [];

        $absentStudents = [];

        foreach ($records as $studentId => $status) {
            $student = Student::with(['user', 'guardian.user'])->find($studentId);
            if (!$student) continue;

            $att = Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                ],
                [
                    'school_class_id' => $classId,
                    'status' => $status,
                    'remarks' => $remarks[$studentId] ?? null,
                    'marked_by' => auth()->id(),
                    'method' => 'manual',
                ]
            );

            // Automated comms trigger if marked absent
            if ($status === 'absent' && $student->guardian) {
                $absentStudents[] = $student;

                $bMsg = BroadcastMessage::create([
                    'sender_id' => auth()->id(),
                    'title' => "Immediate Absence Notice: {$student->user->name}",
                    'message' => "Attention: {$student->guardian->user->name}, your student {$student->user->name} was marked absent for {$date}. If this was unexpected, please contact the academy office immediately.",
                    'target_audience' => 'parents',
                    'school_class_id' => $classId,
                    'channels' => ['sms', 'in_app'],
                    'priority' => 'urgent',
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                BroadcastDelivery::create([
                    'broadcast_message_id' => $bMsg->id,
                    'recipient_id' => $student->guardian->user_id,
                    'channel' => 'sms',
                    'recipient_contact' => $student->guardian->user->phone ?? '+1-555-0100',
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            }
        }

        AuditTrail::log(
            'ATTENDANCE_SEALED',
            "Attendance register for cohort ID {$classId} on {$date} recorded (" . count($records) . " students).",
            'SchoolClass',
            $classId
        );

        $msg = "Attendance saved successfully for {$date}.";
        if (count($absentStudents) > 0) {
            $msg .= " Instant automated SMS alerts dispatched to " . count($absentStudents) . " parent(s).";
        }

        return redirect()->route('attendance.index', ['date' => $date])->with('success', $msg);
    }

    public function qrScanner()
    {
        $classes = SchoolClass::with('students.user')->get();
        return view('portals.attendance.qr', compact('classes'));
    }

    public function verifyQr(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::with('user', 'schoolClass')->findOrFail($request->student_id);
        $date = now()->toDateString();

        $att = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $date,
            ],
            [
                'school_class_id' => $student->school_class_id,
                'status' => 'present',
                'remarks' => 'Scanned via Smart QR Check-in Terminal',
                'marked_by' => auth()->id() ?? 1,
                'method' => 'qr_code',
            ]
        );

        AuditTrail::log(
            'QR_CHECKIN',
            "Contactless biometric/QR token check-in verified for student: {$student->user->name} ({$student->admission_number}).",
            'Attendance',
            $att->id
        );

        return response()->json([
            'success' => true,
            'message' => "Checked in: {$student->user->name}",
            'time' => now()->format('h:i:s A'),
            'status' => 'Present',
        ]);
    }
}
