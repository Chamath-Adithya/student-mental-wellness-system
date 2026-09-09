<?php
/**
 * Automated Database Setup & Migration Script
 * Student Mental Wellness Check-in System
 */

$host = '127.0.0.1';
$port = '3306';
$user = 'root';
$pass = '';
$dbName = 'wellness_system_db';

try {
    // 1. Connect without DB name to ensure creation
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sqlFile = __DIR__ . '/schema.sql';
    if (!file_exists($sqlFile)) {
        die("Error: schema.sql not found at " . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);
    
    // Execute multi-query
    $pdo->exec($sql);

    echo "<h2 style='color:#15803d; font-family:sans-serif;'>✓ Database setup completed successfully!</h2>";
    echo "<p style='font-family:sans-serif;'>Database <code>wellness_system_db</code> and all tables (users, mood_logs, assessments, counseling_requests) have been initialized with seed data.</p>";
    echo "<p><a href='../login.php' style='padding:8px 16px; background:#1e4d2b; color:#fff; text-decoration:none; border-radius:6px;'>Proceed to Login</a></p>";
} catch (PDOException $e) {
    echo "<h2 style='color:#b91c1c; font-family:sans-serif;'>✗ Database setup failed:</h2>";
    echo "<pre style='background:#fee2e2; padding:15px; border-radius:8px;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
