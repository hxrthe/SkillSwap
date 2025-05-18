<?php
<<<<<<< HEAD
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

=======
require_once 'SkillSwapDatabase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$user_id = intval($_SESSION['user_id']);

>>>>>>> maris
try {
    $db = new Database();
    $conn = $db->getConnection();

<<<<<<< HEAD
    // Fetch users that the logged-in user hasn't matched with
    $stmt = $conn->prepare("
        SELECT u.User_ID, u.First_Name
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

    // Return the users as JSON
    header('Content-Type: application/json');
=======
    // Call the stored procedure
    $stmt = $conn->prepare("CALL FetchAvailableUsers(:current_user_id)");
    $stmt->bindParam(':current_user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

>>>>>>> maris
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}