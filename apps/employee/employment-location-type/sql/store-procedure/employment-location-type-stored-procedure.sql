DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkEmploymentLocationTypeExist//
CREATE PROCEDURE checkEmploymentLocationTypeExist(
    IN p_employment_location_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM employment_location_type
    WHERE employment_location_type_id = p_employment_location_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveEmploymentLocationType//
CREATE PROCEDURE saveEmploymentLocationType(
    IN p_employment_location_type_id INT, 
    IN p_employment_location_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_employment_location_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_employment_location_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM employment_location_type WHERE employment_location_type_id = p_employment_location_type_id) THEN
        INSERT INTO employment_location_type (employment_location_type_name, last_log_by) 
        VALUES(p_employment_location_type_name, p_last_log_by);
        
        SET p_new_employment_location_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE employment_location_type
        SET employment_location_type_name = p_employment_location_type_name,
            last_log_by = p_last_log_by
        WHERE employment_location_type_id = p_employment_location_type_id;

        SET p_new_employment_location_type_id = p_employment_location_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteEmploymentLocationType//
CREATE PROCEDURE deleteEmploymentLocationType(
    IN p_employment_location_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM employment_location_type WHERE employment_location_type_id = p_employment_location_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getEmploymentLocationType//
CREATE PROCEDURE getEmploymentLocationType(
    IN p_employment_location_type_id INT
)
BEGIN
	SELECT * FROM employment_location_type
	WHERE employment_location_type_id = p_employment_location_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateEmploymentLocationTypeTable//
CREATE PROCEDURE generateEmploymentLocationTypeTable()
BEGIN
	SELECT employment_location_type_id, employment_location_type_name
    FROM employment_location_type 
    ORDER BY employment_location_type_id;
END //

DROP PROCEDURE IF EXISTS generateEmploymentLocationTypeOptions//
CREATE PROCEDURE generateEmploymentLocationTypeOptions()
BEGIN
	SELECT employment_location_type_id, employment_location_type_name 
    FROM employment_location_type 
    ORDER BY employment_location_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */