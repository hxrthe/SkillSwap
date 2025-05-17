<?php
session_start();
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

try {
    // Get the request ID and response
    $requestId = $_POST['request_id'];
    $response = $_POST['response'];
    $userId = $_SESSION['user_id'];

    // Check if the user is the receiver of this request
    $stmt = $conn->prepare("SELECT * FROM match_requests 
                           WHERE id = :request_id AND receiver_id = :user_id");
    $stmt->execute([
        ':request_id' => $requestId,
        ':user_id' => $userId
    ]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Invalid match request');
    }

    // Update the match request status
    $stmt = $conn->prepare("UPDATE match_requests 
                           SET status = :status, updated_at = NOW() 
                           WHERE id = :request_id");
    
    $status = $response === 'accept' ? 'accepted' : 'declined';
    $stmt->execute([
        ':status' => $status,
        ':request_id' => $requestId
    ]);

    if ($response === 'accept') {
        // Create a new match
        $stmt = $conn->prepare("INSERT INTO matches (user1_id, user2_id, status, created_at) 
                               VALUES (:user1_id, :user2_id, 'pending', NOW())");
        
        $stmt->execute([
            ':user1_id' => $userId,
            ':user2_id' => $stmt->fetch(PDO::FETCH_ASSOC)['sender_id']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Match response updated successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
