<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Staff;
use App\Models\SchoolClass;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Attendance;
use App\Models\BroadcastMessage;
use App\Models\BroadcastDelivery;
use App\Models\AuditTrail;
use App\Models\GalleryItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Key Users
        $adminUser = User::create([
            'name' => 'Dr. Evelyn Vance',
            'email' => 'admin@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1 (555) 234-5670',
            'avatar' => 'scholar/assets/images/member-01.jpg',
            'status' => 'active',
        ]);

        $teacherUser1 = User::create([
            'name' => 'Prof. Marcus Sterling',
            'email' => 'teacher@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'phone' => '+1 (555) 345-6781',
            'avatar' => 'scholar/assets/images/member-02.jpg',
            'status' => 'active',
        ]);

        $teacherUser2 = User::create([
            'name' => 'Dr. Elena Rostova',
            'email' => 'teacher2@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'phone' => '+1 (555) 456-7892',
            'avatar' => 'scholar/assets/images/member-03.jpg',
            'status' => 'active',
        ]);

        $teacherUser3 = User::create([
            'name' => 'Sarah Jenkins, M.A.',
            'email' => 'teacher3@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
            'phone' => '+1 (555) 567-8903',
            'avatar' => 'scholar/assets/images/member-04.jpg',
            'status' => 'active',
        ]);

        $parentUser1 = User::create([
            'name' => 'Arthur Cole',
            'email' => 'parent@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'phone' => '+1 (555) 678-9014',
            'avatar' => 'scholar/assets/images/testimonial-author.jpg',
            'status' => 'active',
        ]);

        $parentUser2 = User::create([
            'name' => 'Jonathan Bennett',
            'email' => 'parent2@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'phone' => '+1 (555) 789-0125',
            'avatar' => 'scholar/assets/images/testimonial-author.jpg',
            'status' => 'active',
        ]);

        $studentUser1 = User::create([
            'name' => 'Alexander Cole',
            'email' => 'student@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1 (555) 890-1236',
            'avatar' => 'scholar/assets/images/event-01.jpg',
            'status' => 'active',
        ]);

        $studentUser2 = User::create([
            'name' => 'Chloe Bennett',
            'email' => 'student2@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1 (555) 901-2347',
            'avatar' => 'scholar/assets/images/event-02.jpg',
            'status' => 'active',
        ]);

        $studentUser3 = User::create([
            'name' => 'Daniel Miller',
            'email' => 'student3@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1 (555) 012-3458',
            'avatar' => 'scholar/assets/images/event-03.jpg',
            'status' => 'active',
        ]);

        $studentUser4 = User::create([
            'name' => 'Emily Watson',
            'email' => 'student4@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1 (555) 123-4569',
            'avatar' => 'scholar/assets/images/course-01.jpg',
            'status' => 'active',
        ]);

        $studentUser5 = User::create([
            'name' => 'Felix Chen',
            'email' => 'student5@scholar.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '+1 (555) 234-5678',
            'avatar' => 'scholar/assets/images/course-02.jpg',
            'status' => 'active',
        ]);

        // 2. Departments
        $deptStem = Department::create([
            'name' => 'Science, Technology & Computing',
            'code' => 'STEM',
            'description' => 'Advanced computational sciences, physics, chemistry and engineering fundamentals.',
        ]);

        $deptMath = Department::create([
            'name' => 'Mathematics & Applied Statistics',
            'code' => 'MATH',
            'description' => 'Pure and applied mathematical reasoning, calculus, logic and data foundations.',
        ]);

        $deptHumanities = Department::create([
            'name' => 'Humanities, Literature & Languages',
            'code' => 'HUM',
            'description' => 'Global history, classical rhetoric, foreign languages and literary criticism.',
        ]);

        // 3. Staff Profiles
        $staff1 = Staff::create([
            'user_id' => $teacherUser1->id,
            'department_id' => $deptMath->id,
            'employee_id' => 'EMP-1001',
            'designation' => 'Head of Department & Senior Lecturer',
            'qualification' => 'Ph.D. Applied Mathematics (MIT)',
            'joining_date' => '2021-08-15',
            'specialization' => 'Calculus, Linear Algebra & Probability',
            'emergency_contact' => '+1 (555) 999-0001',
            'salary' => 84000.00,
        ]);

        $staff2 = Staff::create([
            'user_id' => $teacherUser2->id,
            'department_id' => $deptStem->id,
            'employee_id' => 'EMP-1002',
            'designation' => 'Senior Physics Fellow',
            'qualification' => 'Ph.D. Astrophysics (Oxford)',
            'joining_date' => '2022-01-10',
            'specialization' => 'Quantum Mechanics & Thermodynamics',
            'emergency_contact' => '+1 (555) 999-0002',
            'salary' => 81500.00,
        ]);

        $staff3 = Staff::create([
            'user_id' => $teacherUser3->id,
            'department_id' => $deptHumanities->id,
            'employee_id' => 'EMP-1003',
            'designation' => 'Associate Professor of Literature',
            'qualification' => 'M.A. English Literature (Columbia)',
            'joining_date' => '2023-09-01',
            'specialization' => 'Creative Writing & Modern World Texts',
            'emergency_contact' => '+1 (555) 999-0003',
            'salary' => 74000.00,
        ]);

        // 4. School Classes
        $class10A = SchoolClass::create([
            'name' => 'Grade 10 - Alpha',
            'numeric_grade' => 10,
            'section' => 'A',
            'room_number' => 'Hall 101',
            'capacity' => 35,
            'class_teacher_id' => $staff1->id,
        ]);

        $class11A = SchoolClass::create([
            'name' => 'Grade 11 - STEM Honors',
            'numeric_grade' => 11,
            'section' => 'A',
            'room_number' => 'Science Wing 204',
            'capacity' => 30,
            'class_teacher_id' => $staff2->id,
        ]);

        $class12A = SchoolClass::create([
            'name' => 'Grade 12 - Collegiate Prep',
            'numeric_grade' => 12,
            'section' => 'Honors',
            'room_number' => 'Academy Lab 305',
            'capacity' => 28,
            'class_teacher_id' => $staff3->id,
        ]);

        // 5. Guardians
        $guardian1 = Guardian::create([
            'user_id' => $parentUser1->id,
            'occupation' => 'Senior Systems Architect',
            'relationship' => 'Father',
            'address' => '742 Evergreen Terrace, Tech District',
            'emergency_phone' => '+1 (555) 678-9014',
        ]);

        $guardian2 = Guardian::create([
            'user_id' => $parentUser2->id,
            'occupation' => 'Biomedical Researcher',
            'relationship' => 'Father',
            'address' => '104 Beacon Hill Avenue, Innovation Park',
            'emergency_phone' => '+1 (555) 789-0125',
        ]);

        // 6. Students
        $student1 = Student::create([
            'user_id' => $studentUser1->id,
            'guardian_id' => $guardian1->id,
            'school_class_id' => $class10A->id,
            'admission_number' => 'SCH-2026-001',
            'dob' => '2010-04-12',
            'gender' => 'Male',
            'blood_group' => 'O+',
            'admission_date' => '2024-09-01',
            'roll_number' => '10A-01',
            'status' => 'active',
        ]);

        $student2 = Student::create([
            'user_id' => $studentUser2->id,
            'guardian_id' => $guardian2->id,
            'school_class_id' => $class10A->id,
            'admission_number' => 'SCH-2026-002',
            'dob' => '2010-08-25',
            'gender' => 'Female',
            'blood_group' => 'A+',
            'admission_date' => '2024-09-01',
            'roll_number' => '10A-02',
            'status' => 'active',
        ]);

        $student3 = Student::create([
            'user_id' => $studentUser3->id,
            'guardian_id' => $guardian1->id,
            'school_class_id' => $class10A->id,
            'admission_number' => 'SCH-2026-003',
            'dob' => '2010-02-18',
            'gender' => 'Male',
            'blood_group' => 'B+',
            'admission_date' => '2024-09-01',
            'roll_number' => '10A-03',
            'status' => 'active',
        ]);

        $student4 = Student::create([
            'user_id' => $studentUser4->id,
            'guardian_id' => $guardian2->id,
            'school_class_id' => $class11A->id,
            'admission_number' => 'SCH-2026-004',
            'dob' => '2009-11-03',
            'gender' => 'Female',
            'blood_group' => 'AB+',
            'admission_date' => '2023-09-01',
            'roll_number' => '11A-01',
            'status' => 'active',
        ]);

        $student5 = Student::create([
            'user_id' => $studentUser5->id,
            'guardian_id' => null,
            'school_class_id' => $class11A->id,
            'admission_number' => 'SCH-2026-005',
            'dob' => '2009-06-19',
            'gender' => 'Male',
            'blood_group' => 'O-',
            'admission_date' => '2023-09-01',
            'roll_number' => '11A-02',
            'status' => 'active',
        ]);

        // 7. Subjects
        $subMath = Subject::create([
            'name' => 'Advanced Applied Mathematics',
            'code' => 'MTH-201',
            'school_class_id' => $class10A->id,
            'teacher_id' => $staff1->id,
            'credit_hours' => 4,
            'description' => 'Quadratic functions, polynomial analysis, analytic geometry and introductory vectors.',
        ]);

        $subPhys = Subject::create([
            'name' => 'Classical & Modern Physics',
            'code' => 'PHY-202',
            'school_class_id' => $class10A->id,
            'teacher_id' => $staff2->id,
            'credit_hours' => 4,
            'description' => 'Kinematics, dynamics, optical principles, electromagnetic wave interactions.',
        ]);

        $subEng = Subject::create([
            'name' => 'World Literature & Rhetoric',
            'code' => 'ENG-101',
            'school_class_id' => $class10A->id,
            'teacher_id' => $staff3->id,
            'credit_hours' => 3,
            'description' => 'Critical literary reading, persuasive essay writing and public discourse presentation.',
        ]);

        $subCsc = Subject::create([
            'name' => 'Computer Science & Algorithms',
            'code' => 'CSC-301',
            'school_class_id' => $class11A->id,
            'teacher_id' => $staff1->id,
            'credit_hours' => 4,
            'description' => 'Data structures, algorithm complexity, object-oriented software engineering.',
        ]);

        // 8. Courses
        $course1 = Course::create([
            'subject_id' => $subMath->id,
            'school_class_id' => $class10A->id,
            'teacher_id' => $staff1->id,
            'title' => 'Mastering Differential Calculus & Analytical Geometry',
            'code' => 'CRS-MTH-10A',
            'semester' => 'Fall 2026',
            'description' => 'Comprehensive masterclass covering limits, differential quotients, optimization models and geometric proofs with interactive quizzes.',
            'thumbnail' => 'scholar/assets/images/course-01.jpg',
            'category' => 'Mathematics',
            'total_lessons' => 18,
            'is_published' => true,
        ]);

        $course2 = Course::create([
            'subject_id' => $subPhys->id,
            'school_class_id' => $class10A->id,
            'teacher_id' => $staff2->id,
            'title' => 'Experimental Mechanics & Modern Wave Theory',
            'code' => 'CRS-PHY-10A',
            'semester' => 'Fall 2026',
            'description' => 'Hands-on laboratory explorations into Newtonian force balances, fluid mechanics, wave equations and laboratory measurement rigor.',
            'thumbnail' => 'scholar/assets/images/course-02.jpg',
            'category' => 'Sciences',
            'total_lessons' => 14,
            'is_published' => true,
        ]);

        $course3 = Course::create([
            'subject_id' => $subEng->id,
            'school_class_id' => $class10A->id,
            'teacher_id' => $staff3->id,
            'title' => 'Global Literary Masterpieces & Public Argumentation',
            'code' => 'CRS-ENG-10A',
            'semester' => 'Fall 2026',
            'description' => 'Deep analytical seminar investigating post-colonial narratives, Shakespearean tragic structures, and debate rhetoric.',
            'thumbnail' => 'scholar/assets/images/course-03.jpg',
            'category' => 'Humanities',
            'total_lessons' => 12,
            'is_published' => true,
        ]);

        $course4 = Course::create([
            'subject_id' => $subCsc->id,
            'school_class_id' => $class11A->id,
            'teacher_id' => $staff1->id,
            'title' => 'Algorithms, Modern Python & Applied Systems Architecture',
            'code' => 'CRS-CSC-11A',
            'semester' => 'Fall 2026',
            'description' => 'Full-stack computational thinking, sorting paradigms, graph algorithms, and cloud containerization basics.',
            'thumbnail' => 'scholar/assets/images/course-04.jpg',
            'category' => 'Technology',
            'total_lessons' => 16,
            'is_published' => true,
        ]);

        // 9. LMS Course Materials
        CourseMaterial::create([
            'course_id' => $course1->id,
            'teacher_id' => $staff1->id,
            'title' => 'Calculus Lecture Unit 1: Foundations of Limits & Continuity',
            'description' => 'Official course module notes, worked proofs, step-by-step epsilon-delta explanations and homework problem set 1.',
            'file_type' => 'pdf',
            'file_path' => 'materials/calculus_unit_1.pdf',
            'file_size' => '4.2 MB',
            'download_count' => 142,
            'version' => '1.2',
        ]);

        CourseMaterial::create([
            'course_id' => $course1->id,
            'teacher_id' => $staff1->id,
            'title' => 'Interactive Graphing & Geometric Differentiation Slides',
            'description' => 'Slide deck with animated 3D Cartesian coordinates and interactive Desmos graphing models.',
            'file_type' => 'slides',
            'file_path' => 'materials/differentiation_slides.pptx',
            'file_size' => '8.6 MB',
            'download_count' => 98,
            'version' => '2.0',
        ]);

        CourseMaterial::create([
            'course_id' => $course2->id,
            'teacher_id' => $staff2->id,
            'title' => 'Laboratory Protocol: Projectile Kinematics & High-Speed Photogate Data',
            'description' => 'Standard laboratory safety rules, error propagation formulas and student measurement worksheet.',
            'file_type' => 'pdf',
            'file_path' => 'materials/physics_lab_kinematics.pdf',
            'file_size' => '2.9 MB',
            'download_count' => 114,
            'version' => '1.0',
        ]);

        CourseMaterial::create([
            'course_id' => $course4->id,
            'teacher_id' => $staff1->id,
            'title' => 'Algorithms Sandbox: Python Graph Traversal Starter Repo',
            'description' => 'Complete starter code with automated pytest unit suites for BFS, DFS, and Dijkstra shortest paths.',
            'file_type' => 'document',
            'file_path' => 'materials/graph_traversal_code.zip',
            'file_size' => '1.5 MB',
            'download_count' => 83,
            'version' => '1.1',
        ]);

        // 10. Realistic Attendance Records
        $pastDays = [
            now()->subDays(4)->toDateString(),
            now()->subDays(3)->toDateString(),
            now()->subDays(2)->toDateString(),
            now()->subDays(1)->toDateString(),
            now()->toDateString(),
        ];

        $studentList = [$student1, $student2, $student3, $student4, $student5];

        foreach ($pastDays as $dayIndex => $dateStr) {
            foreach ($studentList as $s) {
                // Determine a realistic status
                $rand = rand(1, 100);
                $status = 'present';
                $remarks = 'Regular on-time arrival';
                $method = 'manual';

                if ($s->id === $student3->id && $dayIndex === 4) {
                    $status = 'absent';
                    $remarks = 'Unexcused morning absence; automated parent SMS dispatched.';
                } elseif ($rand > 90) {
                    $status = 'late';
                    $remarks = 'Transit delay (12 mins late)';
                    $method = 'qr_code';
                } elseif ($rand > 96) {
                    $status = 'excused';
                    $remarks = 'Medical appointment confirmed by parent note';
                }

                Attendance::updateOrCreate(
                    ['student_id' => $s->id, 'date' => $dateStr],
                    [
                        'school_class_id' => $s->school_class_id ?? $class10A->id,
                        'status' => $status,
                        'remarks' => $remarks,
                        'marked_by' => $teacherUser1->id,
                        'method' => $method,
                    ]
                );
            }
        }

        // 11. Ultra-Modern Broadcast Messages & Multi-Channel Deliveries
        $broadcast1 = BroadcastMessage::create([
            'sender_id' => $adminUser->id,
            'title' => 'Urgent: Campus Schedule Update — Severe Weather Protocol Active',
            'message' => 'Dear Scholar Community, all afternoon extracurricular activities and after-school tutoring are shifted online today due to metropolitan weather advisories. School bus lines will depart promptly at 3:15 PM.',
            'target_audience' => 'all',
            'school_class_id' => null,
            'channels' => ['email', 'sms', 'whatsapp', 'in_app'],
            'priority' => 'urgent',
            'status' => 'sent',
            'sent_at' => now()->subHours(5),
        ]);

        $broadcast2 = BroadcastMessage::create([
            'sender_id' => $teacherUser1->id,
            'title' => 'Grade 10 Mathematics: Calculus Midterm Diagnostic Exam Scope',
            'message' => 'To all Grade 10 students and guardians: The Midterm Diagnostic Review document has been published in your LMS Materials portal. Please bring scientific calculators to Tuesday\'s session.',
            'target_audience' => 'class',
            'school_class_id' => $class10A->id,
            'channels' => ['email', 'in_app'],
            'priority' => 'normal',
            'status' => 'sent',
            'sent_at' => now()->subDay(),
        ]);

        // Deliveries for broadcast 1
        $recipients = [$parentUser1, $parentUser2, $teacherUser1, $teacherUser2, $studentUser1, $studentUser2];
        foreach ($recipients as $rec) {
            BroadcastDelivery::create([
                'broadcast_message_id' => $broadcast1->id,
                'recipient_id' => $rec->id,
                'channel' => 'sms',
                'recipient_contact' => $rec->phone ?? '+1 (555) 000-0000',
                'status' => 'delivered',
                'delivered_at' => now()->subHours(5),
            ]);
            BroadcastDelivery::create([
                'broadcast_message_id' => $broadcast1->id,
                'recipient_id' => $rec->id,
                'channel' => 'email',
                'recipient_contact' => $rec->email,
                'status' => 'delivered',
                'delivered_at' => now()->subHours(5),
            ]);
            BroadcastDelivery::create([
                'broadcast_message_id' => $broadcast1->id,
                'recipient_id' => $rec->id,
                'channel' => 'in_app',
                'recipient_contact' => $rec->email,
                'status' => 'delivered',
                'delivered_at' => now()->subHours(5),
            ]);
        }

        // 12. Immutable Chained Audit Trail
        AuditTrail::log(
            'SYSTEM_INIT',
            'System initialization: Root academic session Fall 2026 deployed with cryptographic ledger verification enabled.',
            'System',
            1
        );

        AuditTrail::log(
            'USER_CREATED',
            'Administrative account Dr. Evelyn Vance provisioned with RBAC role: Super Admin.',
            'User',
            $adminUser->id
        );

        AuditTrail::log(
            'ATTENDANCE_MARKED',
            'Bulk attendance confirmed for Grade 10 - Alpha. 5 records validated and sealed.',
            'SchoolClass',
            $class10A->id
        );

        AuditTrail::log(
            'BROADCAST_SENT',
            'Emergency multi-channel broadcast (SMS + Email + WhatsApp) dispatched to 18 recipients.',
            'BroadcastMessage',
            $broadcast1->id
        );

        AuditTrail::log(
            'MATERIAL_PUBLISHED',
            'LMS material "Calculus Lecture Unit 1" uploaded and made downloadable for Grade 10 students.',
            'CourseMaterial',
            1
        );

        // 13. Gallery Items
        $galleryItems = [
            [
                'title' => 'Robotics & Computing Innovation Lab',
                'category' => 'Laboratories',
                'image_path' => 'scholar/assets/images/course-01.jpg',
                'caption' => 'Students collaborating on autonomous obstacle rovers with optical sensors.',
                'is_featured' => true,
            ],
            [
                'title' => 'Annual International STEM Olympiad',
                'category' => 'Events',
                'image_path' => 'scholar/assets/images/event-01.jpg',
                'caption' => 'Over 40 regional schools participating in our state-of-the-art auditorium.',
                'is_featured' => true,
            ],
            [
                'title' => 'Modern Olympic Swimming & Athletics Complex',
                'category' => 'Sports',
                'image_path' => 'scholar/assets/images/banner-item-01.jpg',
                'caption' => 'State champion aquatics training facility with heated indoor lanes.',
                'is_featured' => true,
            ],
            [
                'title' => 'Fine Arts & Symphony Amphitheater',
                'category' => 'Arts & Culture',
                'image_path' => 'scholar/assets/images/event-02.jpg',
                'caption' => 'Spring orchestral performance featuring our student chamber ensemble.',
                'is_featured' => true,
            ],
            [
                'title' => 'Collaborative Central Digital Library',
                'category' => 'Campus Life',
                'image_path' => 'scholar/assets/images/course-03.jpg',
                'caption' => 'Silent research pods, high-speed fiber terminals, and over 60,000 volumes.',
                'is_featured' => true,
            ],
            [
                'title' => 'Biotechnology Genetic Sequencing Suite',
                'category' => 'Laboratories',
                'image_path' => 'scholar/assets/images/course-02.jpg',
                'caption' => 'Hands-on CRISPR simulation and molecular biology research bench.',
                'is_featured' => true,
            ],
        ];

        foreach ($galleryItems as $item) {
            GalleryItem::create($item);
        }
    }
}