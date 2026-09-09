# 🌿 Student Mental Wellness Check-in System (Sansun - සන්සුන්)

> **An Institutional Mental Health Monitoring & Confidential Student Counseling Web Platform**  
> Built with **HTML5, CSS3, Vanilla JavaScript, PHP (PDO), and MySQL**.

---

## 📌 Project Information

- **Project Title**: Student Mental Wellness Check-in System (*සන්සුන් - Sansun*)
- **Student Name**: B.A.I.D Bopitiya
- **Student ID**: `DIT 14253`
- **Course / Batch**: DIT 14 Intake
- **Presentation Title**: Project Proposal Presentation - Student Mental Wellness Check-in-System
- **Live Reference Inspiration**: [Sansun Mental Health Platform (sansun-web-app.vercel.app)](https://sansun-web-app.vercel.app/)
- **GitHub Repository**: [https://github.com/Chamath-Adithya/student-mental-wellness-system](https://github.com/Chamath-Adithya/student-mental-wellness-system)

---

## 📖 Table of Contents

1. [Introduction & Background](#-introduction--background)
2. [Problem Statement & Objectives](#-problem-statement--objectives)
3. [Technology Stack](#-technology-stack)
4. [System Architecture & File Structure](#-system-architecture--file-structure)
5. [Core Features & Modules](#-core-features--modules)
6. [Database Schema & Data Entities](#-database-schema--data-entities)
7. [Step-by-Step Installation & Setup Guide](#-step-by-step-installation--setup-guide)
8. [Default Demo Accounts](#-default-demo-accounts)
9. [API Endpoints Reference](#-api-endpoints-reference)
10. [සිංහල මාර්ගෝපදේශය (Sinhala User Guide)](#-සිංහල-මාර්ගෝපදේශය-sinhala-user-guide)

---

## 💡 Introduction & Background

Many students in higher education institutions experience acute mental distress due to exams, heavy assignment workloads, financial constraints, family expectations, and social pressures. If unaddressed, persistent stress can lead to clinical anxiety and depression.

Due to social stigma, fear of being judged, or simple lack of awareness of available campus counseling facilities, students often hesitate to seek help early. The **Student Mental Wellness Check-in System (Sansun)** provides a safe, stigma-free, confidential digital sanctuary where students can:
1. Conduct regular standardized self-checks (**PHQ-9** and **GAD-7**).
2. Log daily emotional mood patterns.
3. Access guided relaxation techniques (**4-7-8 Breathing** and **5-4-3-2-1 Grounding**).
4. Request one-on-one counseling appointments with institutional counselors (with full option for **anonymous** requests).
5. Chat with an empathetic AI assistant anytime in **Sinhala** or **English**.

---

## 🎯 Problem Statement & Objectives

### Problem Statement
- Student mental health issues are frequently diagnosed only after academic failure or severe crisis occurs.
- Educational institutions lack continuous, proactive digital wellness monitoring.
- Students are often unaware of institutional counseling departments or emergency medical hotlines.

### Project Objectives
- **Regular Monitoring**: Allow students to track their mental state over time using standardized psychological scales.
- **Early Intervention**: Automatically flag high-risk scores (severe depression or suicidal ideation) to alert counselors for proactive outreach.
- **Direct Counseling Bridge**: Facilitate appointment scheduling between students and university counselors.
- **Awareness & Relief**: Provide instant access to emergency helplines (1926, 1333, Sumithrayo) and calming tools.

---

## 💻 Technology Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Frontend UI** | HTML5, CSS3, Vanilla JavaScript | Responsive design, CSS variables, Dark Mode, glassmorphism |
| **Fonts & Icons** | Plus Jakarta Sans, Noto Sans Sinhala, FontAwesome 6 | Clean multilingual typography and iconography |
| **Data Visualization** | Chart.js 4.x | Real-time score trends and demographic distribution charts |
| **Backend** | PHP 8.x (Procedural + OOP helpers) | PDO Database abstraction, prepared statements, sessions |
| **Database** | MySQL 8.x | Relational schema with foreign keys and cascade rules |
| **Local Server** | WampServer (Apache + MySQL) | Hosted at `http://localhost/student-mental-wellness-system` |
| **Version Control** | Git & GitHub | Complete commit history pushed to GitHub |

---

## 📂 System Architecture & File Structure

```
student-mental-wellness-system/
├── api/
│   ├── assessment.php        # Asynchronous PHQ-9 & GAD-7 submission & history endpoint
│   ├── chat.php              # AI Chatbot conversational processing engine (Sinhala/English)
│   ├── counseling.php        # Student counseling request submission API
│   ├── login.php             # JSON authentication endpoint for AJAX modals
│   ├── mood.php              # Daily mood log storage & retrieval API
│   └── register.php          # JSON student registration API
├── assets/
│   ├── css/                  # Styling files
│   ├── js/
│   │   └── main.js           # Core client-side logic (translations, wizard, chart, modals)
│   └── images/               # Illustrations and assets
├── config/
│   └── db.php                # PDO database connection, session handling & helper functions
├── database/
│   └── schema.sql            # Full MySQL database schema and rich seed datasets
├── admin-dashboard.php       # Counselor / Administrator analytics & appointments portal
├── dashboard.php             # Authenticated student dashboard & personalized records
├── index.php                 # Main public portal matching Sansun web application
├── login.php                 # Dedicated user login page
├── logout.php                # Session termination handler
├── register.php              # Dedicated student registration page
├── setup_db.php              # 1-Click automated database installer & seeder
├── README.md                 # Complete project documentation
└── .gitignore                # Git ignore configuration
```

---

## 🌟 Core Features & Modules

### 1. Bilingual Engine (Sinhala & English)
- Instant language toggle (`සිංහල` / `English`) without page reload.
- Full translations for all labels, questionnaires, severity results, and chatbot answers.

### 2. Ambient Rain & Nature Audio Player
- Embedded ambient nature sound stream to help students relax during study sessions.
- Play/Pause toggle with audio state management.
- Direct WhatsApp support contact shortcut.

### 3. Daily Mood Journal
- 4-point emoji emotion selector:
  - 😊 **Happy** (*සතුටුයි*)
  - 😐 **Neutral** (*සාමාන්‍යයි*)
  - 😔 **Sad** (*දුකයි*)
  - 😡 **Angry** (*කෝපයි*)
- Optional reflection text notes saved into the MySQL database.
- Immediate client-side update with historical mood log table.

### 4. Standardized Assessment Wizard (PHQ-9 & GAD-7)
- **PHQ-9**: 9-question Patient Health Questionnaire measuring depression severity:
  - 0–4: Minimal (*සාමාන්‍ය*)
  - 5–9: Mild (*සුළු විෂාදය*)
  - 10–14: Moderate (*මධ්‍යස්ථ විෂාදය*)
  - 15–19: Moderately Severe (*වැඩි විෂාදය*)
  - 20–27: Severe (*ඉතා දැඩි විෂාදය*)
- **GAD-7**: 7-question Generalized Anxiety Disorder scale measuring anxiety:
  - 0–4: Minimal Anxiety (*අවම කාංසාව*)
  - 5–9: Mild Anxiety (*සුළු කාංසාව*)
  - 10–14: Moderate Anxiety (*මධ්‍යස්ථ කාංසාව*)
  - 15–21: Severe Anxiety (*අධික කාංසාව*)
- **High-Risk Safety Flag**: Automatically triggers a prominent red alert banner advising emergency hotline contact (1926) if total score is severe or if Question 9 (self-harm thoughts) is indicated.

### 5. Interactive Wellness Exercises
- **4-7-8 Breathing Technique**: Animated pulsating visual circle that guides students through:
  - Inhale deeply for 4 seconds
  - Hold breath for 7 seconds
  - Exhale slowly for 8 seconds
- **5-4-3-2-1 Sensory Grounding Technique**: Calming guide for panic attacks and acute anxiety episodes:
  - 5 things you can see
  - 4 things you can touch
  - 3 sounds you can hear
  - 2 scents you can smell
  - 1 taste you can focus on

### 6. Counseling Appointment Booking
- Students can schedule confidential sessions with institutional counselors.
- Option to submit **Named** (with Student ID) or completely **Anonymous** requests.
- Selection between **Online Session** (Chat/Video) or **In-Person Office Session**.
- Date & time picker with personal notes.

### 7. AI Wellness Chatbot Widget
- Floating interactive chat assistant located at the bottom-right of the screen.
- Responds empathetically in both Sinhala and English to questions about exam stress, sleeping issues, and panic management.
- Quick prompt chips:
  - *"මට පීඩනයක් දැනෙනවා"* (*I feel stressed*)
  - *"මනස සන්සුන් කරගන්නේ කෙසේද?"* (*How to calm my mind?*)
  - *"1926 අමතන්නේ කෙසේද?"* (*How to contact 1926?*)

### 8. Student Wellness Dashboard (`dashboard.php`)
- **Student Profile**: Shows Student Name, Student ID (`DIT 14253`), Email, and Course/Intake (`DIT 14 Intake`).
- **Score Trend Chart**: Historical line chart displaying past PHQ-9 and GAD-7 scores over time.
- **My Counseling Sessions**: Track status of appointment requests (`Pending`, `Approved`, `Completed`).
- **Printable Report**: One-click print-optimized medical summary of past check-ins.

### 9. Counselor & Admin Control Panel (`admin-dashboard.php`)
- **Live Statistics Cards**: Total requests, pending requests, approved appointments, high-risk flags.
- **Severity Breakdown Chart**: Doughnut chart showing percentage of students in Minimal, Mild, Moderate, and Severe categories.
- **High-Risk Student Monitor**: Immediate alerts for students scoring in critical ranges to facilitate proactive counselor outreach.
- **Appointment Approval Workflow**: One-click actions to Approve, Reject, or Mark Completed.

---

## 🗄️ Database Schema & Data Entities

The application utilizes the `wellness_system_db` MySQL database consisting of four normalized relational tables:

```
+-----------------------------------------------------------------------------------+
|                                wellness_system_db                                 |
+-----------------------------------------------------------------------------------+
|                                                                                   |
|  +--------------------+       1:N       +----------------------+                  |
|  |       users        | --------------> |      mood_logs       |                  |
|  +--------------------+                 +----------------------+                  |
|  | id (PK)            |                 | id (PK)              |                  |
|  | full_name          |                 | user_id (FK -> users)|                  |
|  | student_id         |                 | mood_emoji           |                  |
|  | email              |                 | mood_label           |                  |
|  | password_hash      |                 | note                 |                  |
|  | role               |                 | created_at           |                  |
|  | intake             |                 +----------------------+                  |
|  | created_at         |                                                           |
|  +--------------------+                                                           |
|         |                                                                         |
|         | 1:N                           1:N                                       |
|         |-------------------------> +---------------------------+                 |
|         |                           |        assessments        |                 |
|         |                           +---------------------------+                 |
|         |                           | id (PK)                   |                 |
|         |                           | user_id (FK -> users)     |                 |
|         |                           | student_name              |                 |
|         |                           | test_type (phq9/gad7)     |                 |
|         |                           | total_score               |                 |
|         |                           | severity_level            |                 |
|         |                           | is_high_risk              |                 |
|         |                           | answers_json              |                 |
|         |                           | recommendation            |                 |
|         |                           | created_at                |                 |
|         |                           +---------------------------+                 |
|         | 1:N                                                                     |
|         +-------------------------> +---------------------------+                 |
|                                     |    counseling_requests    |                 |
|                                     +---------------------------+                 |
|                                     | id (PK)                   |                 |
|                                     | user_id (FK -> users)     |                 |
|                                     | student_name              |                 |
|                                     | student_id                |                 |
|                                     | request_type              |                 |
|                                     | preferred_mode            |                 |
|                                     | preferred_date            |                 |
|                                     | notes                     |                 |
|                                     | status                    |                 |
|                                     | created_at                |                 |
|                                     +---------------------------+                 |
+-----------------------------------------------------------------------------------+
```

---

## 🚀 Step-by-Step Installation & Setup Guide

### Step 1: Place the Project in WampServer
Ensure the project is placed inside your WampServer web directory:
```
C:\wamp64\www\student-mental-wellness-system\
```

### Step 2: Start WampServer
1. Open **WampServer** from your Windows Start Menu.
2. Observe the WampServer icon in your Windows notification area (System Tray).
3. Wait until the icon turns **Green** (indicating Apache and MySQL services are active).

### Step 3: Initialize the Database (1-Click Installer)
Open your web browser (Chrome, Edge, Brave, etc.) and navigate to:
```
http://localhost/student-mental-wellness-system/setup_db.php
```
- This automated installer connects to MySQL at `localhost:3306`, creates `wellness_system_db`, generates all tables, and populates initial demo accounts.
- You will see a green **"Setup Completed Successfully!"** confirmation screen.

### Step 4: Access the Application
- **Main Public Web App**: [http://localhost/student-mental-wellness-system/index.php](http://localhost/student-mental-wellness-system/index.php)
- **Login Page**: [http://localhost/student-mental-wellness-system/login.php](http://localhost/student-mental-wellness-system/login.php)
- **Registration Page**: [http://localhost/student-mental-wellness-system/register.php](http://localhost/student-mental-wellness-system/register.php)
- **Student Dashboard**: [http://localhost/student-mental-wellness-system/dashboard.php](http://localhost/student-mental-wellness-system/dashboard.php)
- **Counselor Control Panel**: [http://localhost/student-mental-wellness-system/admin-dashboard.php](http://localhost/student-mental-wellness-system/admin-dashboard.php)

---

## 🔑 Default Demo Accounts

The database comes pre-seeded with sample student and counselor credentials:

| Account Type | Email / Username | Student ID | Password | Portal Destination |
| :--- | :--- | :--- | :--- | :--- |
| **Student** | `student@dit.ac.lk` | `DIT 14253` | `student123` | Student Dashboard (`dashboard.php`) |
| **Counselor / Admin** | `admin@sansun.com` | `ADM-001` | `admin123` | Counselor Control Panel (`admin-dashboard.php`) |

> [!NOTE]  
> New students can also create their own accounts anytime via [register.php](http://localhost/student-mental-wellness-system/register.php) or the registration modal.

---

## 📡 API Endpoints Reference

All API routes communicate using asynchronous JSON payloads (`Content-Type: application/json`):

| Endpoint | Method | Parameters / Body | Description |
| :--- | :--- | :--- | :--- |
| `/api/login.php` | `POST` | `email`, `password` | Validates credentials and initializes PHP session |
| `/api/register.php` | `POST` | `fullname`, `student_id`, `email`, `password` | Creates user account with Bcrypt hash |
| `/api/mood.php` | `POST` | `mood`, `note` | Saves a daily mood log entry to the database |
| `/api/mood.php` | `GET` | - | Returns last 10 mood log entries |
| `/api/assessment.php` | `POST` | `test_type`, `score`, `answers` | Evaluates severity, flags high-risk, and stores test |
| `/api/assessment.php` | `GET` | - | Returns historical assessment records for Chart.js |
| `/api/counseling.php` | `POST` | `request_type`, `student_name`, `preferred_mode`, `preferred_date`, `notes` | Creates new counseling appointment |
| `/api/counseling.php` | `GET` | - | Retrieves appointments for logged-in student |
| `/api/chat.php` | `POST` | `message`, `lang` | Generates bilingual supportive chatbot response |

---

## 🇱🇰 සිංහල මාර්ගෝපදේශය (Sinhala User Guide)

### 1. පද්ධතිය පිළිබඳ හැඳින්වීම
මෙම **Student Mental Wellness Check-in System (*සන්සුන්*)** පද්ධතිය නිර්මාණය කර ඇත්තේ විශ්වවිද්‍යාල සහ උසස් අධ්‍යාපන ආයතන වල සිසුන්ගේ මානසික සුවතාවය නිරීක්ෂණය කිරීමට සහ අවශ්‍ය විටෙක විශ්වවිද්‍යාල උපදේශකවරුන් හා සම්බන්ධ කිරීමටය.

### 2. පද්ධතිය පරිගණකයේ Run කරන ආකාරය
1. **WampServer ආරම්භ කරන්න**: Windows Start Menu එකෙන් **WampServer** open කර Taskbar එකේ ඇති icon එක කොළ පැහැ (Green) වන තුරු සිටින්න.
2. **Database එක සාදාගැනීම (1-Click Setup)**:
   - Web browser එකක් (Chrome/Edge/Brave) open කර පහත link එකට යන්න:
     ```
     http://localhost/student-mental-wellness-system/setup_db.php
     ```
   - එමගින් `wellness_system_db` Database එක ස්වයංක්‍රීයව setup වී අවසන් වේ.
3. **පද්ධතියට පිවිසෙන්න**:
   - ප්‍රධාන වෙබ් අඩවිය: `http://localhost/student-mental-wellness-system/index.php`
   - ශිෂ්‍ය ගිණුමට ලොග් වීම: `student@dit.ac.lk` (මුරපදය: `student123` හෝ ශිෂ්‍ය අංකය: `DIT 14253`)
   - උපදේශක (Admin) ගිණුමට ලොග් වීම: `admin@sansun.com` (මුරපදය: `admin123`)

### 3. ප්‍රධාන පහසුකම් භාවිතා කරන ආකාරය:
- **භාෂාව මාරු කිරීම**: ඉහළ දකුණු කෙළවරේ ඇති `English / සිංහල` බොත්තම මගින් මුළු පද්ධතියම සිංහල හෝ ඉංග්‍රීසි භාෂාවට තත්පරයකින් මාරු කළ හැක.
- **Mood Journal**: ඔබගේ දෛනික මනෝභාවය (😊 Happy, 😐 Neutral, 😔 Sad, 😡 Angry) තෝරා කෙටි සටහනක් සමග Save කරන්න.
- **PHQ-9 සහ GAD-7 පරීක්ෂාවන්**: ප්‍රශ්නාවලියට පිළිතුරු සපයා ඔබගේ මානසික පීඩන මට්ටම ක්ෂණිකව බලාගත හැක. අවදානම් මට්ටමක පවතී නම් පද්ධතිය මගින් රතු පැහැති අනතුරු ඇඟවීමේ පණිවිඩයක් පෙන්වයි.
- **හුස්ම ගැනීමේ අභ්‍යාසය (4-7-8 Breathing)**: තත්පර 4ක් හුස්ම ගැනීම, තත්පර 7ක් රඳවා ගැනීම සහ තත්පර 8ක් පිටකිරීම මගින් මනස සන්සුන් කරගන්න.
- **උපදේශනයක් වෙන්කරවා ගැනීම**: තමන්ගේ අනන්‍යතාවය සහිතව හෝ **අඥාතව (Anonymous)** විශ්වවිද්‍යාල උපදේශකවරයෙකුගෙන් වේලාවක් වෙන්කරවා ගත හැක.
- **AI සහායක (Chatbot)**: පහළ දකුණු කෙළවරේ ඇති chat bubble එක click කර ආතතිය කළමනාකරණය කරගැනීමට අවශ්‍ය උපදෙස් සිංහලෙන් ලබාගන්න.
- **හදිසි ඇමතුම්**: **1926** (ජාතික මානසික සෞඛ්‍ය විද්‍යායතනය) සහ **1333** (CCC Line) නොමිලේ අමතා ක්ෂණික සහාය ලබාගත හැක.

---

## 👨‍💻 Project Credits

- **Author**: B.A.I.D Bopitiya
- **Student Registration**: `DIT 14253`
- **Intake**: DIT 14 Intake
- **Academic Context**: Diploma in Information Technology (DIT) Project Proposal
