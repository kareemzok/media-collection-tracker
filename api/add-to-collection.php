<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$title = $data['title'] ?? '';
$type = $data['type'] ?? '';
$creator = $data['creator'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($title) || empty($type)) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Map AI types to DB enum types if necessary
$validTypes = ['movie', 'music', 'game'];
$type = strtolower($type);
if (!in_array($type, $validTypes)) {
    // Basic mapping for common variations
    if (strpos($type, 'film') !== false)
        $type = 'movie';
    elseif (strpos($type, 'album') !== false)
        $type = 'music';
    elseif (strpos($type, 'video game') !== false)
        $type = 'game';
    else
        $type = 'movie'; // Default or handle error
}

try {
    $stmt = $pdo->prepare("INSERT INTO media_items (user_id, title, creator, type, status, created_at) VALUES (?, ?, ?, ?, 'wishlist', CURRENT_TIMESTAMP)");
    $stmt->execute([$userId, $title, $creator, $type]);

    // Log activity
    $itemId = $pdo->lastInsertId();
    $logStmt = $pdo->prepare("INSERT INTO activity_log (user_id, action, target_id) VALUES (?, ?, ?)");
    $logStmt->execute([$userId, "Added " . htmlspecialchars($title) . " (from recommendation)", $itemId]);

    echo json_encode(['success' => true, 'message' => 'Item added to collection!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
