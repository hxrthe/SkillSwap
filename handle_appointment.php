<?php
session_start();
require_once 'SkillSwapDatabase.php';

header('Content-Type: application/json');

try {
    // Get the match ID and appointment details
    $matchId = $_POST['match_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $userId = $_SESSION['user_id'];

    // Check if the user is part of this match
    $stmt = $conn->prepare("SELECT * FROM matches 
                           WHERE id = :match_id 
                           AND (user1_id = :user_id OR user2_id = :user_id)");
    $stmt->execute([
        ':match_id' => $matchId,
        ':user_id' => $userId
    ]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Invalid match');
    }

    // Check if there's already an appointment
    $stmt = $conn->prepare("SELECT * FROM appointments 
                           WHERE match_id = :match_id");
    $stmt->execute([
        ':match_id' => $matchId
    ]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Appointment already scheduled');
    }

    // Insert the new appointment
    $stmt = $conn->prepare("INSERT INTO appointments (match_id, date, time, created_at) 
                           VALUES (:match_id, :date, :time, NOW())");
    
    $stmt->execute([
        ':match_id' => $matchId,
        ':date' => $date,
        ':time' => $time
    ]);

    // Update match status to active
    $stmt = $conn->prepare("UPDATE matches 
                           SET status = 'active', updated_at = NOW() 
                           WHERE id = :match_id");
    
    $stmt->execute([
        ':match_id' => $matchId
    ]);

    echo json_encode(['success' => true, 'message' => 'Appointment scheduled successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
