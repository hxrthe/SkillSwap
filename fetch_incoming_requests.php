<?php
require_once 'SkillSwapDatabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = intval($_SESSION['user_id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT r.*, u.First_Name AS sender_name
        FROM requests r
        JOIN users u ON r.sender_id = u.User_ID
        WHERE r.receiver_id = :receiver_id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':receiver_id' => $user_id]);
    $incomingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($incomingRequests);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}