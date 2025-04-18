<?php
require_once 'SkillSwapDatabase.php';

class Crud {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser2($username, $email, $password) {
        $stmt = $this->conn->prepare("CALL createUser2(:username, :email, :password)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $password // not hashed yet
        ]);
    }
}
?>
