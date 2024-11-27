DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkCredentialTypeExist//
CREATE PROCEDURE checkCredentialTypeExist(
    IN p_credential_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM credential_type
    WHERE credential_type_id = p_credential_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveCredentialType//
CREATE PROCEDURE saveCredentialType(
    IN p_credential_type_id INT, 
    IN p_credential_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_credential_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_credential_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM credential_type WHERE credential_type_id = p_credential_type_id) THEN
        INSERT INTO credential_type (credential_type_name, last_log_by) 
        VALUES(p_credential_type_name, p_last_log_by);
        
        SET p_new_credential_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE credential_type
        SET credential_type_name = p_credential_type_name,
            last_log_by = p_last_log_by
        WHERE credential_type_id = p_credential_type_id;

        SET p_new_credential_type_id = p_credential_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteCredentialType//
CREATE PROCEDURE deleteCredentialType(
    IN p_credential_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM credential_type WHERE credential_type_id = p_credential_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getCredentialType//
CREATE PROCEDURE getCredentialType(
    IN p_credential_type_id INT
)
BEGIN
	SELECT * FROM credential_type
	WHERE credential_type_id = p_credential_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateCredentialTypeTable//
CREATE PROCEDURE generateCredentialTypeTable()
BEGIN
	SELECT credential_type_id, credential_type_name
    FROM credential_type 
    ORDER BY credential_type_id;
END //

DROP PROCEDURE IF EXISTS generateCredentialTypeOptions//
CREATE PROCEDURE generateCredentialTypeOptions()
BEGIN
	SELECT credential_type_id, credential_type_name 
    FROM credential_type 
    ORDER BY credential_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */