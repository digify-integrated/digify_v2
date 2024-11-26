DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkContactInformationTypeExist//
CREATE PROCEDURE checkContactInformationTypeExist(
    IN p_contact_information_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM contact_information_type
    WHERE contact_information_type_id = p_contact_information_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveContactInformationType//
CREATE PROCEDURE saveContactInformationType(
    IN p_contact_information_type_id INT, 
    IN p_contact_information_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_contact_information_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_contact_information_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM contact_information_type WHERE contact_information_type_id = p_contact_information_type_id) THEN
        INSERT INTO contact_information_type (contact_information_type_name, last_log_by) 
        VALUES(p_contact_information_type_name, p_last_log_by);
        
        SET p_new_contact_information_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE contact_information_type
        SET contact_information_type_name = p_contact_information_type_name,
            last_log_by = p_last_log_by
        WHERE contact_information_type_id = p_contact_information_type_id;

        SET p_new_contact_information_type_id = p_contact_information_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteContactInformationType//
CREATE PROCEDURE deleteContactInformationType(
    IN p_contact_information_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM contact_information_type WHERE contact_information_type_id = p_contact_information_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getContactInformationType//
CREATE PROCEDURE getContactInformationType(
    IN p_contact_information_type_id INT
)
BEGIN
	SELECT * FROM contact_information_type
	WHERE contact_information_type_id = p_contact_information_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateContactInformationTypeTable//
CREATE PROCEDURE generateContactInformationTypeTable()
BEGIN
	SELECT contact_information_type_id, contact_information_type_name
    FROM contact_information_type 
    ORDER BY contact_information_type_id;
END //

DROP PROCEDURE IF EXISTS generateContactInformationTypeOptions//
CREATE PROCEDURE generateContactInformationTypeOptions()
BEGIN
	SELECT contact_information_type_id, contact_information_type_name 
    FROM contact_information_type 
    ORDER BY contact_information_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */