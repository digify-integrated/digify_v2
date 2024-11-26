DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkBankExist//
CREATE PROCEDURE checkBankExist(
    IN p_bank_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM bank
    WHERE bank_id = p_bank_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getBank//
CREATE PROCEDURE getBank(
    IN p_bank_id INT
)
BEGIN
	SELECT * FROM bank
	WHERE bank_id = p_bank_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveBank//
CREATE PROCEDURE saveBank(
    IN p_bank_id INT, 
    IN p_bank_name VARCHAR(100), 
    IN p_bank_identifier_code VARCHAR(100),
    IN p_last_log_by INT, 
    OUT p_new_bank_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_bank_id IS NULL OR NOT EXISTS (SELECT 1 FROM bank WHERE bank_id = p_bank_id) THEN
        INSERT INTO bank (bank_name, bank_identifier_code, last_log_by) 
        VALUES(p_bank_name, p_bank_identifier_code, p_last_log_by);
        
        SET p_new_bank_id = LAST_INSERT_ID();
    ELSE        
        UPDATE bank
        SET bank_name = p_bank_name,
            bank_identifier_code = p_bank_identifier_code,
            last_log_by = p_last_log_by
        WHERE bank_id = p_bank_id;

        SET p_new_bank_id = p_bank_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteBank//
CREATE PROCEDURE deleteBank(
    IN p_bank_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM bank WHERE bank_id = p_bank_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateBankTable//
CREATE PROCEDURE generateBankTable()
BEGIN
    SELECT bank_id, bank_name, bank_identifier_code FROM bank;
END //

DROP PROCEDURE IF EXISTS generateBankOptions//
CREATE PROCEDURE generateBankOptions()
BEGIN
    SELECT bank_id, bank_name
    FROM bank 
    ORDER BY bank_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */