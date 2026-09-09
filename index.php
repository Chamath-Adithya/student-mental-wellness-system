<?php
require_once __DIR__ . '/config/db.php';
$currentUser = current_user();
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>සන්සුන් - Student Mental Wellness Check-in System</title>
    
    <!-- External UI Resources -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #4a7c59;
            --primary-hover: #3b6346;
            --primary-light: #f0f7f4;
            --accent: #5b82a6;
            --bg: #f7f9f8;
            --card-bg: #ffffff;
            --text-main: #2d3732;
            --text-muted: #6b7c75;
            --border: #e2e9e5;
            --shadow: 0 10px 30px rgba(74, 124, 89, 0.08);
            --radius: 20px;
            --danger: #dc2626;
            --whatsapp: #25d366;
        }

        [data-theme="dark"] {
            --bg: #131b17;
            --card-bg: #1c2621;
            --text-main: #e8eee9;
            --text-muted: #8fa097;
            --border: #2c3a33;
            --primary: #6b9e7a;
            --primary-hover: #558362;
            --primary-light: #182820;
            --accent: #7a9ebc;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', system-ui, sans-serif; transition: background-color 0.3s, color 0.3s; }
        html { scroll-behavior: smooth; }
        body { background-color: var(--bg); color: var(--text-main); line-height: 1.6; }

        /* Navigation Header */
        header, .navbar {
            position: sticky; top: 0; background: var(--card-bg);
            border-bottom: 1px solid var(--border); padding: 12px 24px;
            display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
            gap: 10px; z-index: 1000;
        }

        .logo { font-size: 1.25rem; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .nav-links { display: flex; gap: 20px; list-style: none; align-items: center; }
        .nav-links a { text-decoration: none; color: var(--text-main); font-weight: 500; font-size: 0.9rem; }
        .nav-links a:hover { color: var(--primary); }
        
        .nav-controls {
            display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
        }

        .btn-ctrl {
            background: var(--primary-light); border: 1px solid var(--border);
            color: var(--primary); padding: 7px 14px; border-radius: 30px;
            cursor: pointer; font-weight: 600; font-size: 0.825rem;
            display: flex; align-items: center; gap: 6px; text-decoration: none;
            white-space: nowrap;
        }
        .btn-ctrl:hover { background: var(--border); }

        .btn-login {
            background: var(--primary);
            color: #ffffff !important;
            border: none;
        }
        .btn-login:hover { background: var(--primary-hover); }

        .wrapper { max-width: 1080px; margin: 0 auto; padding: 0 20px; }

        .audio-bar {
            background: var(--card-bg); border-bottom: 1px solid var(--border);
            padding: 9px 20px; display: flex; justify-content: center; align-items: center; gap: 14px; font-size: 0.85rem; flex-wrap: wrap;
        }

        /* Hero Section */
        .hero {
            padding: 50px 20px; background: linear-gradient(180deg, var(--primary-light) 0%, var(--bg) 100%);
            border-bottom: 1px solid var(--border);
        }
        
        h1, .hero-title {
            font-size: 2.2rem;
            line-height: 1.3;
            margin: 10px 0;
            font-weight: 700;
        }

        p.hero-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
            margin: 0 0 25px 0;
            line-height: 1.7;
        }

        .hero-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; align-items: center; }

        .btn-action {
            background: var(--primary); color: white; padding: 12px 24px; border-radius: 30px;
            text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: 0.2s;
        }
        .btn-action:hover { background: var(--primary-hover); }

        .btn-whatsapp { background-color: var(--whatsapp); color: white; }
        .btn-whatsapp:hover { opacity: 0.9; }

        .main-img {
            width: 100%;
            max-width: 440px;
            height: auto;
            border-radius: 20px;
            box-shadow: var(--shadow);
            display: block;
            margin: 0 auto;
            object-fit: cover;
        }

        .section { padding: 45px 0; border-bottom: 1px solid var(--border); }
        .section-title { text-align: center; font-size: 1.6rem; color: var(--primary); margin-bottom: 24px; font-weight: 700; }

        /* Mood Journal */
        .mood-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .mood-btn {
            font-size: 28px !important;
            width: 56px;
            height: 56px;
            padding: 0;
            border-radius: 50%;
            border: 2px solid var(--border);
            background-color: var(--card-bg);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, background-color 0.2s, border-color 0.2s;
        }

        .mood-btn:hover, .mood-btn.selected {
            transform: scale(1.18);
            background: var(--primary-light);
            border-color: var(--primary);
        }

        .counseling-banner {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: #ffffff; padding: 30px; border-radius: var(--radius);
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 10px; box-shadow: var(--shadow);
        }

        .banner-content h3 { font-size: 1.3rem; margin-bottom: 6px; color: #ffffff; }
        .banner-content p { font-size: 0.95rem; opacity: 0.92; color: #ffffff; }
        .banner-actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .btn-banner-primary { background-color: #ffffff; color: var(--primary); padding: 10px 20px; border-radius: 30px; font-size: 0.9rem; font-weight: 700; border: none; cursor: pointer; }
        .btn-banner-emergency { background-color: var(--danger); color: #ffffff; padding: 10px 20px; border-radius: 30px; font-size: 0.9rem; font-weight: 700; text-decoration: none; }

        /* Assessment Wizard */
        .wizard-container {
            max-width: 680px; margin: 0 auto; background: var(--card-bg);
            padding: 30px; border-radius: var(--radius); box-shadow: var(--shadow); border: 1px solid var(--border);
        }

        .tab-container { display: flex; background: var(--primary-light); padding: 6px; border-radius: 12px; gap: 6px; margin-bottom: 24px; }
        .tab-btn { flex: 1; padding: 10px; border: none; background: transparent; color: var(--text-muted); font-size: 0.9rem; font-weight: 600; border-radius: 8px; cursor: pointer; }
        .tab-btn.active { background: var(--card-bg); color: var(--primary); box-shadow: 0 2px 6px rgba(0,0,0,0.06); }

        .progress-bar-bg { width: 100%; height: 8px; background: var(--primary-light); border-radius: 10px; overflow: hidden; margin-bottom: 20px; }
        .progress-bar-fill { height: 100%; width: 0%; background: var(--primary); transition: width 0.3s; }

        .options-group { display: grid; gap: 10px; margin: 20px 0; }
        .option-btn { background: var(--bg); border: 1.5px solid var(--border); padding: 12px 16px; border-radius: 12px; cursor: pointer; font-size: 0.9rem; font-weight: 500; display: block; }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .option-btn { background: var(--primary-light); color: var(--primary); border-color: var(--primary); font-weight: 700; }

        .nav-actions { display: flex; gap: 12px; margin-top: 20px; }
        .btn-nav { flex: 1; padding: 12px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; border: none; cursor: pointer; }
        .btn-prev { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }
        .btn-next { background: var(--primary); color: white; }

        .checklist-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 12px; }
        .check-item { background: var(--card-bg); padding: 14px 18px; border-radius: 14px; border: 1px solid var(--border); font-size: 0.9rem; display: flex; align-items: center; gap: 12px; }

        .helpline-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 24px; }
        .helpline-card { background: var(--card-bg); padding: 20px; border-radius: 16px; border: 1px solid var(--border); text-align: center; }
        .helpline-card i { font-size: 1.4rem; color: var(--primary); margin-bottom: 8px; }
        .helpline-card strong { color: var(--primary); font-size: 1.4rem; display: block; margin-bottom: 4px; }
        .helpline-card span { font-size: 0.85rem; color: var(--text-muted); }

        .directory-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: var(--card-bg); border-radius: 12px; overflow: hidden; border: 1px solid var(--border); }
        .directory-table th, .directory-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
        .directory-table th { background: var(--primary-light); color: var(--primary); font-weight: 700; }

        /* Modals */
        .modal { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); z-index: 2000; justify-content: center; align-items: center; }
        .modal-content { background: var(--card-bg); padding: 30px; border-radius: var(--radius); text-align: left; max-width: 480px; width: 92%; position: relative; box-shadow: var(--shadow); max-height: 90vh; overflow-y: auto; }
        .close-btn { position: absolute; top: 14px; right: 18px; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; border: none; background: none; }

        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border);
            background: var(--bg); color: var(--text-main); font-size: 0.9rem; outline: none;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--primary);
        }

        .breath-circle {
            width: 140px; height: 140px; background: var(--primary-light); border: 4px solid var(--primary);
            border-radius: 50%; margin: 25px auto; display: flex; justify-content: center; align-items: center;
            font-weight: bold; color: var(--primary); font-size: 1.1rem; transition: transform 4s ease-in-out;
        }
        .breath-circle.expand { transform: scale(1.3); transition: transform 4s ease-in-out; }
        .breath-circle.hold { transform: scale(1.3); }
        .breath-circle.shrink { transform: scale(0.85); transition: transform 8s ease-in-out; }

        .grounding-list { list-style: none; margin: 15px 0; }
        .grounding-list li { background: var(--bg); padding: 12px; border-radius: 10px; margin-bottom: 10px; border-left: 4px solid var(--primary); font-size: 0.9rem; }

        /* Chatbot Widget */
        .chatbot-toggle {
            position: fixed; bottom: 25px; right: 25px;
            width: 60px; height: 60px; border-radius: 50%;
            background: var(--primary); color: white; border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25); cursor: pointer;
            z-index: 1500; font-size: 1.4rem; display: flex;
            align-items: center; justify-content: center; transition: transform 0.2s;
        }
        .chatbot-toggle:hover { transform: scale(1.08); }

        .chatbot-window {
            position: fixed; bottom: 95px; right: 20px;
            width: 370px; height: 520px; max-width: calc(100vw - 40px);
            background: var(--card-bg); border: 1px solid var(--border);
            border-radius: var(--radius); box-shadow: var(--shadow);
            z-index: 1500; display: none; flex-direction: column;
            overflow: hidden;
        }

        .chat-header { background: var(--primary); color: white; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.95rem; }
        .chat-header div { display: flex; align-items: center; gap: 8px; }
        .chat-header button { background: none; border: none; color: white; font-size: 1.1rem; cursor: pointer; }

        .chat-body {
            flex: 1; padding: 14px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 10px;
            background: var(--bg); font-size: 0.88rem; line-height: 1.5; word-break: break-word;
        }

        .chat-msg { max-width: 85%; padding: 10px 14px; border-radius: 16px; white-space: pre-wrap; }
        .chat-msg.bot { background: var(--card-bg); color: var(--text-main); align-self: flex-start; border: 1px solid var(--border); border-bottom-left-radius: 2px; }
        .chat-msg.user { background: var(--primary); color: white; align-self: flex-end; border-bottom-right-radius: 2px; }

        .quick-chips { display: flex; gap: 6px; padding: 8px 12px; overflow-x: auto; background: var(--card-bg); border-top: 1px solid var(--border); }
        .quick-chips button { background: var(--bg); border: 1px solid var(--border); color: var(--text-main); border-radius: 15px; padding: 5px 10px; font-size: 0.78rem; white-space: nowrap; cursor: pointer; }

        .chat-input-area { display: flex; padding: 10px; background: var(--card-bg); border-top: 1px solid var(--border); gap: 6px; align-items: center; }
        .chat-input-area input { flex: 1; padding: 9px 14px; border-radius: 20px; border: 1px solid var(--border); background: var(--bg); color: var(--text-main); font-size: 0.88rem; outline: none; }
        .chat-input-area button { background: var(--primary); color: white; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }

        @media (max-width: 768px) {
            .hero-grid { grid-template-columns: 1fr; text-align: center; }
            h1, .hero-title { font-size: 1.6rem; text-align: center; }
            p.hero-subtitle { text-align: center; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<?php display_flash(); ?>

<!-- NAVIGATION BAR -->
<nav class="navbar">
    <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
        <i class="fa-solid fa-leaf"></i> <span id="brandName">Sansun</span>
    </a>
    <ul class="nav-links">
        <li><a href="#assessment" id="navAssessment">ඇගයීම</a></li>
        <li><a href="#mood" id="navMood">Mood Journal</a></li>
        <li><a href="#counseling" id="navCounseling">උපදේශනය</a></li>
        <li><a href="#history" id="navHistory">ප්‍රගතිය</a></li>
        <li><a href="#directory" id="navDirectory">සායන</a></li>
        <?php if ($currentUser): ?>
            <?php if ($currentUser['role'] === 'admin' || $currentUser['role'] === 'counselor'): ?>
                <li><a href="<?php echo SITE_URL; ?>/admin-dashboard.php" style="color:var(--primary); font-weight:700;">Admin Dashboard</a></li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/dashboard.php" style="color:var(--primary); font-weight:700;">My Dashboard</a></li>
            <?php endif; ?>
        <?php endif; ?>
    </ul>

    <div class="nav-controls">
        <?php if ($currentUser): ?>
            <a href="<?php echo ($currentUser['role'] === 'admin') ? SITE_URL . '/admin-dashboard.php' : SITE_URL . '/dashboard.php'; ?>" class="btn-ctrl btn-login">
                <i class="fa-solid fa-user-check"></i> <span><?php echo htmlspecialchars($currentUser['name']); ?></span>
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn-ctrl" title="Sign Out">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        <?php else: ?>
            <button class="btn-ctrl btn-login" onclick="openLoginModal()" id="userAuthBtn">
                <i class="fa-solid fa-right-to-bracket"></i> <span id="navLogin">ඇතුළු වන්න</span>
            </button>
        <?php endif; ?>
        
        <button class="btn-ctrl" onclick="openGroundingModal()"><i class="fa-solid fa-hands-holding"></i> Grounding</button>
        <button class="btn-ctrl" onclick="openBreathingModal()"><i class="fa-solid fa-wind"></i> <span id="navBreath">හුස්ම</span></button>
        <button class="btn-ctrl" onclick="toggleLanguage()"><i class="fa-solid fa-globe"></i> <span id="langTxt">English</span></button>
        <button class="btn-ctrl" onclick="toggleTheme()" aria-label="Toggle Theme"><i class="fa-solid fa-moon"></i></button>
    </div>
</nav>

<!-- AMBIENT NATURE AUDIO BAR -->
<div class="audio-bar">
    <span id="audioLabel"><i class="fa-solid fa-music"></i> සොබාදහමේ ශබ්ද (Ambient Sound):</span>
    <button class="btn-ctrl" onclick="toggleAudio()"><i class="fa-solid fa-play" id="audioIcon"></i> Play/Pause</button>
    <a href="https://wa.me/94771234567?text=Hello%20Sansun%20Support" target="_blank" rel="noopener noreferrer" class="btn-ctrl btn-whatsapp">
        <i class="fa-brands fa-whatsapp"></i> <span id="btnWa">WhatsApp Support</span>
    </a>
    <audio id="ambientAudio" loop src="https://cdn.pixabay.com/download/audio/2022/05/27/audio_1808fbf07a.mp3?filename=rain-and-puddle-113337.mp3"></audio>
</div>

<!-- HERO SECTION -->
<section class="hero">
    <div class="wrapper">
        <div class="hero-grid">
            <div class="hero-text">
                <h1 id="heroTitle">ඔබේ මානසික සුවතාවය වෙනුවෙන් සුරක්ෂිත ඉඩක්</h1>
                <p class="hero-subtitle" id="heroDesc">
                    විභාග සහ අධ්‍යාපනික පීඩනය හඳුනාගෙන, මනස සන්සුන් කරගැනීමට අවශ්‍ය වෘත්තීය මගපෙන්වීම් සහ උපදේශන පහසුකම් මෙහි ඇතුළත් වේ.
                </p>
                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="#assessment" class="btn-action">
                        <i class="fa-solid fa-heart-pulse"></i> <span id="heroBtn">පරීක්ෂාව ආරම්භ කරන්න</span>
                    </a>
                    <button class="btn-action btn-whatsapp" onclick="window.open('https://wa.me/94771234567?text=Hello%20Sansun%20Support','_blank')">
                        <i class="fa-brands fa-whatsapp"></i> WhatsApp Chat
                    </button>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=600&q=80" alt="Relaxation" class="main-img">
            </div>
        </div>
    </div>
</section>

<!-- DAILY MOOD JOURNAL SECTION -->
<section class="section" id="mood">
    <div class="wrapper">
        <h2 class="section-title" id="moodTitle">දෛනික මනෝභාවය සටහන් කරන්න (Daily Mood Journal)</h2>
        <div class="wizard-container" style="text-align: center;">
            <p style="margin-bottom:15px; color:var(--text-muted);" id="moodDesc">අද දිනයේ ඔබට දැනෙන හැඟීම තෝරන්න:</p>
            <div class="mood-grid">
                <button class="mood-btn" onclick="selectMood('😊', this)" aria-label="Happy">😊</button>
                <button class="mood-btn" onclick="selectMood('😐', this)" aria-label="Neutral">😐</button>
                <button class="mood-btn" onclick="selectMood('😔', this)" aria-label="Sad">😔</button>
                <button class="mood-btn" onclick="selectMood('😡', this)" aria-label="Angry">😡</button>
            </div>
            <div class="form-group">
                <textarea id="moodNote" rows="2" placeholder="අද දිනය ගැන කුඩා සටහනක් තබන්න (Optional)..."></textarea>
            </div>
            <button class="btn-action" onclick="saveMoodEntry()" id="btnSaveMood" style="margin: 0 auto;">මනෝභාවය Save කරන්න</button>
            <div id="moodLogList" style="margin-top:20px; text-align:left; font-size:0.85rem; color:var(--text-muted);"></div>
        </div>
    </div>
</section>

<!-- COUNSELING BANNER -->
<section class="section" id="counseling">
    <div class="wrapper">
        <div class="counseling-banner">
            <div class="banner-content">
                <h3 id="bannerTitle">ඔබට කවුරුන් හෝ සමඟ කතා කිරීමට අවශ්‍යද?</h3>
                <p id="bannerDesc">විශ්වවිද්‍යාල උපදේශකවරයෙකු හා සම්බන්ධ වීමට හෝ ක්ෂණික සහාය ලබා ගැනීමට ඉදිරියට යන්න.</p>
            </div>
            <div class="banner-actions">
                <button class="btn-banner-primary" onclick="openCounselingModal()" id="btnBookCounselor">උපදේශන වාරයක් වෙන්කරගන්න</button>
                <a href="tel:1926" class="btn-banner-emergency" id="btnCall1926"><i class="fa-solid fa-phone"></i> 1926 අමතන්න</a>
            </div>
        </div>
    </div>
</section>

<!-- ASSESSMENT SECTION (PHQ-9 / GAD-7) -->
<section class="section" id="assessment">
    <div class="wrapper">
        <div class="wizard-container">
            <div class="tab-container">
                <button class="tab-btn active" id="tabPhq" onclick="switchTest('phq9')">PHQ-9 (විෂාදය / Depression)</button>
                <button class="tab-btn" id="tabGad" onclick="switchTest('gad7')">GAD-7 (කාංසාව / Anxiety)</button>
            </div>

            <div class="progress-bar-bg"><div class="progress-bar-fill" id="progressFill"></div></div>

            <form id="wizardForm" onsubmit="event.preventDefault();">
                <div id="questionsContainer"></div>
                <div class="nav-actions">
                    <button type="button" class="btn-nav btn-prev" id="prevBtn" onclick="navigateStep(-1)">ආපසු</button>
                    <button type="button" class="btn-nav btn-next" id="nextBtn" onclick="navigateStep(1)">ඉදිරියට</button>
                </div>
            </form>

            <div id="result" style="display:none; text-align: center; padding: 20px 0;"></div>
        </div>
    </div>
</section>

<!-- PROGRESS CHART SECTION -->
<section class="section" id="history">
    <div class="wrapper">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
            <h2 class="section-title" id="chartTitle" style="margin-bottom:0;">ඔබේ ප්‍රගතිය (Past Scores)</h2>
            <button class="btn-ctrl" onclick="togglePinProtection()" id="btnPinToggle"><i class="fa-solid fa-lock"></i> PIN Lock</button>
        </div>
        <div id="protectedHistoryContent" style="max-width:700px; margin:0 auto; background:var(--card-bg); padding:24px; border-radius:var(--radius); border:1px solid var(--border);">
            <canvas id="historyChart"></canvas>
        </div>
    </div>
</section>

<!-- RESOURCE DIRECTORY SECTION -->
<section class="section" id="directory">
    <div class="wrapper">
        <h2 class="section-title" id="dirTitle">දිස්ත්‍රික්ක අනුව මානසික සෞඛ්‍ය සායන (Resource Directory)</h2>
        <div style="overflow-x:auto;">
            <table class="directory-table">
                <thead>
                    <tr>
                        <th id="thDistrict">දිස්ත්‍රික්කය</th>
                        <th id="thHospital">රෝහල / මධ්‍යස්ථානය</th>
                        <th id="thContact">දුරකථන අංකය</th>
                    </tr>
                </thead>
                <tbody id="directoryBody"></tbody>
            </table>
        </div>
    </div>
</section>

<!-- DAILY CHECKLIST SECTION -->
<section class="section">
    <div class="wrapper">
        <h2 class="section-title" id="checkTitle">දෛනික මනෝවිද්‍යාත්මක පුරුදු (Daily Self-Care)</h2>
        <div class="checklist-grid">
            <div class="check-item"><input type="checkbox"> <span id="chk1">විනාඩි 10ක් හුස්ම ගැනීමේ ව්‍යායාම කිරීම</span></div>
            <div class="check-item"><input type="checkbox"> <span id="chk2">වතුර ලීටර 2ක් ලබාගැනීම</span></div>
            <div class="check-item"><input type="checkbox"> <span id="chk3">විනාඩි 15ක් එළිමහනේ ඇවිදීම</span></div>
            <div class="check-item"><input type="checkbox"> <span id="chk4">පැය 7-8ක සුවබර නින්දක් ලැබීම</span></div>
        </div>
    </div>
</section>

<!-- HELPLINES SECTION -->
<section class="section" id="helplines" style="background: var(--primary-light);">
    <div class="wrapper">
        <h2 class="section-title" id="helpTitle">ඔබට හදිසි සහායක් අවශ්‍යද?</h2>
        <p style="text-align:center; color: var(--text-muted); max-width: 650px; margin: 0 auto 20px auto;" id="helpDesc">
            ඔබ දැඩි මානසික පීඩනයකින් පසුවන්නේ නම්, නොමිලේ සහ උපරිම රහස්‍යභාවයෙන් යුතුව සහාය ලබාගැනීමට පහත සේවාවන් අමතන්න.
        </p>
        
        <div class="helpline-grid">
            <div class="helpline-card">
                <i class="fa-solid fa-phone-flip"></i>
                <strong>1926</strong>
                <span id="help1">ජාතික මානසික සෞඛ්‍ය විද්‍යායතනය</span>
            </div>
            <div class="helpline-card">
                <i class="fa-solid fa-headset"></i>
                <strong>1333</strong>
                <span id="help2">CCC Line (24/7 නොමිලේ)</span>
            </div>
            <div class="helpline-card">
                <i class="fa-solid fa-hands-holding-child"></i>
                <strong>011 2696666</strong>
                <span id="help3">ශ්‍රී ලංකා සුමිත්‍රයෝ</span>
            </div>
        </div>
    </div>
</section>

<!-- CHATBOT WIDGET -->
<button class="chatbot-toggle" onclick="toggleChatbot()" aria-label="Toggle Chatbot">
    <i class="fa-solid fa-comments"></i>
</button>

<div class="chatbot-window" id="chatbotWindow">
    <div class="chat-header">
        <div>
            <i class="fa-solid fa-robot"></i>
            <span id="chatTitle">සන්සුන් AI සහායක</span>
        </div>
        <button onclick="toggleChatbot()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="chat-body" id="chatBody">
        <div class="chat-msg bot" id="botIntroMsg">ආයුබෝවන්! 👋 මම 'සන්සුන්' AI සහායක. ඔබට අද දැනෙන දේ හෝ සිතට වදදෙන ඕනෑම දෙයක් මා සමඟ බෙදාගන්න පුළුවන්. මා ඔබට උදවු කරන්නේ කෙසේද?</div>
    </div>
    <div class="quick-chips">
        <button onclick="sendQuickChip('මට පීඩනයක් දැනෙනවා')">මට පීඩනයක් දැනෙනවා</button>
        <button onclick="sendQuickChip('මනස සන්සුන් කරගන්නේ කෙසේද?')">මනස සන්සුන් කරගන්නේ කෙසේද?</button>
        <button onclick="sendQuickChip('1926 අමතන්නේ කෙසේද?')">1926 අමතන්නේ කෙසේද?</button>
    </div>
    <div class="chat-input-area">
        <input type="text" id="chatInput" placeholder="ඔබේ පණිවිඩය ටයිප් කරන්න..." onkeypress="handleChatKeyPress(event)">
        <button onclick="sendChatMessage()"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
</div>

<!-- LOGIN MODAL -->
<div class="modal" id="loginModal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeLoginModal()">&times;</button>
        <h3 id="modalLoginHeader" style="color:var(--primary); margin-bottom:15px; text-align: center;">ගිණුමට පිවිසෙන්න (Login)</h3>
        <form id="loginForm" onsubmit="handleAjaxLogin(event)">
            <div class="form-group">
                <label id="lblEmail" for="loginEmail">විද්‍යුත් තැපෑල හෝ ශිෂ්‍ය අංකය</label>
                <input type="text" id="loginEmail" required placeholder="student@dit.ac.lk හෝ DIT 14253" value="student@dit.ac.lk">
            </div>
            <div class="form-group">
                <label id="lblPassword" for="loginPassword">මුරපදය (Password)</label>
                <input type="password" id="loginPassword" required placeholder="••••••••" value="student123">
            </div>
            <div id="loginErrorMsg" style="display:none; color:var(--danger); font-size:0.8rem; margin-bottom:10px;"></div>
            <button type="submit" class="btn-action" style="width:100%; justify-content:center; margin-top:5px;" id="btnLoginSubmit">ඇතුළු වන්න</button>
        </form>

        <p style="text-align:center; margin-top:18px; font-size:0.85rem; color:var(--text-muted);" id="loginFooterNote">
            නව ගිණුමක් නොමැතිද? <a href="#" onclick="openRegisterModal()" style="color:var(--primary); font-weight:bold;">ලියාපදිංචි වන්න</a>
        </p>
    </div>
</div>

<!-- REGISTER MODAL -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeRegisterModal()">&times;</button>
        <h3 style="color:var(--primary); margin-bottom:10px; text-align:center;"><i class="fa-solid fa-user-plus"></i> ශිෂ්‍ය ලියාපදිංචිය</h3>
        <p style="text-align:center; color:var(--text-muted); font-size:0.85rem; margin-bottom:18px;">නව ගිණුමක් සාදා පද්ධතියට එකතු වන්න</p>

        <form id="registerForm" onsubmit="handleAjaxRegister(event)">
            <div class="form-group">
                <label>සම්පූර්ණ නම (Full Name) *</label>
                <input type="text" id="regFullname" required placeholder="e.g. B.A.I.D Bopitiya">
            </div>

            <div class="form-group">
                <label>ශිෂ්‍ය අංකය (Student ID) *</label>
                <input type="text" id="regStudentId" required placeholder="e.g. DIT 14253">
            </div>

            <div class="form-group">
                <label>විද්‍යුත් තැපෑල (Email) *</label>
                <input type="email" id="regEmail" required placeholder="name@dit.ac.lk">
            </div>

            <div class="form-group">
                <label>මුරපදය (Password) *</label>
                <input type="password" id="regPassword" required minlength="6" placeholder="••••••••">
            </div>

            <div id="regErrorMsg" style="display:none; color:var(--danger); font-size:0.8rem; margin-bottom:10px;"></div>
            <button type="submit" class="btn-action" style="width: 100%; justify-content: center; margin-top: 6px;">ලියාපදිංචි වන්න</button>
        </form>

        <div style="text-align: center; margin-top: 15px; font-size: 0.85rem; color: var(--text-muted);">
            දැනටමත් ගිණුමක් තිබේද? <a href="#" onclick="switchToLogin()" style="color: var(--primary); font-weight: 600; text-decoration: none;">ලොගින් වන්න</a>
        </div>
    </div>
</div>

<!-- COUNSELING MODAL -->
<div class="modal" id="counselingModal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeCounselingModal()">&times;</button>
        <h3 id="modalCounselHeader" style="color:var(--primary); margin-bottom:15px;">උපදේශන සේවාව හා සම්බන්ධ වන්න</h3>
        <form id="counselingForm" onsubmit="handleCounselingSubmit(event)">
            <div class="form-group">
                <label id="lblPrivacy" for="requestType">රහස්‍යතාවය (Privacy Option)</label>
                <select id="requestType" required onchange="toggleNameFields()">
                    <option value="named" id="optNamed">සාමාන්‍ය (නම සහ ශිෂ්‍ය අංකය ඇතුළත් කරන්න)</option>
                    <option value="anonymous" id="optAnon">අඥාත අයුරින් (Anonymous Request)</option>
                </select>
            </div>

            <div class="form-group" id="studentDetailsGroup">
                <label id="lblName" for="studentName">සම්පූර්ණ නම / ශිෂ්‍ය අංකය</label>
                <input type="text" id="studentName" placeholder="e.g., DIT 14253" value="<?php echo htmlspecialchars($currentUser['student_id'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label id="lblMode" for="preferredMode">උපදේශන ක්‍රමය</label>
                <select id="preferredMode" required>
                    <option value="online" id="optOnline">මාර්ගගත (Online Chat / Video Call)</option>
                    <option value="in-person" id="optInPerson">සෘජුව (In-Person Office Session)</option>
                </select>
            </div>

            <div class="form-group">
                <label id="lblDate" for="preferredDate">කැමති දිනය සහ වේලාව</label>
                <input type="datetime-local" id="preferredDate" required value="<?php echo date('Y-m-d\TH:i', strtotime('+1 day 10:00')); ?>">
            </div>

            <div class="form-group">
                <label id="lblNotes" for="notes">කෙටි සටහනක් (Optional)</label>
                <textarea id="notes" rows="3" placeholder="ඔබට පවසන්නට ඇති දේ මෙහි සටහන් කරන්න..."></textarea>
            </div>

            <button type="submit" class="btn-action" style="width:100%; justify-content:center;" id="btnSubmitCounsel">ඉල්ලීම යොමු කරන්න</button>
        </form>
    </div>
</div>

<!-- BREATHING EXERCISE MODAL (4-7-8) -->
<div class="modal" id="breathModal">
    <div class="modal-content" style="text-align: center;">
        <h3>4-7-8 Breathing Technique</h3>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:5px;" id="breathModalSub">මනස සන්සුන් කර ගැනීමට පහත උපදෙස් අනුගමනය කරන්න.</p>
        <div class="breath-circle" id="breathCircle">ලෑස්ති වන්න...</div>
        <p id="breathInstruction" style="font-weight:600; color:var(--primary); font-size:0.9rem; min-height: 24px;"></p>
        <button class="btn-action" style="margin-top:15px;" onclick="closeBreathingModal()" id="closeBreathBtn">වසා දමන්න</button>
    </div>
</div>

<!-- GROUNDING MODAL (5-4-3-2-1) -->
<div class="modal" id="groundingModal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeGroundingModal()">&times;</button>
        <h3 style="color:var(--primary); margin-bottom:10px;" id="groundTitle">5-4-3-2-1 Grounding Technique</h3>
        <p style="font-size:0.85rem; color:var(--text-muted);" id="groundSub">Panic Attack එකක් හෝ අධික බියක් දැනෙන විට මනස වර්තමානයට ගෙන ඒමට මෙය භාවිතා කරන්න:</p>
        <ul class="grounding-list" id="groundList"></ul>
        <button class="btn-action" style="width:100%; justify-content:center;" onclick="closeGroundingModal()" id="groundCloseBtn">තේරුණා / Close</button>
    </div>
</div>

<footer style="text-align:center; padding:30px 15px; font-size:0.85rem; color:var(--text-muted);" id="footerText">
    <p>© 2026 Student Mental Wellness Check-in System (Sansun). DIT 14253 B.A.I.D Bopitiya. Educational purposes only.</p>
</footer>

<!-- External and Main JavaScript Logic -->
<script>
    const SITE_ROOT = "<?php echo SITE_URL; ?>";
    const IS_LOGGED_IN = <?php echo is_logged_in() ? 'true' : 'false'; ?>;
    const CURRENT_USER_ROLE = "<?php echo $currentUser['role'] ?? 'guest'; ?>";
</script>
<script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
