<p align="center">
  <a href="https://github.com/Legendbwoy/scholar" target="_blank">
    <img src="public/scholar/assets/images/banner-item-01.jpg" alt="Scholar SMS Banner" width="100%" style="border-radius: 16px; max-height: 380px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
  </a>
</p>

<h1 align="center">🎓 Scholar SMS</h1>
<h3 align="center">Enterprise-Grade Modular School Management System & Digital LMS</h3>

<p align="center">
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"></a>
  <a href="https://getbootstrap.com/"><img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5.3"></a>
  <img src="https://img.shields.io/badge/Template-Scholar_v1.0-7a6ad8?style=for-the-badge" alt="Scholar Template">
  <img src="https://img.shields.io/badge/Ledger-SHA--256_Chained-38a169?style=for-the-badge" alt="SHA-256 Ledger">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="MIT License">
</p>

---

## 📖 Executive Summary

**Scholar SMS** is a modern, modular monolith web application designed to unify academic administration, classroom instruction, student engagement, and parent-school transparency. 

Built on **Laravel 11** and customized with the vibrant **Scholar v1.0.0** Bootstrap theme, the system delivers role-segregated workspaces for **Super Administrators**, **Faculty Members**, **Students**, and **Guardians**. It features omni-channel messaging (SMS, WhatsApp, Email, In-App), contactless QR-code attendance verification, and a cryptographically chained (SHA-256) immutable audit trail.

---

## 🏛️ System Architecture

Scholar SMS follows a **Modular Monolith** pattern:
```
school-management-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Dedicated Domain Controllers (Student, Staff, Course, etc.)
│   │   ├── Middleware/          # CheckRole RBAC Middleware & Security Filters
│   ├── Models/                  # Eloquent Entities with Relationships & Auto-Hashing
├── database/
│   ├── migrations/              # Relational Schema with Foreign Constraints & Indexes
│   ├── seeders/                 # Comprehensive Institutional Seed Data
├── public/scholar/              # Integrated Scholar v1.0.0 UI Assets (CSS, JS, Fonts, Images)
├── resources/views/
│   ├── layouts/
│   │   ├── public.blade.php     # Public Landing & Showcase Layout
│   │   ├── portal.blade.php     # Responsive Authenticated Workspace Shell
│   │   └── navigation/          # Role-Segregated Navigation Partials
│   │       ├── admin.blade.php
│   │       ├── teacher.blade.php
│   │       ├── student.blade.php
│   │       └── parent.blade.php
│   ├── landing/                 # Public Landing, Course Catalog & Campus Gallery
│   └── portals/                 # Role-Guarded Application Views
│       ├── admin/
│       ├── teacher/
│       ├── student/
│       ├── parent/
│       ├── students/            # Complete Student CRUD & 360 Profile
│       ├── staff/               # Faculty HR, Qualifications & Directory
│       ├── courses/             # LMS Courses & Digital File Repository
│       ├── academic/            # Classes, Sections & Subject Curriculum
│       ├── attendance/          # Daily Registers, Personal Logs & QR Terminal
│       ├── comms/               # Multi-Channel Dispatch Hub
│       └── audit/               # Immutable Audit Ledger & Verification Tool
└── routes/
    ├── web.php                  # Strict RBAC-Guarded Application Routes
    └── api.php                  # RESTful API Endpoints with Health & Ingestion
```

---

## ✨ Key Features & Capabilities

### 1. 🛡️ Role-Based Access Control (RBAC)
- **Strict Route & Navigation Segregation**: Every portal action is protected by the `role` middleware. Navigation items are dynamically included via dedicated partials (`layouts.navigation.{role}`).
- **Privacy Enforcement**: Students can only view their own academic file; parents can only view their linked wards. Unauthorized access triggers an instant `403 Forbidden`.
- **1-Click Demo Profile Switcher**: Fast-switch between Super Admin, Teacher, Student, and Parent accounts directly from the sidebar.

### 2. 👨‍🎓 Complete Academic Registry (CRUD)
- **Student Information System (SIS)**:
  - Full lifecycle: Enrollment, 360 Academic Profile, Profile Editing, and Archival/Suspension.
  - Generates institutional Admission Numbers (`SCH-2026-XXX`) and Roll Numbers.
  - Connects students with their classroom cohort and verified guardian.
- **Faculty & Staff HR Management**:
  - Employee ID generation (`EMP-2026-XXX`), department assignment, designation, qualification, specialization, and salary tracking.
  - Tracks assigned form-classes and accredited subjects.

### 3. 📚 Digital LMS & Course Materials
- **Curriculum & Course Provisioning**:
  - Provision syllabus units, credit hours, semesters, and category tracks (STEM, Arts, Humanities).
- **Digital Asset Repository**:
  - Upload and distribute lecture slides, lab manuals, and PDFs.
  - Instant file download streaming with file-type badges and size indicators.

### 4. ⏱️ Contactless Attendance & QR Terminal
- **Faculty Roll Call**:
  - Instant daily attendance marking (Present, Absent, Late, Excused) with class-level statistics.
- **Smart QR Check-In Terminal**:
  - Interactive camera-ready scanner interface for contactless biometric or QR badge verification.
- **Automated Absence Notification**:
  - Marking a student absent automatically triggers an emergency SMS and in-app alert to their registered parent.
- **Personalized Student & Parent Views**:
  - Students see their personal attendance percentage, punctuality breakdown, and daily log.
  - Guardians view a live safety card for each of their linked children.

### 5. 📢 Omni-Channel Communications Hub
- **Targeted Broadcast Engine**:
  - Dispatch announcements to All School, Teachers Only, Students Only, Parents Only, or a specific Classroom Cohort.
- **Multi-Channel Delivery Gateway**:
  - Simulated multi-carrier delivery via **SMS**, **WhatsApp**, **Email**, and **In-App Alerts**.
  - Verified delivery receipts tracking sent count, delivery status, and timestamps.

### 6. 🔐 Cryptographically Chained (SHA-256) Audit Trail
- **Tamper-Evident Ledger**:
  - Every critical system action (student enrollment, attendance sealing, course provisioning, broadcast dispatch) is permanently hashed.
  - Implements Merkle-lite hash chaining: `current_hash = SHA256(prev_hash + user_id + action + payload + timestamp)`.
- **Integrity Verification Console**:
  - Built-in audit dashboard verifies the entire blockchain ledger from the Genesis block to the tip in real time.

### 7. 🎨 Scholar v1.0.0 Aesthetic Integration
- High-impact public landing page with interactive hero banners, statistics counter, curriculum showcase, faculty cards, and campus life filterable gallery.
- Modern portal dashboard with Poppins typography, subtle glassmorphism cards, Chart.js analytics, and mobile-friendly sidebar navigation.

---

## 👥 Demo User Accounts & Personas

All accounts use the default password: **`password`**

| Persona | Email | Role | Accessible Workspaces |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@scholar.edu` | `admin` | Executive Console, Academic Registry, Full Staff HR, Cohorts & Curriculum, LMS Authoring, Attendance Control, Comms Dispatch, Cryptographic Audit Trail |
| **Faculty Lead / HOD** | `teacher@scholar.edu` | `teacher` | Faculty Desk, Assigned Cohorts, Student Rosters, LMS Course Authoring & Material Uploads, Attendance Marking, QR Terminal, Comms Dispatch |
| **Student** | `student@scholar.edu` | `student` | Student Workspace, Digital ID 360, Enrolled LMS Courses & Downloads, Personal Attendance Ledger, Campus Bulletins |
| **Parent / Guardian** | `parent@scholar.edu` | `parent` | Guardian Desk, Linked Wards' 360 Profiles, Course Catalog, Wards' Attendance & Safety Oversight, School Advisories |

> 💡 **Tip:** You can switch between any persona in 1 click using the role selector dropdown below your avatar in the sidebar.

---

## 🚀 Quickstart Installation Guide

### Prerequisites
- **PHP** >= 8.2 with `pdo_mysql`, `mbstring`, `openssl`, `curl` extensions enabled
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
# Edit .env with your MySQL credentials:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sms_db
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Run Migrations & Populate Seed Data
# (Ensures all 11 tables, seed users, classes, courses, and audit hashes are generated)
php artisan migrate:fresh --seed

# 6. Launch Application Dev Server
php artisan serve --port=8088
```

Open your browser and navigate to:
```text
Public Portal: http://127.0.0.1:8088/
Portal Login:  http://127.0.0.1:8088/login
```

---

## 🔌 RESTful API Endpoints

The system exposes REST API endpoints for external integrations (mobile apps, biometric gate hardware, or external parent portals):

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/health` | System health check, PHP version, and database connectivity status |
| `GET` | `/api/v1/courses` | Returns published LMS courses with instructor metadata |
| `GET` | `/api/v1/attendance/stats` | Returns institutional attendance counts for a given `?date=YYYY-MM-DD` |
| `POST` | `/api/v1/broadcast/send` | Dispatches omni-channel broadcasts programmatically |

---

## 🔒 Security & Compliance

- **Role Guardrails**: Protected by `App\Http\Middleware\CheckRole`. Direct URL enumeration attempts return `403 Forbidden`.
- **CSRF Protection**: All mutation endpoints enforce CSRF validation.
- **SQL Injection Prevention**: 100% parameter-bound queries via Laravel Eloquent ORM.
- **Password Security**: Bcrypt/Argon2 hashing with salted storage.
- **Cryptographic Audit Log**: Merkle-lite hash chaining makes historical modifications computationally detectable.

---

## 📄 License

This software is released under the **[MIT License](LICENSE)**.

---

<p align="center">
  Crafted with excellence by <a href="https://github.com/Legendbwoy"><strong>Legendbwoy</strong></a>
</p>