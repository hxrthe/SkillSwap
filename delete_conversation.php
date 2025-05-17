<?php
require_once 'SkillSwapDatabase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user1_id = isset($_POST['user1_id']) ? intval($_POST['user1_id']) : 0;
    $user2_id = isset($_POST['user2_id']) ? intval($_POST['user2_id']) : 0;

    if (!$user1_id || !$user2_id) {
        echo json_encode(['error' => 'User IDs are required.']);
        exit();
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("CALL DeleteConversation(:user1_id, :user2_id)");
        $stmt->bindParam(':user1_id', $user1_id, PDO::PARAM_INT);
        $stmt->bindParam(':user2_id', $user2_id, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}