<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("
        SELECT u.User_ID, u.First_Name, u.Skill, u.Offer, u.Exchange
        FROM users u
        WHERE u.User_ID != :user_id
        AND u.User_ID NOT IN (
            SELECT receiver_id FROM matches WHERE sender_id = :user_id
            UNION
            SELECT sender_id FROM matches WHERE receiver_id = :user_id
        )
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Output the fetched users
    header('Content-Type: application/json');
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
exit();