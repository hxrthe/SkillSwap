<?php
session_start();
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

try {
    // Get the sender and receiver IDs
    $senderId = $_SESSION['user_id'];
    $receiverId = $_POST['receiver_id'];

    // Check if there's already a match request
    $stmt = $conn->prepare("SELECT * FROM match_requests 
                           WHERE (sender_id = :sender_id AND receiver_id = :receiver_id) 
                           OR (sender_id = :receiver_id AND receiver_id = :sender_id)");
    $stmt->execute([
        ':sender_id' => $senderId,
        ':receiver_id' => $receiverId
    ]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Match request already exists');
    }

    // Insert the new match request
    $stmt = $conn->prepare("INSERT INTO match_requests (sender_id, receiver_id, status, created_at) 
                           VALUES (:sender_id, :receiver_id, 'pending', NOW())");
    
    $stmt->execute([
        ':sender_id' => $senderId,
        ':receiver_id' => $receiverId
    ]);

    echo json_encode(['success' => true, 'message' => 'Match request sent successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
