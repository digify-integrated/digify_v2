DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkBankAccountTypeExist//
CREATE PROCEDURE checkBankAccountTypeExist(
    IN p_bank_account_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM bank_account_type
    WHERE bank_account_type_id = p_bank_account_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveBankAccountType//
CREATE PROCEDURE saveBankAccountType(
    IN p_bank_account_type_id INT, 
    IN p_bank_account_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_bank_account_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_bank_account_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM bank_account_type WHERE bank_account_type_id = p_bank_account_type_id) THEN
        INSERT INTO bank_account_type (bank_account_type_name, last_log_by) 
        VALUES(p_bank_account_type_name, p_last_log_by);
        
        SET p_new_bank_account_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE bank_account_type
        SET bank_account_type_name = p_bank_account_type_name,
            last_log_by = p_last_log_by
        WHERE bank_account_type_id = p_bank_account_type_id;

        SET p_new_bank_account_type_id = p_bank_account_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteBankAccountType//
CREATE PROCEDURE deleteBankAccountType(
    IN p_bank_account_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM bank_account_type WHERE bank_account_type_id = p_bank_account_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getBankAccountType//
CREATE PROCEDURE getBankAccountType(
    IN p_bank_account_type_id INT
)
BEGIN
	SELECT * FROM bank_account_type
	WHERE bank_account_type_id = p_bank_account_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateBankAccountTypeTable//
CREATE PROCEDURE generateBankAccountTypeTable()
BEGIN
	SELECT bank_account_type_id, bank_account_type_name
    FROM bank_account_type 
    ORDER BY bank_account_type_id;
END //

DROP PROCEDURE IF EXISTS generateBankAccountTypeOptions//
CREATE PROCEDURE generateBankAccountTypeOptions()
BEGIN
	SELECT bank_account_type_id, bank_account_type_name 
    FROM bank_account_type 
    ORDER BY bank_account_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */