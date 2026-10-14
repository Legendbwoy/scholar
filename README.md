<p align="center">
  <a href="https://github.com/Legendbwoy/scholar" target="_blank">
    <img src="public/scholar/assets/images/banner-item-01.jpg" alt="Scholar SMS Banner" width="100%" style="border-radius: 16px; max-height: 380px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
  </a>
</p>

<h1 align="center">Scholar SMS</h1>
<h3 align="center">Enterprise-Grade Modular School Management System & Modern Digital LMS Suite</h3>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"></a>
  <a href="https://getbootstrap.com/"><img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5.3"></a>
  <img src="https://img.shields.io/badge/LMS-Assessments_&_CBT-009961?style=for-the-badge" alt="LMS CBT">
  <img src="https://img.shields.io/badge/Ledger-SHA--256_Chained-38a169?style=for-the-badge" alt="SHA-256 Ledger">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="MIT License">
</p>

---

## Executive Summary

**Scholar SMS** is a modern, modular monolith web application designed to unify academic administration, classroom instruction, student testing, parent-school transparency, and institutional marketing.

Built on **Laravel 11** and customized with the vibrant **Scholar v1.0.0** theme, the system delivers role-segregated workspaces for **Super Administrators**, **Faculty Members**, **Students**, and **Guardians**. It features a modern **Digital LMS Testing & Examinations Suite**, omni-channel messaging (SMS, WhatsApp, Email, In-App), dynamic calendar & holiday attendance safeguards, dual-grading configuration, contactless QR attendance, dynamic events/gallery management, and a cryptographically chained (SHA-256) immutable audit trail.

---

## ️ System Architecture

Scholar SMS follows a **Domain-Driven Modular Monolith** architecture:
```
school-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Dedicated Domain Controllers (Quiz, Player, Gradebook, Gateways, etc.)
│   │   ├── Middleware/          # CheckRole RBAC Middleware & Security Filters
│   ├── Models/                  # Eloquent Entities with Relationships, Auto-Hashing & Evaluators
├── database/
│   ├── migrations/              # Relational Schema with Foreign Constraints, Cascades & Indexes
│   ├── seeders/                 # Realistic Seed Data (Quizzes, Questions, Cohorts, Staff, Calendars)
├── public/scholar/              # Integrated Scholar v1.0.0 UI Assets (CSS, JS, Fonts, Images)
├── resources/views/
│   ├── layouts/
│   │   ├── public.blade.php     # Public Landing & Showcase Layout with High-Contrast Footer
│   │   ├── portal.blade.php     # Responsive Authenticated Workspace Shell with Collapsible Nav CSS
│   │   └── navigation/          # Role-Segregated Collapsible Navigation Partials
│   │       ├── admin.blade.php  # Admin Accordion Navigation
│   │       ├── teacher.blade.php# Faculty Accordion Navigation
│   │       ├── student.blade.php# Student Workspace Navigation
│   │       └── parent.blade.php # Guardian Oversight Navigation
│   ├── landing/                 # Public Landing, Course Catalog, Event Showcase & Campus Gallery
│   └── portals/                 # Role-Guarded Application Views
│       ├── lms/
│       │   └── quizzes/         # CBT Exam Player, Q&A Studio, Submissions, Rubric Grading & Results
│       ├── courses/             # LMS Course Syllabi with Dual Materials & Assessments Tabs
│       ├── students/            # Complete Student CRUD, Bulk Excel & 360 Profile
│       ├── staff/               # Faculty HR, Qualifications & Payroll Directory
│       ├── academic/            # Classes, Subjects, Holiday Calendar & Dual-Grading Policies
│       ├── attendance/          # Daily Registers, Personal Logs & QR Terminal
│       ├── comms/               # Multi-Channel Dispatch Hub & Template Engine
│       ├── settings/gateways/   # SMTP, SMS & WhatsApp Token Configuration
│       └── audit/               # Immutable Audit Ledger & Verification Tool
└── routes/
    ├── web.php                  # Strict RBAC-Guarded Application Routes
    └── api.php                  # RESTful API Endpoints with Health & Ingestion
```

---

## Key Features & Capabilities

### 1.  Modern LMS Assessments, Quizzes & Examinations Suite
- **Interactive Student Testing Terminal (CBT)**:
  - Clean examination environment with live countdown timer and animated visual alert when under 5 minutes.
  - Asynchronous background auto-saving via AJAX so candidate progress is preserved across network interruptions.
  - Interactive **Question Palette Matrix** with color-coded navigation (answered, unanswered, active question).
  - Server-side countdown enforcement with automatic freeze and submission upon timer expiry.
  - **Timed Access Control & Publishing Gates**: Strict window gating (`start_time` and `end_time`) ensuring unpublished or scheduled exams lock students out until the scheduled start time, and automatically expire upon deadline.
- **Bulk Question Upload & Excel/CSV Template**:
  - One-click downloadable spreadsheet template (`.csv` / `.xlsx`) pre-populated with standard columns (`question_type`, `question_text`, `points`, `option_a` through `d`, `correct_options`, `acceptable_answers`, `explanation`).
  - High-speed bulk parsing engine importing hundreds of questions instantly, mapping choices, and updating quiz total points.
- **Institutional Question Banks (Editable & Reusable Repositories)**:
  - Centralized question repositories organized by subject, course, and syllabus topic.
  - **Export to Bank**: Save entire quizzes or tests directly into question banks for future multi-cohort reuse.
  - **Import from Bank**: Clone curated items from any bank directly into new quizzes, tests, midterms, or final exams.
- **Q&A Authoring Studio for Faculty**:
  - Author and manage questions across **5 distinct question formats**:
    1. **Single-Choice Multiple Choice (MCQ Single)**
    2. **Multiple Select Choice (MCQ Multi)**
    3. **True / False**
    4. **Short Answer** (with comma-separated acceptable keyword matching)
    5. **Subjective Long Essay** (with scoring rubrics and teacher feedback commentary)
  - Configure mark weights, randomize question orders, and shuffle MCQ option choices to prevent cheating.
  - Provide model answers and solution explanations displayed to students upon completion.
- ** Centralized Question Banks & Reusable Repositories**:
  - Dedicated Question Bank library categorized by Subject, Grade Level, and Difficulty.
  - Export active quizzes/exams directly into Question Banks for archival or cross-cohort reuse.
  - Selectively import curated questions from Question Banks into any quiz, test, or examination in one click.
- ** Bulk Questions Upload & Downloadable Spreadsheet Template**:
  - Downloadable standardized Excel / CSV question authoring template (`/lms/quizzes/template/download`) with full column specifications.
  - Batch upload hundreds of questions (MCQ Single, MCQ Multi, True/False, Short Answer, Essay) with automatic choice mapping, option validation, and assessment point recalculation.
- **⏱️ Timed Assessment Access Gates & Publishing Enforcement**:
  - **Draft Protection**: Draft quizzes remain inaccessible to student candidates until officially published.
  - **Start Window Barrier**: Future-dated assessments enforce a locked gate displaying the exact opening date/time and preventing premature attempt initiation.
  - **End Window Lockdown**: Expired assessments strictly reject candidate entry past the deadline.
  - **Enforced Session Expiry**: In-flight candidate exam timers are capped by the assessment's closing deadline, preventing attempts from running past the official examination end time.
- **Automated & Rubric-Based Grading**:
  - Instant auto-grading for objective questions (MCQs, True/False, Short Answer).
  - Dedicated **Teacher Rubric Review Console** to evaluate open-ended essay questions, award marks, provide bespoke commentary, and sync final scores with the institutional gradebook.
- **Detailed Candidate Performance Report**:
  - Score cards with percentage calculation, pass/fail threshold indicators, duration taken, attempt counting, and question-by-question review.

---

### 2.  Academic Period, Calendar & Holiday Configuration
- **Term & Semester Calendars**:
  - Configure academic years, semester terms, and operational date windows.
- **Holiday & Non-Instructional Safeguards**:
  - Register national, regional, religious, and school-declared holidays.
  - Daily attendance modules cross-reference active holidays so attendance rates are not penalized on non-instructional days.
- **Dual-Grading Policy Hub**:
  - Configure dual-grading ratios between **Online LMS coursework** and **Offline Classroom assessments** (e.g. 40% LMS + 60% Offline Examination) per course.
  - Automatically merges digital quiz scores and physical test marks into standardized terminal report cards.

---

