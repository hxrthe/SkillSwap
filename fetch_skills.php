<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];
$skillType = $_GET['skill_type'] ?? '';

if (empty($skillType)) {
    echo json_encode(['success' => false, 'error' => 'Invalid skill type']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT skill_name FROM user_skills WHERE user_id = :userId AND skill_type = :skillType");
    $stmt->execute([':userId' => $userId, ':skillType' => $skillType]);
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'skills' => $skills]);
} catch (PDOException $e) {
    error_log("Error fetching skills: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}