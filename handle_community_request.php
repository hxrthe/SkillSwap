<?php
session_start();
require_once 'SP.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$requestId = $data['requestId'] ?? null;
$action = $data['action'] ?? null;

if (!$requestId || !$action) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    $crud = new Crud();

    if ($action === 'approve') {
        $crud->approveCommunity($requestId);
        echo json_encode(['success' => true, 'message' => 'Community approved successfully']);
    } else if ($action === 'decline') {
        $crud->declineCommunity($requestId);
        echo json_encode(['success' => true, 'message' => 'Community declined successfully']);
    } else {
        throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function isAdmin($userId) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE User_ID = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $user && $user['is_admin'] == 1;
    } catch (Exception $e) {
        return false;
    }
} 