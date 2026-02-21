<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!AI_ENABLED_BOOL) {
    echo json_encode(['error' => 'AI recommendations are currently disabled by the site administrator.']);
    exit;
}


if (empty(OPENAI_API_KEY)) {
    echo json_encode(['error' => 'API Key not configured. Please add your OpenAI API Key in the .env file']);
    exit;
}
$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

// Check daily usage
$stmt = $pdo->prepare("SELECT usage_count FROM ai_usage WHERE user_id = ? AND usage_date = ?");
$stmt->execute([$userId, $today]);
$usage = $stmt->fetch();
$currentUsage = $usage ? $usage['usage_count'] : 0;

if ($currentUsage >= AI_DAILY_LIMIT) {
    echo json_encode(['error' => 'You have reached your daily limit of ' . AI_DAILY_LIMIT . ' recommendations. Please try again tomorrow.']);
    exit;
}

// Fetch user's media collection to use as context
$stmt = $pdo->prepare("SELECT title, creator, type, genre, rating FROM media_items WHERE user_id = ? AND (rating >= 4 OR status = 'completed' OR status = 'owned') LIMIT 20");
$stmt->execute([$userId]);
$collection = $stmt->fetchAll();

if (empty($collection)) {
    echo json_encode(['error' => 'Your collection is empty. Add some media items with ratings or genres first to get personalized recommendations!']);
    exit;
}

// Build the prompt
$collectionContext = "";
foreach ($collection as $item) {
    $collectionContext .= "- {$item['title']} ({$item['type']}) by {$item['creator']}. Genre: {$item['genre']}. Rating: {$item['rating']}/5\n";
}

$prompt = "I have a personal media collection. Based on the following items I own or have enjoyed, please recommend 3-5 similar movies, games, or music albums I might like. Format the response as a valid JSON array of objects, where each object has 'title', 'type', 'reason' (why you recommend it), and 'links' (empty array placeholder).\n\nCollection:\n" . $collectionContext . "\n\nReturn ONLY the JSON array.";

$data = [
    'model' => OPENAI_MODEL,
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful media recommendation assistant. Return only valid JSON.'],
        ['role' => 'user', 'content' => $prompt]
    ],
    'temperature' => (float) OPENAI_TEMPERATURE,
    'response_format' => ['type' => 'json_object']
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['error' => 'OpenAI API Error: ' . ($response ?: 'Unknown error')]);
    exit;
}

$responseData = json_decode($response, true);
$aiContent = $responseData['choices'][0]['message']['content'];

// Update usage count
if ($usage) {
    $stmt = $pdo->prepare("UPDATE ai_usage SET usage_count = usage_count + 1 WHERE user_id = ? AND usage_date = ?");
    $stmt->execute([$userId, $today]);
} else {
    $stmt = $pdo->prepare("INSERT INTO ai_usage (user_id, usage_date, usage_count) VALUES (?, ?, 1)");
    $stmt->execute([$userId, $today]);
}

$usageLeft = max(0, AI_DAILY_LIMIT - ($currentUsage + 1));

// Decode the AI content and add usage info
$recommendations = json_decode($aiContent, true);

// Normalize recommendations: handle both direct array and object with 'recommendations' key
if (isset($recommendations['recommendations']) && is_array($recommendations['recommendations'])) {
    $recommendations = $recommendations['recommendations'];
} elseif (!is_array($recommendations)) {
    // If it's not an array, make it empty to avoid JS errors
    $recommendations = [];
}

echo json_encode([
    'recommendations' => $recommendations,
    'usage_left' => $usageLeft,
    'total_limit' => (int) AI_DAILY_LIMIT
]);
