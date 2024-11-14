DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkCityExist//
CREATE PROCEDURE checkCityExist(
    IN p_city_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM city
    WHERE city_id = p_city_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getCity//
CREATE PROCEDURE getCity(
    IN p_city_id INT
)
BEGIN
	SELECT * FROM city
	WHERE city_id = p_city_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveCity//
CREATE PROCEDURE saveCity(
    IN p_city_id INT, 
    IN p_city_name VARCHAR(100), 
    IN p_state_id INT,
    IN p_state_name VARCHAR(100),
    IN p_country_id INT,
    IN p_country_name VARCHAR(100),
    IN p_last_log_by INT, 
    OUT p_new_city_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_city_id IS NULL OR NOT EXISTS (SELECT 1 FROM city WHERE city_id = p_city_id) THEN
        INSERT INTO city (city_name, state_id, state_name, country_id, country_name, last_log_by) 
        VALUES(p_city_name, p_state_id, p_state_name, p_country_id, p_country_name, p_last_log_by);
        
        SET p_new_city_id = LAST_INSERT_ID();
    ELSE        
        UPDATE city
        SET city_name = p_city_name,
            state_id = p_state_id,
            state_name = p_state_name,
            country_name = p_country_name,
            country_id = p_country_id,
            country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE city_id = p_city_id;

        SET p_new_city_id = p_city_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteCity//
CREATE PROCEDURE deleteCity(
    IN p_city_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM city WHERE city_id = p_city_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateCityTable//
CREATE PROCEDURE generateCityTable(
    IN p_filter_by_state TEXT,
    IN p_filter_by_country TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT city_id, city_name, state_name, country_name 
                FROM city ';

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

    SET query = CONCAT(query, ' ORDER BY city_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateCityOptions//
CREATE PROCEDURE generateCityOptions()
BEGIN
    SELECT city_id, city_name, state_name, country_name
    FROM city 
    ORDER BY city_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */