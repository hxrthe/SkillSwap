<?php
require_once 'SkillSwapDatabase.php';

class Crud {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser($firstname, $lastname, $email, $password) {
        $stmt = $this->conn->prepare("CALL createUser(:first_name, :last_name, :email, :password)");
        $stmt->execute([
            ':first_name' => $firstname,
            ':last_name' => $lastname,
            ':email' => $email,
            ':password' => $password // not hashed yet
        ]);
    }
}
?>
