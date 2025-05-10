<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch all sent requests
    $stmt = $conn->prepare("SELECT r.id, r.receiver_id, r.status, u.First_Name AS receiver_name
                            FROM match_requests r
                            JOIN users u ON r.receiver_id = u.User_ID
                            WHERE r.sender_id = :user_id
                            ORDER BY r.created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $sentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'requests' => $sentRequests]);
} catch (PDOException $e) {
    error_log("Error fetching sent requests: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}