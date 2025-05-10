<?php
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['skills']) || !isset($data['skill_type'])) {
        throw new Exception('Invalid data');
    }

    $userId = $data['user_id'];
    $skills = $data['skills'];
    $skillType = $data['skill_type'];

    // Call stored procedure
    $stmt = $conn->prepare("CALL SaveUserSkills(:user_id, :skill_type, :skills)");
    $stmt->execute([
        ':user_id' => $userId,
        ':skill_type' => $skillType,
        ':skills' => json_encode($skills)
    ]);

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
