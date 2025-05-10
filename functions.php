<?php
require_once 'SkillSwapDatabase.php';

/**
 * Fetch users that the logged-in user hasn't matched with.
 *
 * @param int $userId The ID of the logged-in user.
 * @param PDO $conn The database connection.
 * @return array The list of users.
 */
function fetchAvailableUsers($userId, $conn) {
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
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}