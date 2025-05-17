<?php
require_once 'SkillSwapDatabase.php';

try {
    // Create database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    // Test connection
    echo "Database connection successful!\n";
    
    // Create table
    $sql = "CREATE TABLE IF NOT EXISTS predefined_skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category VARCHAR(50) NOT NULL,
        category_name VARCHAR(100) NOT NULL,
        skill_name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $conn->exec($sql);
    echo "Table created successfully!\n";
    
    // Insert sample data
    $sql = "INSERT INTO predefined_skills (category, category_name, skill_name, description) VALUES
        ('programming', 'Programming', 'PHP', 'Server-side scripting language'),
        ('programming', 'Programming', 'JavaScript', 'Client-side programming language'),
        ('design', 'Design', 'Photoshop', 'Image editing and design'),
        ('design', 'Design', 'Illustrator', 'Vector graphics design'),
        ('business', 'Business', 'Marketing', 'Digital marketing strategies'),
        ('business', 'Business', 'Project Management', 'Project planning and execution')";
    
    $conn->exec($sql);
    echo "Sample data inserted successfully!\n";
    
    // Verify data
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM predefined_skills");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Number of records in table: " . $result['count'] . "\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