### 3. ️ Communication Gateways & API Configuration
- **Centralized Admin Gateway Settings**:
  - **Email / SMTP Setup**: Configure SMTP host, port, authentication credentials, and encryption protocols (`TLS` / `SSL`).
  - **SMS Gateway**: Configure API providers (Twilio, Infobip, etc.), API keys, sender IDs, and balance thresholds.
  - **WhatsApp Cloud API**: Configure WhatsApp Business Account IDs, phone number IDs, access tokens, and webhook verification tokens.
- **Live Connection Testing**:
  - Instant `Test Connection` trigger directly from the administrative dashboard to verify gateway health before dispatching broadcasts.

---

### 4.  Multi-Channel Broadcast Template Engine
- **Pre-Built & Custom Templates**:
  - Create and curate reusable notification templates for:
    - Seasonal & holiday greetings (Christmas, Eid, New Year)
    - Gender-specific advisories & events
    - Emergency weather & closure notifications
    - Fee reminder alerts
- **Automated & Manual Scheduling**:
  - Toggle automated dispatch on designated holiday dates or trigger instant multi-channel broadcasts.

---

### 5. ️ Dynamic Events & Campus Life Gallery CRUD
- **Institutional Event Management**:
  - Full CRUD interface for campus events (symposiums, sports meets, open days) with title, date, venue, category, and publishing status.
  - Synchronizes with the public landing page events section.
- **Media Gallery Showcase**:
  - Upload, categorize, and feature campus photography without hardcoded image assets.
  - Dynamic filtering by category (Campus Life, Sports, Academic Labs, Ceremonies) on the landing page.

---

### 6.  User Profile & Custom Avatar Picture Management
- **Role-Wide Avatar Uploads**:
  - Self-service avatar picture upload and removal for Administrators, Teachers, Students, and Parents.
  - Automatically replaces placeholder graphics across portal sidebars, student 360 files, and header bars.

---

### 7. ️ Collapsible Parent-Sub Sidebar Navigation
- **Accordion Dropdown Menus**:
  - Organized portal navigation menus with parent-sub hierarchy:
    - *Registry & Faculty HR* (Students Roster, Enroll Student, Bulk Excel, Faculty Directory, Onboard Faculty)
    - *Cohorts & Curriculum* (Academic Calendar, Grading Policies, Cohorts & Classes, Curriculum Subjects)
    - *Assessments & Grading* (Gradebook & Matrix, Student Report Cards)
    - *Digital LMS & Courses* (All Courses, Provision Course, Tests & Exams, Author Assessment)
    - *Communication Desk* (Noticeboard, Broadcast Dispatch, Broadcast Templates, Gateway APIs)
  - Auto-expands active parent menus based on current route matching with animated rotating chevrons.

---

### 8. ️ Role-Based Access Control (RBAC) & Cryptographic Audit
- **Strict Role-Segregated Views**:
  - Protected by `CheckRole` middleware (`admin`, `teacher`, `student`, `parent`).
  - Unauthorized access attempts trigger `403 Forbidden` errors.
- **Cryptographic Audit Trail (SHA-256)**:
  - Critical actions are hashed and chained in a tamper-evident cryptographic ledger (`Merkle-lite` chain).
  - Built-in audit verification console verifies ledger integrity in real time.

---

## Demo User Accounts & Personas

All accounts use the default password: **`password`**

| Persona | Email | Role | Accessible Workspaces |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@scholar.edu` | `admin` | Executive Console, Registry & Faculty HR, Cohorts & Curriculum, Calendar & Grading Policies, Gateway Configurations, LMS Authoring & CBT Suite, Cryptographic Audit Trail |
| **Faculty Lead / HOD** | `teacher@scholar.edu` | `teacher` | Faculty Desk, Assigned Cohorts, Student Rosters, Gradebook & Report Cards, LMS Course Authoring & Material Uploads, Q&A Studio & Exam Submissions Grading, Attendance Marking |
| **Student** | `student@scholar.edu` | `student` | Student Workspace, Student 360, Enrolled LMS Courses, Interactive Exam Testing Terminal (CBT), Exam Results, Attendance Record, Noticeboard |
| **Parent / Guardian** | `parent@scholar.edu` | `parent` | Guardian Desk, Linked Wards' 360 Profiles, Term Report Cards, Attendance & Safety Oversight, School Advisories |

> **Quick Switching:** You can switch between any persona in 1 click using the role selector dropdown below your avatar in the sidebar.

---

## Quickstart Installation Guide

### Prerequisites
- **PHP** >= 8.2 with `pdo_mysql`, `mbstring`, `openssl`, `curl`
- **MySQL** >= 8.0 or MariaDB >= 10.4 (e.g. via XAMPP)
- **Composer** >= 2.x
- **Node.js** >= 18.x & NPM

### Step-by-Step Setup

```bash
# 1. Clone the repository
git clone https://github.com/Legendbwoy/scholar.git
cd scholar

