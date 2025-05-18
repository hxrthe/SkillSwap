<<<<<<< HEAD
<?php
require_once 'SkillSwapDatabase.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle verification request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify') {
    $email = $_POST['email'];
    $code = $_POST['code'];
    
    $verification = new EmailVerification();
    if ($verification->verifyEmail($email, $code)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
    }
    exit;
}

class EmailVerification {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function generateVerificationCode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function storeVerificationCode($email, $code) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET Verification_Code = :code WHERE Email = :email");
            return $stmt->execute([
                ':code' => $code,
                ':email' => $email
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyEmail($email, $code) {
        try {
            // Start a session to access the stored verification code
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Check if the code matches the one stored in session
            if (isset($_SESSION['temp_verification_code']) && 
                isset($_SESSION['temp_verification_email']) && 
                $_SESSION['temp_verification_email'] === $email && 
                $_SESSION['temp_verification_code'] === $code) {
                
                // Update Is_Verified in database if the user exists
                $stmt = $this->conn->prepare("UPDATE users SET Is_Verified = 1 WHERE Email = :email");
                return $stmt->execute([':email' => $email]);
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function sendVerificationEmail($email, $code) {
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mr.anonymous0852@gmail.com';
            $mail->Password   = 'rnle ydop jdid txcy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('mr.anonymous0852@gmail.com', 'SkillSwap');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'SkillSwap Email Verification';
            $mail->Body    = "Your verification code is: <b>{$code}</b>";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
=======
<?php
require_once 'SkillSwapDatabase.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle verification request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify') {
    $email = $_POST['email'];
    $code = $_POST['code'];
    
    $verification = new EmailVerification();
    if ($verification->verifyEmail($email, $code)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid verification code']);
    }
    exit;
}

class EmailVerification {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function generateVerificationCode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function storeVerificationCode($email, $code) {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET Verification_Code = :code WHERE Email = :email");
            return $stmt->execute([
                ':code' => $code,
                ':email' => $email
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyEmail($email, $code) {
        try {
            // Start a session to access the stored verification code
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Check if the code matches the one stored in session
            if (isset($_SESSION['temp_verification_code']) && 
                isset($_SESSION['temp_verification_email']) && 
                $_SESSION['temp_verification_email'] === $email && 
                $_SESSION['temp_verification_code'] === $code) {
                
                // Update Is_Verified in database if the user exists
                $stmt = $this->conn->prepare("UPDATE users SET Is_Verified = 1 WHERE Email = :email");
                return $stmt->execute([':email' => $email]);
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function sendVerificationEmail($email, $code) {
        try {
            $mail = new PHPMailer(true);

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'mr.anonymous0852@gmail.com';
            $mail->Password   = 'rnle ydop jdid txcy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('mr.anonymous0852@gmail.com', 'SkillSwap');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'SkillSwap Email Verification';
            $mail->Body    = "Your verification code is: <b>{$code}</b>";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
>>>>>>> maris
?> 