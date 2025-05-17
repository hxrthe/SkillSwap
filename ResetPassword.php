<?php
require_once 'SkillSwapDatabase.php';
require_once 'SP.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $verificationCode = $_POST['verification_code'] ?? '';

    // Validate inputs
    if (empty($email) || empty($newPassword) || empty($verificationCode)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // First verify that the email exists and the verification code matches
        $stmt = $conn->prepare("SELECT * FROM users WHERE Email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'Email not found']);
            exit;
        }

        // Verify the code matches the one in session
        session_start();
        if (!isset($_SESSION['temp_verification_code']) || 
            !isset($_SESSION['temp_verification_email']) || 
            $_SESSION['temp_verification_email'] !== $email || 
            $_SESSION['temp_verification_code'] !== $verificationCode) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
            exit;
        }

        // Update the password using the stored procedure
        $stmt = $conn->prepare("CALL updatePassword(?, ?)");
        $stmt->execute([$email, $newPassword]);

        // Clear the verification code from session
        unset($_SESSION['temp_verification_code']);
        unset($_SESSION['temp_verification_email']);

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
} 