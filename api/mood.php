<?php
require_once __DIR__ . '/../config/db.php';

$db = get_db();
$user = current_user();
$userId = $user['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $emoji = sanitize($input['mood'] ?? $input['emoji'] ?? '😊');
    $note = sanitize($input['note'] ?? '');

    $moodLabels = [
        '😊' => 'Happy / සතුටුයි',
        '😐' => 'Neutral / සාමාන්‍යයි',
        '😔' => 'Sad / දුකයි',
        '😡' => 'Angry / කෝපයි'
    ];
    $label = $moodLabels[$emoji] ?? 'Recorded';

    $stmt = $db->prepare("INSERT INTO mood_logs (user_id, mood_emoji, mood_label, note) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $emoji, $label, $note]);

    json_response([
        'success' => true,
        'message' => 'Mood logged successfully.',
        'data' => [
            'mood' => $emoji,
            'label' => $label,
            'note' => $note,
            'date' => date('Y-m-d H:i')
        ]
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
}
