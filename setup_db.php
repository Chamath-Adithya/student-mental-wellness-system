<?php
/**
 * Student Mental Wellness Check-in System
 * One-Click Database Installer & Seeder
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wellness_system_db');

$isCli = (php_sapi_name() === 'cli');
$schemaPath = __DIR__ . '/database/schema.sql';

$status = [];
$error = null;

try {
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found at: {$schemaPath}");
    }

    $sql = file_get_contents($schemaPath);

    // Connect without database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $status[] = "Connected to MySQL server at " . DB_HOST . ":" . DB_PORT . " successfully.";

    // Execute multi-query schema
    $pdo->exec($sql);
    $status[] = "Database 'wellness_system_db' initialized.";
    $status[] = "All relational tables (users, mood_logs, assessments, counseling_requests) generated.";
    $status[] = "Initial administrative and student accounts provisioned:";
    $status[] = " &bull; Student Account: student@dit.ac.lk (Student ID: DIT 14253)";
    $status[] = " &bull; Counselor Account: admin@sansun.com";
    $status[] = "Standard clinical assessment instruments (PHQ-9, GAD-7) and confidential counseling workflows activated.";

} catch (Exception $e) {
    $error = $e->getMessage();
}

if ($isCli) {
    echo "========================================================\n";
    echo "Student Mental Wellness System - Database Setup\n";
    echo "========================================================\n";
    if ($error) {
        echo "ERROR: " . $error . "\n";
        exit(1);
    } else {
        foreach ($status as $msg) {
            echo " [OK] " . strip_tags($msg) . "\n";
        }
        echo "\nSetup completed successfully!\n";
        exit(0);
    }
}
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title>Database Setup | Sansun</title>
    <link rel="icon" type="image/png" href="assets/images/logo.png?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Noto+Sans+Sinhala:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; background: #0b1410; color: #fff; padding: 40px 20px; }
        .card { max-width: 620px; margin: 0 auto; background: #fff; color: #1e293b; border-radius: 20px; padding: 35px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); }
        .success { color: #15803d; }
        .error { color: #b91c1c; }
        ul { padding-left: 20px; margin: 20px 0; line-height: 1.8; font-size: 0.95rem; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; margin-right: 10px; font-size: 0.9rem; }
        .btn-green { background: #1e4d2b; color: #fff; }
        .btn-green:hover { background: #2d6a4f; }
        .brand-logo-img { width: 52px; height: 52px; object-fit: contain; }
    </style>
</head>
<body>
<div class="card">
    <div style="display:flex; align-items:center; gap:14px; margin-bottom:20px;">
        <img src="assets/images/logo.png?v=2" alt="Sansun Logo" class="brand-logo-img">
        <div>
            <h2 style="margin: 0; color: #1e4d2b; font-size:1.4rem;">Sansun</h2>
            <p style="margin: 0; font-size:0.85rem; color:#64748b;">Database Setup & Installer</p>
        </div>
    </div>

    <?php if ($error): ?>
        <h3 class="error">Installation Failed</h3>
        <p>Could not initialize database: <?php echo htmlspecialchars($error); ?></p>
        <p>Please make sure WampServer MySQL service is running.</p>
    <?php else: ?>
        <h3 class="success">&#10004; Setup Completed Successfully!</h3>
        <ul>
            <?php foreach ($status as $s): ?>
                <li><?php echo $s; ?></li>
            <?php endforeach; ?>
        </ul>

        <div style="background: #f0fdf4; padding: 18px 20px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #86efac; font-size: 0.88rem; color: #166534;">
            <strong style="display:block; margin-bottom: 6px; font-size: 0.95rem;">Initial System Accounts:</strong>
            • <strong>Student Portal:</strong> <code>student@dit.ac.lk</code> (or ID: <code>DIT 14253</code>) | Password: <code>student123</code><br>
            • <strong>Counselor Administration:</strong> <code>admin@sansun.com</code> | Password: <code>admin123</code>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="login.php" class="btn btn-green">Proceed to Login</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
