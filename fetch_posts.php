<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$communityId = isset($_GET['community_id']) ? intval($_GET['community_id']) : 0;

if (!$communityId) {
    echo json_encode(['error' => 'Community ID is required.']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT p.id, p.content, p.created_at, u.First_Name AS user_name
        FROM posts p
        JOIN users u ON p.user_id = u.User_ID
        WHERE p.community_id = :community_id
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([':community_id' => $communityId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($posts);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}