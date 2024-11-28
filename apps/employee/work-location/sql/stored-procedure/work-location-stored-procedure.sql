DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkWorkLocationExist//
CREATE PROCEDURE checkWorkLocationExist(
    IN p_work_location_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM work_location
    WHERE work_location_id = p_work_location_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveWorkLocation//
CREATE PROCEDURE saveWorkLocation(
    IN p_work_location_id INT, 
    IN p_work_location_name VARCHAR(100), 
    IN p_address VARCHAR(1000), 
    IN p_city_id INT, 
    IN p_city_name VARCHAR(100), 
    IN p_state_id INT, 
    IN p_state_name VARCHAR(100), 
    IN p_country_id INT, 
    IN p_country_name VARCHAR(100), 
    IN p_phone VARCHAR(20), 
    IN p_telephone VARCHAR(20), 
    IN p_email VARCHAR(255), 
    IN p_last_log_by INT, 
    OUT p_new_work_location_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_work_location_id IS NULL OR NOT EXISTS (SELECT 1 FROM work_location WHERE work_location_id = p_work_location_id) THEN
        INSERT INTO work_location (work_location_name, address, city_id, city_name, state_id, state_name, country_id, country_name, phone, telephone, email, last_log_by) 
        VALUES(p_work_location_name, p_address, p_city_id, p_city_name, p_state_id, p_state_name, p_country_id, p_country_name, p_phone, p_telephone, p_email, p_last_log_by);
        
        SET p_new_work_location_id = LAST_INSERT_ID();
    ELSE
        UPDATE work_location
        SET work_location_name = p_work_location_name,
            address = p_address,
            city_id = p_city_id,
            city_name = p_city_name,
            state_id = p_state_id,
            state_name = p_state_name,
            country_id = p_country_id,
            country_name = p_country_name,
            phone = p_phone,
            telephone = p_telephone,
            email = p_email,
            last_log_by = p_last_log_by
        WHERE work_location_id = p_work_location_id;

        SET p_new_work_location_id = p_work_location_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteWorkLocation//
CREATE PROCEDURE deleteWorkLocation(
    IN p_work_location_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM work_location WHERE work_location_id = p_work_location_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getWorkLocation//
CREATE PROCEDURE getWorkLocation(
    IN p_work_location_id INT
)
BEGIN
	SELECT * FROM work_location
	WHERE work_location_id = p_work_location_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateWorkLocationTable//
CREATE PROCEDURE generateWorkLocationTable(
    IN p_filter_by_city TEXT,
    IN p_filter_by_state TEXT,
    IN p_filter_by_country TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT work_location_id, work_location_name, address, city_name, state_name, country_name 
                FROM work_location ';

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

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY work_location_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateWorkLocationOptions//
CREATE PROCEDURE generateWorkLocationOptions()
BEGIN
	SELECT work_location_id, work_location_name 
    FROM work_location 
    ORDER BY work_location_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */