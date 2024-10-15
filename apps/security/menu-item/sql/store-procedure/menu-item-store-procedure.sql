DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkMenuItemExist//
CREATE PROCEDURE checkMenuItemExist(
    IN p_menu_item_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM menu_item
    WHERE menu_item_id = p_menu_item_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getMenuItem//
CREATE PROCEDURE getMenuItem(
    IN p_menu_item_id INT
)
BEGIN
	SELECT * FROM menu_item
	WHERE menu_item_id = p_menu_item_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveMenuItem//
CREATE PROCEDURE saveMenuItem(
    IN p_menu_item_id INT, 
    IN p_menu_item_name VARCHAR(100), 
    IN p_menu_item_url VARCHAR(50), 
    IN p_menu_item_icon VARCHAR(50), 
    IN p_menu_group_id INT, 
    IN p_menu_group_name VARCHAR(100), 
    IN p_app_module_id INT, 
    IN p_app_module_name VARCHAR(100), 
    IN p_parent_id INT, 
    IN p_parent_name VARCHAR(100), 
    IN p_order_sequence TINYINT(10), 
    IN p_last_log_by INT, 
    OUT p_new_menu_item_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_menu_item_id IS NULL OR NOT EXISTS (SELECT 1 FROM menu_item WHERE menu_item_id = p_menu_item_id) THEN
        INSERT INTO menu_item (menu_item_name, menu_item_url, menu_item_icon, menu_group_id, menu_group_name, app_module_id, app_module_name, parent_id, parent_name, order_sequence, last_log_by) 
        VALUES(p_menu_item_name, p_menu_item_url, p_menu_item_icon, p_menu_group_id, p_menu_group_name, p_app_module_id, p_app_module_name, p_parent_id, p_parent_name, p_order_sequence, p_last_log_by);
        
        SET p_new_menu_item_id = LAST_INSERT_ID();
    ELSE
        UPDATE role_permission
        SET menu_item_name = p_menu_item_name,
            last_log_by = p_last_log_by
        WHERE menu_item_id = p_menu_item_id;
        
        UPDATE menu_item
        SET parent_name = p_menu_item_name,
            last_log_by = p_last_log_by
        WHERE parent_id = p_menu_item_id;
        
        UPDATE menu_item
        SET menu_item_name = p_menu_item_name,
            menu_item_url = p_menu_item_url,
            menu_item_icon = p_menu_item_icon,
            menu_group_id = p_menu_group_id,
            menu_group_name = p_menu_group_name,
            app_module_id = p_app_module_id,
            app_module_name = p_app_module_name,
            parent_id = p_parent_id,
            parent_name = p_parent_name,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE menu_item_id = p_menu_item_id;

        SET p_new_menu_item_id = p_menu_item_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteMenuItem//
CREATE PROCEDURE deleteMenuItem(
    IN p_menu_item_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_permission WHERE menu_item_id = p_menu_item_id;
    DELETE FROM menu_item WHERE menu_item_id = p_menu_item_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateMenuItemTable//
CREATE PROCEDURE generateMenuItemTable(
    IN p_filter_by_app_module TEXT,
    IN p_filter_by_menu_group TEXT,
    IN p_filter_by_parent_id TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT menu_item_id, menu_item_name, menu_group_name, app_module_name, parent_name, order_sequence 
                FROM menu_item ';

    IF p_filter_by_app_module IS NOT NULL AND p_filter_by_app_module <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' app_module_id IN (', p_filter_by_app_module, ')');
    END IF;

    IF p_filter_by_menu_group IS NOT NULL AND p_filter_by_menu_group <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
        SET filter_conditions = CONCAT(filter_conditions, ' menu_group_id IN (', p_filter_by_menu_group, ')');
    END IF;

    IF p_filter_by_menu_group IS NOT NULL AND p_filter_by_menu_group <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
        SET filter_conditions = CONCAT(filter_conditions, ' menu_group_id IN (', p_filter_by_menu_group, ')');
    END IF;

    IF p_filter_by_parent_id IS NOT NULL AND p_filter_by_parent_id <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
        SET filter_conditions = CONCAT(filter_conditions, ' parent_id IN (', p_filter_by_parent_id, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY menu_item_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateMenuItemAssignedRoleTable//
CREATE PROCEDURE generateMenuItemAssignedRoleTable(
    IN p_menu_item_id INT
)
BEGIN
    SELECT role_permission_id, role_name, read_access, write_access, create_access, delete_access, import_access, export_access, log_notes_access 
    FROM role_permission
    WHERE menu_item_id = p_menu_item_id;
END //

DROP PROCEDURE IF EXISTS generateMenuItemOptions//
CREATE PROCEDURE generateMenuItemOptions(
    IN p_menu_item_id INT
)
BEGIN
    IF p_menu_item_id IS NOT NULL AND p_menu_item_id != '' THEN
        SELECT menu_item_id, menu_item_name 
        FROM menu_item 
        WHERE menu_item_id != p_menu_item_id
        ORDER BY menu_item_name;
    ELSE
        SELECT menu_item_id, menu_item_name 
        FROM menu_item 
        ORDER BY menu_item_name;
    END IF;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */