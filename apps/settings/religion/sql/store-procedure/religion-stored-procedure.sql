DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkReligionExist//
CREATE PROCEDURE checkReligionExist(
    IN p_religion_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM religion
    WHERE religion_id = p_religion_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveReligion//
CREATE PROCEDURE saveReligion(
    IN p_religion_id INT, 
    IN p_religion_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_religion_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_religion_id IS NULL OR NOT EXISTS (SELECT 1 FROM religion WHERE religion_id = p_religion_id) THEN
        INSERT INTO religion (religion_name, last_log_by) 
        VALUES(p_religion_name, p_last_log_by);
        
        SET p_new_religion_id = LAST_INSERT_ID();
    ELSE
        UPDATE religion
        SET religion_name = p_religion_name,
            last_log_by = p_last_log_by
        WHERE religion_id = p_religion_id;

        SET p_new_religion_id = p_religion_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteReligion//
CREATE PROCEDURE deleteReligion(
    IN p_religion_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM religion WHERE religion_id = p_religion_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getReligion//
CREATE PROCEDURE getReligion(
    IN p_religion_id INT
)
BEGIN
	SELECT * FROM religion
	WHERE religion_id = p_religion_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateReligionTable//
CREATE PROCEDURE generateReligionTable()
BEGIN
	SELECT religion_id, religion_name
    FROM religion 
    ORDER BY religion_id;
END //

DROP PROCEDURE IF EXISTS generateReligionOptions//
CREATE PROCEDURE generateReligionOptions()
BEGIN
	SELECT religion_id, religion_name 
    FROM religion 
    ORDER BY religion_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */