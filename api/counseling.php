<?php
require_once __DIR__ . '/../config/db.php';

$db = get_db();
$user = current_user();
$userId = $user['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $isAnon = !empty($input['is_anonymous']) || ($input['request_type'] ?? '') === 'anonymous';
    $reqType = $isAnon ? 'anonymous' : 'named';
    $studentName = $isAnon ? 'Anonymous Student' : sanitize($input['student_name'] ?? ($user['name'] ?? 'Student'));
    $studentId = $isAnon ? 'Anonymous' : sanitize($input['student_id'] ?? ($user['student_id'] ?? 'DIT 14253'));
    $preferredMode = sanitize($input['preferred_mode'] ?? 'online');
    $preferredDate = sanitize($input['preferred_date'] ?? date('Y-m-d H:i:s', strtotime('+1 day 10:00')));
    $notes = sanitize($input['notes'] ?? $input['reason'] ?? '');

    $stmt = $db->prepare("
        INSERT INTO counseling_requests (user_id, student_name, student_id, request_type, preferred_mode, preferred_date, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$userId, $studentName, $studentId, $reqType, $preferredMode, $preferredDate, $notes]);

    json_response([
        'success' => true,
        'message' => 'Counseling request submitted successfully.'
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$userId) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    $stmt = $db->prepare("SELECT * FROM counseling_requests WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$userId]);
    $requests = $stmt->fetchAll();

    json_response([
        'success' => true,
        'requests' => $requests
    ]);
}
