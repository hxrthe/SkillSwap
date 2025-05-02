<?php
session_start();

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get the sender and receiver IDs
$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (!$receiver_id) {
    echo json_encode(['error' => 'Receiver ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Insert the request into the database
    $stmt = $conn->prepare("INSERT INTO requests (sender_id, receiver_id, message, status) VALUES (:sender_id, :receiver_id, :message, 'pending')");
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id,
        ':message' => $message
    ]);

    echo json_encode(['success' => 'Request sent successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}