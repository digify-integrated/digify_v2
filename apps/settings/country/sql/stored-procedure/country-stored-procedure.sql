DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkCountryExist//
CREATE PROCEDURE checkCountryExist(
    IN p_country_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM country
    WHERE country_id = p_country_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getCountry//
CREATE PROCEDURE getCountry(
    IN p_country_id INT
)
BEGIN
	SELECT * FROM country
	WHERE country_id = p_country_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveCountry//
CREATE PROCEDURE saveCountry(
    IN p_country_id INT, 
    IN p_country_name VARCHAR(100), 
    IN p_country_code VARCHAR(10),
    IN p_phone_code VARCHAR(10),
    IN p_last_log_by INT, 
    OUT p_new_country_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_country_id IS NULL OR NOT EXISTS (SELECT 1 FROM country WHERE country_id = p_country_id) THEN
        INSERT INTO country (country_name, country_code, phone_code, last_log_by) 
        VALUES(p_country_name, p_country_code, p_phone_code, p_last_log_by);
        
        SET p_new_country_id = LAST_INSERT_ID();
    ELSE
        UPDATE state
        SET country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE country_id = p_country_id;

        UPDATE city
        SET country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE country_id = p_country_id;
        
        UPDATE country
        SET country_name = p_country_name,
            country_code = p_country_code,
            phone_code = p_phone_code,
            last_log_by = p_last_log_by
        WHERE country_id = p_country_id;

        SET p_new_country_id = p_country_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteCountry//
CREATE PROCEDURE deleteCountry(
    IN p_country_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM state WHERE country_id = p_country_id;
    DELETE FROM city WHERE country_id = p_country_id;
    DELETE FROM country WHERE country_id = p_country_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateCountryTable//
CREATE PROCEDURE generateCountryTable()
BEGIN
    SELECT country_id, country_name, country_code, phone_code 
    FROM country
    ORDER BY country_id;
END //

DROP PROCEDURE IF EXISTS generateCountryOptions//
CREATE PROCEDURE generateCountryOptions()
BEGIN
    SELECT country_id, country_name 
    FROM country 
    ORDER BY country_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */