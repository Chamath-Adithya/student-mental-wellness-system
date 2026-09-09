<?php
/**
 * Student Mental Wellness Check-in System (Sansun)
 * Database Configuration & Core Helpers
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Credentials (Standard WampServer defaults)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wellness_system_db');

// Base URL helper
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('SITE_URL', $protocol . $host . '/student-mental-wellness-system');
define('APP_NAME', 'Sansun - Student Mental Wellness');

/**
 * Get PDO Database Connection
 * Auto-creates database and executes schema if not yet installed
 */
function get_db() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Auto-initialize if database not found
        if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
            try {
                $initPdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4", DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                $schemaFile = dirname(__DIR__) . '/database/schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    $initPdo->exec($sql);

                    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]);
                    return $pdo;
                }
            } catch (PDOException $initErr) {
                die("Database auto-initialization failed: " . htmlspecialchars($initErr->getMessage()));
            }
        }
        
        die("<div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:24px;border-radius:12px;background:#fef2f2;border:1px solid #fee2e2;'>
            <h3 style='color:#b91c1c;margin-top:0;'>Cannot connect to MySQL Server</h3>
            <p style='color:#374151;'>Please ensure your <strong>WampServer</strong> is running and MySQL service is active.</p>
            <p style='color:#6b7280;font-size:13px;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>
        </div>");
    }
}

/**
 * Sanitize User Input
 */
function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * JSON Response helper
 */
function json_response($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Flash Notifications
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function display_flash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $type = htmlspecialchars($f['type']);
        $msg = htmlspecialchars($f['message']);
        $icon = ($type === 'success') ? 'fa-circle-check' : 'fa-triangle-exclamation';
        $bg = ($type === 'success') ? '#1e4d2b' : '#dc2626';
        echo "<div id='sysFlashToast' class='flash-toast' style='position:fixed;bottom:24px;left:24px;z-index:9999;padding:12px 20px;border-radius:12px;background:{$bg};color:#fff;box-shadow:0 12px 28px rgba(0,0,0,0.18);display:flex;align-items:center;gap:12px;font-size:0.9rem;font-weight:600;animation:toastSlideIn 0.3s ease;'>
            <i class='fa-solid {$icon}'></i>
            <span>{$msg}</span>
            <button onclick='this.parentElement.remove()' style='background:none;border:none;color:rgba(255,255,255,0.8);font-size:1.1rem;cursor:pointer;margin-left:8px;' title='Dismiss'>&times;</button>
        </div>
        <script>
            setTimeout(() => {
                const t = document.getElementById('sysFlashToast');
                if (t) {
                    t.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    t.style.opacity = '0';
                    t.style.transform = 'translateY(10px)';
                    setTimeout(() => t.remove(), 400);
                }
            }, 4000);
        </script>";
    }
}

/**
 * Redirection Helper
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Authentication Helpers
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'Student',
        'student_id' => $_SESSION['student_id'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'student',
        'intake' => $_SESSION['user_intake'] ?? 'DIT 14 Intake'
    ];
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('danger', 'Please login to access this section.');
        redirect(SITE_URL . '/login.php');
    }
}

function require_admin() {
    require_login();
    $u = current_user();
    if ($u['role'] !== 'admin' && $u['role'] !== 'counselor') {
        set_flash('danger', 'Access denied. Counselor or Administrator privileges required.');
        redirect(SITE_URL . '/dashboard.php');
    }
}
