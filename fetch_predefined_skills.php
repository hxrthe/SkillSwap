<?php
session_start();
require_once 'SkillSwapDatabase.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch predefined skills
    $stmt = $conn->prepare("SELECT id, skill_name FROM predefined_skills ORDER BY skill_name ASC");
    $stmt->execute();
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'skills' => $skills]);
} catch (PDOException $e) {
    error_log("Error fetching predefined skills: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to fetch skills']);
}
?>