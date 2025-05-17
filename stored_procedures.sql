-- Create the predefined_skills table
CREATE TABLE IF NOT EXISTS predefined_skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Stored procedure to get all predefined skills
DELIMITER //
CREATE PROCEDURE GetPredefinedSkills()
BEGIN
    SELECT * FROM predefined_skills ORDER BY category, skill_name;
END //
DELIMITER ;

-- Stored procedure to add a new skill
DELIMITER //
CREATE PROCEDURE AddPredefinedSkill(
    IN p_category VARCHAR(50),
    IN p_category_name VARCHAR(100),
    IN p_skill_name VARCHAR(100),
    IN p_description TEXT
)
BEGIN
    INSERT INTO predefined_skills (category, category_name, skill_name, description)
    VALUES (p_category, p_category_name, p_skill_name, p_description);
END //
DELIMITER ;

-- Stored procedure to update a skill
DELIMITER //
CREATE PROCEDURE UpdatePredefinedSkill(
    IN p_id INT,
    IN p_category VARCHAR(50),
    IN p_category_name VARCHAR(100),
    IN p_skill_name VARCHAR(100),
    IN p_description TEXT
)
BEGIN
    UPDATE predefined_skills 
    SET category = p_category,
        category_name = p_category_name,
        skill_name = p_skill_name,
        description = p_description
    WHERE id = p_id;
END //
DELIMITER ;

-- Stored procedure to delete a skill
DELIMITER //
CREATE PROCEDURE DeletePredefinedSkill(
    IN p_id INT
)
BEGIN
    DELETE FROM predefined_skills WHERE id = p_id;
END //
DELIMITER ;

-- Insert some sample data
CALL AddPredefinedSkill('programming', 'Programming', 'PHP', 'Server-side scripting language');
CALL AddPredefinedSkill('programming', 'Programming', 'JavaScript', 'Client-side programming language');
CALL AddPredefinedSkill('design', 'Design', 'Photoshop', 'Image editing and design');
CALL AddPredefinedSkill('design', 'Design', 'Illustrator', 'Vector graphics design');
CALL AddPredefinedSkill('business', 'Business', 'Marketing', 'Digital marketing strategies');
CALL AddPredefinedSkill('business', 'Business', 'Project Management', 'Project planning and execution');
