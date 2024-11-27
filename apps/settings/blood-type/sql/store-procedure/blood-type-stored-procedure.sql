DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkBloodTypeExist//
CREATE PROCEDURE checkBloodTypeExist(
    IN p_blood_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM blood_type
    WHERE blood_type_id = p_blood_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveBloodType//
CREATE PROCEDURE saveBloodType(
    IN p_blood_type_id INT, 
    IN p_blood_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_blood_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_blood_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM blood_type WHERE blood_type_id = p_blood_type_id) THEN
        INSERT INTO blood_type (blood_type_name, last_log_by) 
        VALUES(p_blood_type_name, p_last_log_by);
        
        SET p_new_blood_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE blood_type
        SET blood_type_name = p_blood_type_name,
            last_log_by = p_last_log_by
        WHERE blood_type_id = p_blood_type_id;

        SET p_new_blood_type_id = p_blood_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteBloodType//
CREATE PROCEDURE deleteBloodType(
    IN p_blood_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM blood_type WHERE blood_type_id = p_blood_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getBloodType//
CREATE PROCEDURE getBloodType(
    IN p_blood_type_id INT
)
BEGIN
	SELECT * FROM blood_type
	WHERE blood_type_id = p_blood_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateBloodTypeTable//
CREATE PROCEDURE generateBloodTypeTable()
BEGIN
	SELECT blood_type_id, blood_type_name
    FROM blood_type 
    ORDER BY blood_type_id;
END //

DROP PROCEDURE IF EXISTS generateBloodTypeOptions//
CREATE PROCEDURE generateBloodTypeOptions()
BEGIN
	SELECT blood_type_id, blood_type_name 
    FROM blood_type 
    ORDER BY blood_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */