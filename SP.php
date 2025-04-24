<?php
require_once 'SkillSwapDatabase.php';

class Crud {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser($firstname, $lastname, $email, $password, $verificationCode, $isVerified = false) {
        try {
            // Call the stored procedure
            $stmt = $this->conn->prepare("CALL CreateUser(?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $firstname,      // p_firstname
                $lastname,       // p_lastname
                $email,         // p_email
                $password,      // p_password
                $verificationCode, // p_verification_code
                $isVerified ? 1 : 0  // p_is_verified
            ]);

        } catch (PDOException $e) {
            // Check for the custom error from the stored procedure
            if ($e->getCode() == '45000') {
                throw new Exception('email_exists');
            } else {
                throw $e;
            }
        }    
    }

    
    
}
?>
