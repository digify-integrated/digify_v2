DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkAppModuleExist//
CREATE PROCEDURE checkAppModuleExist(
    IN p_app_module_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM app_module
    WHERE app_module_id = p_app_module_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveAppModule//
CREATE PROCEDURE saveAppModule(
    IN p_app_module_id INT, 
    IN p_app_module_name VARCHAR(100), 
    IN p_app_module_description VARCHAR(500), 
    IN p_menu_item_id INT, 
    IN p_menu_item_name VARCHAR(100), 
    IN p_order_sequence TINYINT(10), 
    IN p_last_log_by INT, 
    OUT p_new_app_module_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_app_module_id IS NULL OR NOT EXISTS (SELECT 1 FROM app_module WHERE app_module_id = p_app_module_id) THEN
        INSERT INTO app_module (app_module_name, app_module_description, menu_item_id, menu_item_name, order_sequence, last_log_by) 
        VALUES(p_app_module_name, p_app_module_description, p_menu_item_id, p_menu_item_name, p_order_sequence, p_last_log_by);
        
        SET p_new_app_module_id = LAST_INSERT_ID();
    ELSE
        UPDATE app_module
        SET app_module_name = p_app_module_name,
            app_module_description = p_app_module_description,
            menu_item_id = p_menu_item_id,
            menu_item_name = p_menu_item_name,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE app_module_id = p_app_module_id;
        
        UPDATE menu_group
        SET app_module_name = p_app_module_name,
            last_log_by = p_last_log_by
        WHERE app_module_id = p_app_module_id;

        UPDATE menu_item
        SET app_module_name = p_app_module_name,
            last_log_by = p_last_log_by
        WHERE app_module_id = p_app_module_id;

        SET p_new_app_module_id = p_app_module_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Procedure */

DROP PROCEDURE IF EXISTS updateAppLogo//
CREATE PROCEDURE updateAppLogo(
	IN p_app_module_id INT, 
	IN p_app_logo VARCHAR(500), 
	IN p_last_log_by INT
)
BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE app_module
    SET app_logo = p_app_logo,
        last_log_by = p_last_log_by
    WHERE app_module_id = p_app_module_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteAppModule//
CREATE PROCEDURE deleteAppModule(
    IN p_app_module_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM app_module WHERE app_module_id = p_app_module_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getAppModule//
CREATE PROCEDURE getAppModule(
    IN p_app_module_id INT
)
BEGIN
	SELECT * FROM app_module
	WHERE app_module_id = p_app_module_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateAppModuleTable//
CREATE PROCEDURE generateAppModuleTable()
BEGIN
	SELECT app_module_id, app_module_name, app_module_description, app_logo, order_sequence 
    FROM app_module 
    ORDER BY app_module_id;
END //

DROP PROCEDURE IF EXISTS generateAppModuleOptions//
CREATE PROCEDURE generateAppModuleOptions()
BEGIN
	SELECT app_module_id, app_module_name 
    FROM app_module 
    ORDER BY app_module_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */