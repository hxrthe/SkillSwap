<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$currentUserId = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("
        SELECT mr.receiver_id, u.First_Name AS receiver_name, mr.created_at
        FROM match_requests mr
        JOIN users u ON u.User_ID = mr.receiver_id
        WHERE mr.sender_id = :current_user_id
        AND mr.status = 'pending'
        ORDER BY mr.created_at DESC
    ");
    $stmt->execute([':current_user_id' => $currentUserId]);
    $sentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sentRequests);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>