<?php
require_once __DIR__ . '/config/db.php';

if (is_logged_in()) {
    redirect(SITE_URL . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitize($_POST['fullname'] ?? '');
    $studentId = sanitize($_POST['student_id'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $intake = sanitize($_POST['intake'] ?? 'General Intake');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($fullName) || empty($studentId) || empty($email) || empty($password)) {
        $error = 'කරුණාකර සියලුම තොරතුරු සම්පූර්ණ කරන්න.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'වලංගු විද්‍යුත් තැපැල් (Email) ලිපිනයක් ඇතුළත් කරන්න.';
    } elseif (strlen($password) < 6) {
        $error = 'මුරපදයට අවම වශයෙන් අකුරු 6ක් වත් අඩංගු විය යුතුය.';
    } elseif ($password !== $confirmPassword) {
        $error = 'ඇතුළත් කළ මුරපද දෙක එකිනෙක නොගැලපේ.';
    } else {
        $db = get_db();
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $error = 'මෙම විද්‍යුත් තැපෑලෙන් දැනටමත් ගිණුමක් ලියාපදිංචි කර ඇත.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $role = (strpos(strtolower($email), 'admin') !== false) ? 'admin' : 'student';

            $insert = $db->prepare("INSERT INTO users (full_name, student_id, email, password_hash, role, intake) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->execute([$fullName, $studentId, $email, $hash, $role, $intake]);
            $newId = $db->lastInsertId();

            $_SESSION['user_id'] = $newId;
            $_SESSION['user_name'] = $fullName;
            $_SESSION['student_id'] = $studentId;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $role;
            $_SESSION['user_intake'] = $intake;

            set_flash('success', 'ලියාපදිංචි වීම සාර්ථකයි! ආයුබෝවන් ' . $fullName);
            redirect(SITE_URL . '/dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>නව ශිෂ්‍ය ලියාපදිංචිය | Sansun (සන්සුන්) - Register</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2">
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e4d2b;
            --primary-hover: #2d6a4f;
            --primary-light: #e8f5e9;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 20px 40px -15px rgba(30, 77, 43, 0.12);
        }
        [data-theme="dark"] {
            --primary: #52b788;
            --primary-hover: #74c69d;
            --primary-light: #162a20;
            --bg: #0b1410;
            --card-bg: #13221b;
            --text-dark: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #1e382b;
            --shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.6);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; }
        body { background-color: var(--bg); color: var(--text-dark); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; transition: background 0.3s ease, color 0.3s ease; }
        .reg-box {
            width: 100%; max-width: 500px; background: var(--card-bg); border-radius: 24px;
            box-shadow: var(--shadow); border: 1px solid var(--border); padding: 36px 32px;
            position: relative; transition: background 0.3s ease, border-color 0.3s ease;
        }
        .theme-toggle-btn {
            position: absolute; top: 20px; right: 20px; background: var(--primary-light);
            border: 1px solid var(--border); color: var(--primary); width: 38px; height: 38px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: 0.2s ease;
        }
        .theme-toggle-btn:hover { transform: scale(1.08); }
        .brand-logo-img {
            width: 68px; height: 68px; object-fit: contain; margin-bottom: 8px;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.25s ease;
            filter: drop-shadow(0 4px 10px rgba(30, 77, 43, 0.2));
        }
        .brand-logo-img:hover { transform: scale(1.08) rotate(2deg); }
        [data-theme="dark"] .brand-logo-img {
            filter: drop-shadow(0 0 14px rgba(82, 183, 136, 0.7)) drop-shadow(0 0 28px rgba(45, 106, 79, 0.4));
        }
        .logo {
            font-size: 1.45rem; font-weight: 800; color: var(--primary); display: flex; flex-direction: column;
            align-items: center; justify-content: center; margin-bottom: 16px; text-decoration: none;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .form-group { margin-bottom: 14px; text-align: left; }
        .form-group label { display: block; font-size: 0.825rem; font-weight: 600; margin-bottom: 6px; color: var(--text-dark); }
        .form-control {
            width: 100%; padding: 11px 14px; border-radius: 12px; border: 1px solid var(--border);
            font-size: 0.9rem; background: var(--bg); color: var(--text-dark); outline: none; transition: 0.2s;
        }
        .form-control:focus { border-color: var(--primary); background: var(--card-bg); box-shadow: 0 0 0 3px rgba(82, 183, 136, 0.2); }
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .password-toggle-btn {
            position: absolute; right: 12px; background: none; border: none;
            color: var(--text-muted); cursor: pointer; padding: 4px 8px; font-size: 0.95rem;
            transition: color 0.2s ease;
        }
        .password-toggle-btn:hover { color: var(--primary); }
        .btn-submit {
            width: 100%; background: var(--primary); color: white; border: none; padding: 13px;
            border-radius: 12px; font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 10px;
            box-shadow: 0 4px 12px rgba(30, 77, 43, 0.25);
        }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-1px); }
        @media (max-width: 500px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="reg-box">
    <button onclick="toggleTheme()" class="theme-toggle-btn" title="Toggle Theme" aria-label="Toggle Theme">
        <i class="fa-solid fa-moon" id="themeIcon"></i>
    </button>

    <a href="<?php echo SITE_URL; ?>/index.php" class="logo" title="Sansun Home">
        <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2" alt="Sansun Logo" class="brand-logo-img">
        <span>Sansun <span style="font-size:1.05rem; font-weight:700; opacity:0.9;">(සන්සුන්)</span></span>
    </a>
    <h2 style="text-align: center; font-size: 1.4rem; margin-bottom: 6px; color: var(--text-dark);">නව ශිෂ්‍ය ලියාපදිංචිය</h2>
    <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 24px;">Student Mental Wellness Check-in System</p>

    <?php if ($error): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 0.85rem;">
            <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" autocomplete="on">
        <div class="form-group">
            <label for="fullname">සම්පූර්ණ නම (Full Name) *</label>
            <input type="text" id="fullname" name="fullname" class="form-control" required placeholder="e.g. Kasun Perera" value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="student_id">ශිෂ්‍ය අංකය (Student ID) *</label>
                <input type="text" id="student_id" name="student_id" class="form-control" required placeholder="e.g. DIT 14253 / STU-2024-001" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="intake">කණ්ඩායම (Course / Intake)</label>
                <input type="text" id="intake" name="intake" class="form-control" placeholder="e.g. Computing / Intake 2024" value="<?php echo htmlspecialchars($_POST['intake'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="email">විද්‍යුත් තැපෑල (Email Address) *</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="student@dit.ac.lk" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">මුරපදය (Password) *</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="••••••••" style="padding-right: 40px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password', 'pwdToggleIcon1')" aria-label="Toggle password visibility" title="Show/Hide Password">
                        <i class="fa-solid fa-eye" id="pwdToggleIcon1"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="confirm_password">මුරපදය තහවුරු කරන්න *</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="••••••••" style="padding-right: 40px;">
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('confirm_password', 'pwdToggleIcon2')" aria-label="Toggle password visibility" title="Show/Hide Password">
                        <i class="fa-solid fa-eye" id="pwdToggleIcon2"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-user-plus"></i> ලියාපදිංචි වන්න (Register)
        </button>
    </form>

    <div style="text-align: center; margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
        දැනටමත් ගිණුමක් තිබේද? <a href="<?php echo SITE_URL; ?>/login.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">ලොගින් වන්න</a>
    </div>

    <div style="text-align: center; margin-top: 15px;">
        <a href="<?php echo SITE_URL; ?>/index.php" style="color: var(--text-muted); font-size: 0.8rem; text-decoration: none;">
            <i class="fa-solid fa-arrow-left"></i> මුල් පිටුවට (Home)
        </a>
    </div>
</div>

<script>
    function applySavedTheme() {
        const savedTheme = localStorage.getItem('sansun_theme');
        const icon = document.getElementById('themeIcon');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.body.setAttribute('data-theme', 'dark');
            if (icon) icon.className = 'fa-solid fa-sun';
        } else {
            document.body.removeAttribute('data-theme');
            if (icon) icon.className = 'fa-solid fa-moon';
        }
    }
    function toggleTheme() {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const icon = document.getElementById('themeIcon');
        if (isDark) {
            document.body.removeAttribute('data-theme');
            localStorage.setItem('sansun_theme', 'light');
            if (icon) icon.className = 'fa-solid fa-moon';
        } else {
            document.body.setAttribute('data-theme', 'dark');
            localStorage.setItem('sansun_theme', 'dark');
            if (icon) icon.className = 'fa-solid fa-sun';
        }
    }
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye';
        }
    }
    applySavedTheme();
</script>
</body>
</html>
