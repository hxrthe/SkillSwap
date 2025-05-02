<?php
session_start();

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;

if (!$request_id) {
    echo json_encode(['error' => 'Request ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Update the request status to 'accepted'
    $stmt = $conn->prepare("UPDATE requests SET status = 'accepted' WHERE id = :request_id");
    $stmt->execute([':request_id' => $request_id]);

    error_log("Request ID: " . $request_id);
    error_log("Request status updated to 'accepted'");

    echo json_encode(['success' => 'Request accepted successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}