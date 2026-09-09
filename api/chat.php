<?php
require_once __DIR__ . '/../config/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$msg = mb_strtolower(trim($input['message'] ?? ''));
$lang = $input['lang'] ?? 'si';

$reply = '';

if ($lang === 'si') {
    if (strpos($msg, 'පීඩන') !== false || strpos($msg, 'stress') !== false || strpos($msg, 'බය') !== false || strpos($msg, 'exam') !== false) {
        $reply = "විභාග සහ පැවරුම් නිසා පීඩනයක් දැනීම සාමාන්‍යයි. කරුණාකර:\n1. 4-7-8 හුස්ම ගැනීමේ ව්‍යායාමය උත්සාහ කරන්න.\n2. අධ්‍යයන කාලය විනාඩි 25 ක කුඩා කොටස් වලට (Pomodoro) බෙදාගන්න.\n3. ඔබට සවන් දීමට අපගේ විශ්වවිද්‍යාල උපදේශකවරයා සූදානම්. උපදේශන වාරයක් වෙන්කරවා ගන්න.";
    } elseif (strpos($msg, '1926') !== false || strpos($msg, 'හදිසි') !== false || strpos($msg, 'help') !== false) {
        $reply = "ඔබට ක්ෂණික සහ නොමිලේ මානසික සෞඛ්‍ය සහාය ලබාගැනීමට 1926 (ජාතික මානසික සෞඛ්‍ය විද්‍යායතනය) හෝ 1333 (CCC Line - 24/7) නොමිලේ අමතන්න. ඔබ තනිවී නැත.";
    } elseif (strpos($msg, 'සන්සුන්') !== false || strpos($msg, 'relax') !== false || strpos($msg, 'නින්ද') !== false) {
        $reply = "මනස සන්සුන් කරගැනීමට:\n• ඉහළ ඇති සොබාදහමේ වැසි ශබ්දයට (Ambient Sound) සවන් දෙන්න.\n• 5-4-3-2-1 Grounding අභ්‍යාසය කරන්න.\n• මදක් ඇවිදින්න සහ ප්‍රමාණවත් ජලය පානය කරන්න.";
    } elseif (strpos($msg, 'හායි') !== false || strpos($msg, 'hello') !== false || strpos($msg, 'hi') !== false) {
        $reply = "ආයුබෝවන්! මම 'සන්සුන්' AI සහායක. ඔබට අද සිතට දැනෙන ඕනෑම දෙයක් මා සමඟ බෙදාගන්න. මම ඔබට උපකාර කිරීමට සූදානම්.";
    } else {
        $reply = "ඔබගේ පණිවිඩය ලැබුණා. ඔබට විභාග ආතතිය, කාංසාව හෝ මනස සන්සුන් කරගන්නා ආකාරය ගැන අවශ්‍ය ඕනෑම දෙයක් මෙහි විමසන්න පුළුවන්. නැතහොත් ඉහළ ඇති 'ඇගයීම' (Assessment) මගින් ඔබේ මානසික සුවතාවය පරික්ෂා කරගන්න.";
    }
} else {
    if (strpos($msg, 'stress') !== false || strpos($msg, 'pressure') !== false || strpos($msg, 'exam') !== false) {
        $reply = "Exam and academic stress is very common. Here are quick tips:\n1. Try our 4-7-8 breathing exercise in the top bar.\n2. Break your study sessions into 25-minute focus intervals.\n3. Book a confidential session with our student counselor.";
    } elseif (strpos($msg, '1926') !== false || strpos($msg, 'emergency') !== false || strpos($msg, 'helpline') !== false) {
        $reply = "For immediate free 24/7 mental support, call 1926 (National Institute of Mental Health) or 1333 (CCC Line). You are not alone.";
    } elseif (strpos($msg, 'calm') !== false || strpos($msg, 'relax') !== false || strpos($msg, 'sleep') !== false) {
        $reply = "To relax your mind:\n• Play our ambient rain sounds above.\n• Practice the 5-4-3-2-1 Grounding technique.\n• Hydrate and step outside for 15 minutes.";
    } elseif (strpos($msg, 'hi') !== false || strpos($msg, 'hello') !== false) {
        $reply = "Hello! I am the Sansun AI Assistant. Feel free to share whatever is on your mind today. How can I help you?";
    } else {
        $reply = "Thank you for reaching out. You can ask me about stress management, relaxation techniques, or institutional counseling. Take a quick self-check using our PHQ-9 & GAD-7 assessments above!";
    }
}

json_response([
    'success' => true,
    'reply' => $reply
]);
