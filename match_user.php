<?php
<<<<<<< HEAD
session_start();

require_once 'SkillSwapDatabase.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get the sender and receiver IDs
$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : null;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (!$receiver_id) {
    echo json_encode(['error' => 'Receiver ID is required']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Insert the match request into the database
    $stmt = $conn->prepare("INSERT INTO requests (sender_id, receiver_id, message, status) VALUES (:sender_id, :receiver_id, :message, 'pending')");
    $stmt->execute([
        ':sender_id' => $sender_id,
        ':receiver_id' => $receiver_id,
        ':message' => $message
    ]);

    echo json_encode(['success' => 'Match request sent successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
=======
require_once 'SkillSwapDatabase.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $appointment_date = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : null;

    if (!$sender_id || !$receiver_id) {
        echo json_encode(['error' => 'Sender and receiver IDs are required.']);
        exit();
    }

    error_log("Saving request: sender_id = $sender_id, receiver_id = $receiver_id, message = $message");

    try {
        $db = new Database();
        $conn = $db->getConnection();

        // Call the stored procedure
        $stmt = $conn->prepare("CALL CreateMatchRequest(:sender_id, :receiver_id, :message, :appointment_date)");
        $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':appointment_date', $appointment_date, PDO::PARAM_STR);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
>>>>>>> maris
}