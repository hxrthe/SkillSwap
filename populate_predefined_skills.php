<?php
require_once 'SkillSwapDatabase.php';

// Sample predefined skills data
$skills = [
    ['category' => 'Programming', 'skill_name' => 'PHP', 'description' => 'Server-side scripting language'],
    ['category' => 'Programming', 'skill_name' => 'JavaScript', 'description' => 'Client-side programming language'],
    ['category' => 'Programming', 'skill_name' => 'Python', 'description' => 'General-purpose programming language'],
    ['category' => 'Web Development', 'skill_name' => 'HTML/CSS', 'description' => 'Web page structure and styling'],
    ['category' => 'Web Development', 'skill_name' => 'React', 'description' => 'JavaScript library for building user interfaces'],
    ['category' => 'Web Development', 'skill_name' => 'Node.js', 'description' => 'JavaScript runtime environment'],
    ['category' => 'Design', 'skill_name' => 'Photoshop', 'description' => 'Image editing software'],
    ['category' => 'Design', 'skill_name' => 'Illustrator', 'description' => 'Vector graphics editor'],
    ['category' => 'Design', 'skill_name' => 'UI/UX Design', 'description' => 'User interface and experience design'],
    ['category' => 'Business', 'skill_name' => 'Digital Marketing', 'description' => 'Online marketing strategies'],
    ['category' => 'Business', 'skill_name' => 'SEO', 'description' => 'Search engine optimization'],
    ['category' => 'Business', 'skill_name' => 'Social Media Management', 'description' => 'Social media strategy and execution']
];

try {
    $stmt = $conn->prepare("INSERT INTO predefined_skills (category, skill_name, description) VALUES (?, ?, ?)");
    
    foreach ($skills as $skill) {
        $stmt->execute([
            $skill['category'],
            $skill['skill_name'],
            $skill['description']
        ]);
    }
    
    echo "Skills have been successfully populated!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
