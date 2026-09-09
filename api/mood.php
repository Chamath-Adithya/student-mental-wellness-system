<?php
require_once __DIR__ . '/../config/db.php';

$db = get_db();
$user = current_user();
$userId = $user['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $code = sanitize($input['mood_code'] ?? $input['code'] ?? 'balanced');
    $icon = sanitize($input['mood_icon'] ?? $input['icon'] ?? 'fa-seedling');
    $label = sanitize($input['mood_label'] ?? $input['label'] ?? 'Balanced');
    $note = sanitize($input['note'] ?? '');

    try {
        $stmt = $db->prepare("INSERT INTO mood_logs (user_id, mood_code, mood_icon, mood_label, note) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $code, $icon, $label, $note]);

        json_response([
            'success' => true,
            'message' => 'Emotional state logged successfully.',
            'data' => [
                'mood_code' => $code,
                'mood_icon' => $icon,
                'mood_label' => $label,
                'note' => $note,
                'date' => date('Y-m-d H:i')
            ]
        ]);
    } catch (PDOException $e) {
        // In case old table schema was still active, fallback gracefully
        try {
            $stmt = $db->prepare("INSERT INTO mood_logs (user_id, mood_code, mood_label, note) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $code, $label, $note]);
            json_response(['success' => true, 'message' => 'Logged successfully']);
        } catch (Exception $e2) {
            json_response(['success' => false, 'message' => 'Database error: ' . $e2->getMessage()], 500);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        if ($userId) {
            $stmt = $db->prepare("SELECT * FROM mood_logs WHERE user_id = ? ORDER BY id DESC LIMIT 10");
            $stmt->execute([$userId]);
        } else {
            $stmt = $db->query("SELECT * FROM mood_logs ORDER BY id DESC LIMIT 10");
        }
        $logs = $stmt->fetchAll();

        json_response([
            'success' => true,
            'logs' => $logs
        ]);
    } catch (Exception $e) {
        json_response(['success' => false, 'logs' => [], 'error' => $e->getMessage()]);
    }
}
