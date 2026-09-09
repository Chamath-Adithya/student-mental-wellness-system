<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$fullName = sanitize($input['fullname'] ?? $input['name'] ?? '');
$studentId = sanitize($input['student_id'] ?? '');
$email = sanitize($input['email'] ?? '');
$password = $input['password'] ?? '';
$confirmPassword = $input['confirm_password'] ?? '';
$role = sanitize($input['role'] ?? 'student');

if (empty($fullName) || empty($email) || empty($password)) {
    json_response(['success' => false, 'message' => 'Please fill out all required fields.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Invalid email address.'], 400);
}

if (strlen($password) < 6) {
    json_response(['success' => false, 'message' => 'Password must be at least 6 characters long.'], 400);
}

if (!empty($confirmPassword) && $password !== $confirmPassword) {
    json_response(['success' => false, 'message' => 'Passwords do not match.'], 400);
}

// Automatically classify admin role if email contains admin
if (strpos(strtolower($email), 'admin') !== false) {
    $role = 'admin';
} else {
    $role = 'student';
}

$db = get_db();

// Check if email or student ID exists
$checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$checkStmt->execute([$email]);
if ($checkStmt->fetch()) {
    json_response(['success' => false, 'message' => 'An account with this email already exists.'], 409);
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$intake = sanitize($input['intake'] ?? 'General Intake');

$insertStmt = $db->prepare("INSERT INTO users (full_name, student_id, email, password_hash, role, intake) VALUES (?, ?, ?, ?, ?, ?)");
$insertStmt->execute([$fullName, $studentId, $email, $passwordHash, $role, $intake]);
$newUserId = $db->lastInsertId();

// Set user session automatically
$_SESSION['user_id'] = $newUserId;
$_SESSION['user_name'] = $fullName;
$_SESSION['student_id'] = $studentId;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = $role;
$_SESSION['user_intake'] = $intake;

$redirectUrl = ($role === 'admin') ? SITE_URL . '/admin-dashboard.php' : SITE_URL . '/dashboard.php';

json_response([
    'success' => true,
    'message' => 'Account registered successfully.',
    'user' => [
        'id' => $newUserId,
        'name' => $fullName,
        'student_id' => $studentId,
        'email' => $email,
        'role' => $role
    ],
    'redirect' => $redirectUrl
]);
