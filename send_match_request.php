<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;

if (!$receiver_id) {
    echo json_encode(['success' => false, 'error' => 'Receiver ID is required']);
    exit();
}

error_log("send_match_request.php called with sender_id: $sender_id and receiver_id: $receiver_id");

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if a match request already exists
    $stmt = $conn->prepare("SELECT id, status, created_at FROM match_requests WHERE sender_id = :sender_id AND receiver_id = :receiver_id");
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id
    ]);
    $existingRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingRequest) {
        if ($existingRequest['status'] === 'pending') {
            echo json_encode(['success' => false, 'error' => 'A match request already exists and cannot be resent within 24 hours.']);
            exit();
        } else {
            // Update the existing match request to 'pending'
            $stmt = $conn->prepare("UPDATE match_requests SET status = 'pending', created_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $existingRequest['id']]);

            echo json_encode(['success' => true, 'message' => 'Match request updated successfully.']);
            exit();
        }
    }

    // Insert a new match request
    $stmt = $conn->prepare("INSERT INTO match_requests (sender_id, receiver_id, status, created_at) VALUES (:sender_id, :receiver_id, 'pending', NOW())");
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Match request sent successfully.']);
} catch (PDOException $e) {
    error_log("Error inserting match request: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}