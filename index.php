<?php
require_once __DIR__ . '/config/db.php';
require_login();

$currentUser = current_user();
if ($currentUser['role'] === 'admin' || $currentUser['role'] === 'counselor') {
    redirect(SITE_URL . '/admin-dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Mental Wellness Check-in System</title>
    
    <!-- Modern Typography & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #1e4d2b;
            --primary-accent: #2d6a4f;
            --primary-light: #f0f7f4;
            --accent: #40916c;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.04);
            --shadow: 0 12px 28px -6px rgba(30, 77, 43, 0.08);
            --radius: 16px;
            --danger: #dc2626;
            --success: #16a34a;
            --warning: #d97706;
        }

        [data-theme="dark"] {
            --bg: #0b1320;
            --card-bg: #151f30;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #243248;
            --primary: #52b788;
            --primary-accent: #74c69d;
            --primary-light: #182820;
            --shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.4);
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; transition: background-color 0.25s, color 0.25s; }
        html { scroll-behavior: smooth; }
        body { background-color: var(--bg); color: var(--text-main); line-height: 1.6; }

        /* Institutional Navbar */
        .site-navbar {
            position: sticky; top: 0; background: var(--card-bg);
            border-bottom: 1px solid var(--border); padding: 12px 30px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 16px; z-index: 1000; box-shadow: var(--shadow-sm);
        }

        .brand-logo {
            display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-main);
        }
        .brand-icon-box {
            width: 40px; height: 40px; border-radius: 10px; background: var(--primary-light);
            color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
        }
        .brand-meta { line-height: 1.2; }
        .brand-title { font-weight: 700; font-size: 1.1rem; color: var(--primary); }
        .brand-sub { font-size: 0.75rem; color: var(--text-muted); }

        .nav-menu { display: flex; gap: 24px; list-style: none; align-items: center; }
        .nav-menu a { text-decoration: none; color: var(--text-main); font-size: 0.9rem; font-weight: 600; transition: color 0.2s; display: flex; align-items: center; gap: 6px; }
        .nav-menu a:hover { color: var(--primary); }

        .nav-toolbar { display: flex; align-items: center; gap: 10px; }

        .student-chip {
            display: flex; align-items: center; gap: 10px; padding: 6px 14px; border-radius: 30px;
            background: var(--primary-light); border: 1px solid var(--border); text-decoration: none; color: var(--text-main);
        }
        .student-avatar {
            width: 28px; height: 28px; border-radius: 50%; background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;
        }
        .student-details { line-height: 1.1; }
        .student-name { font-size: 0.8rem; font-weight: 700; color: var(--primary); }
        .student-id { font-size: 0.7rem; color: var(--text-muted); }

        .btn-tool {
            background: var(--card-bg); border: 1px solid var(--border); color: var(--text-main);
            padding: 7px 12px; border-radius: 10px; font-size: 0.825rem; font-weight: 600;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;
        }
        .btn-tool:hover { border-color: var(--primary); color: var(--primary); }
        .btn-tool-danger { color: var(--danger); }
        .btn-tool-danger:hover { background: #fee2e2; border-color: #fca5a5; }

        .wrapper { max-width: 1120px; margin: 0 auto; padding: 0 24px; }

        /* Ambient Audio Strip */
        .ambient-strip {
            background: var(--card-bg); border-bottom: 1px solid var(--border);
            padding: 10px 30px; display: flex; justify-content: center; align-items: center; gap: 16px; font-size: 0.85rem; flex-wrap: wrap;
        }

        /* Hero Banner */
        .hero-section {
            padding: 45px 0; background: linear-gradient(180deg, var(--primary-light) 0%, var(--bg) 100%);
            border-bottom: 1px solid var(--border);
        }
        .hero-layout { display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; align-items: center; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px;
            background: var(--card-bg); border: 1px solid var(--border); color: var(--primary);
            font-size: 0.8rem; font-weight: 700; margin-bottom: 16px;
        }
        .hero-heading { font-size: 2.2rem; line-height: 1.25; font-weight: 700; margin-bottom: 14px; }
        .hero-lead { font-size: 1rem; color: var(--text-muted); margin-bottom: 28px; line-height: 1.7; }
        .hero-buttons { display: flex; gap: 12px; flex-wrap: wrap; }

        .btn-primary-action {
            background: var(--primary); color: #fff; padding: 12px 26px; border-radius: 12px;
            font-weight: 600; font-size: 0.925rem; text-decoration: none; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-primary-action:hover { background: var(--primary-accent); transform: translateY(-1px); }

        .btn-outline-action {
            background: var(--card-bg); color: var(--text-main); padding: 12px 22px; border-radius: 12px;
            font-weight: 600; font-size: 0.925rem; text-decoration: none; border: 1px solid var(--border); cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-outline-action:hover { border-color: var(--primary); color: var(--primary); }

        .hero-visual-card {
            background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 30px; box-shadow: var(--shadow); text-align: center;
        }
        .hero-visual-icon {
            width: 70px; height: 70px; border-radius: 50%; background: var(--primary-light);
            color: var(--primary); display: flex; align-items: center; justify-content: center;
            font-size: 2rem; margin: 0 auto 16px auto;
        }

        /* Section Layout */
        .section-wrapper { padding: 50px 0; border-bottom: 1px solid var(--border); }
        .section-header { text-align: center; margin-bottom: 35px; }
        .section-title { font-size: 1.6rem; font-weight: 700; color: var(--primary); margin-bottom: 8px; }
        .section-subtitle { font-size: 0.95rem; color: var(--text-muted); max-width: 600px; margin: 0 auto; }

        /* Mood Vector Cards Grid */
        .mood-cards-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;
        }
        .mood-card-item {
            background: var(--card-bg); border: 1.5px solid var(--border); border-radius: var(--radius);
            padding: 24px 18px; text-align: center; cursor: pointer; transition: all 0.2s ease;
        }
        .mood-card-item:hover { transform: translateY(-3px); border-color: var(--primary); box-shadow: var(--shadow); }
        .mood-card-item.active { border-color: var(--primary); background: var(--primary-light); box-shadow: var(--shadow); }
        .mood-icon-wrapper {
            width: 50px; height: 50px; border-radius: 12px; margin: 0 auto 14px auto;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .icon-thriving { background: #dcfce7; color: #15803d; }
        .icon-balanced { background: #ccfbf1; color: #0f766e; }
        .icon-fatigued { background: #fef3c7; color: #b45309; }
        .icon-distressed { background: #fee2e2; color: #b91c1c; }

        .mood-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; }
        .mood-desc { font-size: 0.8rem; color: var(--text-muted); }

        /* Assessment Card */
        .assessment-card {
            max-width: 720px; margin: 0 auto; background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 32px; box-shadow: var(--shadow);
        }
        .tab-bar { display: flex; background: var(--primary-light); border-radius: 12px; padding: 6px; gap: 6px; margin-bottom: 24px; }
        .tab-btn {
            flex: 1; padding: 10px; border: none; background: transparent; border-radius: 8px;
            font-size: 0.88rem; font-weight: 600; color: var(--text-muted); cursor: pointer;
        }
        .tab-btn.active { background: var(--card-bg); color: var(--primary); box-shadow: var(--shadow-sm); }

        .progress-track { width: 100%; height: 6px; background: var(--primary-light); border-radius: 10px; overflow: hidden; margin-bottom: 20px; }
        .progress-fill { height: 100%; width: 0%; background: var(--primary); transition: width 0.3s ease; }

        .question-statement { font-size: 1.15rem; font-weight: 700; color: var(--text-main); margin-bottom: 20px; min-height: 56px; }
        .options-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
        .option-choice {
            padding: 14px 18px; border: 1px solid var(--border); border-radius: 12px; cursor: pointer;
            font-size: 0.925rem; font-weight: 500; display: flex; align-items: center; justify-content: space-between;
            background: var(--bg); transition: all 0.2s;
        }
        .option-choice:hover { border-color: var(--primary); background: var(--primary-light); }
        .option-choice.selected { border-color: var(--primary); background: var(--primary-light); font-weight: 700; color: var(--primary); }

        /* Counseling Banner */
        .counsel-strip {
            background: linear-gradient(135deg, var(--primary), #1b4332); color: #fff;
            border-radius: var(--radius); padding: 36px; display: flex; justify-content: space-between;
            align-items: center; gap: 24px; flex-wrap: wrap; box-shadow: var(--shadow);
        }
        .counsel-strip h3 { font-size: 1.35rem; margin-bottom: 6px; }
        .counsel-strip p { opacity: 0.9; font-size: 0.95rem; }

        /* Tables & Helplines */
        .clean-table { width: 100%; border-collapse: collapse; background: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid var(--border); }
        .clean-table th, .clean-table td { padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .clean-table th { background: var(--primary-light); color: var(--primary); font-weight: 700; }

        .helpline-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .helpline-box {
            background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px;
            padding: 22px; text-align: center; box-shadow: var(--shadow-sm);
        }
        .helpline-number { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin: 6px 0 2px 0; }

        /* Modals */
        .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 2000; align-items: center; justify-content: center; }
        .modal-panel { background: var(--card-bg); border-radius: var(--radius); max-width: 500px; width: 92%; padding: 32px; position: relative; box-shadow: var(--shadow); max-height: 90vh; overflow-y: auto; }
        .modal-close { position: absolute; top: 16px; right: 18px; font-size: 1.4rem; background: none; border: none; cursor: pointer; color: var(--text-muted); }

        .form-field { margin-bottom: 16px; }
        .form-field label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--bg); color: var(--text-main); font-size: 0.9rem; outline: none; }
        .form-input:focus { border-color: var(--primary); }

        /* Pulsating Breathing Circle */
        .breath-orb {
            width: 130px; height: 130px; border-radius: 50%; background: var(--primary-light);
            border: 4px solid var(--primary); margin: 24px auto; display: flex; align-items: center;
            justify-content: center; font-weight: 700; color: var(--primary); font-size: 1rem;
            transition: transform 4s ease-in-out;
        }
        .breath-orb.expand { transform: scale(1.35); transition: transform 4s ease-in-out; }
        .breath-orb.hold { transform: scale(1.35); }
        .breath-orb.shrink { transform: scale(0.85); transition: transform 8s ease-in-out; }

        /* Chatbot Floating Widget */
        .chat-trigger {
            position: fixed; bottom: 25px; right: 25px; width: 56px; height: 56px; border-radius: 50%;
            background: var(--primary); color: #fff; border: none; box-shadow: 0 8px 24px rgba(30, 77, 43, 0.35);
            cursor: pointer; z-index: 1500; font-size: 1.3rem; display: flex; align-items: center; justify-content: center;
        }
        .chat-drawer {
            position: fixed; bottom: 95px; right: 25px; width: 370px; height: 520px; max-width: calc(100vw - 40px);
            background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius);
            box-shadow: var(--shadow); z-index: 1500; display: none; flex-direction: column; overflow: hidden;
        }
        .chat-top { background: var(--primary); color: #fff; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
        .chat-stream { flex: 1; padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: var(--bg); font-size: 0.88rem; }
        .chat-bubble { max-width: 85%; padding: 10px 14px; border-radius: 14px; line-height: 1.5; white-space: pre-wrap; }
        .chat-bubble.bot { background: var(--card-bg); color: var(--text-main); align-self: flex-start; border: 1px solid var(--border); border-bottom-left-radius: 2px; }
        .chat-bubble.user { background: var(--primary); color: #fff; align-self: flex-end; border-bottom-right-radius: 2px; }

        @media (max-width: 850px) {
            .hero-layout { grid-template-columns: 1fr; text-align: center; }
            .hero-heading { font-size: 1.8rem; }
            .nav-menu { display: none; }
        }
    </style>
</head>
<body>

<?php display_flash(); ?>

<!-- 1. INSTITUTIONAL TOP NAVBAR -->
<header class="site-navbar">
    <a href="<?php echo SITE_URL; ?>/index.php" class="brand-logo">
        <div class="brand-icon-box">
            <i class="fa-solid fa-brain"></i>
        </div>
        <div class="brand-meta">
            <div class="brand-title" id="txtBrand">Student Mental Wellness</div>
            <div class="brand-sub">Confidential Check-in System</div>
        </div>
    </a>

    <ul class="nav-menu">
        <li><a href="#assessment"><i class="fa-solid fa-clipboard-check"></i> <span id="navAssessment">Assessment</span></a></li>
        <li><a href="#mood"><i class="fa-solid fa-seedling"></i> <span id="navMood">Mood Log</span></a></li>
        <li><a href="#counseling"><i class="fa-solid fa-user-doctor"></i> <span id="navCounseling">Counseling</span></a></li>
        <li><a href="#history"><i class="fa-solid fa-chart-line"></i> <span id="navHistory">History</span></a></li>
        <li><a href="#directory"><i class="fa-solid fa-hospital"></i> <span id="navDirectory">Clinics</span></a></li>
        <li><a href="<?php echo SITE_URL; ?>/dashboard.php"><i class="fa-solid fa-user"></i> <span>Dashboard</span></a></li>
    </ul>

    <div class="nav-toolbar">
        <!-- Logged-in Student Identity Chip -->
        <a href="<?php echo SITE_URL; ?>/dashboard.php" class="student-chip">
            <div class="student-avatar"><?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?></div>
            <div class="student-details">
                <div class="student-name"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                <div class="student-id"><?php echo htmlspecialchars($currentUser['student_id'] ?: 'Student'); ?></div>
            </div>
        </a>

        <!-- Interactive Wellness Modals Controls -->
        <button class="btn-tool" onclick="openBreathingModal()" title="4-7-8 Breathing Technique">
            <i class="fa-solid fa-wind"></i> <span id="btnTxtBreath">Breathing</span>
        </button>
        <button class="btn-tool" onclick="openGroundingModal()" title="5-4-3-2-1 Sensory Grounding">
            <i class="fa-solid fa-hands-holding"></i> Grounding
        </button>

        <!-- Language & Theme Switchers -->
        <button class="btn-tool" onclick="toggleLanguage()">
            <i class="fa-solid fa-globe"></i> <span id="langTxt">සිංහල</span>
        </button>
        <button class="btn-tool" onclick="toggleTheme()" aria-label="Toggle Theme">
            <i class="fa-solid fa-moon"></i>
        </button>

        <!-- Logout -->
        <a href="<?php echo SITE_URL; ?>/logout.php" class="btn-tool btn-tool-danger" title="Sign Out">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</header>

<!-- 2. AMBIENT RELAXATION AUDIO STRIP -->
<div class="ambient-strip">
    <span id="txtAudioLabel"><i class="fa-solid fa-headphones"></i> Nature Sound Therapy (Ambient Rain):</span>
    <button class="btn-tool" onclick="toggleAmbientAudio()" id="btnAudioToggle">
        <i class="fa-solid fa-play" id="audioIcon"></i> Play Sound
    </button>
    <a href="https://wa.me/94771234567?text=Hello%20Student%20Wellness%20Support" target="_blank" rel="noopener noreferrer" class="btn-tool" style="color:#16a34a;">
        <i class="fa-brands fa-whatsapp"></i> Student Support Desk
    </a>
</div>

<!-- 3. HERO SECTION -->
<section class="hero-section">
    <div class="wrapper">
        <div class="hero-layout">
            <div>
                <div class="hero-badge">
                    <i class="fa-solid fa-shield-halved"></i> Institutional Counseling Bridge
                </div>
                <h1 class="hero-heading" id="heroTitle">A Safe & Confidential Space for Your Mental Wellness</h1>
                <p class="hero-lead" id="heroDesc">
                    Identify academic, exam, and personal stress early. Regular check-ins empower you to track emotional resilience and access professional counseling when you need it.
                </p>
                <div class="hero-buttons">
                    <a href="#assessment" class="btn-primary-action">
                        <i class="fa-solid fa-heart-pulse"></i> <span id="heroBtn">Take Self-Check Assessment</span>
                    </a>
                    <a href="#mood" class="btn-outline-action">
                        <i class="fa-solid fa-calendar-check"></i> <span id="heroMoodBtn">Daily Reflection</span>
                    </a>
                </div>
            </div>

            <div>
                <div class="hero-visual-card">
                    <div class="hero-visual-icon">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <h3 style="font-size:1.25rem; margin-bottom:8px;" id="cardGreeting">Welcome, <?php echo htmlspecialchars($currentUser['name']); ?></h3>
                    <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:20px;">
                        Registration ID: <strong><?php echo htmlspecialchars($currentUser['student_id']); ?></strong> &bull; <?php echo htmlspecialchars($currentUser['intake']); ?>
                    </p>
                    <div style="display:flex; justify-content:space-around; border-top:1px solid var(--border); padding-top:16px;">
                        <div>
                            <div style="font-size:1.4rem; font-weight:700; color:var(--primary);">100%</div>
                            <small style="font-size:0.75rem; color:var(--text-muted);">Confidential</small>
                        </div>
                        <div>
                            <div style="font-size:1.4rem; font-weight:700; color:var(--primary);">PHQ-9</div>
                            <small style="font-size:0.75rem; color:var(--text-muted);">Depression Scale</small>
                        </div>
                        <div>
                            <div style="font-size:1.4rem; font-weight:700; color:var(--primary);">GAD-7</div>
                            <small style="font-size:0.75rem; color:var(--text-muted);">Anxiety Scale</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. VECTOR-ICON DAILY MOOD JOURNAL -->
<section class="section-wrapper" id="mood">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="moodHeading">Daily Emotional Reflection</h2>
            <p class="section-subtitle" id="moodSubtitle">Select your dominant emotional state today to log patterns over the academic semester.</p>
        </div>

        <div style="max-width:850px; margin:0 auto;">
            <div class="mood-cards-grid">
                <!-- Thriving -->
                <div class="mood-card-item" onclick="selectVectorMood('thriving', 'fa-sun', 'Thriving / ප්‍රබෝධමත්', this)">
                    <div class="mood-icon-wrapper icon-thriving">
                        <i class="fa-solid fa-sun"></i>
                    </div>
                    <div class="mood-title" id="mTitle1">Thriving</div>
                    <div class="mood-desc" id="mDesc1">Energized & Motivated</div>
                </div>

                <!-- Balanced -->
                <div class="mood-card-item" onclick="selectVectorMood('balanced', 'fa-seedling', 'Balanced / සන්සුන්', this)">
                    <div class="mood-icon-wrapper icon-balanced">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <div class="mood-title" id="mTitle2">Balanced</div>
                    <div class="mood-desc" id="mDesc2">Calm & In Control</div>
                </div>

                <!-- Fatigued -->
                <div class="mood-card-item" onclick="selectVectorMood('fatigued', 'fa-cloud-rain', 'Fatigued / වෙහෙසයි', this)">
                    <div class="mood-icon-wrapper icon-fatigued">
                        <i class="fa-solid fa-cloud-rain"></i>
                    </div>
                    <div class="mood-title" id="mTitle3">Fatigued</div>
                    <div class="mood-desc" id="mDesc3">Low Energy / Drained</div>
                </div>

                <!-- Distressed -->
                <div class="mood-card-item" onclick="selectVectorMood('distressed', 'fa-bolt', 'Distressed / පීඩිතයි', this)">
                    <div class="mood-icon-wrapper icon-distressed">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="mood-title" id="mTitle4">Distressed</div>
                    <div class="mood-desc" id="mDesc4">Anxious / Overwhelmed</div>
                </div>
            </div>

            <div style="background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow-sm);">
                <div class="form-field">
                    <label for="moodNotes" id="lblMoodNote">Reflections or Notes (Optional)</label>
                    <textarea id="moodNotes" rows="2" class="form-input" placeholder="What influenced your emotional state today? (e.g. coursework, sleep, personal)"></textarea>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <button class="btn-primary-action" onclick="submitMoodLog()" id="btnSaveMood">
                        <i class="fa-solid fa-check"></i> Save Daily Reflection
                    </button>
                    <small id="moodStatusIndicator" style="color:var(--text-muted);"></small>
                </div>
                <div id="moodRecentStream" style="margin-top:20px; font-size:0.875rem;"></div>
            </div>
        </div>
    </div>
</section>

<!-- 5. COUNSELING STRIP BANNER -->
<section class="section-wrapper" id="counseling">
    <div class="wrapper">
        <div class="counsel-strip">
            <div>
                <h3 id="counselStripTitle">Need Confidential Guidance from a Professional Counselor?</h3>
                <p id="counselStripDesc">Schedule a one-on-one session online or in-person with complete privacy (Anonymous requests permitted).</p>
            </div>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <button class="btn-outline-action" onclick="openCounselingModal()" style="background:#fff; color:var(--primary); font-weight:700;">
                    <i class="fa-solid fa-calendar-plus"></i> <span id="btnBookCounsel">Schedule Session</span>
                </button>
                <a href="tel:1926" class="btn-primary-action" style="background:#dc2626;">
                    <i class="fa-solid fa-phone"></i> 1926 Emergency
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 6. CLINICAL ASSESSMENT WIZARD (PHQ-9 & GAD-7) -->
<section class="section-wrapper" id="assessment">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="assessHeading">Standardized Self-Check Assessments</h2>
            <p class="section-subtitle" id="assessSubtitle">Evidence-based clinical questionnaires designed to measure depression and anxiety indicators.</p>
        </div>

        <div class="assessment-card">
            <!-- Tabs -->
            <div class="tab-bar">
                <button class="tab-btn active" id="tabPhq" onclick="switchTest('phq9')">
                    <i class="fa-solid fa-chart-simple"></i> PHQ-9 (Depression Screening)
                </button>
                <button class="tab-btn" id="tabGad" onclick="switchTest('gad7')">
                    <i class="fa-solid fa-heart-pulse"></i> GAD-7 (Anxiety Screening)
                </button>
            </div>

            <div class="progress-track">
                <div class="progress-fill" id="progressFill"></div>
            </div>

            <!-- Question Flow -->
            <div id="quizFlow">
                <div style="font-size:0.8rem; font-weight:700; color:var(--primary); text-transform:uppercase; margin-bottom:6px;" id="qStepNum">
                    Question 1 of 9
                </div>
                <div class="question-statement" id="qStatement"></div>
                <div class="options-list" id="optionsContainer"></div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px;">
                    <button class="btn-outline-action" id="btnPrevQ" onclick="moveQuestion(-1)" style="display:none;">
                        <i class="fa-solid fa-arrow-left"></i> Previous
                    </button>
                    <button class="btn-primary-action" id="btnNextQ" onclick="moveQuestion(1)">
                        Next <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Result Box -->
            <div id="assessmentResultView" style="display:none; text-align:center; padding:10px 0;"></div>
        </div>
    </div>
</section>

<!-- 7. SCORE TREND CHART -->
<section class="section-wrapper" id="history">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="chartHeading">Personal Score History & Trends</h2>
            <p class="section-subtitle" id="chartSubtitle">Track changes in depression and anxiety indicators across consecutive check-ins.</p>
        </div>

        <div style="max-width:850px; margin:0 auto; background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:28px; box-shadow:var(--shadow);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                <span style="font-size:0.85rem; font-weight:700; color:var(--primary); text-transform:uppercase;">Historical Trajectory</span>
                <a href="<?php echo SITE_URL; ?>/dashboard.php" class="btn-tool">
                    <i class="fa-solid fa-table"></i> View Detailed Logs
                </a>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="mainTrendsChart"></canvas>
            </div>
        </div>
    </div>
</section>

<!-- 8. RESOURCE DIRECTORY & HELPLINES -->
<section class="section-wrapper" id="directory">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="dirHeading">Institutional & National Healthcare Directory</h2>
            <p class="section-subtitle" id="dirSubtitle">Direct contacts to psychiatric and mental healthcare clinics across Sri Lanka.</p>
        </div>

        <div style="max-width:900px; margin:0 auto 40px auto; overflow-x:auto;">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th id="thDist">District</th>
                        <th id="thHosp">Hospital / Specialized Center</th>
                        <th id="thTel">Direct Contact</th>
                    </tr>
                </thead>
                <tbody id="clinicsTableBody"></tbody>
            </table>
        </div>

        <div class="section-header" style="margin-bottom:20px;">
            <h3 style="font-size:1.25rem;" id="helpTitle">Emergency 24/7 Support Lines</h3>
        </div>

        <div class="helpline-row" style="max-width:900px; margin:0 auto;">
            <div class="helpline-box">
                <i class="fa-solid fa-phone-volume" style="color:var(--primary); font-size:1.5rem;"></i>
                <div class="helpline-number">1926</div>
                <strong id="hl1">National Mental Health Institute</strong>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Toll-free 24/7 Government Helpline</p>
            </div>

            <div class="helpline-box">
                <i class="fa-solid fa-headset" style="color:var(--primary); font-size:1.5rem;"></i>
                <div class="helpline-number">1333</div>
                <strong id="hl2">CCC Line Crisis Support</strong>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Confidential emotional relief</p>
            </div>

            <div class="helpline-box">
                <i class="fa-solid fa-hands-holding-child" style="color:var(--primary); font-size:1.5rem;"></i>
                <div class="helpline-number">011 2696666</div>
                <strong id="hl3">Sri Lanka Sumithrayo</strong>
                <p style="font-size:0.75rem; color:var(--text-muted); margin-top:4px;">Befriending and suicide prevention</p>
            </div>
        </div>
    </div>
</section>

<!-- 9. MODALS -->

<!-- A. 4-7-8 Breathing Guide Modal -->
<div class="modal-backdrop" id="modalBreath">
    <div class="modal-panel" style="text-align:center;">
        <button class="modal-close" onclick="closeBreathingModal()">&times;</button>
        <h3 style="font-size:1.3rem; color:var(--primary); margin-bottom:6px;">4-7-8 Breathing Technique</h3>
        <p style="font-size:0.85rem; color:var(--text-muted);" id="breathSub">A clinically proven rhythm to reduce heart rate and trigger the parasympathetic nervous system.</p>
        
        <div class="breath-orb" id="breathOrb">Ready...</div>
        <div id="breathInstruction" style="font-weight:700; color:var(--primary); font-size:0.95rem; min-height:26px;"></div>

        <button class="btn-outline-action" onclick="closeBreathingModal()" style="margin-top:20px;">Close Exercise</button>
    </div>
</div>

<!-- B. 5-4-3-2-1 Grounding Modal -->
<div class="modal-backdrop" id="modalGround">
    <div class="modal-panel">
        <button class="modal-close" onclick="closeGroundingModal()">&times;</button>
        <h3 style="font-size:1.3rem; color:var(--primary); margin-bottom:8px;">5-4-3-2-1 Sensory Grounding</h3>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:18px;">When experiencing academic panic or racing thoughts, use your 5 senses to re-anchor in the present moment:</p>

        <div style="display:flex; flex-direction:column; gap:10px; font-size:0.9rem;" id="groundingItems"></div>

        <button class="btn-primary-action" onclick="closeGroundingModal()" style="width:100%; justify-content:center; margin-top:22px;">
            Acknowledge & Close
        </button>
    </div>
</div>

<!-- C. Counseling Booking Modal -->
<div class="modal-backdrop" id="modalCounsel">
    <div class="modal-panel">
        <button class="modal-close" onclick="closeCounselingModal()">&times;</button>
        <h3 style="font-size:1.3rem; color:var(--primary); margin-bottom:8px;">Schedule Counseling Session</h3>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:20px;">Institutional sessions are strictly confidential. You may also submit anonymously.</p>

        <form id="counselForm" onsubmit="submitCounselingBooking(event)">
            <div class="form-field">
                <label for="counselPrivacy">Confidentiality Preference</label>
                <select id="counselPrivacy" class="form-input" onchange="toggleCounselorPrivacy(this.value)">
                    <option value="named">Standard (Include My Name & Student ID)</option>
                    <option value="anonymous">Anonymous Request (Identity Concealed)</option>
                </select>
            </div>

            <div class="form-field" id="counselNameField">
                <label for="counselName">Student Name & ID</label>
                <input type="text" id="counselName" class="form-input" value="<?php echo htmlspecialchars($currentUser['name'] . ' (' . $currentUser['student_id'] . ')'); ?>">
            </div>

            <div class="form-field">
                <label for="counselMode">Preferred Session Format</label>
                <select id="counselMode" class="form-input">
                    <option value="online">Online Confidential Session (Video / Chat)</option>
                    <option value="in-person">In-Person Office Session (Health Unit)</option>
                </select>
            </div>

            <div class="form-field">
                <label for="counselDate">Preferred Date & Time</label>
                <input type="datetime-local" id="counselDate" class="form-input" required value="<?php echo date('Y-m-d\TH:i', strtotime('+1 day 10:00')); ?>">
            </div>

            <div class="form-field">
                <label for="counselNotes">Reason / Specific Notes (Optional)</label>
                <textarea id="counselNotes" rows="3" class="form-input" placeholder="Briefly share any topics or concerns you would like to discuss..."></textarea>
            </div>

            <button type="submit" class="btn-primary-action" style="width:100%; justify-content:center;">
                <i class="fa-solid fa-paper-plane"></i> Submit Appointment Request
            </button>
        </form>
    </div>
</div>

<!-- 10. AI CHATBOT WIDGET -->
<button class="chat-trigger" onclick="toggleChatWindow()" aria-label="Open AI Assistant">
    <i class="fa-solid fa-comment-dots"></i>
</button>

<div class="chat-drawer" id="chatDrawer">
    <div class="chat-top">
        <div style="display:flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-brain"></i>
            <span id="chatHeaderTitle">Wellness AI Assistant</span>
        </div>
        <button onclick="toggleChatWindow()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:1.1rem;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="chat-stream" id="chatStream">
        <div class="chat-bubble bot" id="chatWelcomeMsg">Hello! I am your student wellness assistant. Feel free to share whatever is on your mind. How can I assist you today?</div>
    </div>

    <div style="display:flex; gap:6px; padding:8px 12px; overflow-x:auto; background:var(--card-bg); border-top:1px solid var(--border);">
        <button class="btn-tool" style="font-size:0.75rem; padding:4px 8px;" onclick="sendQuickPrompt('I feel overwhelmed with exam stress')">Exam Stress</button>
        <button class="btn-tool" style="font-size:0.75rem; padding:4px 8px;" onclick="sendQuickPrompt('How can I calm my mind right now?')">Calm My Mind</button>
        <button class="btn-tool" style="font-size:0.75rem; padding:4px 8px;" onclick="sendQuickPrompt('How to contact 1926 helpline?')">1926 Hotline</button>
    </div>

    <div style="display:flex; padding:10px; background:var(--card-bg); border-top:1px solid var(--border); gap:8px;">
        <input type="text" id="chatInputField" class="form-input" style="border-radius:20px;" placeholder="Type your message..." onkeypress="if(event.key==='Enter') sendChatMessage()">
        <button onclick="sendChatMessage()" class="btn-primary-action" style="padding:10px 14px; border-radius:50%;">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<footer style="text-align:center; padding:35px 20px; font-size:0.85rem; color:var(--text-muted); border-top:1px solid var(--border); background:var(--card-bg);">
    <p>&copy; <?php echo date('Y'); ?> Student Mental Wellness Check-in System. Developed by B.A.I.D Bopitiya (DIT 14253 - DIT 14 Intake).</p>
    <p style="font-size:0.78rem; margin-top:4px;">Clinical Disclaimer: Screening tools (PHQ-9 & GAD-7) are for educational wellness tracking and do not substitute for formal clinical diagnosis.</p>
</footer>

<!-- External Scripts Configuration -->
<script>
    const SITE_ROOT = "<?php echo SITE_URL; ?>";
    const LOGGED_IN_STUDENT_NAME = "<?php echo htmlspecialchars($currentUser['name']); ?>";
    const LOGGED_IN_STUDENT_ID = "<?php echo htmlspecialchars($currentUser['student_id']); ?>";
</script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
