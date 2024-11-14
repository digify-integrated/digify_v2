DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkCurrencyExist//
CREATE PROCEDURE checkCurrencyExist(
    IN p_currency_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM currency
    WHERE currency_id = p_currency_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getCurrency//
CREATE PROCEDURE getCurrency(
    IN p_currency_id INT
)
BEGIN
	SELECT * FROM currency
	WHERE currency_id = p_currency_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveCurrency//
CREATE PROCEDURE saveCurrency(
    IN p_currency_id INT, 
    IN p_currency_name VARCHAR(100), 
    IN p_symbol VARCHAR(5),
    IN p_shorthand VARCHAR(10),
    IN p_last_log_by INT, 
    OUT p_new_currency_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_currency_id IS NULL OR NOT EXISTS (SELECT 1 FROM currency WHERE currency_id = p_currency_id) THEN
        INSERT INTO currency (currency_name, symbol, shorthand, last_log_by) 
        VALUES(p_currency_name, p_symbol, p_shorthand, p_last_log_by);
        
        SET p_new_currency_id = LAST_INSERT_ID();
    ELSE        
        UPDATE currency
        SET currency_name = p_currency_name,
            symbol = p_symbol,
            shorthand = p_shorthand,
            last_log_by = p_last_log_by
        WHERE currency_id = p_currency_id;

        SET p_new_currency_id = p_currency_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteCurrency//
CREATE PROCEDURE deleteCurrency(
    IN p_currency_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM currency WHERE currency_id = p_currency_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateCurrencyTable//
CREATE PROCEDURE generateCurrencyTable()
BEGIN
    SELECT currency_id, currency_name, symbol, shorthand 
    FROM currency
    ORDER BY currency_id;
END //

DROP PROCEDURE IF EXISTS generateCurrencyOptions//
CREATE PROCEDURE generateCurrencyOptions()
BEGIN
    SELECT currency_id, currency_name, symbol
    FROM currency 
    ORDER BY currency_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */