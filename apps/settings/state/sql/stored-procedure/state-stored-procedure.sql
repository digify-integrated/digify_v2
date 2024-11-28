DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkStateExist//
CREATE PROCEDURE checkStateExist(
    IN p_state_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM state
    WHERE state_id = p_state_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getState//
CREATE PROCEDURE getState(
    IN p_state_id INT
)
BEGIN
	SELECT * FROM state
	WHERE state_id = p_state_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveState//
CREATE PROCEDURE saveState(
    IN p_state_id INT, 
    IN p_state_name VARCHAR(100), 
    IN p_country_id INT,
    IN p_country_name VARCHAR(100),
    IN p_last_log_by INT, 
    OUT p_new_state_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_state_id IS NULL OR NOT EXISTS (SELECT 1 FROM state WHERE state_id = p_state_id) THEN
        INSERT INTO state (state_name, country_id, country_name, last_log_by) 
        VALUES(p_state_name, p_country_id, p_country_name, p_last_log_by);
        
        SET p_new_state_id = LAST_INSERT_ID();
    ELSE
        UPDATE city
        SET state_name = p_state_name,
            last_log_by = p_last_log_by
        WHERE state_id = p_state_id;

        UPDATE work_location
        SET state_name = p_state_name,
            last_log_by = p_last_log_by
        WHERE state_id = p_state_id;
        
        UPDATE state
        SET state_name = p_state_name,
            country_id = p_country_id,
            country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE state_id = p_state_id;

        SET p_new_state_id = p_state_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteState//
CREATE PROCEDURE deleteState(
    IN p_state_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM city WHERE state_id = p_state_id;
    DELETE FROM state WHERE state_id = p_state_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateStateTable//
CREATE PROCEDURE generateStateTable(
    IN p_filter_by_country TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT state_id, state_name, country_name 
                FROM state ';

    IF p_filter_by_country IS NOT NULL AND p_filter_by_country <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' country_id IN (', p_filter_by_country, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY state_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateStateOptions//
CREATE PROCEDURE generateStateOptions()
BEGIN
    SELECT state_id, state_name 
    FROM state 
    ORDER BY state_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */