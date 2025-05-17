<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    error_log('Session user_id is not set'); // Log the session issue
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];
error_log('Session user_id: ' . $userId); // Log the user ID

$data = json_decode(file_get_contents('php://input'), true);
error_log('Raw input data: ' . file_get_contents('php://input')); // Log raw input
error_log('Decoded data: ' . print_r($data, true)); // Log decoded data

$skills = $data['skills'] ?? [];
$skillType = $data['skill_type'] ?? '';

error_log('Skills: ' . print_r($skills, true)); // Log skills
error_log('Skill Type: ' . $skillType); // Log skill type

if (empty($skills) || empty($skillType)) {
    error_log('Invalid input: skills or skill_type is empty'); // Log the error
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Call the stored procedure
    $stmt = $conn->prepare("CALL SaveUserSkills(:userId, :skillType, :skills)");
    $stmt->execute([
        ':userId' => $userId,
        ':skillType' => $skillType,
        ':skills' => json_encode($skills)
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Error executing stored procedure: " . $e->getMessage()); // Log the error
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}