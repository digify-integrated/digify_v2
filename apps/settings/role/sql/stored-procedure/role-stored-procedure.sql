DELIMITER //

/* Check Stored Procedures */

DROP PROCEDURE IF EXISTS checkRoleExist//
CREATE PROCEDURE checkRoleExist(
    IN p_role_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM role
    WHERE role_id = p_role_id;
END //

DROP PROCEDURE IF EXISTS checkRolePermissionExist//
CREATE PROCEDURE checkRolePermissionExist(
    IN p_role_permission_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM role_permission
    WHERE role_permission_id = p_role_permission_id;
END //

DROP PROCEDURE IF EXISTS checkRoleSystemActionPermissionExist//
CREATE PROCEDURE checkRoleSystemActionPermissionExist(
    IN p_role_system_action_permission_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM role_system_action_permission
    WHERE role_system_action_permission_id = p_role_system_action_permission_id;
END //

DROP PROCEDURE IF EXISTS checkRoleUserAccountExist//
CREATE PROCEDURE checkRoleUserAccountExist(
    IN p_role_user_account_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM role_user_account
    WHERE role_user_account_id = p_role_user_account_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedures */

DROP PROCEDURE IF EXISTS saveRole//
CREATE PROCEDURE saveRole(
    IN p_role_id INT,
    IN p_role_name VARCHAR(100),
    IN p_role_description VARCHAR(200),
    IN p_last_log_by INT,
    OUT p_new_role_id INT)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_role_id IS NULL OR NOT EXISTS (SELECT 1 FROM role WHERE role_id = p_role_id) THEN
        INSERT INTO role (role_name, role_description, last_log_by) 
	    VALUES(p_role_name, p_role_description, p_last_log_by);
        
        SET p_new_role_id = LAST_INSERT_ID();
    ELSE
        UPDATE role_permission
        SET role_name = p_role_name,
            last_log_by = p_last_log_by
        WHERE role_id = p_role_id;

        UPDATE role_system_action_permission
        SET role_name = p_role_name,
            last_log_by = p_last_log_by
        WHERE role_id = p_role_id;

        UPDATE role_user_account
        SET role_name = p_role_name,
            last_log_by = p_last_log_by
        WHERE role_id = p_role_id;

        UPDATE role
        SET role_name = p_role_name,
        role_name = p_role_name,
        role_description = p_role_description,
        last_log_by = p_last_log_by
        WHERE role_id = p_role_id;

        SET p_new_role_id = p_role_id;
    END IF;

    COMMIT;
END //


/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Insert Stored Procedures */

DROP PROCEDURE IF EXISTS insertRolePermission//
CREATE PROCEDURE insertRolePermission(
    IN p_role_id INT,
    IN p_role_name VARCHAR(100),
    IN p_menu_item_id INT,
    IN p_menu_item_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO role_permission (role_id, role_name, menu_item_id, menu_item_name, last_log_by) 
	VALUES(p_role_id, p_role_name, p_menu_item_id, p_menu_item_name, p_last_log_by);

    COMMIT;
END //

DROP PROCEDURE IF EXISTS insertRoleSystemActionPermission//
CREATE PROCEDURE insertRoleSystemActionPermission(
    IN p_role_id INT,
    IN p_role_name VARCHAR(100),
    IN p_system_action_id INT,
    IN p_system_action_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO role_system_action_permission (role_id, role_name, system_action_id, system_action_name, last_log_by) 
	VALUES(p_role_id, p_role_name, p_system_action_id, p_system_action_name, p_last_log_by);

    COMMIT;
END //

DROP PROCEDURE IF EXISTS insertRoleUserAccount//
CREATE PROCEDURE insertRoleUserAccount(
    IN p_role_id INT,
    IN p_role_name VARCHAR(100),
    IN p_user_account_id INT,
    IN p_file_as VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO role_user_account (role_id, role_name, user_account_id, file_as, last_log_by) 
	VALUES(p_role_id, p_role_name, p_user_account_id, p_file_as, p_last_log_by);

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedures */

DROP PROCEDURE IF EXISTS updateRolePermission//
CREATE PROCEDURE updateRolePermission(
    IN p_role_permission_id INT,
    IN p_access_type VARCHAR(10),
    IN p_access TINYINT(1),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_access_type = 'read' THEN
        UPDATE role_permission
        SET read_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    ELSEIF p_access_type = 'write' THEN
        UPDATE role_permission
        SET write_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    ELSEIF p_access_type = 'create' THEN
        UPDATE role_permission
        SET create_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    ELSEIF p_access_type = 'delete' THEN
        UPDATE role_permission
        SET delete_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    ELSEIF p_access_type = 'import' THEN
        UPDATE role_permission
        SET import_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    ELSEIF p_access_type = 'export' THEN
        UPDATE role_permission
        SET export_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    ELSE
        UPDATE role_permission
        SET log_notes_access = p_access,
            last_log_by = p_last_log_by
        WHERE role_permission_id = p_role_permission_id;
    END IF;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateRoleSystemActionPermission//
CREATE PROCEDURE updateRoleSystemActionPermission(
    IN p_role_system_action_permission_id INT,
    IN p_system_action_access TINYINT(1),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE role_system_action_permission
    SET system_action_access = p_system_action_access,
        last_log_by = p_last_log_by
    WHERE role_system_action_permission_id = p_role_system_action_permission_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedures */

DROP PROCEDURE IF EXISTS deleteRole//
CREATE PROCEDURE deleteRole(
    IN p_role_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_permission WHERE role_id = p_role_id;
    DELETE FROM role_system_action_permission WHERE role_id = p_role_id;
    DELETE FROM role_user_account WHERE role_id = p_role_id;
    DELETE FROM role WHERE role_id = p_role_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS deleteRolePermission//
CREATE PROCEDURE deleteRolePermission(
    IN p_role_permission_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_permission WHERE role_permission_id = p_role_permission_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS deleteRoleSystemActionPermission//
CREATE PROCEDURE deleteRoleSystemActionPermission(
    IN p_role_system_action_permission_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_system_action_permission WHERE role_system_action_permission_id = p_role_system_action_permission_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS deleteRoleUserAccount//
CREATE PROCEDURE deleteRoleUserAccount(
    IN p_role_user_account_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_user_account WHERE role_user_account_id = p_role_user_account_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedures */

DROP PROCEDURE IF EXISTS getRole//
CREATE PROCEDURE getRole(
    IN p_role_id INT
)
BEGIN
	SELECT * FROM role
    WHERE role_id = p_role_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedures */

DROP PROCEDURE IF EXISTS generateRoleTable//
CREATE PROCEDURE generateRoleTable()
BEGIN
	SELECT role_id, role_name, role_description
    FROM role 
    ORDER BY role_id;
END //

DROP PROCEDURE IF EXISTS generateRoleMenuItemPermissionTable//
CREATE PROCEDURE generateRoleMenuItemPermissionTable(
    IN p_role_id INT
)
BEGIN
	SELECT role_permission_id, menu_item_name, read_access, write_access, create_access, delete_access 
    FROM role_permission
    WHERE role_id = p_role_id
    ORDER BY menu_item_name;
END //

DROP PROCEDURE IF EXISTS generateRoleSystemActionPermissionTable//
CREATE PROCEDURE generateRoleSystemActionPermissionTable(
    IN p_role_id INT
)
BEGIN
	SELECT role_system_action_permission_id, system_action_name, system_action_access 
    FROM role_system_action_permission
    WHERE role_id = p_role_id
    ORDER BY system_action_name;
END //

DROP PROCEDURE IF EXISTS generateRoleUserAccountTable//
CREATE PROCEDURE generateRoleUserAccountTable(
    IN p_role_id INT
)
BEGIN
	SELECT role_user_account_id, user_account_id, file_as 
    FROM role_user_account
    WHERE role_id = p_role_id
    ORDER BY file_as;
END //

DROP PROCEDURE IF EXISTS generateUserAccountRoleList//
CREATE PROCEDURE generateUserAccountRoleList(
    IN p_user_account_id INT
)
BEGIN
	SELECT role_user_account_id, role_name, date_assigned
    FROM role_user_account
    WHERE user_account_id = p_user_account_id
    ORDER BY role_name;
END //

DROP PROCEDURE IF EXISTS generateRoleAssignedMenuItemTable//
CREATE PROCEDURE generateRoleAssignedMenuItemTable(
    IN p_role_id INT
)
BEGIN
    SELECT role_permission_id, menu_item_name, read_access, write_access, create_access, delete_access, import_access, export_access, log_notes_access 
    FROM role_permission
    WHERE role_id = p_role_id;
END //

DROP PROCEDURE IF EXISTS generateRoleAssignedSystemActionTable//
CREATE PROCEDURE generateRoleAssignedSystemActionTable(
    IN p_role_id INT
)
BEGIN
    SELECT role_system_action_permission_id, system_action_name, system_action_access 
    FROM role_system_action_permission
    WHERE role_id = p_role_id;
END //

DROP PROCEDURE IF EXISTS generateUserAccountRoleList//
CREATE PROCEDURE generateUserAccountRoleList(
    IN p_user_account_id INT
)
BEGIN
	SELECT role_user_account_id, role_name, date_assigned
    FROM role_user_account
    WHERE user_account_id = p_user_account_id
    ORDER BY role_name;
END //

DROP PROCEDURE IF EXISTS generateUserAccountRoleDualListBoxOptions//
CREATE PROCEDURE generateUserAccountRoleDualListBoxOptions(
    IN p_user_account_id INT
)
BEGIN
	SELECT role_id, role_name 
    FROM role 
    WHERE role_id NOT IN (SELECT role_id FROM role_user_account WHERE user_account_id = p_user_account_id)
    ORDER BY role_name;
END //

DROP PROCEDURE IF EXISTS generateMenuItemRoleDualListBoxOptions//
CREATE PROCEDURE generateMenuItemRoleDualListBoxOptions(
    IN p_menu_item_id INT
)
BEGIN
	SELECT role_id, role_name 
    FROM role 
    WHERE role_id NOT IN (SELECT role_id FROM role_permission WHERE menu_item_id = p_menu_item_id)
    ORDER BY role_name;
END //

DROP PROCEDURE IF EXISTS generateSystemActionRoleDualListBoxOptions//
CREATE PROCEDURE generateSystemActionRoleDualListBoxOptions(
    IN p_system_action_id INT
)
BEGIN
	SELECT role_id, role_name 
    FROM role 
    WHERE role_id NOT IN (SELECT role_id FROM role_system_action_permission WHERE system_action_id = p_system_action_id)
    ORDER BY role_name;
END //

DROP PROCEDURE IF EXISTS generateRoleMenuItemDualListBoxOptions//
CREATE PROCEDURE generateRoleMenuItemDualListBoxOptions(
    IN p_role_id INT
)
BEGIN
	SELECT menu_item_id, menu_item_name 
    FROM menu_item 
    WHERE menu_item_id NOT IN (SELECT menu_item_id FROM role_permission WHERE role_id = p_role_id)
    ORDER BY menu_item_name;
END //

DROP PROCEDURE IF EXISTS generateRoleSystemActionDualListBoxOptions//
CREATE PROCEDURE generateRoleSystemActionDualListBoxOptions(
    IN p_role_id INT
)
BEGIN
	SELECT system_action_id, system_action_name 
    FROM system_action
    WHERE system_action_id NOT IN (SELECT system_action_id FROM role_system_action_permission WHERE role_id = p_role_id)
    ORDER BY system_action_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */