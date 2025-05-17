
<?php
session_start();
require_once 'SkillSwapDatabase.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT r.id, r.message, r.status, r.created_at, u.User_ID AS sender_id, u.First_Name AS sender_name, u.Last_Name AS sender_last_name
    FROM requests r
    JOIN users u ON u.User_ID = r.sender_id
    WHERE r.receiver_id = :receiver_id
    AND r.status = 'pending'
    ORDER BY r.created_at DESC
");
$stmt->execute([':receiver_id' => $_SESSION['user_id']]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['requests' => $requests]);