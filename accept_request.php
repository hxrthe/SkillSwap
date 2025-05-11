<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;

if (!$request_id) {
    echo json_encode(['success' => false, 'error' => 'Request ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if request exists and is still pending
    $stmt = $conn->prepare("
        SELECT * FROM match_requests 
        WHERE id = :request_id AND status = 'pending'
    ");
    $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        echo json_encode(['error' => 'Invalid or expired request']);
        exit();
    }

    // Update request status to 'ongoing' after acceptance
    $stmt = $conn->prepare("
        UPDATE match_requests 
        SET status = 'ongoing', updated_at = NOW() 
        WHERE id = :request_id
    ");
    $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        echo json_encode(['error' => 'Failed to update request status']);
        exit();
    }

    // Fetch updated request details
    $stmt = $conn->prepare("
        SELECT r.*, u.First_Name AS sender_name, u2.First_Name AS receiver_name 
        FROM match_requests r
        LEFT JOIN users u ON r.sender_id = u.User_ID 
        LEFT JOIN users u2 ON r.receiver_id = u2.User_ID 
        WHERE r.id = :request_id
    ");
    $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
    $stmt->execute();
    $updatedRequest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$updatedRequest) {
        echo json_encode(['error' => 'Failed to fetch updated request details']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Request accepted successfully!',
        'request' => $updatedRequest
    ]);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Failed to accept request. Please try again.',
        'details' => $e->getMessage()
    ]);
}
?>
