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
                $firstname,      
                $lastname,      
                $email,        
                $password,     
                $verificationCode, 
                $isVerified ? 1 : 0  
            ]);

        } catch (PDOException $e) {
           
            if ($e->getCode() == '45000') {
                throw new Exception('email_exists');
            } else {
                throw $e;
            }
        }    
    }

    public function updatePassword($email, $newPassword) {
        $stmt = $this->conn->prepare("CALL updatePassword(:email, :password)");
        return $stmt->execute([
            ':email' => $email,
            ':password' => $newPassword
        ]);
    }
    
    
}
?>