<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$postContent = isset($_POST['content']) ? trim($_POST['content']) : '';
$communityId = isset($_POST['community_id']) ? intval($_POST['community_id']) : 0;

if (!$postContent || !$communityId) {
    echo json_encode(['error' => 'Post content and community ID are required.']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        INSERT INTO posts (user_id, content, community_id, created_at)
        VALUES (:user_id, :content, :community_id, NOW())
    ");
    $stmt->execute([
        ':user_id' => $_SESSION['user_id'],
        ':content' => $postContent,
        ':community_id' => $communityId
    ]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>