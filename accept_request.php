<?php
session_start();
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit;
}

if (!isset($_POST['request_id'])) {
    echo json_encode(['success' => false, 'error' => 'No request ID provided.']);
    exit;
}

$requestId = intval($_POST['request_id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Update the request status to 'accepted'
    $stmt = $conn->prepare("UPDATE requests SET status = 'accepted' WHERE id = :id AND receiver_id = :receiver_id");
    $stmt->execute([
        ':id' => $requestId,
        ':receiver_id' => $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to accept request. Please try again.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
