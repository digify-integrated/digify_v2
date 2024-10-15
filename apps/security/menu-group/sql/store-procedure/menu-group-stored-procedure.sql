DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkMenuGroupExist//
CREATE PROCEDURE checkMenuGroupExist(
    IN p_menu_group_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM menu_group
    WHERE menu_group_id = p_menu_group_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveMenuGroup//
CREATE PROCEDURE saveMenuGroup(
    IN p_menu_group_id INT, 
    IN p_menu_group_name VARCHAR(100), 
    IN p_app_module_id INT, 
    IN p_app_module_name VARCHAR(100), 
    IN p_order_sequence TINYINT(10), 
    IN p_last_log_by INT, 
    OUT p_new_menu_group_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_menu_group_id IS NULL OR NOT EXISTS (SELECT 1 FROM menu_group WHERE menu_group_id = p_menu_group_id) THEN
        INSERT INTO menu_group (menu_group_name, app_module_id, app_module_name, order_sequence, last_log_by) 
        VALUES(p_menu_group_name, p_app_module_id, p_app_module_name, p_order_sequence, p_last_log_by);
        
        SET p_new_menu_group_id = LAST_INSERT_ID();
    ELSE
        UPDATE menu_group
        SET menu_group_name = p_menu_group_name,
            app_module_id = p_app_module_id,
            app_module_name = p_app_module_name,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE menu_group_id = p_menu_group_id;
        
        UPDATE menu_item
        SET menu_group_name = p_menu_group_name,
            app_module_id = p_app_module_id,
            app_module_name = p_app_module_name,
            last_log_by = p_last_log_by
        WHERE menu_group_id = p_menu_group_id;

        SET p_new_menu_group_id = p_menu_group_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteMenuGroup//
CREATE PROCEDURE deleteMenuGroup(
    IN p_menu_group_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM menu_group WHERE menu_group_id = p_menu_group_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getMenuGroup//
CREATE PROCEDURE getMenuGroup(
    IN p_menu_group_id INT
)
BEGIN
	SELECT * FROM menu_group
	WHERE menu_group_id = p_menu_group_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateMenuGroupTable//
CREATE PROCEDURE generateMenuGroupTable(
    IN p_filter_by_app_module TEXT
)
BEGIN
    DECLARE query TEXT;

    SET query = 'SELECT menu_group_id, menu_group_name, app_module_name, order_sequence FROM menu_group';

    IF p_filter_by_app_module IS NOT NULL AND p_filter_by_app_module <> '' THEN
        SET query = CONCAT(query, ' WHERE app_module_id IN (', p_filter_by_app_module, ')');
    END IF;

    SET query = CONCAT(query, ' ORDER BY menu_group_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END//

DROP PROCEDURE IF EXISTS generateMenuGroupOptions//
CREATE PROCEDURE generateMenuGroupOptions()
BEGIN
	SELECT menu_group_id, menu_group_name 
    FROM menu_group 
    ORDER BY menu_group_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */