<?php
require_once 'SkillSwapDatabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = intval($_SESSION['user_id']);

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Call the stored procedure
    $stmt = $conn->prepare("CALL FetchAvailableUsers(:current_user_id)");
    $stmt->bindParam(':current_user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}