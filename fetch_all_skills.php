<?php
require_once 'SkillSwapDatabase.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM ViewAllSkills");
    $stmt->execute();
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'skills' => $skills]);
} catch (PDOException $e) {
    error_log("Error fetching all skills: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}