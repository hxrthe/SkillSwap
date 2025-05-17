<?php
require_once 'SkillSwapDatabase.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM communities ORDER BY created_at DESC");
    $stmt->execute();
    $communities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($communities);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}