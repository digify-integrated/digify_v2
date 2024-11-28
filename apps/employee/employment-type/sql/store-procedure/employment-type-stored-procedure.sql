DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkEmploymentTypeExist//
CREATE PROCEDURE checkEmploymentTypeExist(
    IN p_employment_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM employment_type
    WHERE employment_type_id = p_employment_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveEmploymentType//
CREATE PROCEDURE saveEmploymentType(
    IN p_employment_type_id INT, 
    IN p_employment_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_employment_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_employment_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM employment_type WHERE employment_type_id = p_employment_type_id) THEN
        INSERT INTO employment_type (employment_type_name, last_log_by) 
        VALUES(p_employment_type_name, p_last_log_by);
        
        SET p_new_employment_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE employment_type
        SET employment_type_name = p_employment_type_name,
            last_log_by = p_last_log_by
        WHERE employment_type_id = p_employment_type_id;

        SET p_new_employment_type_id = p_employment_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteEmploymentType//
CREATE PROCEDURE deleteEmploymentType(
    IN p_employment_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM employment_type WHERE employment_type_id = p_employment_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getEmploymentType//
CREATE PROCEDURE getEmploymentType(
    IN p_employment_type_id INT
)
BEGIN
	SELECT * FROM employment_type
	WHERE employment_type_id = p_employment_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateEmploymentTypeTable//
CREATE PROCEDURE generateEmploymentTypeTable()
BEGIN
	SELECT employment_type_id, employment_type_name
    FROM employment_type 
    ORDER BY employment_type_id;
END //

DROP PROCEDURE IF EXISTS generateEmploymentTypeOptions//
CREATE PROCEDURE generateEmploymentTypeOptions()
BEGIN
	SELECT employment_type_id, employment_type_name 
    FROM employment_type 
    ORDER BY employment_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */