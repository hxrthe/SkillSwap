<?php
session_start();
require_once 'SkillSwapDatabase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id'];
    $targetUserId = intval($_POST['target_user_id']);

    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Insert the match request into the database
        $stmt = $conn->prepare("
            INSERT INTO match_requests (sender_id, receiver_id, status, created_at)
            VALUES (:sender_id, :receiver_id, 'pending', NOW())
        ");
        $stmt->execute([
            ':sender_id' => $currentUserId,
            ':receiver_id' => $targetUserId
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>