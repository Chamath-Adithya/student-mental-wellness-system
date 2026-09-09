<?php
require_once __DIR__ . '/config/db.php';
require_login();

$currentUser = current_user();
$isAdmin = ($currentUser['role'] === 'admin' || $currentUser['role'] === 'counselor');
if ($isAdmin && !isset($_GET['preview'])) {
    redirect(SITE_URL . '/admin-dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sansun (සන්සුන්) - Student Mental Wellness Check-in System</title>
    
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2">
    
    <!-- Modern Typography & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            --shadow-sm: 0 2px 6px rgba(0,0,0,0.04);
            --shadow: 0 14px 30px -6px rgba(30, 77, 43, 0.08);
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
            --shadow: 0 14px 30px -6px rgba(0, 0, 0, 0.4);
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; transition: background-color 0.25s, color 0.25s; }
        html { scroll-behavior: smooth; }
        body { background-color: var(--bg); color: var(--text-main); line-height: 1.6; }

        .wrapper { max-width: 1140px; margin: 0 auto; padding: 0 24px; }

        /* ==========================================================================
           1. SIMPLIFIED & ELEGANT INSTITUTIONAL NAVBAR
           ========================================================================== */
        .site-navbar {
            position: sticky; top: 0; background: var(--card-bg);
            border-bottom: 1px solid var(--border); padding: 12px 28px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 20px; z-index: 1000; box-shadow: var(--shadow-sm);
        }

        .brand-logo {
            display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-main);
            flex-shrink: 0;
        }
        .brand-logo-img {
            width: 44px; height: 44px; object-fit: contain;
            border-radius: 12px;
            padding: 2px;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.25s ease;
            filter: drop-shadow(0 3px 8px rgba(30, 77, 43, 0.16));
        }
        .brand-logo:hover .brand-logo-img {
            transform: scale(1.08) rotate(2deg);
            filter: drop-shadow(0 5px 12px rgba(30, 77, 43, 0.25));
        }
        [data-theme="dark"] .brand-logo-img {
            filter: drop-shadow(0 0 10px rgba(82, 183, 136, 0.65)) drop-shadow(0 0 20px rgba(45, 106, 79, 0.35));
        }
        [data-theme="dark"] .brand-logo:hover .brand-logo-img {
            filter: drop-shadow(0 0 14px rgba(82, 183, 136, 0.85)) drop-shadow(0 0 26px rgba(45, 106, 79, 0.5));
        }
        .brand-meta { line-height: 1.25; }
        .brand-title {
            font-weight: 800; font-size: 1.25rem; color: var(--primary); letter-spacing: -0.3px;
            display: flex; align-items: center; gap: 6px;
        }
        .brand-title-badge {
            font-size: 0.72rem; font-weight: 600; padding: 2px 7px;
            background: var(--primary-light); color: var(--primary);
            border-radius: 6px; border: 1px solid var(--border);
        }
        .brand-sub { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }

        .nav-menu { display: flex; gap: 28px; list-style: none; align-items: center; }
        .nav-menu a {
            text-decoration: none; color: var(--text-main); font-size: 0.925rem; font-weight: 600;
            display: flex; align-items: center; gap: 7px; transition: color 0.2s; white-space: nowrap;
        }
        .nav-menu a:hover { color: var(--primary); }

        .nav-toolbar { display: flex; align-items: center; gap: 12px; }

        /* Sound Pill Button */
        .btn-sound-pill {
            background: var(--primary-light); border: 1px solid var(--border); color: var(--primary);
            padding: 8px 14px; border-radius: 30px; font-size: 0.85rem; font-weight: 700;
            cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-sound-pill:hover { background: var(--primary); color: #fff; }
        .btn-sound-pill.playing { background: var(--primary); color: #fff; box-shadow: 0 0 12px rgba(30, 77, 43, 0.35); }

        /* Student Profile Dropdown Container */
        .profile-dropdown-container { position: relative; }
        .student-chip-btn {
            display: flex; align-items: center; gap: 10px; padding: 6px 14px 6px 8px; border-radius: 30px;
            background: var(--bg); border: 1px solid var(--border); cursor: pointer; color: var(--text-main);
            transition: all 0.2s;
        }
        .student-chip-btn:hover { border-color: var(--primary); background: var(--primary-light); }
        .student-avatar {
            width: 32px; height: 32px; border-radius: 50%; background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;
        }
        .student-name-text { font-size: 0.875rem; font-weight: 700; color: var(--primary); max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .profile-dropdown-menu {
            position: absolute; top: calc(100% + 10px); right: 0; width: 260px; background: var(--card-bg);
            border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow);
            padding: 10px 0; display: none; flex-direction: column; z-index: 2000;
        }
        .profile-dropdown-menu.show { display: flex; animation: dropDownFade 0.2s ease; }
        @keyframes dropDownFade {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-header { padding: 12px 18px; line-height: 1.3; }
        .dropdown-name { font-weight: 700; font-size: 0.95rem; color: var(--text-main); }
        .dropdown-meta { font-size: 0.775rem; color: var(--text-muted); margin-top: 3px; }
        .dropdown-divider { height: 1px; background: var(--border); margin: 6px 0; }
        .dropdown-item {
            padding: 10px 18px; color: var(--text-main); text-decoration: none; font-size: 0.875rem;
            font-weight: 600; display: flex; align-items: center; gap: 10px; transition: background 0.15s;
        }
        .dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
        .dropdown-danger { color: var(--danger); }
        .dropdown-danger:hover { background: #fee2e2; color: #b91c1c; }

        /* ==========================================================================
           2. HERO SECTION
           ========================================================================== */
        .hero-section {
            padding: 55px 0 50px 0;
            background: linear-gradient(180deg, var(--primary-light) 0%, var(--bg) 100%);
            border-bottom: 1px solid var(--border);
        }
        .hero-layout { display: grid; grid-template-columns: 1.2fr 0.9fr; gap: 40px; align-items: center; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px;
            background: var(--card-bg); border: 1px solid var(--border); color: var(--primary);
            font-size: 0.825rem; font-weight: 700; margin-bottom: 16px; box-shadow: var(--shadow-sm);
        }
        .hero-heading { font-size: 2.35rem; line-height: 1.25; font-weight: 800; margin-bottom: 14px; letter-spacing: -0.5px; }
        .hero-lead { font-size: 1.05rem; color: var(--text-muted); margin-bottom: 30px; line-height: 1.7; }
        .hero-buttons { display: flex; gap: 14px; flex-wrap: wrap; }

        .btn-primary-action {
            background: var(--primary); color: #fff; padding: 13px 26px; border-radius: 12px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 14px rgba(30, 77, 43, 0.2);
        }
        .btn-primary-action:hover { background: var(--primary-accent); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(30, 77, 43, 0.3); }

        .btn-outline-action {
            background: var(--card-bg); color: var(--text-main); padding: 13px 24px; border-radius: 12px;
            font-weight: 700; font-size: 0.95rem; text-decoration: none; border: 1.5px solid var(--border); cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s;
        }
        .btn-outline-action:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }

        .hero-visual-card {
            background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 32px 28px; box-shadow: var(--shadow); text-align: center;
        }
        .hero-visual-icon {
            width: 68px; height: 68px; border-radius: 50%; background: var(--primary-light);
            color: var(--primary); display: flex; align-items: center; justify-content: center;
            font-size: 2rem; margin: 0 auto 16px auto;
        }

        /* ==========================================================================
           3. SECTION COMMON STYLES
           ========================================================================== */
        .section-wrapper { padding: 55px 0; border-bottom: 1px solid var(--border); }
        .section-header { text-align: center; margin-bottom: 35px; }
        .section-title { font-size: 1.7rem; font-weight: 800; color: var(--primary); margin-bottom: 8px; letter-spacing: -0.3px; }
        .section-subtitle { font-size: 0.98rem; color: var(--text-muted); max-width: 620px; margin: 0 auto; line-height: 1.6; }

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
            width: 52px; height: 52px; border-radius: 14px; margin: 0 auto 14px auto;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .icon-thriving { background: #dcfce7; color: #15803d; }
        .icon-balanced { background: #ccfbf1; color: #0f766e; }
        .icon-fatigued { background: #fef3c7; color: #b45309; }
        .icon-distressed { background: #fee2e2; color: #b91c1c; }

        .mood-title { font-weight: 700; font-size: 1.05rem; margin-bottom: 4px; }
        .mood-desc { font-size: 0.825rem; color: var(--text-muted); }

        /* Assessment Card */
        .assessment-card {
            max-width: 740px; margin: 0 auto; background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 34px; box-shadow: var(--shadow);
        }
        .tab-bar { display: flex; background: var(--primary-light); border-radius: 12px; padding: 6px; gap: 6px; margin-bottom: 24px; }
        .tab-btn {
            flex: 1; padding: 11px; border: none; background: transparent; border-radius: 8px;
            font-size: 0.9rem; font-weight: 700; color: var(--text-muted); cursor: pointer; transition: all 0.2s;
        }
        .tab-btn.active { background: var(--card-bg); color: var(--primary); box-shadow: var(--shadow-sm); }

        .progress-track { width: 100%; height: 6px; background: var(--primary-light); border-radius: 10px; overflow: hidden; margin-bottom: 20px; }
        .progress-fill { height: 100%; width: 0%; background: var(--primary); transition: width 0.3s ease; }

        .question-statement { font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 22px; min-height: 56px; }
        .options-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
        .option-choice {
            padding: 14px 18px; border: 1px solid var(--border); border-radius: 12px; cursor: pointer;
            font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;
            background: var(--bg); transition: all 0.2s;
        }
        .option-choice:hover { border-color: var(--primary); background: var(--primary-light); }
        .option-choice.selected { border-color: var(--primary); background: var(--primary-light); font-weight: 700; color: var(--primary); }

        /* Relief Cards Grid */
        .relief-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .relief-card {
            background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius);
            padding: 28px; box-shadow: var(--shadow-sm); display: flex; flex-direction: column; justify-content: space-between;
        }
        .relief-icon {
            width: 46px; height: 46px; border-radius: 12px; background: var(--primary-light); color: var(--primary);
            display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 16px;
        }

        /* Clean Tables & Helplines */
        .clean-table { width: 100%; border-collapse: collapse; background: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid var(--border); }
        .clean-table th, .clean-table td { padding: 14px 18px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .clean-table th { background: var(--primary-light); color: var(--primary); font-weight: 700; }

        .helpline-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; }
        .helpline-box {
            background: var(--card-bg); border: 1px solid var(--border); border-radius: 14px;
            padding: 24px; text-align: center; box-shadow: var(--shadow-sm);
        }
        .helpline-number { font-size: 1.6rem; font-weight: 800; color: var(--primary); margin: 6px 0 2px 0; }

        /* Modals */
        .modal-backdrop { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 3000; align-items: center; justify-content: center; }
        .modal-panel { background: var(--card-bg); border-radius: var(--radius); max-width: 500px; width: 92%; padding: 32px; position: relative; box-shadow: var(--shadow); max-height: 90vh; overflow-y: auto; }
        .modal-close { position: absolute; top: 16px; right: 18px; font-size: 1.4rem; background: none; border: none; cursor: pointer; color: var(--text-muted); }

        .form-field { margin-bottom: 16px; }
        .form-field label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: 10px; background: var(--bg); color: var(--text-main); font-size: 0.9rem; outline: none; }
        .form-input:focus { border-color: var(--primary); }

        /* Pulsating Breathing Orb */
        .breath-orb {
            width: 140px; height: 140px; border-radius: 50%; background: var(--primary-light);
            border: 4px solid var(--primary); margin: 24px auto; display: flex; align-items: center;
            justify-content: center; font-weight: 700; color: var(--primary); font-size: 1.05rem;
            transition: transform 4s ease-in-out;
        }
        .breath-orb.expand { transform: scale(1.35); transition: transform 4s ease-in-out; }
        .breath-orb.hold { transform: scale(1.35); }
        .breath-orb.shrink { transform: scale(0.85); transition: transform 8s ease-in-out; }

        /* Floating AI Chatbot */
        .chat-trigger {
            position: fixed; bottom: 25px; right: 25px; width: 56px; height: 56px; border-radius: 50%;
            background: var(--primary); color: #fff; border: none; box-shadow: 0 8px 24px rgba(30, 77, 43, 0.35);
            cursor: pointer; z-index: 1500; font-size: 1.3rem; display: flex; align-items: center; justify-content: center;
            transition: transform 0.2s ease;
        }
        .chat-trigger:hover { transform: scale(1.05); }
        .chat-drawer {
            position: fixed; bottom: 95px; right: 25px; width: 380px; height: 520px; max-width: calc(100vw - 40px);
            background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius);
            box-shadow: var(--shadow); z-index: 1500; display: none; flex-direction: column; overflow: hidden;
        }
        .chat-top { background: var(--primary); color: #fff; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
        .chat-stream { flex: 1; padding: 14px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: var(--bg); font-size: 0.88rem; }
        .chat-bubble { max-width: 85%; padding: 10px 14px; border-radius: 14px; line-height: 1.5; white-space: pre-wrap; }
        .chat-bubble.bot { background: var(--card-bg); color: var(--text-main); align-self: flex-start; border: 1px solid var(--border); border-bottom-left-radius: 2px; }
        .chat-bubble.user { background: var(--primary); color: #fff; align-self: flex-end; border-bottom-right-radius: 2px; }

        @media (max-width: 880px) {
            .hero-layout { grid-template-columns: 1fr; text-align: center; }
            .hero-heading { font-size: 1.9rem; }
            .nav-menu { display: none; }
        }
    </style>
</head>
<body>

<?php display_flash(); ?>

<?php if ($isAdmin): ?>
<div style="background:#0f172a; color:#fff; padding:9px 24px; font-size:0.85rem; display:flex; justify-content:space-between; align-items:center; z-index:9999; border-bottom:1px solid #334155;">
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="background:#1e4d2b; color:#86efac; padding:3px 10px; border-radius:12px; font-size:0.75rem; font-weight:800; letter-spacing:0.5px;">COUNSELOR PREVIEW</span>
        <span>You are currently previewing the Student Wellness Portal interface.</span>
    </div>
    <a href="<?php echo SITE_URL; ?>/admin-dashboard.php" style="color:#52b788; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
        <i class="fa-solid fa-arrow-left"></i> Return to Control Panel
    </a>
</div>
<?php endif; ?>

<!-- ==========================================================================
     1. INSTITUTIONAL TOP NAVBAR
     ========================================================================== -->
<header class="site-navbar">
    <a href="<?php echo SITE_URL; ?>/index.php" class="brand-logo" title="Sansun (සන්සුන්) - Home">
        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2" alt="Sansun Logo" class="brand-logo-img">
        <div class="brand-meta">
            <div class="brand-title"><span id="txtBrand">Sansun</span> <span class="brand-title-badge">සන්සුන්</span></div>
            <div class="brand-sub" id="txtBrandSub">Student Mental Wellness</div>
        </div>
    </a>

    <!-- Clean 4-Item Navigation Links -->
    <ul class="nav-menu">
        <li><a href="#mood"><i class="fa-solid fa-seedling"></i> <span id="navMood">Daily Reflection</span></a></li>
        <li><a href="#assessment"><i class="fa-solid fa-clipboard-check"></i> <span id="navAssessment">Self-Check</span></a></li>
        <li><a href="#counseling"><i class="fa-solid fa-user-doctor"></i> <span id="navCounseling">Counseling</span></a></li>
        <li><a href="#directory"><i class="fa-solid fa-phone-volume"></i> <span id="navDirectory">24/7 Support</span></a></li>
    </ul>

    <!-- Clean Toolbar: Ambient Sound Pill + Profile Dropdown -->
    <div class="nav-toolbar">
        <button class="btn-sound-pill" onclick="toggleAudio()" id="btnAudio" title="Toggle Calming Ambient Rain">
            <i class="fa-solid fa-cloud-rain" id="audioIcon"></i>
            <span id="audioText">Calm Rain</span>
        </button>

        <div class="profile-dropdown-container">
            <button class="student-chip-btn" onclick="toggleProfileMenu(event)" id="profileTrigger">
                <div class="student-avatar"><?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?></div>
                <div class="student-name-text"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                <i class="fa-solid fa-chevron-down" style="font-size:0.7rem;"></i>
            </button>

            <div class="profile-dropdown-menu" id="profileDropdown">
                <div class="dropdown-header">
                    <div class="dropdown-name"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                    <div class="dropdown-meta">
                        ID: <strong><?php echo htmlspecialchars($currentUser['student_id'] ?: 'DIT 14253'); ?></strong> &bull; <?php echo htmlspecialchars($currentUser['intake']); ?>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="<?php echo SITE_URL; ?>/dashboard.php" class="dropdown-item">
                    <i class="fa-solid fa-chart-pie" style="color:var(--primary);"></i> Student Dashboard
                </a>
                <a href="javascript:void(0)" onclick="openBreathingModal(); closeProfileMenu();" class="dropdown-item">
                    <i class="fa-solid fa-wind" style="color:var(--accent);"></i> 4-7-8 Breathing Exercise
                </a>
                <a href="javascript:void(0)" onclick="openGroundingModal(); closeProfileMenu();" class="dropdown-item">
                    <i class="fa-solid fa-hands-holding" style="color:var(--accent);"></i> 5-4-3-2-1 Grounding
                </a>
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0)" onclick="toggleLanguage(); closeProfileMenu();" class="dropdown-item">
                    <i class="fa-solid fa-globe"></i> Switch to <span id="dropLangTxt" style="font-weight:700; margin-left:4px;">සිංහල</span>
                </a>
                <a href="javascript:void(0)" onclick="toggleThemeMode(); closeProfileMenu();" class="dropdown-item">
                    <i class="fa-solid fa-moon"></i> Toggle Dark/Light Mode
                </a>
                <div class="dropdown-divider"></div>
                <a href="<?php echo SITE_URL; ?>/logout.php" class="dropdown-item dropdown-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Sign Out
                </a>
            </div>
        </div>
    </div>
</header>

<!-- ==========================================================================
     2. HERO SECTION
     ========================================================================== -->
<section class="hero-section">
    <div class="wrapper">
        <div class="hero-layout">
            <div>
                <div class="hero-badge">
                    <i class="fa-solid fa-shield-halved"></i> University Student Sanctuary
                </div>
                <h1 class="hero-heading" id="heroHeading">A Safe & Confidential Space for Your Mental Wellness</h1>
                <p class="hero-lead" id="heroDescription">
                    Track your emotional resilience, conduct private screening assessments (PHQ-9 & GAD-7), and connect with certified counselors whenever you need support.
                </p>
                <div class="hero-buttons">
                    <a href="#assessment" class="btn-primary-action">
                        <i class="fa-solid fa-heart-pulse"></i> <span id="heroBtn">Take Self-Check Assessment</span>
                    </a>
                    <a href="#mood" class="btn-outline-action">
                        <i class="fa-solid fa-seedling"></i> <span id="heroMoodBtn">Daily Reflection</span>
                    </a>
                </div>
            </div>

            <div>
                <div class="hero-visual-card">
                    <div class="hero-visual-icon">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <h3 style="font-size:1.3rem; margin-bottom:8px;" id="cardGreeting">Welcome, <?php echo htmlspecialchars($currentUser['name']); ?></h3>
                    <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:20px;">
                        Registration ID: <strong><?php echo htmlspecialchars($currentUser['student_id']); ?></strong> &bull; <?php echo htmlspecialchars($currentUser['intake']); ?>
                    </p>
                    <div style="display:flex; justify-content:space-around; border-top:1px solid var(--border); padding-top:16px;">
                        <div>
                            <div style="font-size:1.4rem; font-weight:800; color:var(--primary);">100%</div>
                            <small style="font-size:0.75rem; color:var(--text-muted);">Confidential</small>
                        </div>
                        <div>
                            <div style="font-size:1.4rem; font-weight:800; color:var(--primary);">PHQ-9</div>
                            <small style="font-size:0.75rem; color:var(--text-muted);">Depression Scale</small>
                        </div>
                        <div>
                            <div style="font-size:1.4rem; font-weight:800; color:var(--primary);">GAD-7</div>
                            <small style="font-size:0.75rem; color:var(--text-muted);">Anxiety Scale</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     3. VECTOR-ICON DAILY MOOD REFLECTION
     ========================================================================== -->
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
                    <div class="mood-title" id="mTitle1">Thriving <span style="font-size:1.2rem;">😊</span></div>
                    <div class="mood-desc" id="mDesc1">Energized & Motivated</div>
                </div>

                <!-- Balanced -->
                <div class="mood-card-item active" onclick="selectVectorMood('balanced', 'fa-seedling', 'Balanced / සන්සුන්', this)">
                    <div class="mood-icon-wrapper icon-balanced">
                        <i class="fa-solid fa-seedling"></i>
                    </div>
                    <div class="mood-title" id="mTitle2">Balanced <span style="font-size:1.2rem;">😌</span></div>
                    <div class="mood-desc" id="mDesc2">Calm & In Control</div>
                </div>

                <!-- Fatigued -->
                <div class="mood-card-item" onclick="selectVectorMood('fatigued', 'fa-cloud-rain', 'Fatigued / වෙහෙසයි', this)">
                    <div class="mood-icon-wrapper icon-fatigued">
                        <i class="fa-solid fa-cloud-rain"></i>
                    </div>
                    <div class="mood-title" id="mTitle3">Fatigued <span style="font-size:1.2rem;">🥱</span></div>
                    <div class="mood-desc" id="mDesc3">Low Energy / Drained</div>
                </div>

                <!-- Distressed -->
                <div class="mood-card-item" onclick="selectVectorMood('distressed', 'fa-bolt', 'Distressed / පීඩිතයි', this)">
                    <div class="mood-icon-wrapper icon-distressed">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="mood-title" id="mTitle4">Distressed <span style="font-size:1.2rem;">😣</span></div>
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

<!-- ==========================================================================
     4. STANDARDIZED CLINICAL SELF-CHECK ASSESSMENT
     ========================================================================== -->
<section class="section-wrapper" id="assessment" style="background:var(--card-bg);">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="assessHeading">Standardized Mental Health Self-Check</h2>
            <p class="section-subtitle" id="assessSubtitle">Internationally validated PHQ-9 (Depression) and GAD-7 (Anxiety) screening modules.</p>
        </div>

        <div class="assessment-card">
            <!-- Tabs -->
            <div class="tab-bar">
                <button class="tab-btn active" id="tabPhq" onclick="selectAssessmentTab('phq9')">
                    PHQ-9 (Depression Scale)
                </button>
                <button class="tab-btn" id="tabGad" onclick="selectAssessmentTab('gad7')">
                    GAD-7 (Anxiety Scale)
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="progress-track">
                <div class="progress-fill" id="assessmentProgressBar"></div>
            </div>

            <!-- Questions Wizard View -->
            <div id="assessmentWizard">
                <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:8px;" id="qStepIndicator">
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

            <!-- Result Screen -->
            <div id="assessmentResultView" style="display:none; text-align:center; padding:10px 0;"></div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     5. IMMEDIATE RELIEF & COUNSELING SERVICES
     ========================================================================== -->
<section class="section-wrapper" id="counseling">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title">Support & Relief Tools</h2>
            <p class="section-subtitle">Take an active step towards feeling better right now, or schedule a conversation with our university counseling team.</p>
        </div>

        <div class="relief-grid">
            <!-- Tool 1: 4-7-8 Breathing -->
            <div class="relief-card">
                <div>
                    <div class="relief-icon"><i class="fa-solid fa-wind"></i></div>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">4-7-8 Breathing Technique</h3>
                    <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:18px;">
                        A clinically proven rhythmic breathing pattern designed to calm your nervous system and reduce acute academic anxiety.
                    </p>
                </div>
                <button class="btn-outline-action" onclick="openBreathingModal()" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-play"></i> Start Breathing Exercise
                </button>
            </div>

            <!-- Tool 2: 5-4-3-2-1 Grounding -->
            <div class="relief-card">
                <div>
                    <div class="relief-icon"><i class="fa-solid fa-hands-holding"></i></div>
                    <h3 style="font-size:1.15rem; margin-bottom:8px;">5-4-3-2-1 Sensory Grounding</h3>
                    <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:18px;">
                        When feeling overwhelmed or experiencing racing thoughts, use your 5 senses to re-anchor into the present moment.
                    </p>
                </div>
                <button class="btn-outline-action" onclick="openGroundingModal()" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-eye"></i> Start Grounding Guide
                </button>
            </div>

            <!-- Tool 3: Confidential Counseling -->
            <div class="relief-card" style="border-color:var(--primary); background:var(--primary-light);">
                <div>
                    <div class="relief-icon" style="background:var(--primary); color:#fff;"><i class="fa-solid fa-user-doctor"></i></div>
                    <h3 style="font-size:1.15rem; margin-bottom:8px; color:var(--primary);">Talk to a Campus Counselor</h3>
                    <p style="font-size:0.875rem; color:var(--text-main); margin-bottom:18px;">
                        Institutional counseling sessions are private, confidential, and judgment-free. Both online and in-person formats are available.
                    </p>
                </div>
                <button class="btn-primary-action" onclick="openCounselingModal()" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-calendar-check"></i> Book Counseling Session
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     6. PERSONAL SCORE HISTORY TRAJECTORY
     ========================================================================== -->
<section class="section-wrapper" id="history">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="chartHeading">Personal Score History & Trends</h2>
            <p class="section-subtitle" id="chartSubtitle">Track changes in depression and anxiety indicators across consecutive check-ins.</p>
        </div>

        <div style="max-width:850px; margin:0 auto; background:var(--card-bg); border:1px solid var(--border); border-radius:var(--radius); padding:28px; box-shadow:var(--shadow);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                <span style="font-size:0.85rem; font-weight:700; color:var(--primary); text-transform:uppercase;">Score Trajectory</span>
                <a href="<?php echo SITE_URL; ?>/dashboard.php" class="btn-outline-action" style="padding:6px 14px; font-size:0.825rem;">
                    <i class="fa-solid fa-chart-pie"></i> Detailed Dashboard
                </a>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="mainTrendsChart"></canvas>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================================================
     7. 24/7 HELPLINES & CLINIC DIRECTORY
     ========================================================================== -->
<section class="section-wrapper" id="directory">
    <div class="wrapper">
        <div class="section-header">
            <h2 class="section-title" id="dirHeading">National Crisis Support & Clinics</h2>
            <p class="section-subtitle" id="dirSubtitle">Free, 24/7 confidential helplines and specialized mental healthcare units in Sri Lanka.</p>
        </div>

        <div class="helpline-row" style="max-width:900px; margin:0 auto 35px auto;">
            <div class="helpline-box">
                <i class="fa-solid fa-phone-volume" style="color:var(--primary); font-size:1.6rem;"></i>
                <div class="helpline-number">1926</div>
                <strong id="hl1">National Mental Health Institute</strong>
                <p style="font-size:0.775rem; color:var(--text-muted); margin-top:4px;">Toll-free 24/7 Government Helpline</p>
            </div>

            <div class="helpline-box">
                <i class="fa-solid fa-headset" style="color:var(--primary); font-size:1.6rem;"></i>
                <div class="helpline-number">1333</div>
                <strong id="hl2">CCC Line Crisis Support</strong>
                <p style="font-size:0.775rem; color:var(--text-muted); margin-top:4px;">Confidential emotional relief</p>
            </div>

            <div class="helpline-box">
                <i class="fa-solid fa-hands-holding-child" style="color:var(--primary); font-size:1.6rem;"></i>
                <div class="helpline-number">011 2696666</div>
                <strong id="hl3">Sri Lanka Sumithrayo</strong>
                <p style="font-size:0.775rem; color:var(--text-muted); margin-top:4px;">Befriending and suicide prevention</p>
            </div>
        </div>

        <div style="max-width:900px; margin:0 auto; overflow-x:auto;">
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
    </div>
</section>

<!-- ==========================================================================
     8. MODALS (Breathing, Grounding, Counseling)
     ========================================================================== -->

<!-- A. 4-7-8 Breathing Guide Modal -->
<div class="modal-backdrop" id="modalBreath">
    <div class="modal-panel" style="text-align:center;">
        <button class="modal-close" onclick="closeBreathingModal()">&times;</button>
        <h3 style="font-size:1.3rem; color:var(--primary); margin-bottom:6px;">4-7-8 Breathing Exercise</h3>
        <p style="font-size:0.85rem; color:var(--text-muted);" id="breathSub">A clinically proven rhythm to reduce heart rate and trigger relaxation.</p>
        
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
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:18px;">When experiencing academic panic or racing thoughts, use your 5 senses to re-anchor in the present:</p>

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

<!-- ==========================================================================
     9. AI WELLNESS CHATBOT
     ========================================================================== -->
<button class="chat-trigger" onclick="toggleChatWindow()" aria-label="Open AI Assistant">
    <i class="fa-solid fa-comment-dots"></i>
</button>

<div class="chat-drawer" id="chatDrawer">
    <div class="chat-top">
        <div style="display:flex; align-items:center; gap:8px;">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2" alt="Sansun Logo" style="width:26px; height:26px; object-fit:contain; filter:drop-shadow(0 0 6px rgba(255,255,255,0.45));">
            <span id="chatHeaderTitle">Sansun Wellness AI</span>
        </div>
        <button onclick="toggleChatWindow()" style="background:none; border:none; color:#fff; cursor:pointer; font-size:1.1rem;"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="chat-stream" id="chatStream">
        <div class="chat-bubble bot" id="chatWelcomeMsg">Hello! I am your student wellness assistant. Feel free to share whatever is on your mind. How can I assist you today?</div>
    </div>

    <div style="display:flex; gap:6px; padding:8px 12px; overflow-x:auto; background:var(--card-bg); border-top:1px solid var(--border);">
        <button class="btn-outline-action" style="font-size:0.75rem; padding:4px 10px;" onclick="sendQuickPrompt('I feel overwhelmed with exam stress')">Exam Stress</button>
        <button class="btn-outline-action" style="font-size:0.75rem; padding:4px 10px;" onclick="sendQuickPrompt('How can I calm my mind right now?')">Calm My Mind</button>
        <button class="btn-outline-action" style="font-size:0.75rem; padding:4px 10px;" onclick="sendQuickPrompt('How to contact 1926 helpline?')">1926 Hotline</button>
    </div>

    <div style="display:flex; padding:10px; background:var(--card-bg); border-top:1px solid var(--border); gap:8px;">
        <input type="text" id="chatInputField" class="form-input" style="border-radius:20px;" placeholder="Type your message..." onkeypress="if(event.key==='Enter') sendChatMessage()">
        <button onclick="sendChatMessage()" class="btn-primary-action" style="padding:10px 14px; border-radius:50%;">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<!-- ==========================================================================
     10. FOOTER
     ========================================================================== -->
<footer style="text-align:center; padding:45px 20px 35px; font-size:0.85rem; color:var(--text-muted); border-top:1px solid var(--border); background:var(--card-bg);">
    <div style="display:inline-flex; align-items:center; gap:10px; margin-bottom:12px;">
        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2" alt="Sansun Logo" class="brand-logo-img" style="width:36px; height:36px;">
        <span style="font-weight:800; font-size:1.15rem; color:var(--primary); letter-spacing:-0.3px;">Sansun (සන්සුන්)</span>
    </div>
    <p>&copy; <?php echo date('Y'); ?> <strong>Sansun</strong> - Student Mental Wellness Check-in System. Developed by B.A.I.D Bopitiya (DIT 14253 - DIT 14 Intake).</p>
    <p style="font-size:0.78rem; margin-top:6px; max-width:650px; margin-left:auto; margin-right:auto; opacity:0.85;">Clinical Disclaimer: Screening tools (PHQ-9 & GAD-7) are for educational wellness tracking and self-reflection, and do not substitute for formal psychiatric evaluation or diagnosis.</p>
</footer>

<!-- Global Client Configuration & Scripts -->
<script>
    const SITE_ROOT = "<?php echo SITE_URL; ?>";
    const LOGGED_IN_STUDENT_NAME = "<?php echo htmlspecialchars($currentUser['name']); ?>";
    const LOGGED_IN_STUDENT_ID = "<?php echo htmlspecialchars($currentUser['student_id']); ?>";

    // Profile Dropdown Toggle Logic
    function toggleProfileMenu(e) {
        if (e) e.stopPropagation();
        const menu = document.getElementById('profileDropdown');
        if (menu) menu.classList.toggle('show');
    }

    function closeProfileMenu() {
        const menu = document.getElementById('profileDropdown');
        if (menu) menu.classList.remove('show');
    }

    document.addEventListener('click', (e) => {
        const container = document.querySelector('.profile-dropdown-container');
        if (container && !container.contains(e.target)) {
            closeProfileMenu();
        }
    });
</script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
