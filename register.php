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
    $intake = sanitize($_POST['intake'] ?? 'DIT 14 Intake');
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
    <title>ශිෂ්‍ය ලියාපදිංචිය | Register - Sansun</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4a7c59;
            --primary-hover: #3b6346;
            --primary-light: #eef4f0;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --shadow: 0 15px 35px rgba(74, 124, 89, 0.12);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; }
        body { background-color: var(--bg); color: var(--text-dark); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .reg-box { width: 100%; max-width: 480px; background: var(--card-bg); border-radius: 20px; box-shadow: var(--shadow); border: 1px solid var(--border); padding: 35px 30px; }
        .logo { font-size: 1.5rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 16px; text-decoration: none; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .form-group { margin-bottom: 14px; text-align: left; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 6px; color: var(--text-dark); }
        .form-control { width: 100%; padding: 11px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 0.9rem; background: var(--bg); outline: none; transition: 0.2s; }
        .form-control:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.15); }
        .btn-submit { width: 100%; background: var(--primary); color: white; border: none; padding: 12px; border-radius: 10px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 10px; }
        .btn-submit:hover { background: var(--primary-hover); }
        @media (max-width: 500px) { .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="reg-box">
    <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
        <i class="fa-solid fa-leaf"></i> Sansun (සන්සුන්)
    </a>
    <h2 style="text-align: center; font-size: 1.4rem; margin-bottom: 6px; color: var(--text-dark);">නව ශිෂ්‍ය ලියාපදිංචිය</h2>
    <p style="text-align: center; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 24px;">Student Mental Wellness Check-in System</p>

    <?php if ($error): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 0.85rem;">
            <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="fullname">සම්පූර්ණ නම (Full Name) *</label>
            <input type="text" id="fullname" name="fullname" class="form-control" required placeholder="e.g. B.A.I.D Bopitiya" value="<?php echo htmlspecialchars($_POST['fullname'] ?? ''); ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="student_id">ශිෂ්‍ය අංකය (Student ID) *</label>
                <input type="text" id="student_id" name="student_id" class="form-control" required placeholder="e.g. DIT 14253" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="intake">කණ්ඩායම (Course / Intake)</label>
                <input type="text" id="intake" name="intake" class="form-control" placeholder="DIT 14 Intake" value="<?php echo htmlspecialchars($_POST['intake'] ?? 'DIT 14 Intake'); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="email">විද්‍යුත් තැපෑල (Email Address) *</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="name@dit.ac.lk" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="password">මුරපදය (Password) *</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6" placeholder="••••••••">
            </div>
            <div class="form-group">
                <label for="confirm_password">මුරපදය තහවුරු කරන්න *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="••••••••">
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

</body>
</html>
