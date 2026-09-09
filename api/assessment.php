<?php
require_once __DIR__ . '/../config/db.php';

$db = get_db();
$user = current_user();
$userId = $user['id'] ?? null;
$studentName = $user['name'] ?? 'Guest Student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $testType = sanitize($input['test_type'] ?? 'phq9');
    $score = (int)($input['score'] ?? 0);
    $answers = $input['answers'] ?? [];
    $answersJson = json_encode($answers);

    $isHighRisk = 0;
    $severity = '';
    $recommendation = '';

    if ($testType === 'phq9') {
        if ($score <= 4) {
            $severity = 'Minimal / සාමාන්‍ය';
            $recommendation = 'ඔබගේ මානසික මට්ටම සාමාන්‍ය මට්ටමක පවතී. දෛනික සුවතා පුරුදු දිගටම පවත්වා ගන්න.';
        } elseif ($score <= 9) {
            $severity = 'Mild / සුළු විෂාදය';
            $recommendation = 'සුළු මානසික වෙහෙසක් දක්නට ලැබේ. විවේකය, හුස්ම ගැනීමේ ව්‍යායාම සහ ප්‍රියතම ක්‍රියාකාරකම් වල යෙදෙන්න.';
        } elseif ($score <= 14) {
            $severity = 'Moderate / මධ්‍යස්ථ විෂාදය';
            $recommendation = 'මධ්‍යස්ථ පීඩනයක් හඳුනාගෙන ඇත. විශ්වවිද්‍යාල උපදේශකවරයෙකු සමඟ සාකච්ඡා කිරීම නිර්දේශ කෙරේ.';
        } elseif ($score <= 19) {
            $severity = 'Moderately Severe / වැඩි විෂාදය';
            $isHighRisk = 1;
            $recommendation = 'දැඩි මානසික අවපීඩනයක් පවතී. කරුණාකර වෘත්තීය උපදේශනයක් හෝ 1926 ක්ෂණික දුරකථන අංකය අමතන්න.';
        } else {
            $severity = 'Severe / ඉතා දැඩි විෂාදය';
            $isHighRisk = 1;
            $recommendation = 'අධික මානසික පීඩනයක් හඳුනාගන්නා ලදී. වහාම උපදේශන සේවාව හා සම්බන්ධ වීම හෝ 1926 අමතන්න.';
        }

        // Flag self-harm if question 9 (index 8) was answered > 0
        if (isset($answers[8]) && (int)$answers[8] > 0) {
            $isHighRisk = 1;
        }
    } else { // gad7
        if ($score <= 4) {
            $severity = 'Minimal Anxiety / සාමාන්‍ය';
            $recommendation = 'කාංසාව අවම මට්ටමක පවතී. සන්සුන්ව දෛනික වැඩකටයුතු කරගෙන යන්න.';
        } elseif ($score <= 9) {
            $severity = 'Mild Anxiety / සුළු කාංසාව';
            $recommendation = 'සුළු නොසන්සුන්බවක් දැනේ නම් 4-7-8 හුස්ම ගැනීමේ ව්‍යායාමය හෝ Grounding අභ්‍යාසය කරන්න.';
        } elseif ($score <= 14) {
            $severity = 'Moderate Anxiety / මධ්‍යස්ථ කාංසාව';
            $recommendation = 'විභාග හෝ අධ්‍යාපනික වැඩ නිසා නොසන්සුන් බවක් ඇති විය හැක. උපදේශන සාකච්ඡාවක් ලබාගන්න.';
        } else {
            $severity = 'Severe Anxiety / අධික කාංසාව';
            $isHighRisk = 1;
            $recommendation = 'කාංසාව ඉතා ඉහළ මට්ටමක පවතී. වහාම වෘත්තීය උපදේශකවරයෙකුගේ සහාය ලබාගන්න.';
        }
    }

    $stmt = $db->prepare("INSERT INTO assessments (user_id, student_name, test_type, total_score, severity_level, is_high_risk, answers_json, recommendation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $studentName, $testType, $score, $severity, $isHighRisk, $answersJson, $recommendation]);

    json_response([
        'success' => true,
        'score' => $score,
        'severity' => $severity,
        'is_high_risk' => $isHighRisk,
        'recommendation' => $recommendation
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($userId) {
        $stmt = $db->prepare("SELECT id, test_type, total_score, severity_level, is_high_risk, created_at FROM assessments WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([$userId]);
    } else {
        $stmt = $db->query("SELECT id, test_type, total_score, severity_level, is_high_risk, created_at FROM assessments ORDER BY id DESC LIMIT 15");
    }
    $history = $stmt->fetchAll();

    json_response([
        'success' => true,
        'history' => $history
    ]);
}
