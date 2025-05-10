-- Create the predefined_skills table
CREATE TABLE IF NOT EXISTS predefined_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create stored procedure to get predefined skills
DELIMITER //
CREATE PROCEDURE GetPredefinedSkills()
BEGIN
    SELECT * FROM predefined_skills ORDER BY category, skill_name;
END //
DELIMITER ;

-- Insert some sample data
INSERT INTO predefined_skills (category, category_name, skill_name, description) VALUES
('programming', 'Programming', 'PHP', 'Server-side scripting language'),
('programming', 'Programming', 'JavaScript', 'Client-side programming language'),
('design', 'Design', 'Photoshop', 'Image editing and design'),
('design', 'Design', 'Illustrator', 'Vector graphics design'),
('business', 'Business', 'Marketing', 'Digital marketing strategies'),
('business', 'Business', 'Project Management', 'Project planning and execution');
