<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Start buffering output
ob_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set response type
header('Content-Type: application/json');

try {
    // Check for autoloader
    if (!file_exists('vendor/autoload.php')) {
        throw new Exception("Composer autoloader not found. Please run 'composer install' first.");
    }

    require 'vendor/autoload.php';
    require_once 'SkillSwapDatabase.php';

    if (isset($_POST["email"]) && isset($_POST["subject"]) && isset($_POST["message"]) && isset($_POST["verification_code"])) {
        if (empty($_POST["email"]) || empty($_POST["subject"]) || empty($_POST["message"]) || empty($_POST["verification_code"])) {
            throw new Exception("Please fill in all fields.");
        }
        
        // Store verification code in session
        session_start();
        $_SESSION['temp_verification_code'] = $_POST["verification_code"];
        $_SESSION['temp_verification_email'] = $_POST["email"];
        
        $mail = new PHPMailer(true);

        // Email server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'mr.anonymous0852@gmail.com';
        $mail->Password   = 'rnle ydop jdid txcy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->SMTPDebug  = 0;

        // Set email details
        $mail->setFrom('mr.anonymous0852@gmail.com', 'SkillSwap');
        $mail->addAddress($_POST["email"]);
        $mail->isHTML(true);
        $mail->Subject = $_POST["subject"];
        $mail->Body    = $_POST["message"];

        if ($mail->send()) {
            $response = ['status' => 'success', 'message' => 'Email sent successfully!'];
        } else {
            throw new Exception("Failed to send email: " . $mail->ErrorInfo);
        }

    } else {
        throw new Exception("Missing required fields: " . 
            (empty($_POST["email"]) ? 'email ' : '') . 
            (empty($_POST["subject"]) ? 'subject ' : '') . 
            (empty($_POST["message"]) ? 'message ' : '') .
            (empty($_POST["verification_code"]) ? 'verification_code' : ''));
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

// Clear buffer and send response
ob_end_clean();
echo json_encode($response);
exit;
?>