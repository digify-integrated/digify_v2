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

DROP PROCEDURE IF EXISTS generateEmployeeTable//
CREATE PROCEDURE generateEmployeeTable(
    IN p_filter_by_parent_employee TEXT,
    IN p_filter_by_manager TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT employee_id, employee_name, parent_employee_name, manager_name
                FROM employee ';

    IF p_filter_by_parent_employee IS NOT NULL AND p_filter_by_parent_employee <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' parent_employee_id IN (', p_filter_by_parent_employee, ')');
    END IF;

    IF p_filter_by_manager IS NOT NULL AND p_filter_by_manager <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' manager_id IN (', p_filter_by_manager, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY employee_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
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