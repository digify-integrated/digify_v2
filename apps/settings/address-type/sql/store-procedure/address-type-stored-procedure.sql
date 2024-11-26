DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkAddressTypeExist//
CREATE PROCEDURE checkAddressTypeExist(
    IN p_address_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM address_type
    WHERE address_type_id = p_address_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveAddressType//
CREATE PROCEDURE saveAddressType(
    IN p_address_type_id INT, 
    IN p_address_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_address_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_address_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM address_type WHERE address_type_id = p_address_type_id) THEN
        INSERT INTO address_type (address_type_name, last_log_by) 
        VALUES(p_address_type_name, p_last_log_by);
        
        SET p_new_address_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE address_type
        SET address_type_name = p_address_type_name,
            last_log_by = p_last_log_by
        WHERE address_type_id = p_address_type_id;

        SET p_new_address_type_id = p_address_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteAddressType//
CREATE PROCEDURE deleteAddressType(
    IN p_address_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM address_type WHERE address_type_id = p_address_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getAddressType//
CREATE PROCEDURE getAddressType(
    IN p_address_type_id INT
)
BEGIN
	SELECT * FROM address_type
	WHERE address_type_id = p_address_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateAddressTypeTable//
CREATE PROCEDURE generateAddressTypeTable()
BEGIN
	SELECT address_type_id, address_type_name
    FROM address_type 
    ORDER BY address_type_id;
END //

DROP PROCEDURE IF EXISTS generateAddressTypeOptions//
CREATE PROCEDURE generateAddressTypeOptions()
BEGIN
	SELECT address_type_id, address_type_name 
    FROM address_type 
    ORDER BY address_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */