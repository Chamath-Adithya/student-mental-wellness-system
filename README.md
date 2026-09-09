# Student Mental Wellness Check-in-System (Sansun - සන්සුන්)

An institutional mental health screening and student support web application built with **HTML5, CSS3, Vanilla JavaScript, PHP (PDO), and MySQL**.

- **Student / Project Lead**: B.A.I.D Bopitiya (Student ID: `DIT 14253`)
- **Course / Batch**: DIT 14 Intake
- **Live Reference**: [Sansun Mental Health Platform (sansun-web-app.vercel.app)](https://sansun-web-app.vercel.app/)

---

## 🌟 Key Features

### 1. Robust Authentication & Role Management
- **Student Registration (`register.php`)**: Full Name, Student ID (`DIT 14253`), Email, Course/Intake, Password with secure Bcrypt hashing.
- **Login Portal (`login.php`)**: Secure authentication for both Students and Institutional Counselors/Admins.
- **Role-Based Navigation**: Students are redirected to the Student Wellness Dashboard (`dashboard.php`), and Counselors are directed to the Counselor Control Panel (`admin-dashboard.php`).

### 2. Main Portal (`index.php`)
- **Bilingual Interface**: Seamless toggle between **Sinhala (සිංහල)** and **English**.
- **Dark / Light Theme Switcher**: Calming Sage Green palette (`#4a7c59`) with persistent theme storage.
- **Ambient Rain & Nature Audio**: Calming ambient stream with Play/Pause and direct WhatsApp support integration.
- **Daily Mood Journal**: 4-point emoji picker (😊 Happy, 😐 Neutral, 😔 Sad, 😡 Angry) with notes saved directly to the MySQL database.
- **Clinical Mental Health Screening Wizard**:
  - **PHQ-9**: 9-question standard Patient Health Questionnaire for depression screening.
  - **GAD-7**: 7-question Generalized Anxiety Disorder scale.
  - Automatic severity scoring (Minimal, Mild, Moderate, Severe).
  - **High-Risk Detection Banner**: Proactive warnings with emergency hotline recommendations if severe distress or self-harm thoughts are indicated.
- **Progress Tracking (Past Scores Chart)**: Historical stress and anxiety trend line chart powered by Chart.js with optional PIN privacy lock.
- **Interactive Wellness Modals**:
  - **4-7-8 Breathing Technique**: Animated pulsating visual guide (Inhale 4s, Hold 7s, Exhale 8s).
  - **5-4-3-2-1 Grounding Exercise**: Sensory guidance for panic attack recovery.
  - **Counseling Appointment Booking**: Named or Anonymous session request submission.
- **AI Wellness Chatbot**: Instant conversational support with pre-set quick prompt chips.
- **Helpline Access**: Direct telephone links to **1926** (NIMH), **1333** (CCC Line 24/7), and **011 2696666** (Sumithrayo).

### 3. Student Wellness Dashboard (`dashboard.php`)
- Student Profile overview (`B.A.I.D Bopitiya - DIT 14253`).
- Personal Assessment history table and interactive trend charts.
- My Counseling Appointments schedule and approval statuses.
- Printable Official Student Wellness Summary Report (`@media print` optimized).

### 4. Counselor & Administrator Control Panel (`admin-dashboard.php`)
- Real-time KPI Stats: Total Counseling Requests, Pending Requests, Approved Sessions, and High-Risk Student Alerts.
- Severity Breakdown Distribution Chart.
- High-Risk Student Screening Monitor for immediate proactive outreach.
- Counseling Session Request Approval & Status Management (Pending, Approved, Completed, Rejected).

---

## 🗄️ Database Architecture (`wellness_system_db`)

1. `users`: Student and Counselor accounts with hashed credentials and course batches.
2. `mood_logs`: Daily emotional mood logs with timestamps and personal reflections.
3. `assessments`: PHQ-9 and GAD-7 screening records with numerical scores, severity classifications, and high-risk flags.
4. `counseling_requests`: Confidential student appointment requests with preferred dates, formats (online / in-person), and counselor statuses.

---

## 🚀 Quick Setup Instructions (WampServer)

### 1. Location
This project is placed in your local WampServer root directory:
```
C:\wamp64\www\student-mental-wellness-system\
```

### 2. Start WampServer
1. Open **WampServer** from your Windows Start Menu.
2. Wait until the system tray WampServer icon turns **Green**.

### 3. Initialize the Database (1-Click)
Open your browser and navigate to:
```
http://localhost/student-mental-wellness-system/setup_db.php
```
This automatically initializes `wellness_system_db`, generates all tables, and seeds demo accounts.

---

## 🔑 Demo Login Accounts

| Role | Email / Student ID | Password | Access Portal |
| :--- | :--- | :--- | :--- |
| **Student** | `student@dit.ac.lk` or `DIT 14253` | `student123` | `http://localhost/student-mental-wellness-system/login.php` |
| **Admin Counselor** | `admin@sansun.com` | `admin123` | `http://localhost/student-mental-wellness-system/login.php` |

---

## 🇱🇰 සිංහල උපදෙස් (Sinhala Guide)

1. **WampServer එක Run කරන්න**: Windows Start Menu එකෙන් WampServer open කර system tray icon එක කොළ පාට (Green) වන තුරු සිටින්න.
2. **Database එක සකස් කරන්න**: Browser එකේ `http://localhost/student-mental-wellness-system/setup_db.php` open කරන්න.
3. **පද්ධතිය භාවිතා කරන්න**:
   - ප්‍රධාන වෙබ් අඩවිය: `http://localhost/student-mental-wellness-system/index.php`
   - ශිෂ්‍ය Dashboard එක: `http://localhost/student-mental-wellness-system/dashboard.php`
   - උපදේශක (Admin) Dashboard එක: `http://localhost/student-mental-wellness-system/admin-dashboard.php`
