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
    IN p_state_id INT,
    IN p_state_name VARCHAR(100),
    IN p_country_id INT,
    IN p_country_name VARCHAR(100),
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
        INSERT INTO bank (bank_name, state_id, state_name, country_id, country_name, last_log_by) 
        VALUES(p_bank_name, p_state_id, p_state_name, p_country_id, p_country_name, p_last_log_by);
        
        SET p_new_bank_id = LAST_INSERT_ID();
    ELSE        
        UPDATE bank
        SET bank_name = p_bank_name,
            state_id = p_state_id,
            state_name = p_state_name,
            country_name = p_country_name,
            country_id = p_country_id,
            country_name = p_country_name,
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
CREATE PROCEDURE generateBankTable(
    IN p_filter_by_state TEXT,
    IN p_filter_by_country TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT bank_id, bank_name, state_name, country_name 
                FROM bank ';

    IF p_filter_by_state IS NOT NULL AND p_filter_by_state <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' state_id IN (', p_filter_by_state, ')');
    END IF;

    IF p_filter_by_country IS NOT NULL AND p_filter_by_country <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
         SET filter_conditions = CONCAT(filter_conditions, ' country_id IN (', p_filter_by_country, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY bank_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateBankOptions//
CREATE PROCEDURE generateBankOptions()
BEGIN
    SELECT bank_id, bank_name, state_name, country_name
    FROM bank 
    ORDER BY bank_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */