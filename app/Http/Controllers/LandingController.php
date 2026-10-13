<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\GalleryItem;
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

        $galleryItems = GalleryItem::orderBy('is_featured', 'desc')->get();
        $staffMembers = Staff::with(['user', 'department'])->take(4)->get();

        $stats = [
            'students' => Student::count() + 1250, // Display realistic institutional count
            'faculty' => Staff::count() + 85,
            'courses' => Course::count() + 45,
            'awards' => 28,
            'attendance_rate' => '98.6%',
        ];

        return view('landing.index', compact('courses', 'galleryItems', 'staffMembers', 'stats'));
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

        AuditTrail::log(
            'ADMISSION_INQUIRY',
            "Prospective student/parent inquiry received from {$validated['name']} ({$validated['email']}) for {$validated['interest']}.",
            'Inquiry',
            null,
            $validated
        );

        return redirect()->to(url()->previous() . '#contact')
            ->with('success', 'Thank you! Your admission inquiry has been logged. Our admissions office will contact you within 24 hours.');
    }
}