<?php
require_once 'SkillSwapDatabase.php';

$db = new Database();
$conn = $db->getConnection();

try {
    // First check if the table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'requests'");
    $stmt->execute();
    $exists = $stmt->fetch();

    if ($exists) {
        echo "Match requests table already exists.\n";
    } else {
        // Create match_requests table
        $sql = "CREATE TABLE IF NOT EXISTS match_requests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
            message TEXT,
            appointment_date DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(User_ID),
            FOREIGN KEY (receiver_id) REFERENCES users(User_ID),
            UNIQUE KEY unique_request (sender_id, receiver_id)
        )";

        $conn->exec($sql);
        echo "Match requests table created successfully!\n";
    }

    // Verify table creation
    $stmt = $conn->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'skillswap' AND table_name = 'requests'");
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "Table verification successful.\n";
    } else {
        throw new PDOException("Table creation verification failed.");
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Try to drop the table if it exists and recreate it
    try {
        $conn->exec("DROP TABLE IF EXISTS requests");
        echo "Dropped existing table. Attempting to recreate...\n";
        
        // Recreate the table
        $sql = "CREATE TABLE IF NOT EXISTS match_requests (
            id INT PRIMARY KEY AUTO_INCREMENT,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
            message TEXT,
            appointment_date DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(User_ID),
            FOREIGN KEY (receiver_id) REFERENCES users(User_ID),
            UNIQUE KEY unique_request (sender_id, receiver_id)
        )";

        $conn->exec($sql);
        echo "Table recreated successfully!\n";
    } catch (PDOException $e2) {
        echo "Error recreating table: " . $e2->getMessage() . "\n";
    }
}
?>
