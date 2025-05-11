<?php
require_once 'SkillSwapDatabase.php';
$db = new Database();
$conn = $db->getConnection();

// Replace with the actual admin email and plain password
$email = 'jane.smith@example.com';
$plainPassword = '12345';

// Hash the password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Update the database
$stmt = $conn->prepare("UPDATE admins SET Password = ? WHERE Email = ?");
$stmt->execute([$hashedPassword, $email]);

echo "Password updated and hashed!";
?>