<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\User;

class LmsQuizSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $teacherUser = User::where('role', 'teacher')->first() ?? $admin;
        if (!$teacherUser) {
            return;
        }

        // Pick courses to attach quizzes to
        $courses = Course::take(3)->get();
        if ($courses->isEmpty()) {
            return;
        }

        foreach ($courses as $index => $course) {
            // 1. Weekly Knowledge Check (Quiz)
            $quiz1 = Quiz::updateOrCreate(
                ['course_id' => $course->id, 'title' => "{$course->title} - Module 1 Quick Knowledge Check"],
                [
                    'created_by' => $teacherUser->id,
                    'instructions' => "A short 15-minute assessment testing foundational principles covered in Week 1-2 lectures.",
                    'type' => 'quiz',
                    'grading_component' => 'quiz',
                    'duration_minutes' => 15,
                    'total_points' => 30,
                    'pass_points' => 18,
                    'shuffle_questions' => true,
                    'shuffle_options' => true,
                    'auto_submit_on_expiry' => true,
                    'show_correct_answers' => true,
                    'max_attempts' => 2,
                    'start_time' => now()->subDays(5),
                    'end_time' => now()->addDays(20),
                    'is_published' => true,
                ]
            );

            // Add Questions to Quiz 1
            $q1 = QuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz1->id, 'order_index' => 1],
                [
                    'question_text' => "What is the primary objective of modular code organization?",
                    'question_type' => 'mcq_single',
                    'points' => 10,
                    'explanation' => "Modular architecture isolates concerns, improves maintainability, and facilitates code reusability across modern software engineering workflows.",
                ]
            );

            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q1->id, 'option_text' => "To enhance maintainability, separation of concerns, and reusability."], ['is_correct' => true, 'order_index' => 1]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q1->id, 'option_text' => "To increase file sizes and compile times."], ['is_correct' => false, 'order_index' => 2]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q1->id, 'option_text' => "To bypass hardware CPU constraints entirely."], ['is_correct' => false, 'order_index' => 3]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q1->id, 'option_text' => "To eliminate all runtime testing necessities."], ['is_correct' => false, 'order_index' => 4]);

            $q2 = QuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz1->id, 'order_index' => 2],
                [
                    'question_text' => "Automated unit tests eliminate the need for any integration or manual smoke testing.",
                    'question_type' => 'true_false',
                    'points' => 10,
                    'explanation' => "False. Unit tests only verify components in isolation. Integration tests and end-to-end testing are still critical.",
                ]
            );

            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q2->id, 'option_text' => "True"], ['is_correct' => false, 'order_index' => 1]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q2->id, 'option_text' => "False"], ['is_correct' => true, 'order_index' => 2]);

            $q3 = QuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz1->id, 'order_index' => 3],
                [
                    'question_text' => "Which of the following are benefits of version control systems such as Git? (Select all that apply)",
                    'question_type' => 'mcq_multi',
                    'points' => 10,
                    'explanation' => "Git allows distributed branching, audit trails, and collaborative code integration.",
                ]
            );

            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q3->id, 'option_text' => "Traceable history of changes and commit audit logs."], ['is_correct' => true, 'order_index' => 1]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q3->id, 'option_text' => "Concurrent branch isolation for collaborative feature work."], ['is_correct' => true, 'order_index' => 2]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q3->id, 'option_text' => "Guaranteed automatic bug-free compile execution."], ['is_correct' => false, 'order_index' => 3]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $q3->id, 'option_text' => "Facilitates seamless rollback of breaking regressions."], ['is_correct' => true, 'order_index' => 4]);

            $quiz1->recalculateTotalPoints();

            // 2. Mid-Term Assessment / Examination
            $exam1 = Quiz::updateOrCreate(
                ['course_id' => $course->id, 'title' => "{$course->title} - Mid-Semester Comprehensive Examination"],
                [
                    'created_by' => $teacherUser->id,
                    'instructions' => "Official Mid-Semester examination covering core theory, analytical problems, and applied case studies. Requires timely completion within the scheduled window.",
                    'type' => 'midterm',
                    'grading_component' => 'midterm',
                    'duration_minutes' => 60,
                    'total_points' => 50,
                    'pass_points' => 25,
                    'shuffle_questions' => true,
                    'shuffle_options' => true,
                    'auto_submit_on_expiry' => true,
                    'show_correct_answers' => false,
                    'max_attempts' => 1,
                    'start_time' => now()->subDay(),
                    'end_time' => now()->addDays(14),
                    'is_published' => true,
                ]
            );

            // Add Questions to Exam 1
            $eq1 = QuizQuestion::updateOrCreate(
                ['quiz_id' => $exam1->id, 'order_index' => 1],
                [
                    'question_text' => "Explain the core distinction between synchronous and asynchronous operations in modern computing architectures.",
                    'question_type' => 'essay',
                    'points' => 20,
                    'explanation' => "Synchronous execution blocks subsequent threads until completion; asynchronous processing delegates background jobs and invokes callbacks/promises without freezing the execution thread.",
                ]
            );

            $eq2 = QuizQuestion::updateOrCreate(
                ['quiz_id' => $exam1->id, 'order_index' => 2],
                [
                    'question_text' => "Which protocol provides encrypted end-to-end transport communication for modern web applications?",
                    'question_type' => 'mcq_single',
                    'points' => 15,
                    'explanation' => "HTTPS uses TLS/SSL encryption to secure HTTP communication between client and server.",
                ]
            );

            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $eq2->id, 'option_text' => "HTTPS (Hypertext Transfer Protocol Secure) via TLS"], ['is_correct' => true, 'order_index' => 1]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $eq2->id, 'option_text' => "FTP (File Transfer Protocol)"], ['is_correct' => false, 'order_index' => 2]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $eq2->id, 'option_text' => "Telnet"], ['is_correct' => false, 'order_index' => 3]);
            QuizQuestionOption::updateOrCreate(['quiz_question_id' => $eq2->id, 'option_text' => "HTTP/1.0 unencrypted"], ['is_correct' => false, 'order_index' => 4]);

            $eq3 = QuizQuestion::updateOrCreate(
                ['quiz_id' => $exam1->id, 'order_index' => 3],
                [
                    'question_text' => "What standard acronym represents the architectural paradigm consisting of Create, Read, Update, and Delete operations in persistent databases?",
                    'question_type' => 'short_answer',
                    'points' => 15,
                    'acceptable_answers' => "CRUD, crud, C.R.U.D",
                    'explanation' => "CRUD stands for Create, Read, Update, Delete.",
                ]
            );

            $exam1->recalculateTotalPoints();
        }
    }
}
