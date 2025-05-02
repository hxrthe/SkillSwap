<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;

if (!$request_id) {
    echo json_encode(['error' => 'Invalid request ID']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT m.*, u.First_Name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.User_ID
        WHERE m.request_id = :request_id
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([':request_id' => $request_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}