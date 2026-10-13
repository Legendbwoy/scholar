<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AcademicController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\AuditController;

/*
|--------------------------------------------------------------------------
| Public Landing & Marketing Pages
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/gallery', [LandingController::class, 'gallery'])->name('landing.gallery');
Route::get('/curriculum/{id}', [LandingController::class, 'courseDetail'])->whereNumber('id')->name('landing.course');
Route::post('/inquiry', [LandingController::class, 'submitInquiry'])->name('landing.inquiry');

/*
|--------------------------------------------------------------------------
| Authentication & Role Switcher
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/demo/{role}', [AuthController::class, 'switchRole'])->name('auth.demo.switch');

/*
|--------------------------------------------------------------------------
| Super Administrator Only Workspaces (Admin Role Exclusive)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Student Enrollment, Editing & Archiving
    // Bulk Student Excel Onboarding & Template
    Route::get('/students/bulk', [StudentController::class, 'bulkCreate'])->name('students.bulk');
    Route::get('/students/bulk/template', [StudentController::class, 'downloadTemplate'])->name('students.bulk.template');
    Route::post('/students/bulk/upload', [StudentController::class, 'bulkUpload'])->name('students.bulk.upload');

    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->whereNumber('id')->name('students.edit');
    Route::put('/students/{id}', [StudentController::class, 'update'])->whereNumber('id')->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->whereNumber('id')->name('students.destroy');

    // Faculty & Staff Complete HR / Payroll / Onboarding CRUD
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}', [StaffController::class, 'show'])->whereNumber('id')->name('staff.show');
    Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->whereNumber('id')->name('staff.edit');
    Route::put('/staff/{id}', [StaffController::class, 'update'])->whereNumber('id')->name('staff.update');
    Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->whereNumber('id')->name('staff.destroy');

    // Cohort & Curriculum Creation
    Route::post('/academic/classes', [AcademicController::class, 'storeClass'])->name('academic.classes.store');
    Route::post('/academic/subjects', [AcademicController::class, 'storeSubject'])->name('academic.subjects.store');

    // Security Ledger & Cryptographic Verification Audit Trail
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    Route::get('/audit/verify', [AuditController::class, 'verify'])->name('audit.verify');
});

/*
|--------------------------------------------------------------------------
| Faculty & Administration Workspaces (Admin & Teacher Roles)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    // Student Rosters (Faculty & Admins can inspect class lists)
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');

    // Cohorts & Curriculum Directories
    Route::get('/academic/classes', [AcademicController::class, 'classes'])->name('academic.classes');
    Route::get('/academic/subjects', [AcademicController::class, 'subjects'])->name('academic.subjects');

    // LMS Course Authoring & Content Management
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->whereNumber('id')->name('courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->whereNumber('id')->name('courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->whereNumber('id')->name('courses.destroy');
    Route::post('/courses/{id}/materials', [CourseController::class, 'storeMaterial'])->whereNumber('id')->name('courses.materials.store');
    Route::delete('/materials/{id}', [CourseController::class, 'destroyMaterial'])->whereNumber('id')->name('materials.destroy');

    // Attendance Operations & QR Scanning Terminal
    Route::get('/attendance/mark/{classId}', [AttendanceController::class, 'mark'])->whereNumber('classId')->name('attendance.mark');
    Route::post('/attendance/mark', [AttendanceController::class, 'storeMark'])->name('attendance.mark.store');
    Route::get('/attendance/qr', [AttendanceController::class, 'qrScanner'])->name('attendance.qr');
    Route::post('/attendance/qr/verify', [AttendanceController::class, 'verifyQr'])->name('attendance.qr.verify');

    // Institutional Broadcast Dispatch
    Route::get('/broadcasts/create', [CommunicationController::class, 'create'])->name('broadcasts.create');
    Route::post('/broadcasts', [CommunicationController::class, 'send'])->name('broadcasts.send');
});

/*
|--------------------------------------------------------------------------
| Authenticated Core Workspaces (Shared across all verified roles)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Attendance View (Dynamically tailors between Faculty register, Student log, and Guardian ward view)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // LMS & Course Exploration (All authenticated users can view enrolled courses & download learning assets)
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->whereNumber('id')->name('courses.show');
    Route::get('/materials/{id}/download', [CourseController::class, 'downloadMaterial'])->whereNumber('id')->name('materials.download');

    // Communications & Campus Bulletins
    Route::get('/broadcasts', [CommunicationController::class, 'index'])->name('broadcasts.index');
    Route::get('/broadcasts/{id}', [CommunicationController::class, 'show'])->whereNumber('id')->name('broadcasts.show');

    // Student 360 Profile (Student self-view, Guardian ward-view, or Staff oversight)
    Route::get('/students/{id}', [StudentController::class, 'show'])->whereNumber('id')->name('students.show');
});
