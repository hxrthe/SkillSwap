<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;

if (!$request_id) {
    echo json_encode(['success' => false, 'error' => 'Request ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Update the request status to 'declined'
    $stmt = $conn->prepare("UPDATE match_requests SET status = 'declined' WHERE id = :id");
    $stmt->execute([':id' => $request_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Error declining request: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}