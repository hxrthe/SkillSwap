<?php
session_start();
<<<<<<< HEAD

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : null;

if (!$request_id) {
    echo json_encode(['error' => 'Request ID is required']);
    exit();
}

=======
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit;
}

if (!isset($_POST['request_id'])) {
    echo json_encode(['success' => false, 'error' => 'No request ID provided.']);
    exit;
}

$requestId = intval($_POST['request_id']);

>>>>>>> maris
try {
    $db = new Database();
    $conn = $db->getConnection();

    // Update the request status to 'accepted'
<<<<<<< HEAD
    $stmt = $conn->prepare("UPDATE requests SET status = 'accepted' WHERE id = :request_id");
    $stmt->execute([':request_id' => $request_id]);

    error_log("Request ID: " . $request_id);
    error_log("Request status updated to 'accepted'");

    echo json_encode(['success' => 'Request accepted successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
=======
    $stmt = $conn->prepare("UPDATE requests SET status = 'accepted' WHERE id = :id AND receiver_id = :receiver_id");
    $stmt->execute([
        ':id' => $requestId,
        ':receiver_id' => $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to accept request. Please try again.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
>>>>>>> maris
