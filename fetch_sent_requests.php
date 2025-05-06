<?php
require_once 'SkillSwapDatabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User ID is required.']);
    exit();
}

$user_id = intval($_SESSION['user_id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT r.*, u.First_Name AS receiver_name
        FROM requests r
        JOIN users u ON r.receiver_id = u.User_ID
        WHERE r.sender_id = :sender_id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':sender_id' => $user_id]);
    $sentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Log the output
    error_log("Sent Requests: " . json_encode($sentRequests));

    echo json_encode($sentRequests);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}