DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkGenderExist//
CREATE PROCEDURE checkGenderExist(
    IN p_gender_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM gender
    WHERE gender_id = p_gender_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveGender//
CREATE PROCEDURE saveGender(
    IN p_gender_id INT, 
    IN p_gender_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_gender_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_gender_id IS NULL OR NOT EXISTS (SELECT 1 FROM gender WHERE gender_id = p_gender_id) THEN
        INSERT INTO gender (gender_name, last_log_by) 
        VALUES(p_gender_name, p_last_log_by);
        
        SET p_new_gender_id = LAST_INSERT_ID();
    ELSE
        UPDATE gender
        SET gender_name = p_gender_name,
            last_log_by = p_last_log_by
        WHERE gender_id = p_gender_id;

        SET p_new_gender_id = p_gender_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteGender//
CREATE PROCEDURE deleteGender(
    IN p_gender_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM gender WHERE gender_id = p_gender_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getGender//
CREATE PROCEDURE getGender(
    IN p_gender_id INT
)
BEGIN
	SELECT * FROM gender
	WHERE gender_id = p_gender_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateGenderTable//
CREATE PROCEDURE generateGenderTable()
BEGIN
	SELECT gender_id, gender_name
    FROM gender 
    ORDER BY gender_id;
END //

DROP PROCEDURE IF EXISTS generateGenderOptions//
CREATE PROCEDURE generateGenderOptions()
BEGIN
	SELECT gender_id, gender_name 
    FROM gender 
    ORDER BY gender_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */