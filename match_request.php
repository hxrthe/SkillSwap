<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$currentUserId = $_SESSION['user_id'];
$matchedUserId = isset($_POST['target_user_id']) ? intval($_POST['target_user_id']) : 0;

if (!$matchedUserId) {
    echo json_encode(['success' => false, 'error' => 'Invalid user']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

try {
    // Insert into match_requests table (if you use it)
    $stmt = $conn->prepare("
        INSERT INTO match_requests (sender_id, receiver_id, status, created_at)
        VALUES (:sender_id, :receiver_id, 'pending', NOW())
    ");
    $stmt->execute([
        ':sender_id' => $currentUserId,
        ':receiver_id' => $matchedUserId
    ]);

    // Insert into requests table for inbox
    $stmt2 = $conn->prepare("
        INSERT INTO requests (sender_id, receiver_id, message, status, created_at)
        VALUES (:sender_id, :receiver_id, :message, 'pending', NOW())
    ");
    $stmt2->execute([
        ':sender_id' => $currentUserId,
        ':receiver_id' => $matchedUserId,
        ':message' => 'Matched with you!'
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>