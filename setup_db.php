<?php
/**
 * Student Mental Wellness Check-in System (Sansun)
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
    $status[] = "Database 'wellness_system_db' created / initialized.";
    $status[] = "All tables (users, mood_logs, assessments, counseling_requests) generated.";
    $status[] = "Default seed accounts loaded:";
    $status[] = " &bull; Student Account: student@dit.ac.lk / student123 (DIT 14253 B.A.I.D Bopitiya)";
    $status[] = " &bull; Admin/Counselor Account: admin@sansun.com / admin123";
    $status[] = "Sample mood entries, assessments (PHQ-9 & GAD-7), and counseling sessions loaded.";

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
    <title>Database Setup | Sansun Wellness</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #131b17; color: #fff; padding: 40px 20px; }
        .card { max-width: 620px; margin: 0 auto; background: #fff; color: #1e293b; border-radius: 16px; padding: 35px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .success { color: #15803d; }
        .error { color: #b91c1c; }
        ul { padding-left: 20px; margin: 20px 0; line-height: 1.8; font-size: 0.95rem; }
        .btn { display: inline-block; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-right: 10px; font-size: 0.9rem; }
        .btn-green { background: #4a7c59; color: #fff; }
        .btn-dark { background: #0f172a; color: #fff; }
    </style>
</head>
<body>
<div class="card">
    <h2 style="margin-top: 0; color: #4a7c59;">Sansun Wellness - Database Installer</h2>

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

        <div style="background: #f0fdf4; padding: 16px; border-radius: 10px; margin-bottom: 24px; border: 1px dashed #86efac; font-size: 0.88rem;">
            <strong>Demo Login Credentials:</strong><br>
            • <strong>Student Account:</strong> <code>student@dit.ac.lk</code> | Password: <code>student123</code><br>
            • <strong>Admin Counselor:</strong> <code>admin@sansun.com</code> | Password: <code>admin123</code>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="index.php" class="btn btn-green">Open Main Web App</a>
            <a href="login.php" class="btn btn-dark">Login to Portal</a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
