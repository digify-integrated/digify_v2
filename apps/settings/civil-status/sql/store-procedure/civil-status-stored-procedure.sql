DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkCivilStatusExist//
CREATE PROCEDURE checkCivilStatusExist(
    IN p_civil_status_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM civil_status
    WHERE civil_status_id = p_civil_status_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveCivilStatus//
CREATE PROCEDURE saveCivilStatus(
    IN p_civil_status_id INT, 
    IN p_civil_status_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_civil_status_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_civil_status_id IS NULL OR NOT EXISTS (SELECT 1 FROM civil_status WHERE civil_status_id = p_civil_status_id) THEN
        INSERT INTO civil_status (civil_status_name, last_log_by) 
        VALUES(p_civil_status_name, p_last_log_by);
        
        SET p_new_civil_status_id = LAST_INSERT_ID();
    ELSE
        UPDATE civil_status
        SET civil_status_name = p_civil_status_name,
            last_log_by = p_last_log_by
        WHERE civil_status_id = p_civil_status_id;

        SET p_new_civil_status_id = p_civil_status_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteCivilStatus//
CREATE PROCEDURE deleteCivilStatus(
    IN p_civil_status_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM civil_status WHERE civil_status_id = p_civil_status_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getCivilStatus//
CREATE PROCEDURE getCivilStatus(
    IN p_civil_status_id INT
)
BEGIN
	SELECT * FROM civil_status
	WHERE civil_status_id = p_civil_status_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateCivilStatusTable//
CREATE PROCEDURE generateCivilStatusTable()
BEGIN
	SELECT civil_status_id, civil_status_name
    FROM civil_status 
    ORDER BY civil_status_id;
END //

DROP PROCEDURE IF EXISTS generateCivilStatusOptions//
CREATE PROCEDURE generateCivilStatusOptions()
BEGIN
	SELECT civil_status_id, civil_status_name 
    FROM civil_status 
    ORDER BY civil_status_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */