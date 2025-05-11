DELIMITER //

CREATE PROCEDURE SaveUserSkills(
    IN user_id INT,
    IN can_share_json JSON,
    IN want_to_learn_json JSON
)
BEGIN
    -- Delete existing skills for the user
    DELETE FROM user_skills WHERE user_id = user_id;

    -- Insert can_share skills
    IF JSON_LENGTH(can_share_json) > 0 THEN
        INSERT INTO user_skills (user_id, skill_id, skill_type)
        SELECT 
            user_id,
            predefined_skills.id,
            'can_share'
        FROM JSON_TABLE(
            can_share_json,
            "$[*]"
            COLUMNS(
                skill_name VARCHAR(255) PATH "$"
            )
        ) as skills
        JOIN predefined_skills ON predefined_skills.skill_name = skills.skill_name;
    END IF;

    -- Insert want_to_learn skills
    IF JSON_LENGTH(want_to_learn_json) > 0 THEN
        INSERT INTO user_skills (user_id, skill_id, skill_type)
        SELECT 
            user_id,
            predefined_skills.id,
            'want_to_learn'
        FROM JSON_TABLE(
            want_to_learn_json,
            "$[*]"
            COLUMNS(
                skill_name VARCHAR(255) PATH "$"
            )
        ) as skills
        JOIN predefined_skills ON predefined_skills.skill_name = skills.skill_name;
    END IF;

END //

DELIMITER ;
