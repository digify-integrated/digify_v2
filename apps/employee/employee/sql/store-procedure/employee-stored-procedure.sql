DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkEmployeeExist//
CREATE PROCEDURE checkEmployeeExist(
    IN p_employee_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM employee
    WHERE employee_id = p_employee_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Insert Stored Procedure */

DROP PROCEDURE IF EXISTS insertEmployee//
CREATE PROCEDURE insertEmployee(
    IN p_full_name VARCHAR(1000),
    IN p_first_name VARCHAR(300),
    IN p_middle_name VARCHAR(300),
    IN p_last_name VARCHAR(300),
    IN p_suffix VARCHAR(10),
    IN p_last_log_by INT, 
    OUT p_new_employee_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO employee (full_name, first_name, middle_name, last_name, suffix, last_log_by) 
    VALUES(p_full_name, p_first_name, p_middle_name, p_last_name, p_suffix, p_last_log_by);
        
    SET p_new_employee_id = LAST_INSERT_ID();

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedure */

DROP PROCEDURE IF EXISTS updateEmployee//
CREATE PROCEDURE updateEmployee(
    IN p_employee_id INT,
    IN p_full_name VARCHAR(1000),
    IN p_first_name VARCHAR(300),
    IN p_middle_name VARCHAR(300),
    IN p_last_name VARCHAR(300),
    IN p_suffix VARCHAR(10),
    IN p_nickname VARCHAR(100),
    IN p_private_address VARCHAR(500),
    IN p_private_address_city_id INT,
	IN p_private_address_city_name VARCHAR(100),
	IN p_private_address_state_id INT,
	IN p_private_address_state_name VARCHAR(100),
	IN p_private_address_country_id INT,
	IN p_private_address_country_name VARCHAR(100),
    IN p_civil_status_id INT,
    IN p_civil_status_name VARCHAR(100),
    IN p_dependents INT,
    IN p_religion_id INT,
    IN p_religion_name VARCHAR(100),
    IN p_blood_type_id INT,
    IN p_blood_type_name VARCHAR(100),
    IN p_height FLOAT,
    IN p_weight FLOAT,
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET full_name = p_full_name,
        first_name = p_first_name,
        middle_name = p_middle_name,
        last_name = p_last_name,
        suffix = p_suffix,
        nickname = p_nickname,
        private_address = p_private_address,
        private_address_city_id = p_private_address_city_id,
        private_address_city_name = p_private_address_city_name,
        private_address_state_id = p_private_address_state_id,
        private_address_state_name = p_private_address_state_name,
        private_address_country_id = p_private_address_country_id,
        private_address_country_name = p_private_address_country_name,
        civil_status_id = p_civil_status_id,
        civil_status_name = p_civil_status_name,
        dependents = p_dependents,
        religion_id = p_religion_id,
        religion_name = p_religion_name,
        blood_type_id = p_blood_type_id,
        blood_type_name = p_blood_type_name,
        height = p_height,
        weight = p_weight,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteEmployee//
CREATE PROCEDURE deleteEmployee(
    IN p_employee_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM employee WHERE employee_id = p_employee_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getEmployee//
CREATE PROCEDURE getEmployee(
    IN p_employee_id INT
)
BEGIN
	SELECT * FROM employee
	WHERE employee_id = p_employee_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateEmployeeCard//
CREATE PROCEDURE generateEmployeeCard(
    IN p_search_value TEXT,
    IN p_filter_by_company TEXT,
    IN p_filter_by_department TEXT,
    IN p_filter_by_job_position TEXT,
    IN p_filter_by_employee_status TEXT,
    IN p_filter_by_work_location TEXT,
    IN p_filter_by_employment_type TEXT,
    IN p_filter_by_gender TEXT,
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT employee_id, employee_image, full_name, department_name, job_position_name, employment_status
                FROM employee WHERE 1';

    IF p_search_value IS NOT NULL AND p_search_value <> '' THEN
        SET query = CONCAT(query, ' AND (
            first_name LIKE ? OR
            middle_name LIKE ? OR
            last_name LIKE ? OR
            suffix LIKE ? OR
            department_name LIKE ? OR
            job_position_name LIKE ? OR
            employment_status LIKE ?
        )');
    END IF;

    IF p_filter_by_company IS NOT NULL AND p_filter_by_company <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' company_id IN (', p_filter_by_company, ')');
    END IF;

    IF p_filter_by_department IS NOT NULL AND p_filter_by_department <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' department_id IN (', p_filter_by_department, ')');
    END IF;

    IF p_filter_by_job_position IS NOT NULL AND p_filter_by_job_position <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' job_position_id IN (', p_filter_by_job_position, ')');
    END IF;

    IF p_filter_by_employee_status IS NOT NULL AND p_filter_by_employee_status <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' employment_status IN (', p_filter_by_employee_status, ')');
    END IF;

    IF p_filter_by_work_location IS NOT NULL AND p_filter_by_work_location <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' work_location_id IN (', p_filter_by_work_location, ')');
    END IF;

    IF p_filter_by_employment_type IS NOT NULL AND p_filter_by_employment_type <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' employment_type_id IN (', p_filter_by_employment_type, ')');
    END IF;

    IF p_filter_by_gender IS NOT NULL AND p_filter_by_gender <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' gender_id IN (', p_filter_by_gender, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY full_name LIMIT ?, ?;');

    PREPARE stmt FROM query;
    IF p_search_value IS NOT NULL AND p_search_value <> '' THEN
        EXECUTE stmt USING CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), p_offset, p_limit;
    ELSE
        EXECUTE stmt USING p_offset, p_limit;
    END IF;

    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateEmployeeOptions//
CREATE PROCEDURE generateEmployeeOptions()
BEGIN
	SELECT employee_id, employee_name
    FROM employee 
    ORDER BY employee_name;
END //

DROP PROCEDURE IF EXISTS generateParentEmployeeOptions//
CREATE PROCEDURE generateParentEmployeeOptions(
    IN p_employee_id INT
)
BEGIN
	SELECT employee_id, employee_name
    FROM employee 
    WHERE employee_id != p_employee_id
    ORDER BY employee_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */