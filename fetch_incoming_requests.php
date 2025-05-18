<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$currentUserId = $_SESSION['user_id'];

error_log("Logged-in User ID from session: " . $currentUserId);

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("
        SELECT r.id, r.message, r.status, r.created_at, u.User_ID AS sender_id, u.First_Name AS sender_name, u.Last_Name AS sender_last_name, u.Profile_Picture
        FROM requests r
        JOIN users u ON u.User_ID = r.sender_id
        WHERE r.receiver_id = :receiver_id
        AND r.status = 'pending'
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':receiver_id' => $currentUserId]);
    $incomingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("Incoming Requests: " . print_r($incomingRequests, true)); // Debugging
    echo json_encode($incomingRequests);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>