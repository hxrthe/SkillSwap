<?php
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['user_id']) || !isset($data['can_share_json']) || !isset($data['want_to_learn_json'])) {
        throw new Exception('Invalid data');
    }

    $userId = $data['user_id'];
    $canShareJson = $data['can_share_json'];
    $wantToLearnJson = $data['want_to_learn_json'];

    // Call stored procedure
    $stmt = $conn->prepare("CALL SaveUserSkills(:user_id, :can_share_json, :want_to_learn_json)");
    $stmt->execute([
        ':user_id' => $userId,
        ':can_share_json' => $canShareJson,
        ':want_to_learn_json' => $wantToLearnJson
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
