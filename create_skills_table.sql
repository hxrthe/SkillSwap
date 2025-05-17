CREATE TABLE IF NOT EXISTS predefined_skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    skill_name VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL
);

-- Insert some common skills
INSERT INTO predefined_skills (skill_name, category) VALUES
('JavaScript', 'Programming'),
('Python', 'Programming'),
('Java', 'Programming'),
('HTML/CSS', 'Web Development'),
('React', 'Web Development'),
('Node.js', 'Web Development'),
('SQL', 'Database'),
('MySQL', 'Database'),
('MongoDB', 'Database'),
('Git', 'Version Control'),
('Docker', 'DevOps'),
('AWS', 'Cloud'),
('Photoshop', 'Design'),
('Illustrator', 'Design'),
('UI/UX Design', 'Design'),
('Project Management', 'Management'),
('Agile', 'Management'),
('Scrum', 'Management'),
('Digital Marketing', 'Marketing'),
('SEO', 'Marketing'),
('Content Writing', 'Writing'),
('Technical Writing', 'Writing'),
('Public Speaking', 'Communication'),
('Leadership', 'Soft Skills'),
('Problem Solving', 'Soft Skills'),
('Teamwork', 'Soft Skills');
