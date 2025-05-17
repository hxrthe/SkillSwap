<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = intval($_SESSION['user_id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch ongoing requests where the logged-in user is the sender or receiver
    $stmt = $conn->prepare("
        SELECT r.*, 
            CASE WHEN r.sender_id = :user_id THEN u2.First_Name ELSE u.First_Name END AS other_name,
            CASE WHEN r.sender_id = :user_id THEN r.receiver_id ELSE r.sender_id END AS other_user_id
        FROM match_requests r
        JOIN users u ON r.sender_id = u.User_ID
        JOIN users u2 ON r.receiver_id = u2.User_ID
        WHERE (r.sender_id = :user_id OR r.receiver_id = :user_id) AND r.status = 'ongoing'
        ORDER BY r.updated_at DESC
    ");
    
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $ongoingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ongoingRequests);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch ongoing requests']);
}
?>
