DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkLanguageProficiencyExist//
CREATE PROCEDURE checkLanguageProficiencyExist(
    IN p_language_proficiency_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM language_proficiency
    WHERE language_proficiency_id = p_language_proficiency_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getLanguageProficiency//
CREATE PROCEDURE getLanguageProficiency(
    IN p_language_proficiency_id INT
)
BEGIN
	SELECT * FROM language_proficiency
	WHERE language_proficiency_id = p_language_proficiency_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveLanguageProficiency//
CREATE PROCEDURE saveLanguageProficiency(
    IN p_language_proficiency_id INT, 
    IN p_language_proficiency_name VARCHAR(100), 
    IN p_language_proficiency_description VARCHAR(200),
    IN p_last_log_by INT, 
    OUT p_new_language_proficiency_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_language_proficiency_id IS NULL OR NOT EXISTS (SELECT 1 FROM language_proficiency WHERE language_proficiency_id = p_language_proficiency_id) THEN
        INSERT INTO language_proficiency (language_proficiency_name, language_proficiency_description, last_log_by) 
        VALUES(p_language_proficiency_name, p_language_proficiency_description, p_last_log_by);
        
        SET p_new_language_proficiency_id = LAST_INSERT_ID();
    ELSE        
        UPDATE language_proficiency
        SET language_proficiency_name = p_language_proficiency_name,
            language_proficiency_description = p_language_proficiency_description,
            last_log_by = p_last_log_by
        WHERE language_proficiency_id = p_language_proficiency_id;

        SET p_new_language_proficiency_id = p_language_proficiency_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteLanguageProficiency//
CREATE PROCEDURE deleteLanguageProficiency(
    IN p_language_proficiency_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM language_proficiency WHERE language_proficiency_id = p_language_proficiency_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateLanguageProficiencyTable//
CREATE PROCEDURE generateLanguageProficiencyTable()
BEGIN
    SELECT language_proficiency_id, language_proficiency_name, language_proficiency_description 
    FROM language_proficiency
    ORDER BY language_proficiency_id;
END //

DROP PROCEDURE IF EXISTS generateLanguageProficiencyOptions//
CREATE PROCEDURE generateLanguageProficiencyOptions()
BEGIN
    SELECT language_proficiency_id, language_proficiency_name 
    FROM language_proficiency 
    ORDER BY language_proficiency_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */