DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkCompanyExist//
CREATE PROCEDURE checkCompanyExist(
    IN p_company_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM company
    WHERE company_id = p_company_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveCompany//
CREATE PROCEDURE saveCompany(
    IN p_company_id INT, 
    IN p_company_name VARCHAR(100), 
    IN p_address VARCHAR(1000), 
    IN p_city_id INT, 
    IN p_city_name VARCHAR(100), 
    IN p_state_id INT, 
    IN p_state_name VARCHAR(100), 
    IN p_country_id INT, 
    IN p_country_name VARCHAR(100), 
    IN p_tax_id VARCHAR(100), 
    IN p_currency_id INT, 
    IN p_currency_name VARCHAR(100), 
    IN p_phone VARCHAR(20), 
    IN p_telephone VARCHAR(20), 
    IN p_email VARCHAR(255), 
    IN p_website VARCHAR(255), 
    IN p_last_log_by INT, 
    OUT p_new_company_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_company_id IS NULL OR NOT EXISTS (SELECT 1 FROM company WHERE company_id = p_company_id) THEN
        INSERT INTO company (company_name, address, city_id, city_name, state_id, state_name, country_id, country_name, tax_id, currency_id, currency_name, phone, telephone, email, website, last_log_by) 
        VALUES(p_company_name, p_address, p_city_id, p_city_name, p_state_id, p_state_name, p_country_id, p_country_name, p_tax_id, p_currency_id, p_currency_name, p_phone, p_telephone, p_email, p_website, p_last_log_by);
        
        SET p_new_company_id = LAST_INSERT_ID();
    ELSE
        UPDATE company
        SET company_name = p_company_name,
            address = p_address,
            city_id = p_city_id,
            city_name = p_city_name,
            state_id = p_state_id,
            state_name = p_state_name,
            country_id = p_country_id,
            country_name = p_country_name,
            tax_id = p_tax_id,
            currency_id = p_currency_id,
            currency_name = p_currency_name,
            phone = p_phone,
            telephone = p_telephone,
            email = p_email,
            website = p_website,
            last_log_by = p_last_log_by
        WHERE company_id = p_company_id;

        SET p_new_company_id = p_company_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Procedure */

DROP PROCEDURE IF EXISTS updateCompanyLogo//
CREATE PROCEDURE updateCompanyLogo(
	IN p_company_id INT, 
	IN p_company_logo VARCHAR(500), 
	IN p_last_log_by INT
)
BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE company
    SET company_logo = p_company_logo,
        last_log_by = p_last_log_by
    WHERE company_id = p_company_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteCompany//
CREATE PROCEDURE deleteCompany(
    IN p_company_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM company WHERE company_id = p_company_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getCompany//
CREATE PROCEDURE getCompany(
    IN p_company_id INT
)
BEGIN
	SELECT * FROM company
	WHERE company_id = p_company_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateCompanyTable//
CREATE PROCEDURE generateCompanyTable(
    IN p_filter_by_city TEXT,
    IN p_filter_by_state TEXT,
    IN p_filter_by_country TEXT,
    IN p_filter_by_currency TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT company_id, company_name, company_logo, address, city_name, state_name, country_name 
                FROM company ';

    IF p_filter_by_city IS NOT NULL AND p_filter_by_city <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' city_id IN (', p_filter_by_city, ')');
    END IF;

    IF p_filter_by_state IS NOT NULL AND p_filter_by_state <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' state_id IN (', p_filter_by_state, ')');
    END IF;

    IF p_filter_by_country IS NOT NULL AND p_filter_by_country <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' country_id IN (', p_filter_by_country, ')');
    END IF;

    IF p_filter_by_currency IS NOT NULL AND p_filter_by_currency <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' currency_id IN (', p_filter_by_currency, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY company_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateCompanyOptions//
CREATE PROCEDURE generateCompanyOptions()
BEGIN
	SELECT company_id, company_name 
    FROM company 
    ORDER BY company_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */