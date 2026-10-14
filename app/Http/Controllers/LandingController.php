<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\GalleryItem;
use App\Models\Event;
use App\Models\Staff;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AuditTrail;

class LandingController extends Controller
{
    public function index()
    {
        $courses = Course::with(['teacher.user', 'subject', 'schoolClass'])
            ->where('is_published', true)
            ->get();

        $galleryItems = GalleryItem::orderBy('is_featured', 'desc')->take(9)->get();
        $events = Event::published()->upcoming()->take(4)->get();
        $staffMembers = Staff::with(['user', 'department'])->take(4)->get();

        // Calculate dynamic real attendance rate if records exist
        $totalAttendance = \App\Models\Attendance::count();
        $presentAttendance = \App\Models\Attendance::whereIn('status', ['present', 'late'])->count();
        $calculatedRate = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) . '%' : '98.5%';

        $stats = [
            'students' => setting('landing_stats_students') ? (int)setting('landing_stats_students') : Student::count(),
            'faculty' => setting('landing_stats_faculty') ? (int)setting('landing_stats_faculty') : Staff::count(),
            'courses' => setting('landing_stats_courses') ? (int)setting('landing_stats_courses') : Course::count(),
            'awards' => (int)setting('landing_stats_awards', 28),
            'attendance_rate' => setting('landing_stats_attendance', $calculatedRate),
        ];

        return view('landing.index', compact('courses', 'galleryItems', 'events', 'staffMembers', 'stats'));
    }

    public function gallery()
    {
        $galleryItems = GalleryItem::all();
        $categories = GalleryItem::select('category')->distinct()->pluck('category');
        return view('landing.gallery', compact('galleryItems', 'categories'));
    }

    public function courseDetail($id)
    {
        $course = Course::with(['teacher.user', 'subject', 'schoolClass', 'materials'])->findOrFail($id);
        $relatedCourses = Course::where('id', '!=', $id)->take(3)->get();
        return view('landing.course-detail', compact('course', 'relatedCourses'));
    }

    public function submitInquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'interest' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $interest = $validated['interest'] ?? 'General Admissions';

        AuditTrail::log(
            'ADMISSION_INQUIRY',
            "Prospective student/parent inquiry received from {$validated['name']} ({$validated['email']}) for {$interest}.",
            'Inquiry',
            null,
            $validated
        );

        return redirect()->to(url()->previous() . '#contact')
            ->with('success', 'Thank you! Your admission inquiry has been logged. Our admissions office will contact you within 24 hours.');
    }
}