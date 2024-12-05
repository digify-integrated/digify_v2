DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkLanguageExist//
CREATE PROCEDURE checkLanguageExist(
    IN p_language_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM language
    WHERE language_id = p_language_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveLanguage//
CREATE PROCEDURE saveLanguage(
    IN p_language_id INT, 
    IN p_language_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_language_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_language_id IS NULL OR NOT EXISTS (SELECT 1 FROM language WHERE language_id = p_language_id) THEN
        INSERT INTO language (language_name, last_log_by) 
        VALUES(p_language_name, p_last_log_by);
        
        SET p_new_language_id = LAST_INSERT_ID();
    ELSE
        UPDATE language
        SET language_name = p_language_name,
            last_log_by = p_last_log_by
        WHERE language_id = p_language_id;

        SET p_new_language_id = p_language_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteLanguage//
CREATE PROCEDURE deleteLanguage(
    IN p_language_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM language WHERE language_id = p_language_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getLanguage//
CREATE PROCEDURE getLanguage(
    IN p_language_id INT
)
BEGIN
	SELECT * FROM language
	WHERE language_id = p_language_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateLanguageTable//
CREATE PROCEDURE generateLanguageTable()
BEGIN
	SELECT language_id, language_name
    FROM language 
    ORDER BY language_id;
END //

DROP PROCEDURE IF EXISTS generateLanguageOptions//
CREATE PROCEDURE generateLanguageOptions()
BEGIN
	SELECT language_id, language_name 
    FROM language 
    ORDER BY language_name;
END //

DROP PROCEDURE IF EXISTS generateEmployeeLanguageOptions//
CREATE PROCEDURE generateEmployeeLanguageOptions(
    IN p_employee_id INT
)
BEGIN
	SELECT language_id, language_name 
    FROM language
    WHERE language_id NOT IN (SELECT language_id FROM employee_language WHERE employee_id = p_employee_id)
    ORDER BY language_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */