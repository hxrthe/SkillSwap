<?php
session_start();
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentUserId = $_SESSION['user_id'];
    $targetUserId = intval($_POST['target_user_id']);
    $message = $_POST['message'];

    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Insert the match request into the database
        $stmt = $conn->prepare("
            INSERT INTO requests (sender_id, receiver_id, message, status, created_at)
            VALUES (:sender_id, :receiver_id, :message, 'pending', NOW())
        ");
        $stmt->execute([
            ':sender_id' => $currentUserId,
            ':receiver_id' => $targetUserId,
            ':message' => $message
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