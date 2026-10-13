<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Guardian;
use App\Models\AuditTrail;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'schoolClass', 'guardian.user']);

        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                  ->orWhere('roll_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->paginate(15)->withQueryString();
        $classes = SchoolClass::all();

        return view('portals.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $guardians = Guardian::with('user')->get();
        return view('portals.students.create', compact('classes', 'guardians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'school_class_id' => 'required|exists:school_classes,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'student',
            'password' => Hash::make($validated['password'] ?? 'password'),
            'avatar' => 'scholar/assets/images/event-01.jpg',
            'status' => 'active',
        ]);

        $nextNum = Student::count() + 1;
        $admNumber = 'SCH-2026-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $student = Student::create([
            'user_id' => $user->id,
            'guardian_id' => $validated['guardian_id'] ?? null,
            'school_class_id' => $validated['school_class_id'],
            'admission_number' => $admNumber,
            'dob' => $validated['dob'] ?? null,
            'gender' => $validated['gender'],
            'blood_group' => $validated['blood_group'] ?? 'O+',
            'admission_date' => now()->toDateString(),
            'roll_number' => 'R-' . $nextNum,
            'status' => 'active',
        ]);

        AuditTrail::log(
            'STUDENT_ENROLLED',
            "New student enrolled: {$user->name} ({$admNumber}) in class ID {$student->school_class_id}.",
            'Student',
            $student->id,
            $student->toArray()
        );

        return redirect()->route('students.index')->with('success', "Student {$user->name} registered with Admission Number: {$admNumber}");
    }

    public function bulkCreate()
    {
        $classes = SchoolClass::withCount('students')->get();
        $recentBatches = AuditTrail::where('action', 'STUDENT_BULK_ONBOARDING')->latest('id')->take(5)->get();

        return view('portals.students.bulk', compact('classes', 'recentBatches'));
    }

    public function downloadTemplate(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));
        $classes = SchoolClass::orderBy('numeric_grade')->get();

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="scholar_student_onboarding_template.csv"',
            ];

            $callback = function () {
                $file = fopen('php://output', 'w');
                // Write UTF-8 BOM for Microsoft Excel compatibility
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, [
                    'Full Name',
                    'Email Address',
                    'Phone Number',
                    'Class / Cohort',
                    'Gender',
                    'Date of Birth (YYYY-MM-DD)',
                    'Blood Group',
                    'Guardian Name',
                    'Guardian Email',
                    'Guardian Phone'
                ]);

                fputcsv($file, ['Sophia Martinez', 'sophia.martinez@scholar.edu', '+1-555-0191', 'Grade 10 - Alpha', 'Female', '2009-04-12', 'O+', 'Robert Martinez', 'robert.martinez@gmail.com', '+1-555-0192']);
                fputcsv($file, ['Liam O\'Connor', 'liam.oconnor@scholar.edu', '+1-555-0193', 'Grade 10 - Alpha', 'Male', '2009-08-25', 'A+', 'Deirdre O\'Connor', 'deirdre.oc@gmail.com', '+1-555-0194']);
                fputcsv($file, ['Aisha Al-Mansoor', 'aisha.mansoor@scholar.edu', '+1-555-0195', 'Grade 10 - Beta', 'Female', '2009-11-03', 'B+', 'Tariq Al-Mansoor', 'tariq.mansoor@gmail.com', '+1-555-0196']);
                fputcsv($file, ['Lucas Silva', 'lucas.silva@scholar.edu', '+1-555-0197', 'Grade 9 - Standard', 'Male', '2010-02-18', 'AB+', 'Elena Silva', 'elena.silva@gmail.com', '+1-555-0198']);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Native XLSX format using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // 1. Primary Data Entry Sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Student Onboarding');

        $headers = [
            'Full Name',
            'Email Address',
            'Phone Number',
            'Class / Cohort',
            'Gender',
            'Date of Birth (YYYY-MM-DD)',
            'Blood Group',
            'Guardian Name',
            'Guardian Email',
            'Guardian Phone'
        ];

        foreach ($headers as $idx => $headerText) {
            $colLetter = chr(65 + $idx);
            $sheet->setCellValue($colLetter . '1', $headerText);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '7A6AD8'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $samples = [
            ['Sophia Martinez', 'sophia.martinez@scholar.edu', '+1-555-0191', 'Grade 10 - Alpha', 'Female', '2009-04-12', 'O+', 'Robert Martinez', 'robert.martinez@gmail.com', '+1-555-0192'],
            ['Liam O\'Connor', 'liam.oconnor@scholar.edu', '+1-555-0193', 'Grade 10 - Alpha', 'Male', '2009-08-25', 'A+', 'Deirdre O\'Connor', 'deirdre.oc@gmail.com', '+1-555-0194'],
            ['Aisha Al-Mansoor', 'aisha.mansoor@scholar.edu', '+1-555-0195', 'Grade 10 - Beta', 'Female', '2009-11-03', 'B+', 'Tariq Al-Mansoor', 'tariq.mansoor@gmail.com', '+1-555-0196'],
            ['Lucas Silva', 'lucas.silva@scholar.edu', '+1-555-0197', 'Grade 9 - Standard', 'Male', '2010-02-18', 'AB+', 'Elena Silva', 'elena.silva@gmail.com', '+1-555-0198'],
        ];

        foreach ($samples as $rIdx => $row) {
            $rowNum = $rIdx + 2;
            foreach ($row as $cIdx => $val) {
                $colLetter = chr(65 + $cIdx);
                $sheet->setCellValue($colLetter . $rowNum, $val);
            }
        }

        // 2. Secondary Informational Sheet: Active Cohorts Reference
        $classSheet = $spreadsheet->createSheet();
        $classSheet->setTitle('Cohort Reference');
        $classSheet->setCellValue('A1', 'Cohort ID');
        $classSheet->setCellValue('B1', 'Cohort Name');
        $classSheet->setCellValue('C1', 'Grade');
        $classSheet->setCellValue('D1', 'Section');

        $classHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F35525'],
            ],
        ];
        $classSheet->getStyle('A1:D1')->applyFromArray($classHeaderStyle);

        foreach ($classes as $cIdx => $cls) {
            $r = $cIdx + 2;
            $classSheet->setCellValue('A' . $r, $cls->id);
            $classSheet->setCellValue('B' . $r, $cls->name);
            $classSheet->setCellValue('C' . $r, $cls->numeric_grade);
            $classSheet->setCellValue('D' . $r, $cls->section);
        }
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $classSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'scholar_student_onboarding_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:15360',
            'default_class_id' => 'nullable|exists:school_classes,id',
            'default_password' => 'nullable|string|min:6',
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        $rows = [];

        // Parse file based on extension
        if (in_array($extension, ['csv', 'txt'])) {
            if (($handle = fopen($path, 'r')) !== false) {
                while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else {
            // XLSX / XLS via PhpSpreadsheet
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);
            } catch (\Exception $e) {
                return back()->with('error', 'Unable to parse spreadsheet file: ' . $e->getMessage());
            }
        }

        if (empty($rows) || count($rows) < 2) {
            return back()->with('error', 'The uploaded file appears to be empty or contains only header columns.');
        }

        $rawHeaders = array_shift($rows);
        $headerMap = [];
        foreach ($rawHeaders as $idx => $headerText) {
            $clean = strtolower(trim((string)$headerText));
            $clean = trim($clean, "\xEF\xBB\xBF");

            if (str_contains($clean, 'name') && !str_contains($clean, 'guardian')) $headerMap['name'] = $idx;
            elseif (str_contains($clean, 'email') && !str_contains($clean, 'guardian')) $headerMap['email'] = $idx;
            elseif (str_contains($clean, 'phone') && !str_contains($clean, 'guardian')) $headerMap['phone'] = $idx;
            elseif (str_contains($clean, 'class') || str_contains($clean, 'cohort')) $headerMap['class'] = $idx;
            elseif (str_contains($clean, 'gender') || str_contains($clean, 'sex')) $headerMap['gender'] = $idx;
            elseif (str_contains($clean, 'birth') || str_contains($clean, 'dob')) $headerMap['dob'] = $idx;
            elseif (str_contains($clean, 'blood')) $headerMap['blood_group'] = $idx;
            elseif (str_contains($clean, 'guardian') && str_contains($clean, 'name')) $headerMap['guardian_name'] = $idx;
            elseif (str_contains($clean, 'guardian') && str_contains($clean, 'email')) $headerMap['guardian_email'] = $idx;
            elseif (str_contains($clean, 'guardian') && str_contains($clean, 'phone')) $headerMap['guardian_phone'] = $idx;
        }

        if (!isset($headerMap['name']) || !isset($headerMap['email'])) {
            return back()->with('error', 'Missing required columns: "Full Name" and "Email Address" must be present in the spreadsheet header.');
        }

        $allClasses = SchoolClass::all();
        $defaultPassword = $request->input('default_password', 'password');
        $defaultClassId = $request->input('default_class_id');

        $enrolledCount = 0;
        $skippedRows = [];
        $existingEmails = User::pluck('email')->map(fn($e) => strtolower($e))->toArray();
        $processedBatchEmails = [];

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 2;

            $name = trim((string)($row[$headerMap['name']] ?? ''));
            $email = strtolower(trim((string)($row[$headerMap['email']] ?? '')));

            if ($name === '' && $email === '') {
                continue;
            }

            if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedRows[] = "Row {$rowNumber}: Invalid name or email format ('{$email}').";
                continue;
            }

            if (in_array($email, $existingEmails, true) || in_array($email, $processedBatchEmails, true)) {
                $skippedRows[] = "Row {$rowNumber}: Email {$email} is already registered in the system.";
                continue;
            }

            // Resolve Cohort / Class
            $classId = null;
            $classRaw = isset($headerMap['class']) ? trim((string)($row[$headerMap['class']] ?? '')) : '';

            if ($classRaw !== '') {
                $matchedClass = $allClasses->first(function ($c) use ($classRaw) {
                    return strcasecmp($c->name, $classRaw) === 0 
                        || strcasecmp("Grade {$c->numeric_grade} - {$c->section}", $classRaw) === 0
                        || strcasecmp("{$c->name} ({$c->section})", $classRaw) === 0
                        || (string)$c->id === $classRaw;
                });
                if ($matchedClass) {
                    $classId = $matchedClass->id;
                }
            }

            if (!$classId && $defaultClassId) {
                $classId = $defaultClassId;
            }

            if (!$classId) {
                $classId = $allClasses->first()?->id;
            }

            if (!$classId) {
                $skippedRows[] = "Row {$rowNumber}: Cohort '{$classRaw}' could not be resolved and no default class selected.";
                continue;
            }

            $genderRaw = isset($headerMap['gender']) ? ucfirst(strtolower(trim((string)($row[$headerMap['gender']] ?? '')))) : 'Other';
            $gender = in_array($genderRaw, ['Male', 'Female', 'Other']) ? $genderRaw : 'Other';
            $bloodGroup = isset($headerMap['blood_group']) ? trim((string)($row[$headerMap['blood_group']] ?? '')) : 'O+';
            if ($bloodGroup === '') $bloodGroup = 'O+';

            $phone = isset($headerMap['phone']) ? trim((string)($row[$headerMap['phone']] ?? '')) : null;

            $dobRaw = isset($headerMap['dob']) ? trim((string)($row[$headerMap['dob']] ?? '')) : null;
            $dob = null;
            if ($dobRaw) {
                if (is_numeric($dobRaw)) {
                    $dob = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dobRaw)->format('Y-m-d');
                } else {
                    $parsed = strtotime($dobRaw);
                    if ($parsed) $dob = date('Y-m-d', $parsed);
                }
            }

            $guardianId = null;
            $gEmail = isset($headerMap['guardian_email']) ? strtolower(trim((string)($row[$headerMap['guardian_email']] ?? ''))) : '';
            $gName = isset($headerMap['guardian_name']) ? trim((string)($row[$headerMap['guardian_name']] ?? '')) : '';
            $gPhone = isset($headerMap['guardian_phone']) ? trim((string)($row[$headerMap['guardian_phone']] ?? '')) : null;

            if ($gEmail !== '' && filter_var($gEmail, FILTER_VALIDATE_EMAIL)) {
                $gUser = User::where('email', $gEmail)->first();
                if (!$gUser) {
                    $gUser = User::create([
                        'name' => $gName ?: 'Guardian of ' . $name,
                        'email' => $gEmail,
                        'phone' => $gPhone,
                        'role' => 'parent',
                        'password' => Hash::make('password'),
                        'avatar' => 'scholar/assets/images/testimonial-author.jpg',
                        'status' => 'active',
                    ]);
                    $existingEmails[] = $gEmail;
                }
                $guardian = Guardian::firstOrCreate(
                    ['user_id' => $gUser->id],
                    ['relationship' => 'Parent', 'occupation' => 'Professional', 'emergency_contact' => $gPhone]
                );
                $guardianId = $guardian->id;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => 'student',
                'password' => Hash::make($defaultPassword),
                'avatar' => 'scholar/assets/images/event-01.jpg',
                'status' => 'active',
            ]);

            $existingEmails[] = $email;
            $processedBatchEmails[] = $email;

            $currentTotal = Student::count() + 1;
            $admNumber = 'SCH-2026-' . str_pad($currentTotal, 3, '0', STR_PAD_LEFT);
            $rollNumber = 'R-' . $currentTotal;

            $student = Student::create([
                'user_id' => $user->id,
                'guardian_id' => $guardianId,
                'school_class_id' => $classId,
                'admission_number' => $admNumber,
                'dob' => $dob,
                'gender' => $gender,
                'blood_group' => $bloodGroup,
                'admission_date' => now()->toDateString(),
                'roll_number' => $rollNumber,
                'status' => 'active',
            ]);

            $enrolledCount++;
        }

        AuditTrail::log(
            'STUDENT_BULK_ONBOARDING',
            "Bulk student import completed: {$enrolledCount} students enrolled from file '{$file->getClientOriginalName()}'. " . count($skippedRows) . " skipped rows.",
            'Student',
            null,
            ['enrolled_count' => $enrolledCount, 'skipped_count' => count($skippedRows)]
        );

        $feedback = "Bulk onboarding completed successfully! {$enrolledCount} students enrolled and allocated institutional admission credentials.";
        if (count($skippedRows) > 0) {
            return redirect()->route('students.index')->with('success', $feedback)->with('import_warnings', $skippedRows);
        }

        return redirect()->route('students.index')->with('success', $feedback);
    }

    public function show($id)
    {
        $student = Student::with(['user', 'schoolClass.classTeacher.user', 'guardian.user', 'attendances'])->findOrFail($id);

        $currentUser = auth()->user();
        if ($currentUser->isStudent() && $currentUser->student?->id != $student->id) {
            abort(403, 'Access Denied: Students may only access their own personal academic record.');
        }

        if ($currentUser->isParent()) {
            $wardIds = $currentUser->guardian ? $currentUser->guardian->students->pluck('id')->toArray() : [];
            if (!in_array($student->id, $wardIds)) {
                abort(403, 'Access Denied: Guardians may only access records for their registered wards.');
            }
        }

        $attendances = $student->attendances()->orderBy('date', 'desc')->take(20)->get();

        return view('portals.students.show', compact('student', 'attendances'));
    }

    public function edit($id)
    {
        $student = Student::with(['user', 'guardian', 'schoolClass'])->findOrFail($id);
        $classes = SchoolClass::all();
        $guardians = Guardian::with('user')->get();

        return view('portals.students.edit', compact('student', 'classes', 'guardians'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'phone' => 'nullable|string',
            'school_class_id' => 'required|exists:school_classes,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'roll_number' => 'nullable|string|max:50',
            'gender' => 'required|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|string',
            'status' => 'required|in:active,inactive,graduated,suspended',
        ]);

        $oldValues = [
            'name' => $student->user->name,
            'email' => $student->user->email,
            'class_id' => $student->school_class_id,
            'status' => $student->status,
        ];

        // Update User
        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'] === 'active' ? 'active' : 'inactive',
        ]);

        // Update Student
        $student->update([
            'school_class_id' => $validated['school_class_id'],
            'guardian_id' => $validated['guardian_id'] ?? null,
            'roll_number' => $validated['roll_number'] ?? $student->roll_number,
            'gender' => $validated['gender'],
            'dob' => $validated['dob'] ?? null,
            'blood_group' => $validated['blood_group'] ?? 'O+',
            'status' => $validated['status'],
        ]);

        AuditTrail::log(
            'STUDENT_UPDATED',
            "Student profile updated for {$student->user->name} ({$student->admission_number}).",
            'Student',
            $student->id,
            $validated,
            $oldValues
        );

        return redirect()->route('students.show', $student->id)->with('success', "Student profile for {$student->user->name} successfully updated.");
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->user->name;
        $student->update(['status' => 'suspended']);
        $student->user->update(['status' => 'inactive']);

        AuditTrail::log(
            'STUDENT_SUSPENDED',
            "Student account {$name} (ID: {$student->id}) suspended / archived.",
            'Student',
            $student->id
        );

        return redirect()->route('students.index')->with('info', "Student {$name} has been archived & marked as suspended.");
    }
}