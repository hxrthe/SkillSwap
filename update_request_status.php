<?php
require_once 'SkillSwapDatabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    if (!$request_id || !in_array($status, ['accepted', 'declined'])) {
        echo json_encode(['error' => 'Invalid request or status.']);
        exit();
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("UPDATE match_requests SET status = :status WHERE id = :request_id");
        $stmt->execute([
            ':status' => $status,
            ':request_id' => $request_id
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}