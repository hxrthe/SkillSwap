-- Drop existing procedures if they exist
DROP PROCEDURE IF EXISTS skillswap.SaveUserSkills;
DROP PROCEDURE IF EXISTS skillswap.GetUserSkills;
DROP PROCEDURE IF EXISTS skillswap.UpdateUserProfile;
DROP PROCEDURE IF EXISTS skillswap.UpdateUserPassword;
DROP PROCEDURE IF EXISTS skillswap.UpdateUserTheme;

-- Select the database
USE skillswap;

DELIMITER //

-- Create procedure to save skills
CREATE PROCEDURE skillswap.SaveUserSkills(
    IN p_user_id INT,
    IN p_can_share_skills TEXT,
    IN p_want_to_learn_skills TEXT
)
BEGIN
    -- Delete existing skills for the user
    DELETE FROM skillswap.user_skills WHERE user_id = p_user_id;

    -- Insert new skills
    INSERT INTO skillswap.user_skills (user_id, skill_type, skills)
    VALUES 
        (p_user_id, 'can_share', p_can_share_skills),
        (p_user_id, 'want_to_learn', p_want_to_learn_skills);
END //

-- Create procedure to get user skills
CREATE PROCEDURE skillswap.GetUserSkills(
    IN p_user_id INT
)
BEGIN
    SELECT 
        can_share_skills,
        want_to_learn_skills
    FROM skillswap.user_skills
    WHERE user_id = p_user_id;
END //

-- Create procedure to update user profile
CREATE PROCEDURE skillswap.UpdateUserProfile(
    IN p_user_id INT,
    IN p_first_name VARCHAR(50),
    IN p_last_name VARCHAR(50),
    IN p_email VARCHAR(100)
)
BEGIN
    UPDATE skillswap.users 
    SET 
        First_Name = p_first_name,
        Last_Name = p_last_name,
        Email = p_email
    WHERE User_ID = p_user_id;
END //

-- Create procedure to update user password
CREATE PROCEDURE skillswap.UpdateUserPassword(
    IN p_user_id INT,
    IN p_current_password VARCHAR(255),
    IN p_new_password VARCHAR(255)
)
BEGIN
    DECLARE current_password VARCHAR(255);
    DECLARE password_match BOOLEAN;
    
    -- Get current password
    SELECT Password INTO current_password 
    FROM skillswap.users 
    WHERE User_ID = p_user_id;
    
    -- Verify current password
    SET password_match = password_verify(p_current_password, current_password);
    
    IF password_match THEN
        -- Update password
        UPDATE skillswap.users 
        SET Password = password_hash(p_new_password, PASSWORD_DEFAULT)
        WHERE User_ID = p_user_id;
        
        SELECT 'Password updated successfully' as message;
    ELSE
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Current password is incorrect';
    END IF;
END //

-- Create procedure to update user theme
CREATE PROCEDURE skillswap.UpdateUserTheme(
    IN p_user_id INT,
    IN p_theme_preference VARCHAR(10)
)
BEGIN
    UPDATE skillswap.users 
    SET Theme_Preference = p_theme_preference
    WHERE User_ID = p_user_id;
END //

DELIMITER ;
