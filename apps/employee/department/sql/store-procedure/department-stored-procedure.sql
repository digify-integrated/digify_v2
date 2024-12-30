DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkDepartmentExist//
CREATE PROCEDURE checkDepartmentExist(
    IN p_department_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM department
    WHERE department_id = p_department_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveDepartment//
CREATE PROCEDURE saveDepartment(
    IN p_department_id INT, 
    IN p_department_name VARCHAR(100), 
    IN p_parent_department_id INT, 
    IN p_parent_department_name VARCHAR(100), 
    IN p_manager_id INT, 
    IN p_manager_name VARCHAR(1000), 
    IN p_last_log_by INT, 
    OUT p_new_department_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_department_id IS NULL OR NOT EXISTS (SELECT 1 FROM department WHERE department_id = p_department_id) THEN
        INSERT INTO department (department_name, parent_department_id, parent_department_name, manager_id, manager_name, last_log_by) 
        VALUES(p_department_name, p_parent_department_id, p_parent_department_name, p_manager_id, p_manager_name, p_last_log_by);
        
        SET p_new_department_id = LAST_INSERT_ID();
    ELSE
        UPDATE department
        SET department_name = p_department_name,
            parent_department_id = p_parent_department_id,
            parent_department_name = p_parent_department_name,
            manager_id = p_manager_id,
            manager_name = p_manager_name,
            last_log_by = p_last_log_by
        WHERE department_id = p_department_id;

        SET p_new_department_id = p_department_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteDepartment//
CREATE PROCEDURE deleteDepartment(
    IN p_department_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM department WHERE department_id = p_department_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getDepartment//
CREATE PROCEDURE getDepartment(
    IN p_department_id INT
)
BEGIN
	SELECT * FROM department
	WHERE department_id = p_department_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateDepartmentTable//
CREATE PROCEDURE generateDepartmentTable(
    IN p_filter_by_parent_department TEXT,
    IN p_filter_by_manager TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT department_id, department_name, parent_department_name, manager_name
                FROM department ';

    IF p_filter_by_parent_department IS NOT NULL AND p_filter_by_parent_department <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' parent_department_id IN (', p_filter_by_parent_department, ')');
    END IF;

    IF p_filter_by_manager IS NOT NULL AND p_filter_by_manager <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' manager_id IN (', p_filter_by_manager, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY department_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateDepartmentOptions//
CREATE PROCEDURE generateDepartmentOptions()
BEGIN
	SELECT department_id, department_name
    FROM department 
    ORDER BY department_name;
END //

DROP PROCEDURE IF EXISTS generateParentDepartmentOptions//
CREATE PROCEDURE generateParentDepartmentOptions(
    IN p_department_id INT
)
BEGIN
	SELECT department_id, department_name
    FROM department 
    WHERE department_id != p_department_id
    ORDER BY department_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */