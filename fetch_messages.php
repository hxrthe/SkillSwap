<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

<<<<<<< HEAD
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
=======
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;
>>>>>>> maris

if (!$request_id) {
    echo json_encode(['error' => 'Invalid request ID']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
<<<<<<< HEAD
        SELECT m.*, u.First_Name AS sender_name
        FROM messages m
        JOIN users u ON m.sender_id = u.User_ID
        WHERE m.request_id = :request_id
=======
        SELECT m.*, u.First_Name AS sender_name, 
               DATE_FORMAT(m.created_at, '%a, %b %d, %Y %h:%i %p') as formatted_date,
               UNIX_TIMESTAMP(m.created_at) as timestamp
        FROM messages m 
        JOIN users u ON m.sender_id = u.User_ID 
        WHERE m.request_id = :request_id 
>>>>>>> maris
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([':request_id' => $request_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

<<<<<<< HEAD
=======
    header('Content-Type: application/json');
>>>>>>> maris
    echo json_encode($messages);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}