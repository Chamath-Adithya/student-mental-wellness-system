<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// Read raw JSON or POST form data
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$identifier = sanitize($input['email'] ?? $input['identifier'] ?? '');
$password = $input['password'] ?? '';

if (empty($identifier) || empty($password)) {
    json_response(['success' => false, 'message' => 'Please provide email/student ID and password.'], 400);
}

$db = get_db();
$stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR student_id = ? LIMIT 1");
$stmt->execute([$identifier, $identifier]);
$user = $stmt->fetch();

// Password check (or demo fallback)
$passwordValid = false;
if ($user && password_verify($password, $user['password_hash'])) {
    $passwordValid = true;
} elseif ($user && ($password === 'admin123' || $password === 'student123')) {
    $passwordValid = true;
}

if (!$user || !$passwordValid) {
    json_response(['success' => false, 'message' => 'Invalid credentials. Please verify your details.'], 401);
}

// Start Session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['student_id'] = $user['student_id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['user_intake'] = $user['intake'] ?? 'DIT 14 Intake';

$redirectUrl = ($user['role'] === 'admin' || $user['role'] === 'counselor') 
    ? SITE_URL . '/admin-dashboard.php' 
    : SITE_URL . '/dashboard.php';

json_response([
    'success' => true,
    'message' => 'Login successful.',
    'user' => [
        'id' => $user['id'],
        'name' => $user['full_name'],
        'student_id' => $user['student_id'],
        'email' => $user['email'],
        'role' => $user['role']
    ],
    'redirect' => $redirectUrl
]);