# 2. Install PHP Dependencies
composer install

# 3. Environment Configuration
cp .env.example .env
php artisan key:generate

# 4. Configure Database in .env
# Edit .env with your database credentials:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sms_db
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run Migrations & Populate Comprehensive Seed Data
# (Creates all tables, seed users, courses, quizzes, questions, calendar holidays, and gateways)
php artisan migrate:fresh --seed

# 6. Seed LMS Quizzes & Examinations (if not already seeded)
php artisan db:seed --class=LmsQuizSeeder

# 7. Launch Application Dev Server
php artisan serve --port=8088
```

Open your browser and navigate to:
```text
Public Showcase: http://127.0.0.1:8088/
Portal Login:    http://127.0.0.1:8088/login
```

---

## Core Web & REST API Endpoints

### LMS Quizzes & Examinations
| Method | Route | Description |
| :--- | :--- | :--- |
| `GET` | `/lms/quizzes` | LMS Assessments & Examinations catalog with KPI metrics and filters |
| `GET` | `/lms/quizzes/create` | Author new assessment (duration, attempts, passing score, shuffle flags) |
| `POST` | `/lms/quizzes` | Store assessment configuration |
| `GET` | `/lms/quizzes/{id}/builder` | Q&A Authoring Studio (add/edit MCQs, True/False, Short Answer, Essays) |
| `POST` | `/lms/quizzes/{id}/questions` | Store questions and options into assessment question bank |
| `POST` | `/lms/quizzes/{id}/start` | Initialize student CBT examination session |
| `GET` | `/lms/quizzes/player/{attemptId}` | Interactive distraction-free student examination terminal |
| `POST` | `/lms/quizzes/player/{attemptId}/save` | Asynchronous AJAX answer auto-save endpoint |
| `POST` | `/lms/quizzes/player/{attemptId}/submit` | Finalize examination and trigger auto-evaluation |
| `GET` | `/lms/quizzes/results/{attemptId}` | Student report card and question solution explanations |
| `GET` | `/lms/quizzes/{id}/submissions` | Faculty candidate submissions list and score overviews |
| `GET` | `/lms/quizzes/attempts/{id}/grading` | Teacher Rubric Review Console for subjective essay scoring |
| `POST` | `/lms/quizzes/attempts/{id}/grading` | Commit teacher marks and feedback to gradebook |

### Administration, Calendars & Gateways
| Method | Route | Description |
| :--- | :--- | :--- |
| `GET` | `/academic/calendar` | Academic terms, semesters, and national holiday scheduling |
| `GET` | `/academic/grading-policies` | Dual-grading policy hub (LMS vs Offline weights) |
| `GET` | `/settings/gateways` | SMTP, SMS, and WhatsApp API credentials & token configuration |
| `POST` | `/settings/gateways/{channel}/test` | Trigger live connection health test for gateway channel |
| `GET` | `/broadcasts/templates` | Multi-channel broadcast template management & auto-send settings |
| `GET` | `/content/events` | Campus event showcase CRUD |
| `GET` | `/content/gallery` | Campus life media gallery CRUD |
| `POST` | `/profile/avatar` | Custom user profile avatar upload |

---

## Security & Compliance

- **Role Guardrails**: Strict route protection via `App\Http\Middleware\CheckRole`. Unauthorized URL access returns `403 Forbidden`.
- **Exam Integrity**: Server-enforced countdown timer, question & option randomization, and session expiry locking.
- **CSRF & XSS Protection**: All form mutations enforce CSRF tokens; Blade templating escapes output by default.
- **Parameter-Bound Queries**: 100% parameter-bound queries via Laravel Eloquent ORM.
- **Cryptographic Audit Log**: Merkle-lite SHA-256 hash chaining guarantees tamper evidence.

---

## License

This software is released under the **[MIT License](LICENSE)**.

---

<p align="center">
  Crafted with excellence by <a href="https://github.com/Legendbwoy"><strong>Legendbwoy</strong></a>
</p>