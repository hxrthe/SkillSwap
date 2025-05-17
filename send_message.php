<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$request_id || !$message) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        INSERT INTO messages (request_id, sender_id, message)
        VALUES (:request_id, :sender_id, :message)
    ");
    $stmt->execute([
        ':request_id' => $request_id,
        ':sender_id' => $_SESSION['user_id'],
        ':message' => $message
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}