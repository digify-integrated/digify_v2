-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2024 at 11:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digifydb`
--
CREATE DATABASE IF NOT EXISTS `digifydb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `digifydb`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `buildAppModuleStack`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `buildAppModuleStack` (IN `p_user_account_id` INT)   BEGIN
    SELECT DISTINCT(am.app_module_id) as app_module_id, am.app_module_name, am.menu_item_id, app_logo, app_module_description
    FROM app_module am
    JOIN menu_item mi ON mi.app_module_id = am.app_module_id
    WHERE EXISTS (
        SELECT 1
        FROM role_permission mar
        WHERE mar.menu_item_id = mi.menu_item_id
        AND mar.read_access = 1
        AND mar.role_id IN (
            SELECT role_id
            FROM role_user_account
            WHERE user_account_id = p_user_account_id
        )
    )
    ORDER BY am.order_sequence, am.app_module_name;
END$$

DROP PROCEDURE IF EXISTS `buildMenuGroup`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `buildMenuGroup` (IN `p_user_account_id` INT, IN `p_app_module_id` INT)   BEGIN
    SELECT DISTINCT(mg.menu_group_id) as menu_group_id, mg.menu_group_name as menu_group_name
    FROM menu_group mg
    JOIN menu_item mi ON mi.menu_group_id = mg.menu_group_id
    WHERE EXISTS (
        SELECT 1
        FROM role_permission mar
        WHERE mar.menu_item_id = mi.menu_item_id
        AND mar.read_access = 1
        AND mar.role_id IN (
            SELECT role_id
            FROM role_user_account
            WHERE user_account_id = p_user_account_id
        )
    )
    AND mg.app_module_id = p_app_module_id
    ORDER BY mg.order_sequence;
END$$

DROP PROCEDURE IF EXISTS `buildMenuItem`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `buildMenuItem` (IN `p_user_account_id` INT, IN `p_app_module_id` INT)   BEGIN
    SELECT mi.menu_item_id, mi.menu_item_name, mi.menu_item_url, mi.parent_id, mi.app_module_id, mi.menu_item_icon
    FROM menu_item AS mi
    INNER JOIN role_permission AS mar ON mi.menu_item_id = mar.menu_item_id
    INNER JOIN role_user_account AS ru ON mar.role_id = ru.role_id
    WHERE mar.read_access = 1 AND ru.user_account_id = p_user_account_id AND mi.app_module_id = p_app_module_id
    ORDER BY mi.order_sequence;
END$$

DROP PROCEDURE IF EXISTS `checkAccessRights`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkAccessRights` (IN `p_user_account_id` INT, IN `p_menu_item_id` INT, IN `p_access_type` VARCHAR(10))   BEGIN
	IF p_access_type = 'read' THEN
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where read_access = 1 AND menu_item_id = p_menu_item_id);
    ELSEIF p_access_type = 'write' THEN
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where write_access = 1 AND menu_item_id = p_menu_item_id);
    ELSEIF p_access_type = 'create' THEN
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where create_access = 1 AND menu_item_id = p_menu_item_id);       
    ELSEIF p_access_type = 'delete' THEN
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where delete_access = 1 AND menu_item_id = p_menu_item_id);
    ELSEIF p_access_type = 'import' THEN
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where import_access = 1 AND menu_item_id = p_menu_item_id);
    ELSEIF p_access_type = 'export' THEN
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where export_access = 1 AND menu_item_id = p_menu_item_id);
    ELSE
        SELECT COUNT(role_id) AS total
        FROM role_user_account
        WHERE user_account_id = p_user_account_id AND role_id IN (SELECT role_id FROM role_permission where log_notes_access = 1 AND menu_item_id = p_menu_item_id);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `checkAppModuleExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkAppModuleExist` (IN `p_app_module_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM app_module
    WHERE app_module_id = p_app_module_id;
END$$

DROP PROCEDURE IF EXISTS `checkLoginCredentialsExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkLoginCredentialsExist` (IN `p_user_account_id` INT, IN `p_credentials` VARCHAR(255))   BEGIN
    SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id = p_user_account_id
       OR username = BINARY p_credentials
       OR email = BINARY p_credentials;
END$$

DROP PROCEDURE IF EXISTS `checkMenuGroupExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkMenuGroupExist` (IN `p_menu_group_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM menu_group
    WHERE menu_group_id = p_menu_group_id;
END$$

DROP PROCEDURE IF EXISTS `checkMenuItemExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkMenuItemExist` (IN `p_menu_item_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM menu_item
    WHERE menu_item_id = p_menu_item_id;
END$$

DROP PROCEDURE IF EXISTS `checkRoleExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkRoleExist` (IN `p_role_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM role
    WHERE role_id = p_role_id;
END$$

DROP PROCEDURE IF EXISTS `checkRolePermissionExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkRolePermissionExist` (IN `p_role_permission_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM role_permission
    WHERE role_permission_id = p_role_permission_id;
END$$

DROP PROCEDURE IF EXISTS `checkRoleSystemActionPermissionExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkRoleSystemActionPermissionExist` (IN `p_role_system_action_permission_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM role_system_action_permission
    WHERE role_system_action_permission_id = p_role_system_action_permission_id;
END$$

DROP PROCEDURE IF EXISTS `checkRoleUserAccountExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkRoleUserAccountExist` (IN `p_role_user_account_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM role_user_account
    WHERE role_user_account_id = p_role_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `checkSystemActionAccessRights`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkSystemActionAccessRights` (IN `p_user_account_id` INT, IN `p_system_action_id` INT)   BEGIN
    SELECT COUNT(role_id) AS total
    FROM role_system_action_permission 
    WHERE system_action_id = p_system_action_id 
    AND system_action_access = 1 
    AND role_id IN (SELECT role_id FROM role_user_account WHERE user_account_id = p_user_account_id);
END$$

DROP PROCEDURE IF EXISTS `checkSystemActionExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkSystemActionExist` (IN `p_system_action_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM system_action
    WHERE system_action_id = p_system_action_id;
END$$

DROP PROCEDURE IF EXISTS `deleteAppModule`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteAppModule` (IN `p_app_module_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM app_module WHERE app_module_id = p_app_module_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteMenuGroup`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMenuGroup` (IN `p_menu_group_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM menu_group WHERE menu_group_id = p_menu_group_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteMenuItem`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteMenuItem` (IN `p_menu_item_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_permission WHERE menu_item_id = p_menu_item_id;
    DELETE FROM menu_item WHERE menu_item_id = p_menu_item_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteRole`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteRole` (IN `p_role_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `deleteRolePermission`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteRolePermission` (IN `p_role_permission_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_permission WHERE role_permission_id = p_role_permission_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteRoleSystemActionPermission`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteRoleSystemActionPermission` (IN `p_role_system_action_permission_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_system_action_permission WHERE role_system_action_permission_id = p_role_system_action_permission_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteRoleUserAccount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteRoleUserAccount` (IN `p_role_user_account_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_user_account WHERE role_user_account_id = p_role_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteSystemAction`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteSystemAction` (IN `p_system_action_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_system_action_permission WHERE system_action_id = p_system_action_id;
    DELETE FROM system_action WHERE system_action_id = p_system_action_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `exportData`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `exportData` (IN `p_table_name` VARCHAR(255), IN `p_columns` TEXT, IN `p_ids` TEXT)   BEGIN
    SET @sql = CONCAT('SELECT ', p_columns, ' FROM ', p_table_name);

    IF p_table_name = 'app_module' THEN
        SET @sql = CONCAT(@sql, ' WHERE app_module_id IN (', p_ids, ')');
    END IF;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateAppModuleOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateAppModuleOptions` ()   BEGIN
	SELECT app_module_id, app_module_name 
    FROM app_module 
    ORDER BY app_module_name;
END$$

DROP PROCEDURE IF EXISTS `generateAppModuleTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateAppModuleTable` ()   BEGIN
	SELECT app_module_id, app_module_name, app_module_description, app_logo, order_sequence 
    FROM app_module 
    ORDER BY app_module_id;
END$$

DROP PROCEDURE IF EXISTS `generateExportOption`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateExportOption` (IN `p_databasename` VARCHAR(500), IN `p_table_name` VARCHAR(500))   BEGIN
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_schema = p_databasename 
    AND table_name = p_table_name
    ORDER BY ordinal_position;
END$$

DROP PROCEDURE IF EXISTS `generateInternalNotes`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateInternalNotes` (IN `p_table_name` VARCHAR(255), IN `p_reference_id` INT)   BEGIN
	SELECT internal_notes_id, internal_note, internal_note_by, internal_note_date
    FROM internal_notes
    WHERE table_name = p_table_name AND reference_id  = p_reference_id
    ORDER BY internal_note_date DESC;
END$$

DROP PROCEDURE IF EXISTS `generateLogNotes`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateLogNotes` (IN `p_table_name` VARCHAR(255), IN `p_reference_id` INT)   BEGIN
	SELECT log, changed_by, changed_at
    FROM audit_log
    WHERE table_name = p_table_name AND reference_id  = p_reference_id
    ORDER BY changed_at DESC;
END$$

DROP PROCEDURE IF EXISTS `generateMenuGroupOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateMenuGroupOptions` ()   BEGIN
	SELECT menu_group_id, menu_group_name 
    FROM menu_group 
    ORDER BY menu_group_name;
END$$

DROP PROCEDURE IF EXISTS `generateMenuGroupTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateMenuGroupTable` (IN `p_filter_by_app_module` TEXT)   BEGIN
    DECLARE query TEXT;

    SET query = 'SELECT menu_group_id, menu_group_name, app_module_name, order_sequence FROM menu_group';

    IF p_filter_by_app_module IS NOT NULL AND p_filter_by_app_module <> '' THEN
        SET query = CONCAT(query, ' WHERE app_module_id IN (', p_filter_by_app_module, ')');
    END IF;

    SET query = CONCAT(query, ' ORDER BY menu_group_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateMenuItemAssignedRoleTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateMenuItemAssignedRoleTable` (IN `p_menu_item_id` INT)   BEGIN
    SELECT role_permission_id, role_name, read_access, write_access, create_access, delete_access, import_access, export_access, log_notes_access 
    FROM role_permission
    WHERE menu_item_id = p_menu_item_id;
END$$

DROP PROCEDURE IF EXISTS `generateMenuItemOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateMenuItemOptions` (IN `p_menu_item_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `generateMenuItemRoleDualListBoxOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateMenuItemRoleDualListBoxOptions` (IN `p_menu_item_id` INT)   BEGIN
	SELECT role_id, role_name 
    FROM role 
    WHERE role_id NOT IN (SELECT role_id FROM role_permission WHERE menu_item_id = p_menu_item_id)
    ORDER BY role_name;
END$$

DROP PROCEDURE IF EXISTS `generateMenuItemTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateMenuItemTable` (IN `p_filter_by_app_module` TEXT, IN `p_filter_by_parent_id` TEXT)   BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT menu_item_id, menu_item_name, app_module_name, parent_name, order_sequence 
                FROM menu_item ';

    IF p_filter_by_app_module IS NOT NULL AND p_filter_by_app_module <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' app_module_id IN (', p_filter_by_app_module, ')');
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
END$$

DROP PROCEDURE IF EXISTS `generateRoleAssignedMenuItemTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleAssignedMenuItemTable` (IN `p_role_id` INT)   BEGIN
    SELECT role_permission_id, menu_item_name, read_access, write_access, create_access, delete_access, import_access, export_access, log_notes_access 
    FROM role_permission
    WHERE role_id = p_role_id;
END$$

DROP PROCEDURE IF EXISTS `generateRoleAssignedSystemActionTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleAssignedSystemActionTable` (IN `p_role_id` INT)   BEGIN
    SELECT role_system_action_permission_id, system_action_name, system_action_access 
    FROM role_system_action_permission
    WHERE role_id = p_role_id;
END$$

DROP PROCEDURE IF EXISTS `generateRoleMenuItemDualListBoxOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleMenuItemDualListBoxOptions` (IN `p_role_id` INT)   BEGIN
	SELECT menu_item_id, menu_item_name 
    FROM menu_item 
    WHERE menu_item_id NOT IN (SELECT menu_item_id FROM role_permission WHERE role_id = p_role_id)
    ORDER BY menu_item_name;
END$$

DROP PROCEDURE IF EXISTS `generateRoleMenuItemPermissionTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleMenuItemPermissionTable` (IN `p_role_id` INT)   BEGIN
	SELECT role_permission_id, menu_item_name, read_access, write_access, create_access, delete_access 
    FROM role_permission
    WHERE role_id = p_role_id
    ORDER BY menu_item_name;
END$$

DROP PROCEDURE IF EXISTS `generateRoleSystemActionDualListBoxOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleSystemActionDualListBoxOptions` (IN `p_role_id` INT)   BEGIN
	SELECT system_action_id, system_action_name 
    FROM system_action
    WHERE system_action_id NOT IN (SELECT system_action_id FROM role_system_action_permission WHERE role_id = p_role_id)
    ORDER BY system_action_name;
END$$

DROP PROCEDURE IF EXISTS `generateRoleSystemActionPermissionTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleSystemActionPermissionTable` (IN `p_role_id` INT)   BEGIN
	SELECT role_system_action_permission_id, system_action_name, system_action_access 
    FROM role_system_action_permission
    WHERE role_id = p_role_id
    ORDER BY system_action_name;
END$$

DROP PROCEDURE IF EXISTS `generateRoleTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleTable` ()   BEGIN
	SELECT role_id, role_name, role_description
    FROM role 
    ORDER BY role_id;
END$$

DROP PROCEDURE IF EXISTS `generateRoleUserAccountTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRoleUserAccountTable` (IN `p_role_id` INT)   BEGIN
	SELECT role_user_account_id, user_account_id, file_as 
    FROM role_user_account
    WHERE role_id = p_role_id
    ORDER BY file_as;
END$$

DROP PROCEDURE IF EXISTS `generateSystemActionAssignedRoleTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSystemActionAssignedRoleTable` (IN `p_system_action_id` INT)   BEGIN
    SELECT role_system_action_permission_id, role_name, system_action_access 
    FROM role_system_action_permission
    WHERE system_action_id = p_system_action_id;
END$$

DROP PROCEDURE IF EXISTS `generateSystemActionOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSystemActionOptions` ()   BEGIN
    SELECT system_action_id, system_action_name 
    FROM system_action 
    ORDER BY system_action_name;
END$$

DROP PROCEDURE IF EXISTS `generateSystemActionRoleDualListBoxOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSystemActionRoleDualListBoxOptions` (IN `p_system_action_id` INT)   BEGIN
	SELECT role_id, role_name 
    FROM role 
    WHERE role_id NOT IN (SELECT role_id FROM role_system_action_permission WHERE system_action_id = p_system_action_id)
    ORDER BY role_name;
END$$

DROP PROCEDURE IF EXISTS `generateSystemActionTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSystemActionTable` ()   BEGIN
    SELECT system_action_id, system_action_name, system_action_description 
    FROM system_action
    ORDER BY system_action_id;
END$$

DROP PROCEDURE IF EXISTS `generateUserAccountRoleDualListBoxOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateUserAccountRoleDualListBoxOptions` (IN `p_user_account_id` INT)   BEGIN
	SELECT role_id, role_name 
    FROM role 
    WHERE role_id NOT IN (SELECT role_id FROM role_user_account WHERE user_account_id = p_user_account_id)
    ORDER BY role_name;
END$$

DROP PROCEDURE IF EXISTS `generateUserAccountRoleList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateUserAccountRoleList` (IN `p_user_account_id` INT)   BEGIN
	SELECT role_user_account_id, role_name, date_assigned
    FROM role_user_account
    WHERE user_account_id = p_user_account_id
    ORDER BY role_name;
END$$

DROP PROCEDURE IF EXISTS `getAppModule`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAppModule` (IN `p_app_module_id` INT)   BEGIN
	SELECT * FROM app_module
	WHERE app_module_id = p_app_module_id;
END$$

DROP PROCEDURE IF EXISTS `getEmailNotificationTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getEmailNotificationTemplate` (IN `p_notification_setting_id` INT)   BEGIN
	SELECT * FROM notification_setting_email_template
	WHERE notification_setting_id = p_notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getEmailSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getEmailSetting` (IN `p_email_setting_id` INT)   BEGIN
	SELECT * FROM email_setting
    WHERE email_setting_id = p_email_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getInternalNotesAttachment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getInternalNotesAttachment` (IN `p_internal_notes_id` INT)   BEGIN
	SELECT * FROM internal_notes_attachment
	WHERE internal_notes_id = p_internal_notes_id;
END$$

DROP PROCEDURE IF EXISTS `getLoginCredentials`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLoginCredentials` (IN `p_user_account_id` INT, IN `p_credentials` VARCHAR(255))   BEGIN
    SELECT *
    FROM user_account
    WHERE user_account_id = p_user_account_id
       OR username = BINARY p_credentials
       OR email = BINARY p_credentials;
END$$

DROP PROCEDURE IF EXISTS `getMenuGroup`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMenuGroup` (IN `p_menu_group_id` INT)   BEGIN
	SELECT * FROM menu_group
	WHERE menu_group_id = p_menu_group_id;
END$$

DROP PROCEDURE IF EXISTS `getMenuItem`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMenuItem` (IN `p_menu_item_id` INT)   BEGIN
	SELECT * FROM menu_item
	WHERE menu_item_id = p_menu_item_id;
END$$

DROP PROCEDURE IF EXISTS `getPasswordHistory`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPasswordHistory` (IN `p_user_account_id` INT)   BEGIN
    SELECT password 
    FROM password_history
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `getRole`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRole` (IN `p_role_id` INT)   BEGIN
	SELECT * FROM role
    WHERE role_id = p_role_id;
END$$

DROP PROCEDURE IF EXISTS `getSecuritySetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSecuritySetting` (IN `p_security_setting_id` INT)   BEGIN
	SELECT * FROM security_setting
	WHERE security_setting_id = p_security_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getSystemAction`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSystemAction` (IN `p_system_action_id` INT)   BEGIN
	SELECT * FROM system_action
	WHERE system_action_id = p_system_action_id;
END$$

DROP PROCEDURE IF EXISTS `getUploadSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUploadSetting` (IN `p_upload_setting_id` INT)   BEGIN
	SELECT * FROM upload_setting
	WHERE upload_setting_id = p_upload_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getUploadSettingFileExtension`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUploadSettingFileExtension` (IN `p_upload_setting_id` INT)   BEGIN
	SELECT * FROM upload_setting_file_extension
	WHERE upload_setting_id = p_upload_setting_id;
END$$

DROP PROCEDURE IF EXISTS `insertRolePermission`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertRolePermission` (IN `p_role_id` INT, IN `p_role_name` VARCHAR(100), IN `p_menu_item_id` INT, IN `p_menu_item_name` VARCHAR(100), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO role_permission (role_id, role_name, menu_item_id, menu_item_name, last_log_by) 
	VALUES(p_role_id, p_role_name, p_menu_item_id, p_menu_item_name, p_last_log_by);

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `insertRoleSystemActionPermission`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertRoleSystemActionPermission` (IN `p_role_id` INT, IN `p_role_name` VARCHAR(100), IN `p_system_action_id` INT, IN `p_system_action_name` VARCHAR(100), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO role_system_action_permission (role_id, role_name, system_action_id, system_action_name, last_log_by) 
	VALUES(p_role_id, p_role_name, p_system_action_id, p_system_action_name, p_last_log_by);

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `insertRoleUserAccount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertRoleUserAccount` (IN `p_role_id` INT, IN `p_role_name` VARCHAR(100), IN `p_user_account_id` INT, IN `p_file_as` VARCHAR(100), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO role_user_account (role_id, role_name, user_account_id, file_as, last_log_by) 
	VALUES(p_role_id, p_role_name, p_user_account_id, p_file_as, p_last_log_by);

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveAppModule`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveAppModule` (IN `p_app_module_id` INT, IN `p_app_module_name` VARCHAR(100), IN `p_app_module_description` VARCHAR(500), IN `p_menu_item_id` INT, IN `p_menu_item_name` VARCHAR(100), IN `p_order_sequence` TINYINT(10), IN `p_last_log_by` INT, OUT `p_new_app_module_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `saveImport`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveImport` (IN `p_table_name` VARCHAR(255), IN `p_columns` TEXT, IN `p_placeholders` TEXT, IN `p_updateFields` TEXT, IN `p_values` TEXT)   BEGIN
    SET @sql = CONCAT(
        'INSERT INTO ', p_table_name, ' (', p_columns, ') ',
        'VALUES ', p_values, ' ',
        'ON DUPLICATE KEY UPDATE ', p_updateFields
    );

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `saveMenuGroup`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveMenuGroup` (IN `p_menu_group_id` INT, IN `p_menu_group_name` VARCHAR(100), IN `p_app_module_id` INT, IN `p_app_module_name` VARCHAR(100), IN `p_order_sequence` TINYINT(10), IN `p_last_log_by` INT, OUT `p_new_menu_group_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `saveMenuItem`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveMenuItem` (IN `p_menu_item_id` INT, IN `p_menu_item_name` VARCHAR(100), IN `p_menu_item_url` VARCHAR(50), IN `p_menu_item_icon` VARCHAR(50), IN `p_app_module_id` INT, IN `p_app_module_name` VARCHAR(100), IN `p_parent_id` INT, IN `p_parent_name` VARCHAR(100), IN `p_table_name` VARCHAR(100), IN `p_order_sequence` TINYINT(10), IN `p_last_log_by` INT, OUT `p_new_menu_item_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_menu_item_id IS NULL OR NOT EXISTS (SELECT 1 FROM menu_item WHERE menu_item_id = p_menu_item_id) THEN
        INSERT INTO menu_item (menu_item_name, menu_item_url, menu_item_icon, app_module_id, app_module_name, parent_id, parent_name, table_name, order_sequence, last_log_by) 
        VALUES(p_menu_item_name, p_menu_item_url, p_menu_item_icon, p_app_module_id, p_app_module_name, p_parent_id, p_parent_name, p_table_name, p_order_sequence, p_last_log_by);
        
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
            app_module_id = p_app_module_id,
            app_module_name = p_app_module_name,
            parent_id = p_parent_id,
            parent_name = p_parent_name,
            table_name = p_table_name,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE menu_item_id = p_menu_item_id;

        SET p_new_menu_item_id = p_menu_item_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveRole`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveRole` (IN `p_role_id` INT, IN `p_role_name` VARCHAR(100), IN `p_role_description` VARCHAR(200), IN `p_last_log_by` INT, OUT `p_new_role_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `saveSystemAction`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveSystemAction` (IN `p_system_action_id` INT, IN `p_system_action_name` VARCHAR(100), IN `p_system_action_description` VARCHAR(200), IN `p_last_log_by` INT, OUT `p_new_system_action_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_system_action_id IS NULL OR NOT EXISTS (SELECT 1 FROM system_action WHERE system_action_id = p_system_action_id) THEN
        INSERT INTO system_action (system_action_name, system_action_description, last_log_by) 
        VALUES(p_system_action_name, p_system_action_description, p_last_log_by);
        
        SET p_new_system_action_id = LAST_INSERT_ID();
    ELSE
        UPDATE role_system_action_permission
        SET system_action_name = p_system_action_name,
            last_log_by = p_last_log_by
        WHERE system_action_id = p_system_action_id;
        
        UPDATE system_action
        SET system_action_name = p_system_action_name,
            system_action_description = p_system_action_description,
            last_log_by = p_last_log_by
        WHERE system_action_id = p_system_action_id;

        SET p_new_system_action_id = p_system_action_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateAccountLock`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateAccountLock` (IN `p_user_account_id` INT, IN `p_locked` VARCHAR(255), IN `p_account_lock_duration` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET locked = p_locked, 
        account_lock_duration = p_account_lock_duration
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateAppLogo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateAppLogo` (IN `p_app_module_id` INT, IN `p_app_logo` VARCHAR(500), IN `p_last_log_by` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `updateFailedOTPAttempts`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateFailedOTPAttempts` (IN `p_user_account_id` INT, IN `p_failed_otp_attempts` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    
    UPDATE user_account
    SET failed_otp_attempts = p_failed_otp_attempts
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateLastConnection`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLastConnection` (IN `p_user_account_id` INT, IN `p_session_token` VARCHAR(255), IN `p_last_connection_date` DATETIME)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    
    UPDATE user_account
    SET session_token = p_session_token, 
        last_connection_date = p_last_connection_date
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateLoginAttempt`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLoginAttempt` (IN `p_user_account_id` INT, IN `p_failed_login_attempts` VARCHAR(255), IN `p_last_failed_login_attempt` DATETIME)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET failed_login_attempts = p_failed_login_attempts, 
        last_failed_login_attempt = p_last_failed_login_attempt
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateOTP`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOTP` (IN `p_user_account_id` INT, IN `p_otp` VARCHAR(255), IN `p_otp_expiry_date` VARCHAR(255), IN `p_failed_otp_attempts` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET otp = p_otp, 
        otp_expiry_date = p_otp_expiry_date, 
        failed_otp_attempts = p_failed_otp_attempts
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateOTPAsExpired`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateOTPAsExpired` (IN `p_user_account_id` INT, IN `p_otp_expiry_date` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET otp_expiry_date = p_otp_expiry_date
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateResetToken`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateResetToken` (IN `p_user_account_id` INT, IN `p_reset_token` VARCHAR(255), IN `p_reset_token_expiry_date` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET reset_token = p_reset_token, 
        reset_token_expiry_date = p_reset_token_expiry_date
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateResetTokenAsExpired`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateResetTokenAsExpired` (IN `p_user_account_id` INT, IN `p_reset_token_expiry_date` VARCHAR(255))   BEGIN
    UPDATE user_account
    SET reset_token_expiry_date = p_reset_token_expiry_date
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `updateRolePermission`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateRolePermission` (IN `p_role_permission_id` INT, IN `p_access_type` VARCHAR(10), IN `p_access` TINYINT(1), IN `p_last_log_by` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `updateRoleSystemActionPermission`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateRoleSystemActionPermission` (IN `p_role_system_action_permission_id` INT, IN `p_system_action_access` TINYINT(1), IN `p_last_log_by` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `updateUserPassword`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserPassword` (IN `p_user_account_id` INT, IN `p_password` VARCHAR(255), IN `p_password_expiry_date` VARCHAR(255), IN `p_locked` VARCHAR(255), IN `p_failed_login_attempts` VARCHAR(255), IN `p_account_lock_duration` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO password_history (user_account_id, password) 
    VALUES (p_user_account_id, p_password);

    UPDATE user_account
    SET password = p_password, 
        password_expiry_date = p_password_expiry_date, 
        last_password_change = NOW(), 
        locked = p_locked, 
        failed_login_attempts = p_failed_login_attempts, 
        account_lock_duration = p_account_lock_duration, 
        last_log_by = p_user_account_id
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `app_module`
--

DROP TABLE IF EXISTS `app_module`;
CREATE TABLE `app_module` (
  `app_module_id` int(10) UNSIGNED NOT NULL,
  `app_module_name` varchar(100) NOT NULL,
  `app_module_description` varchar(500) NOT NULL,
  `app_logo` varchar(500) DEFAULT NULL,
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `menu_item_name` varchar(100) NOT NULL,
  `order_sequence` tinyint(10) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_module`
--

INSERT INTO `app_module` (`app_module_id`, `app_module_name`, `app_module_description`, `app_logo`, `menu_item_id`, `menu_item_name`, `order_sequence`, `created_date`, `last_log_by`) VALUES
(1, 'Settings', 'Centralized management hub for comprehensive organizational oversight and control', '../security/app-module/image/logo/1/fm981w.png', 1, 'App Module', 3, '2024-10-13 16:19:59', 2),
(2, 'Employees', 'Centralize employee information', '../security/app-module/image/logo/2/kwDc.png', 23, 'Inventory Overview', 1, '2024-10-13 16:19:59', 1),
(3, 'Customer', 'Bring all your customer information into one easy-to-access location', '../security/app-module/image/logo/3/rL4r.png', 50, 'Customer', 3, '2024-10-13 16:19:59', 1),
(4, 'Website Studio', 'Create and customize your website', '../security/app-module/image/logo/4/TnX0.png', 54, 'Websites', 1, '2024-10-13 16:19:59', 1),
(5, 'CRM', 'Track leads and close opportunities', '../security/app-module/image/logo/5/CxLn.png', 73, 'My Bookings', 3, '2024-10-13 16:19:59', 1);

--
-- Triggers `app_module`
--
DROP TRIGGER IF EXISTS `app_module_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `app_module_trigger_insert` AFTER INSERT ON `app_module` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'App module created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('app_module', NEW.app_module_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `app_module_trigger_update`;
DELIMITER $$
CREATE TRIGGER `app_module_trigger_update` AFTER UPDATE ON `app_module` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'App module changed.<br/><br/>';

    IF NEW.app_module_name <> OLD.app_module_name THEN
        SET audit_log = CONCAT(audit_log, "App Module Name: ", OLD.app_module_name, " -> ", NEW.app_module_name, "<br/>");
    END IF;

    IF NEW.app_module_description <> OLD.app_module_description THEN
        SET audit_log = CONCAT(audit_log, "App Module Description: ", OLD.app_module_description, " -> ", NEW.app_module_description, "<br/>");
    END IF;

    IF NEW.menu_item_name <> OLD.menu_item_name THEN
        SET audit_log = CONCAT(audit_log, "Menu Item: ", OLD.menu_item_name, " -> ", NEW.menu_item_name, "<br/>");
    END IF;

    IF NEW.order_sequence <> OLD.order_sequence THEN
        SET audit_log = CONCAT(audit_log, "Order Sequence: ", OLD.order_sequence, " -> ", NEW.order_sequence, "<br/>");
    END IF;
    
    IF audit_log <> 'App module changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('app_module', NEW.app_module_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `audit_log_id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `log` text NOT NULL,
  `changed_by` int(10) UNSIGNED NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`audit_log_id`, `table_name`, `reference_id`, `log`, `changed_by`, `changed_at`, `created_date`) VALUES
(1, 'app_module', 1, 'App module changed.<br/><br/>Menu Item: Account Setting -> App Module<br/>', 1, '2024-10-13 16:27:40', '2024-10-13 16:27:40'),
(2, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-10-13 16:20:55 -> 2024-10-19 17:23:18<br/>', 1, '2024-10-19 17:23:18', '2024-10-19 17:23:18'),
(3, 'menu_item', 5, 'Menu item changed.<br/><br/>Menu Item Icon:  -> ki-duotone ki-shop<br/>', 2, '2024-10-19 19:57:02', '2024-10-19 19:57:02'),
(4, 'menu_item', 5, 'Menu item changed.<br/><br/>Menu Item Icon: ki-duotone ki-shop -> ki-outline ki-shop<br/>', 2, '2024-10-19 20:07:07', '2024-10-19 20:07:07'),
(5, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-10-19 17:23:18 -> 2024-10-21 09:52:26<br/>', 1, '2024-10-21 09:52:26', '2024-10-21 09:52:26'),
(6, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-10-21 09:52:26 -> 2024-10-21 15:07:34<br/>', 1, '2024-10-21 15:07:34', '2024-10-21 15:07:34'),
(7, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-10-21 15:07:34 -> 2024-10-21 15:25:07<br/>', 1, '2024-10-21 15:25:07', '2024-10-21 15:25:07'),
(8, 'app_module', 1, 'App module changed.<br/><br/>Order Sequence: 100 -> 10<br/>', 2, '2024-10-21 16:53:19', '2024-10-21 16:53:19'),
(9, 'app_module', 1, 'App module changed.<br/><br/>Order Sequence: 10 -> 2<br/>', 2, '2024-10-21 16:53:24', '2024-10-21 16:53:24'),
(10, 'app_module', 1, 'App module changed.<br/><br/>Order Sequence: 2 -> 1<br/>', 2, '2024-10-21 16:53:26', '2024-10-21 16:53:26'),
(11, 'app_module', 1, 'App module changed.<br/><br/>Order Sequence: 1 -> 12<br/>', 2, '2024-10-21 16:53:29', '2024-10-21 16:53:29'),
(12, 'app_module', 1, 'App module changed.<br/><br/>Order Sequence: 12 -> 3<br/>', 2, '2024-10-21 16:53:30', '2024-10-21 16:53:30'),
(13, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-10-21 15:25:07 -> 2024-10-21 21:04:17<br/>', 1, '2024-10-21 21:04:17', '2024-10-21 21:04:17'),
(14, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-10-21 21:04:17 -> 2024-10-23 14:09:19<br/>', 1, '2024-10-23 14:09:19', '2024-10-23 14:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `email_setting`
--

DROP TABLE IF EXISTS `email_setting`;
CREATE TABLE `email_setting` (
  `email_setting_id` int(10) UNSIGNED NOT NULL,
  `email_setting_name` varchar(100) NOT NULL,
  `email_setting_description` varchar(200) NOT NULL,
  `mail_host` varchar(100) NOT NULL,
  `port` varchar(10) NOT NULL,
  `smtp_auth` int(1) NOT NULL,
  `smtp_auto_tls` int(1) NOT NULL,
  `mail_username` varchar(200) NOT NULL,
  `mail_password` varchar(250) NOT NULL,
  `mail_encryption` varchar(20) DEFAULT NULL,
  `mail_from_name` varchar(200) DEFAULT NULL,
  `mail_from_email` varchar(200) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_setting`
--

INSERT INTO `email_setting` (`email_setting_id`, `email_setting_name`, `email_setting_description`, `mail_host`, `port`, `smtp_auth`, `smtp_auto_tls`, `mail_username`, `mail_password`, `mail_encryption`, `mail_from_name`, `mail_from_email`, `created_date`, `last_log_by`) VALUES
(1, 'Security Email Setting', '\r\nEmail setting for security emails.', 'smtp.hostinger.com', '465', 1, 0, 'cgmi-noreply@christianmotors.ph', 'UsDpF0dYRC6M9v0tT3MHq%2BlrRJu01%2Fb95Dq%2BAeCfu2Y%3D', 'ssl', 'cgmi-noreply@christianmotors.ph', 'cgmi-noreply@christianmotors.ph', '2024-10-13 16:15:22', 1);

--
-- Triggers `email_setting`
--
DROP TRIGGER IF EXISTS `email_setting_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `email_setting_trigger_insert` AFTER INSERT ON `email_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Email setting created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('email_setting', NEW.email_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `email_setting_trigger_update`;
DELIMITER $$
CREATE TRIGGER `email_setting_trigger_update` AFTER UPDATE ON `email_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Email setting changed.<br/><br/>';

    IF NEW.email_setting_name <> OLD.email_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Email Setting Name: ", OLD.email_setting_name, " -> ", NEW.email_setting_name, "<br/>");
    END IF;

    IF NEW.email_setting_description <> OLD.email_setting_description THEN
        SET audit_log = CONCAT(audit_log, "Email Setting Description: ", OLD.email_setting_description, " -> ", NEW.email_setting_description, "<br/>");
    END IF;

    IF NEW.mail_host <> OLD.mail_host THEN
        SET audit_log = CONCAT(audit_log, "Host: ", OLD.mail_host, " -> ", NEW.mail_host, "<br/>");
    END IF;

    IF NEW.port <> OLD.port THEN
        SET audit_log = CONCAT(audit_log, "Port: ", OLD.port, " -> ", NEW.port, "<br/>");
    END IF;

    IF NEW.smtp_auth <> OLD.smtp_auth THEN
        SET audit_log = CONCAT(audit_log, "SMTP Authentication: ", OLD.smtp_auth, " -> ", NEW.smtp_auth, "<br/>");
    END IF;

    IF NEW.smtp_auto_tls <> OLD.smtp_auto_tls THEN
        SET audit_log = CONCAT(audit_log, "SMTP Auto TLS: ", OLD.smtp_auto_tls, " -> ", NEW.smtp_auto_tls, "<br/>");
    END IF;

    IF NEW.mail_username <> OLD.mail_username THEN
        SET audit_log = CONCAT(audit_log, "Mail Username: ", OLD.mail_username, " -> ", NEW.mail_username, "<br/>");
    END IF;

    IF NEW.mail_encryption <> OLD.mail_encryption THEN
        SET audit_log = CONCAT(audit_log, "Mail Encryption: ", OLD.mail_encryption, " -> ", NEW.mail_encryption, "<br/>");
    END IF;

    IF NEW.mail_from_name <> OLD.mail_from_name THEN
        SET audit_log = CONCAT(audit_log, "Mail From Name: ", OLD.mail_from_name, " -> ", NEW.mail_from_name, "<br/>");
    END IF;

    IF NEW.mail_from_email <> OLD.mail_from_email THEN
        SET audit_log = CONCAT(audit_log, "Mail From Email: ", OLD.mail_from_email, " -> ", NEW.mail_from_email, "<br/>");
    END IF;
    
    IF audit_log <> 'Email setting changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('email_setting', NEW.email_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `internal_notes`
--

DROP TABLE IF EXISTS `internal_notes`;
CREATE TABLE `internal_notes` (
  `internal_notes_id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `internal_note` varchar(5000) NOT NULL,
  `internal_note_by` int(10) UNSIGNED NOT NULL,
  `internal_note_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_notes`
--

INSERT INTO `internal_notes` (`internal_notes_id`, `table_name`, `reference_id`, `internal_note`, `internal_note_by`, `internal_note_date`, `created_date`) VALUES
(1, 'app_module', 1, 'asdasdasdasdsdad', 1, '2024-10-21 16:32:17', '2024-10-21 16:32:17'),
(37, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:43', '2024-10-21 21:07:43'),
(38, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:47', '2024-10-21 21:07:47'),
(39, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:47', '2024-10-21 21:07:47'),
(40, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:47', '2024-10-21 21:07:47'),
(41, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56'),
(42, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56'),
(43, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56'),
(44, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56'),
(45, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56'),
(46, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56'),
(47, 'app_module', 1, 'asdasdasd', 1, '2024-10-21 21:07:56', '2024-10-21 21:07:56');

-- --------------------------------------------------------

--
-- Table structure for table `internal_notes_attachment`
--

DROP TABLE IF EXISTS `internal_notes_attachment`;
CREATE TABLE `internal_notes_attachment` (
  `internal_notes_attachment_id` int(10) UNSIGNED NOT NULL,
  `internal_notes_id` int(10) UNSIGNED NOT NULL,
  `attachment_file_name` varchar(500) NOT NULL,
  `attachment_file_size` double NOT NULL,
  `attachment_path_file` varchar(500) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internal_notes_attachment`
--

INSERT INTO `internal_notes_attachment` (`internal_notes_attachment_id`, `internal_notes_id`, `attachment_file_name`, `attachment_file_size`, `attachment_path_file`, `created_date`) VALUES
(1, 1, 'asdasd', 120000, 'asdasd.pdf', '2024-10-21 21:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `menu_group`
--

DROP TABLE IF EXISTS `menu_group`;
CREATE TABLE `menu_group` (
  `menu_group_id` int(10) UNSIGNED NOT NULL,
  `menu_group_name` varchar(100) NOT NULL,
  `app_module_id` int(10) UNSIGNED NOT NULL,
  `app_module_name` varchar(100) NOT NULL,
  `order_sequence` tinyint(10) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_group`
--

INSERT INTO `menu_group` (`menu_group_id`, `menu_group_name`, `app_module_id`, `app_module_name`, `order_sequence`, `created_date`, `last_log_by`) VALUES
(1, 'Technical', 1, 'Settings', 100, '2024-10-13 16:19:36', 2),
(2, 'Administration', 1, 'Settings', 5, '2024-10-13 16:19:36', 2);

--
-- Triggers `menu_group`
--
DROP TRIGGER IF EXISTS `menu_group_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `menu_group_trigger_insert` AFTER INSERT ON `menu_group` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Menu group created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('menu_group', NEW.menu_group_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `menu_group_trigger_update`;
DELIMITER $$
CREATE TRIGGER `menu_group_trigger_update` AFTER UPDATE ON `menu_group` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Menu group changed.<br/><br/>';

    IF NEW.menu_group_name <> OLD.menu_group_name THEN
        SET audit_log = CONCAT(audit_log, "Menu Group Name: ", OLD.menu_group_name, " -> ", NEW.menu_group_name, "<br/>");
    END IF;
    
      IF NEW.app_module_name <> OLD.app_module_name THEN
        SET audit_log = CONCAT(audit_log, "App Module: ", OLD.app_module_name, " -> ", NEW.app_module_name, "<br/>");
    END IF;

    IF NEW.order_sequence <> OLD.order_sequence THEN
        SET audit_log = CONCAT(audit_log, "Order Sequence: ", OLD.order_sequence, " -> ", NEW.order_sequence, "<br/>");
    END IF;
    
    IF audit_log <> 'Menu group changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('menu_group', NEW.menu_group_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `menu_item`
--

DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE `menu_item` (
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `menu_item_name` varchar(100) NOT NULL,
  `menu_item_url` varchar(50) DEFAULT NULL,
  `menu_item_icon` varchar(50) DEFAULT NULL,
  `app_module_id` int(10) UNSIGNED NOT NULL,
  `app_module_name` varchar(100) NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `order_sequence` tinyint(10) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_item`
--

INSERT INTO `menu_item` (`menu_item_id`, `menu_item_name`, `menu_item_url`, `menu_item_icon`, `app_module_id`, `app_module_name`, `parent_id`, `parent_name`, `table_name`, `order_sequence`, `created_date`, `last_log_by`) VALUES
(1, 'App Module', 'app-module.php', '', 1, 'Settings', 0, '', 'app_module', 1, '2024-10-19 21:30:54', 2),
(2, 'General Settings', 'general-settings.php', '', 1, 'Settings', 0, '', '', 7, '2024-10-19 21:30:54', 2),
(3, 'Users & Companies', '', '', 1, 'Settings', 0, '', '', 21, '2024-10-19 21:30:54', 2),
(4, 'User Account', 'user-account.php', 'ki-outline ki-user', 1, 'Settings', 3, 'Users & Companies', 'user_account', 21, '2024-10-19 21:30:54', 2),
(5, 'Company', 'company.php', 'ki-outline ki-shop', 1, 'Settings', 3, 'Users & Companies', 'company', 3, '2024-10-19 21:30:54', 2),
(6, 'Role', 'role.php', '', 1, 'Settings', NULL, NULL, 'role', 3, '2024-10-19 21:30:54', 2),
(7, 'User Interface', '', '', 1, 'Settings', NULL, NULL, '', 16, '2024-10-19 21:30:54', 2),
(8, 'Menu Item', 'menu-item.php', 'ki-outline ki-data', 1, 'Settings', 7, 'User Interface', 'menu_item', 2, '2024-10-19 21:30:54', 2),
(9, 'System Action', 'system-action.php', 'ki-outline ki-key-square', 1, 'Settings', 7, 'User Interface', 'system_action', 2, '2024-10-19 21:30:54', 2);

--
-- Triggers `menu_item`
--
DROP TRIGGER IF EXISTS `menu_item_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `menu_item_trigger_insert` AFTER INSERT ON `menu_item` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Menu item created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('menu_item', NEW.menu_item_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `menu_item_trigger_update`;
DELIMITER $$
CREATE TRIGGER `menu_item_trigger_update` AFTER UPDATE ON `menu_item` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Menu item changed.<br/><br/>';

    IF NEW.menu_item_name <> OLD.menu_item_name THEN
        SET audit_log = CONCAT(audit_log, "Menu Item Name: ", OLD.menu_item_name, " -> ", NEW.menu_item_name, "<br/>");
    END IF;

    IF NEW.menu_item_url <> OLD.menu_item_url THEN
        SET audit_log = CONCAT(audit_log, "Menu Item URL: ", OLD.menu_item_url, " -> ", NEW.menu_item_url, "<br/>");
    END IF;

    IF NEW.menu_item_icon <> OLD.menu_item_icon THEN
        SET audit_log = CONCAT(audit_log, "Menu Item Icon: ", OLD.menu_item_icon, " -> ", NEW.menu_item_icon, "<br/>");
    END IF;

    IF NEW.app_module_name <> OLD.app_module_name THEN
        SET audit_log = CONCAT(audit_log, "App Module: ", OLD.app_module_name, " -> ", NEW.app_module_name, "<br/>");
    END IF;

    IF NEW.parent_name <> OLD.parent_name THEN
        SET audit_log = CONCAT(audit_log, "Parent: ", OLD.parent_name, " -> ", NEW.parent_name, "<br/>");
    END IF;

    IF NEW.table_name <> OLD.table_name THEN
        SET audit_log = CONCAT(audit_log, "Parent: ", OLD.table_name, " -> ", NEW.table_name, "<br/>");
    END IF;

    IF NEW.order_sequence <> OLD.order_sequence THEN
        SET audit_log = CONCAT(audit_log, "Order Sequence: ", OLD.order_sequence, " -> ", NEW.order_sequence, "<br/>");
    END IF;
    
    IF audit_log <> 'Menu item changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('menu_item', NEW.menu_item_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting`
--

DROP TABLE IF EXISTS `notification_setting`;
CREATE TABLE `notification_setting` (
  `notification_setting_id` int(10) UNSIGNED NOT NULL,
  `notification_setting_name` varchar(100) NOT NULL,
  `notification_setting_description` varchar(200) NOT NULL,
  `system_notification` int(1) NOT NULL DEFAULT 1,
  `email_notification` int(1) NOT NULL DEFAULT 0,
  `sms_notification` int(1) NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_setting`
--

INSERT INTO `notification_setting` (`notification_setting_id`, `notification_setting_name`, `notification_setting_description`, `system_notification`, `email_notification`, `sms_notification`, `created_date`, `last_log_by`) VALUES
(1, 'Login OTP', 'Notification setting for Login OTP received by the users.', 0, 1, 0, '2024-10-13 16:15:08', 1),
(2, 'Forgot Password', 'Notification setting when the user initiates forgot password.', 0, 1, 0, '2024-10-13 16:15:08', 1),
(3, 'Registration Verification', 'Notification setting when the user sign-up for an account.', 0, 1, 0, '2024-10-13 16:15:08', 1);

--
-- Triggers `notification_setting`
--
DROP TRIGGER IF EXISTS `notification_setting_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `notification_setting_trigger_insert` AFTER INSERT ON `notification_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Notification setting created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('notification_setting', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `notification_setting_trigger_update`;
DELIMITER $$
CREATE TRIGGER `notification_setting_trigger_update` AFTER UPDATE ON `notification_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Notification setting changed.<br/><br/>';

    IF NEW.notification_setting_name <> OLD.notification_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Notification Setting Name: ", OLD.notification_setting_name, " -> ", NEW.notification_setting_name, "<br/>");
    END IF;

    IF NEW.notification_setting_description <> OLD.notification_setting_description THEN
        SET audit_log = CONCAT(audit_log, "Notification Setting Description: ", OLD.notification_setting_description, " -> ", NEW.notification_setting_description, "<br/>");
    END IF;

    IF NEW.system_notification <> OLD.system_notification THEN
        SET audit_log = CONCAT(audit_log, "System Notification: ", OLD.system_notification, " -> ", NEW.system_notification, "<br/>");
    END IF;

    IF NEW.email_notification <> OLD.email_notification THEN
        SET audit_log = CONCAT(audit_log, "Email Notification: ", OLD.email_notification, " -> ", NEW.email_notification, "<br/>");
    END IF;

    IF NEW.sms_notification <> OLD.sms_notification THEN
        SET audit_log = CONCAT(audit_log, "SMS Notification: ", OLD.sms_notification, " -> ", NEW.sms_notification, "<br/>");
    END IF;

    IF audit_log <> 'Notification setting changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('notification_setting', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting_email_template`
--

DROP TABLE IF EXISTS `notification_setting_email_template`;
CREATE TABLE `notification_setting_email_template` (
  `notification_setting_email_id` int(10) UNSIGNED NOT NULL,
  `notification_setting_id` int(10) UNSIGNED NOT NULL,
  `email_notification_subject` varchar(200) NOT NULL,
  `email_notification_body` longtext NOT NULL,
  `email_setting_id` int(10) UNSIGNED NOT NULL,
  `email_setting_name` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_setting_email_template`
--

INSERT INTO `notification_setting_email_template` (`notification_setting_email_id`, `notification_setting_id`, `email_notification_subject`, `email_notification_body`, `email_setting_id`, `email_setting_name`, `created_date`, `last_log_by`) VALUES
(1, 1, 'Login OTP - Secure Access to Your Account', '<p>To ensure the security of your account, we have generated a unique One-Time Password (OTP) for you to use during the login process. Please use the following OTP to access your account:</p>\n<p><br>OTP: <strong>#{OTP_CODE}</strong></p>\n<p><br>Please note that this OTP is valid for &nbsp;<strong>#{OTP_CODE_VALIDITY}</strong>. Once you have logged in successfully, we recommend enabling two-factor authentication for an added layer of security.<br>If you did not initiate this login or believe it was sent to you in error, please disregard this email and delete it immediately. Your account\'s security remains our utmost priority.</p>\n<p>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', '2024-10-13 16:15:08', 1),
(2, 2, 'Password Reset Request - Action Required', '<p>We received a request to reset your password. To proceed with the password reset, please follow the steps below:</p>\n<ol>\n<li>\n<p>Click on the following link to reset your password:&nbsp; <strong><a href=\"#{RESET_LINK}\">Password Reset Link</a></strong></p>\n</li>\n<li>\n<p>If you did not request this password reset, please ignore this email. Your account remains secure.</p>\n</li>\n</ol>\n<p>Please note that this link is time-sensitive and will expire after <strong>#{RESET_LINK_VALIDITY}</strong>. If you do not reset your password within this timeframe, you may need to request another password reset.</p>\n<p><br>If you did not initiate this password reset request or believe it was sent to you in error, please disregard this email and delete it immediately. Your account\'s security remains our utmost priority.<br><br>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', '2024-10-13 16:15:08', 1),
(3, 3, 'Sign Up Verification - Action Required', '<p>Thank you for registering! To complete your registration, please verify your email address by clicking the link below:</p>\n<p><a href=\"#{REGISTRATION_VERIFICATION_LINK}\">Click to verify your account</a></p>\n<p>Important: This link is time-sensitive and will expire after #{REGISTRATION_VERIFICATION_VALIDITY}. If you do not verify your email within this timeframe, you may need to request another verification link.</p>\n<p>If you did not register for an account with us, please ignore this email. Your account will not be activated.</p>\n<p>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', '2024-10-13 16:15:08', 1);

--
-- Triggers `notification_setting_email_template`
--
DROP TRIGGER IF EXISTS `notification_setting_email_template_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `notification_setting_email_template_trigger_insert` AFTER INSERT ON `notification_setting_email_template` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Email notification template created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('notification_setting_email_template', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `notification_setting_email_template_trigger_update`;
DELIMITER $$
CREATE TRIGGER `notification_setting_email_template_trigger_update` AFTER UPDATE ON `notification_setting_email_template` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Email notification template changed.<br/><br/>';

    IF NEW.email_notification_subject <> OLD.email_notification_subject THEN
        SET audit_log = CONCAT(audit_log, "Email Notification Subject: ", OLD.email_notification_subject, " -> ", NEW.email_notification_subject, "<br/>");
    END IF;

    IF NEW.email_notification_body <> OLD.email_notification_body THEN
        SET audit_log = CONCAT(audit_log, "Email Notification Body: ", OLD.email_notification_body, " -> ", NEW.email_notification_body, "<br/>");
    END IF;

    IF NEW.email_setting_name <> OLD.email_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Email Setting Name: ", OLD.email_setting_name, " -> ", NEW.email_setting_name, "<br/>");
    END IF;

    IF audit_log <> 'Email notification template changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('notification_setting_email_template', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting_sms_template`
--

DROP TABLE IF EXISTS `notification_setting_sms_template`;
CREATE TABLE `notification_setting_sms_template` (
  `notification_setting_sms_id` int(10) UNSIGNED NOT NULL,
  `notification_setting_id` int(10) UNSIGNED NOT NULL,
  `sms_notification_message` varchar(500) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `notification_setting_sms_template`
--
DROP TRIGGER IF EXISTS `notification_setting_sms_template_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `notification_setting_sms_template_trigger_insert` AFTER INSERT ON `notification_setting_sms_template` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'SMS notification template created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('notification_setting_sms_template', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `notification_setting_sms_template_trigger_update`;
DELIMITER $$
CREATE TRIGGER `notification_setting_sms_template_trigger_update` AFTER UPDATE ON `notification_setting_sms_template` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'SMS notification template changed.<br/><br/>';

    IF NEW.sms_notification_message <> OLD.sms_notification_message THEN
        SET audit_log = CONCAT(audit_log, "SMS Notification Message: ", OLD.sms_notification_message, " -> ", NEW.sms_notification_message, "<br/>");
    END IF;

    IF audit_log <> 'SMS notification template changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('notification_setting_sms_template', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting_system_template`
--

DROP TABLE IF EXISTS `notification_setting_system_template`;
CREATE TABLE `notification_setting_system_template` (
  `notification_setting_system_id` int(10) UNSIGNED NOT NULL,
  `notification_setting_id` int(10) UNSIGNED NOT NULL,
  `system_notification_title` varchar(200) NOT NULL,
  `system_notification_message` varchar(500) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `notification_setting_system_template`
--
DROP TRIGGER IF EXISTS `notification_setting_system_template_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `notification_setting_system_template_trigger_insert` AFTER INSERT ON `notification_setting_system_template` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'System notification template created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('notification_setting_system_template', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `notification_setting_system_template_trigger_update`;
DELIMITER $$
CREATE TRIGGER `notification_setting_system_template_trigger_update` AFTER UPDATE ON `notification_setting_system_template` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'System notification template changed.<br/><br/>';

    IF NEW.system_notification_title <> OLD.system_notification_title THEN
        SET audit_log = CONCAT(audit_log, "System Notification Title: ", OLD.system_notification_title, " -> ", NEW.system_notification_title, "<br/>");
    END IF;

    IF NEW.system_notification_message <> OLD.system_notification_message THEN
        SET audit_log = CONCAT(audit_log, "System Notification Message: ", OLD.system_notification_message, " -> ", NEW.system_notification_message, "<br/>");
    END IF;

    IF audit_log <> 'System notification template changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('notification_setting_system_template', NEW.notification_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

DROP TABLE IF EXISTS `password_history`;
CREATE TABLE `password_history` (
  `password_history_id` int(10) UNSIGNED NOT NULL,
  `user_account_id` int(10) UNSIGNED NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_change_date` datetime DEFAULT current_timestamp(),
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `role_description` varchar(200) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`, `role_description`, `created_date`, `last_log_by`) VALUES
(1, 'Administrator', 'Full access to all features and data within the system. This role have similar access levels to the Admin but is not as powerful as the Super Admin.', '2024-10-13 16:22:48', 1);

--
-- Triggers `role`
--
DROP TRIGGER IF EXISTS `role_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `role_trigger_insert` AFTER INSERT ON `role` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role', NEW.role_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `role_trigger_update`;
DELIMITER $$
CREATE TRIGGER `role_trigger_update` AFTER UPDATE ON `role` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role changed.<br/><br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.role_description <> OLD.role_description THEN
        SET audit_log = CONCAT(audit_log, "Role Description: ", OLD.role_description, " -> ", NEW.role_description, "<br/>");
    END IF;
    
    IF audit_log <> 'Role changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role', NEW.role_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE `role_permission` (
  `role_permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `menu_item_id` int(10) UNSIGNED NOT NULL,
  `menu_item_name` varchar(100) NOT NULL,
  `read_access` tinyint(1) NOT NULL DEFAULT 0,
  `write_access` tinyint(1) NOT NULL DEFAULT 0,
  `create_access` tinyint(1) NOT NULL DEFAULT 0,
  `delete_access` tinyint(1) NOT NULL DEFAULT 0,
  `import_access` tinyint(1) NOT NULL DEFAULT 0,
  `export_access` tinyint(1) NOT NULL DEFAULT 0,
  `log_notes_access` tinyint(1) NOT NULL DEFAULT 0,
  `date_assigned` datetime NOT NULL DEFAULT current_timestamp(),
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`role_permission_id`, `role_id`, `role_name`, `menu_item_id`, `menu_item_name`, `read_access`, `write_access`, `create_access`, `delete_access`, `import_access`, `export_access`, `log_notes_access`, `date_assigned`, `created_date`, `last_log_by`) VALUES
(1, 1, 'Administrator', 1, 'App Module', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(2, 1, 'Administrator', 2, 'General Settings', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(3, 1, 'Administrator', 3, 'Users & Companies', 1, 0, 0, 0, 0, 0, 0, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(4, 1, 'Administrator', 4, 'User Account', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(5, 1, 'Administrator', 5, 'Company', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(6, 1, 'Administrator', 6, 'Role', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(7, 1, 'Administrator', 7, 'User Interface', 1, 0, 0, 0, 0, 0, 0, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(8, 1, 'Administrator', 8, 'Menu Group', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(9, 1, 'Administrator', 9, 'Menu Item', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(10, 1, 'Administrator', 10, 'System Action', 1, 1, 1, 1, 1, 1, 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1);

--
-- Triggers `role_permission`
--
DROP TRIGGER IF EXISTS `role_permission_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `role_permission_trigger_insert` AFTER INSERT ON `role_permission` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role permission created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role_permission', NEW.role_permission_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `role_permission_trigger_update`;
DELIMITER $$
CREATE TRIGGER `role_permission_trigger_update` AFTER UPDATE ON `role_permission` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role permission changed.<br/><br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.menu_item_name <> OLD.menu_item_name THEN
        SET audit_log = CONCAT(audit_log, "Menu Item: ", OLD.menu_item_name, " -> ", NEW.menu_item_name, "<br/>");
    END IF;

    IF NEW.read_access <> OLD.read_access THEN
        SET audit_log = CONCAT(audit_log, "Read Access: ", OLD.read_access, " -> ", NEW.read_access, "<br/>");
    END IF;

    IF NEW.write_access <> OLD.write_access THEN
        SET audit_log = CONCAT(audit_log, "Write Access: ", OLD.write_access, " -> ", NEW.write_access, "<br/>");
    END IF;

    IF NEW.create_access <> OLD.create_access THEN
        SET audit_log = CONCAT(audit_log, "Create Access: ", OLD.create_access, " -> ", NEW.create_access, "<br/>");
    END IF;

    IF NEW.delete_access <> OLD.delete_access THEN
        SET audit_log = CONCAT(audit_log, "Delete Access: ", OLD.delete_access, " -> ", NEW.delete_access, "<br/>");
    END IF;

    IF NEW.import_access <> OLD.import_access THEN
        SET audit_log = CONCAT(audit_log, "Import Access: ", OLD.import_access, " -> ", NEW.import_access, "<br/>");
    END IF;

    IF NEW.export_access <> OLD.export_access THEN
        SET audit_log = CONCAT(audit_log, "Export Access: ", OLD.export_access, " -> ", NEW.export_access, "<br/>");
    END IF;

    IF NEW.log_notes_access <> OLD.log_notes_access THEN
        SET audit_log = CONCAT(audit_log, "Log Notes Access: ", OLD.log_notes_access, " -> ", NEW.log_notes_access, "<br/>");
    END IF;
    
    IF audit_log <> 'Role permission changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role_permission', NEW.role_permission_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_system_action_permission`
--

DROP TABLE IF EXISTS `role_system_action_permission`;
CREATE TABLE `role_system_action_permission` (
  `role_system_action_permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `system_action_id` int(10) UNSIGNED NOT NULL,
  `system_action_name` varchar(100) NOT NULL,
  `system_action_access` tinyint(1) NOT NULL DEFAULT 0,
  `date_assigned` datetime NOT NULL DEFAULT current_timestamp(),
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_system_action_permission`
--

INSERT INTO `role_system_action_permission` (`role_system_action_permission_id`, `role_id`, `role_name`, `system_action_id`, `system_action_name`, `system_action_access`, `date_assigned`, `created_date`, `last_log_by`) VALUES
(1, 1, 'Administrator', 1, 'Update System Settings', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(2, 1, 'Administrator', 2, 'Update Security Settings', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(3, 1, 'Administrator', 3, 'Activate User Account', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(4, 1, 'Administrator', 4, 'Deactivate User Account', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(5, 1, 'Administrator', 5, 'Lock User Account', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(6, 1, 'Administrator', 6, 'Unlock User Account', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(7, 1, 'Administrator', 7, 'Add Role User Account', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(8, 1, 'Administrator', 8, 'Delete Role User Account', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(9, 1, 'Administrator', 9, 'Add Role Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(10, 1, 'Administrator', 10, 'Update Role Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(11, 1, 'Administrator', 11, 'Delete Role Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(12, 1, 'Administrator', 12, 'Add Role System Action Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(13, 1, 'Administrator', 13, 'Update Role System Action Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(14, 1, 'Administrator', 14, 'Delete Role System Action Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(15, 1, 'Administrator', 15, 'Add File Extension Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1),
(16, 1, 'Administrator', 16, 'Delete File Extension Access', 1, '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1);

--
-- Triggers `role_system_action_permission`
--
DROP TRIGGER IF EXISTS `role_system_action_permission_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `role_system_action_permission_trigger_insert` AFTER INSERT ON `role_system_action_permission` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role system action permission created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role_system_action_permission', NEW.role_system_action_permission_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `role_system_action_permission_trigger_update`;
DELIMITER $$
CREATE TRIGGER `role_system_action_permission_trigger_update` AFTER UPDATE ON `role_system_action_permission` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role system action permission changed.<br/><br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.system_action_name <> OLD.system_action_name THEN
        SET audit_log = CONCAT(audit_log, "System Action: ", OLD.system_action_name, " -> ", NEW.system_action_name, "<br/>");
    END IF;

    IF NEW.system_action_access <> OLD.system_action_access THEN
        SET audit_log = CONCAT(audit_log, "System Action Access: ", OLD.system_action_access, " -> ", NEW.system_action_access, "<br/>");
    END IF;
    
    IF audit_log <> 'Role system action permission changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role_system_action_permission', NEW.role_system_action_permission_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role_user_account`
--

DROP TABLE IF EXISTS `role_user_account`;
CREATE TABLE `role_user_account` (
  `role_user_account_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `user_account_id` int(10) UNSIGNED NOT NULL,
  `file_as` varchar(300) NOT NULL,
  `date_assigned` datetime NOT NULL DEFAULT current_timestamp(),
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user_account`
--

INSERT INTO `role_user_account` (`role_user_account_id`, `role_id`, `role_name`, `user_account_id`, `file_as`, `date_assigned`, `created_date`, `last_log_by`) VALUES
(1, 1, 'Administrator', 2, 'Administrator', '2024-10-13 16:22:48', '2024-10-13 16:22:48', 1);

--
-- Triggers `role_user_account`
--
DROP TRIGGER IF EXISTS `role_user_account_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `role_user_account_trigger_insert` AFTER INSERT ON `role_user_account` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role user account created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role_user_account', NEW.role_user_account_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `role_user_account_trigger_update`;
DELIMITER $$
CREATE TRIGGER `role_user_account_trigger_update` AFTER UPDATE ON `role_user_account` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role user account changed. <br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.file_as <> OLD.file_as THEN
        SET audit_log = CONCAT(audit_log, "User Account Name: ", OLD.file_as, " -> ", NEW.file_as, "<br/>");
    END IF;
    
    IF audit_log <> 'Role user account changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role_user_account', NEW.role_user_account_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `security_setting`
--

DROP TABLE IF EXISTS `security_setting`;
CREATE TABLE `security_setting` (
  `security_setting_id` int(10) UNSIGNED NOT NULL,
  `security_setting_name` varchar(100) NOT NULL,
  `value` varchar(2000) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `security_setting`
--

INSERT INTO `security_setting` (`security_setting_id`, `security_setting_name`, `value`, `created_date`, `last_log_by`) VALUES
(1, 'Max Failed Login Attempt', '5', '2024-10-13 16:13:00', 1),
(2, 'Max Failed OTP Attempt', '5', '2024-10-13 16:13:00', 1),
(3, 'Default Forgot Password Link', 'http://localhost/digify/password-reset.php?id=', '2024-10-13 16:13:00', 1),
(4, 'Password Expiry Duration', '180', '2024-10-13 16:13:00', 1),
(5, 'Session Timeout Duration', '240', '2024-10-13 16:13:00', 1),
(6, 'OTP Duration', '5', '2024-10-13 16:13:00', 1),
(7, 'Reset Password Token Duration', '10', '2024-10-13 16:13:00', 1),
(8, 'Registration Verification Token Duration', '180', '2024-10-13 16:13:00', 1);

--
-- Triggers `security_setting`
--
DROP TRIGGER IF EXISTS `security_setting_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `security_setting_trigger_insert` AFTER INSERT ON `security_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Security Setting created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('security_setting', NEW.security_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `security_setting_trigger_update`;
DELIMITER $$
CREATE TRIGGER `security_setting_trigger_update` AFTER UPDATE ON `security_setting` FOR EACH ROW BEGIN
     DECLARE audit_log TEXT DEFAULT 'Security setting changed.<br/><br/>';

    IF NEW.security_setting_name <> OLD.security_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Security Setting Name: ", OLD.security_setting_name, " -> ", NEW.security_setting_name, "<br/>");
    END IF;

    IF NEW.value <> OLD.value THEN
        SET audit_log = CONCAT(audit_log, "Value: ", OLD.value, " -> ", NEW.value, "<br/>");
    END IF;
    
    IF audit_log <> 'Security setting changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('security_setting', NEW.security_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `system_action`
--

DROP TABLE IF EXISTS `system_action`;
CREATE TABLE `system_action` (
  `system_action_id` int(10) UNSIGNED NOT NULL,
  `system_action_name` varchar(100) NOT NULL,
  `system_action_description` varchar(200) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_action`
--

INSERT INTO `system_action` (`system_action_id`, `system_action_name`, `system_action_description`, `created_date`, `last_log_by`) VALUES
(1, 'Update System Settings', 'Access to update the system settings.', '2024-10-13 16:12:41', 1),
(2, 'Update Security Settings', 'Access to update the security settings.', '2024-10-13 16:12:41', 1),
(3, 'Activate User Account', 'Access to activate the user account.', '2024-10-13 16:12:41', 1),
(4, 'Deactivate User Account', 'Access to deactivate the user account.', '2024-10-13 16:12:41', 1),
(5, 'Lock User Account', 'Access to lock the user account.', '2024-10-13 16:12:41', 1),
(6, 'Unlock User Account', 'Access to unlock the user account.', '2024-10-13 16:12:41', 1),
(7, 'Add Role User Account', 'Access to assign roles to user account.', '2024-10-13 16:12:41', 1),
(8, 'Delete Role User Account', 'Access to delete roles to user account.', '2024-10-13 16:12:41', 1),
(9, 'Add Role Access', 'Access to add role access.', '2024-10-13 16:12:41', 1),
(10, 'Update Role Access', 'Access to update role access.', '2024-10-13 16:12:41', 1),
(11, 'Delete Role Access', 'Access to delete role access.', '2024-10-13 16:12:41', 1),
(12, 'Add Role System Action Access', 'Access to add the role system action access.', '2024-10-13 16:12:41', 1),
(13, 'Update Role System Action Access', 'Access to update the role system action access.', '2024-10-13 16:12:41', 1),
(14, 'Delete Role System Action Access', 'Access to delete the role system action access.', '2024-10-13 16:12:41', 1),
(15, 'Add File Extension Access', 'Access to assign the file extension to the upload setting.', '2024-10-13 16:12:41', 1),
(16, 'Delete File Extension Access', 'Access to delete the file extension to the upload setting.', '2024-10-13 16:12:41', 1);

--
-- Triggers `system_action`
--
DROP TRIGGER IF EXISTS `system_action_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `system_action_trigger_insert` AFTER INSERT ON `system_action` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'System action created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('system_action', NEW.system_action_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `system_action_trigger_update`;
DELIMITER $$
CREATE TRIGGER `system_action_trigger_update` AFTER UPDATE ON `system_action` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'System action changed.<br/><br/>';

    IF NEW.system_action_name <> OLD.system_action_name THEN
        SET audit_log = CONCAT(audit_log, "System Action Name: ", OLD.system_action_name, " -> ", NEW.system_action_name, "<br/>");
    END IF;

    IF NEW.system_action_description <> OLD.system_action_description THEN
        SET audit_log = CONCAT(audit_log, "System Action Description: ", OLD.system_action_description, " -> ", NEW.system_action_description, "<br/>");
    END IF;
    
    IF audit_log <> 'System action changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('system_action', NEW.system_action_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `upload_setting`
--

DROP TABLE IF EXISTS `upload_setting`;
CREATE TABLE `upload_setting` (
  `upload_setting_id` int(10) UNSIGNED NOT NULL,
  `upload_setting_name` varchar(100) NOT NULL,
  `upload_setting_description` varchar(200) NOT NULL,
  `max_file_size` double NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `upload_setting`
--

INSERT INTO `upload_setting` (`upload_setting_id`, `upload_setting_name`, `upload_setting_description`, `max_file_size`, `created_date`, `last_log_by`) VALUES
(1, 'App Logo', 'Sets the upload setting when uploading app logo.', 800, '2024-10-13 16:12:22', 1),
(2, 'Internal Notes Attachment', 'Sets the upload setting when uploading internal notes attachement.', 800, '2024-10-13 16:12:22', 1),
(3, 'Import File', 'Sets the upload setting when importing data.', 800, '2024-10-13 16:12:22', 2);

--
-- Triggers `upload_setting`
--
DROP TRIGGER IF EXISTS `upload_setting_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `upload_setting_trigger_insert` AFTER INSERT ON `upload_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Upload Setting created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('upload_setting', NEW.upload_setting_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `upload_setting_trigger_update`;
DELIMITER $$
CREATE TRIGGER `upload_setting_trigger_update` AFTER UPDATE ON `upload_setting` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Upload setting changed.<br/><br/>';

    IF NEW.upload_setting_name <> OLD.upload_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Upload Setting Name: ", OLD.upload_setting_name, " -> ", NEW.upload_setting_name, "<br/>");
    END IF;

    IF NEW.upload_setting_description <> OLD.upload_setting_description THEN
        SET audit_log = CONCAT(audit_log, "Upload Setting Description: ", OLD.upload_setting_description, " -> ", NEW.upload_setting_description, "<br/>");
    END IF;

    IF NEW.max_file_size <> OLD.max_file_size THEN
        SET audit_log = CONCAT(audit_log, "Max File Size: ", OLD.max_file_size, " -> ", NEW.max_file_size, "<br/>");
    END IF;
    
    IF audit_log <> 'Upload setting changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('upload_setting', NEW.upload_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `upload_setting_file_extension`
--

DROP TABLE IF EXISTS `upload_setting_file_extension`;
CREATE TABLE `upload_setting_file_extension` (
  `upload_setting_file_extension_id` int(10) UNSIGNED NOT NULL,
  `upload_setting_id` int(10) UNSIGNED NOT NULL,
  `upload_setting_name` varchar(100) NOT NULL,
  `file_extension_id` int(10) UNSIGNED NOT NULL,
  `file_extension_name` varchar(100) NOT NULL,
  `file_extension` varchar(10) NOT NULL,
  `date_assigned` datetime DEFAULT current_timestamp(),
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `upload_setting_file_extension`
--

INSERT INTO `upload_setting_file_extension` (`upload_setting_file_extension_id`, `upload_setting_id`, `upload_setting_name`, `file_extension_id`, `file_extension_name`, `file_extension`, `date_assigned`, `created_date`, `last_log_by`) VALUES
(1, 1, 'App Logo', 63, 'PNG', 'png', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(2, 1, 'App Logo', 61, 'JPG', 'jpg', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(3, 1, 'App Logo', 62, 'JPEG', 'jpeg', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(4, 2, 'Internal Notes Attachment', 63, 'PNG', 'png', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(5, 2, 'Internal Notes Attachment', 61, 'JPG', 'jpg', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(6, 2, 'Internal Notes Attachment', 62, 'JPEG', 'jpeg', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(7, 2, 'Internal Notes Attachment', 127, 'PDF', 'pdf', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(8, 2, 'Internal Notes Attachment', 125, 'DOC', 'doc', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(9, 2, 'Internal Notes Attachment', 125, 'DOCX', 'docx', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(10, 2, 'Internal Notes Attachment', 130, 'TXT', 'txt', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(11, 2, 'Internal Notes Attachment', 92, 'XLS', 'xls', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(12, 2, 'Internal Notes Attachment', 94, 'XLSX', 'xlsx', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(13, 2, 'Internal Notes Attachment', 89, 'PPT', 'ppt', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(14, 2, 'Internal Notes Attachment', 90, 'PPTX', 'pptx', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1),
(15, 3, 'Import File', 25, 'CSV', 'csv', '2024-10-13 16:12:22', '2024-10-13 16:12:22', 1);

--
-- Triggers `upload_setting_file_extension`
--
DROP TRIGGER IF EXISTS `upload_setting_file_extension_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `upload_setting_file_extension_trigger_insert` AFTER INSERT ON `upload_setting_file_extension` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Upload Setting File Extension created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('upload_setting_file_extension', NEW.upload_setting_file_extension_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account` (
  `user_account_id` int(10) UNSIGNED NOT NULL,
  `file_as` varchar(300) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(500) DEFAULT NULL,
  `locked` varchar(255) NOT NULL DEFAULT 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0%3D',
  `active` varchar(255) NOT NULL DEFAULT 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0%3D',
  `last_failed_login_attempt` datetime DEFAULT NULL,
  `failed_login_attempts` varchar(255) DEFAULT NULL,
  `last_connection_date` datetime DEFAULT NULL,
  `password_expiry_date` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry_date` varchar(255) DEFAULT NULL,
  `receive_notification` varchar(255) NOT NULL DEFAULT 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D',
  `two_factor_auth` varchar(255) NOT NULL DEFAULT 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D',
  `otp` varchar(255) DEFAULT NULL,
  `otp_expiry_date` varchar(255) DEFAULT NULL,
  `failed_otp_attempts` varchar(255) DEFAULT NULL,
  `last_password_change` datetime DEFAULT NULL,
  `account_lock_duration` varchar(255) DEFAULT NULL,
  `last_password_reset` datetime DEFAULT NULL,
  `multiple_session` varchar(255) DEFAULT 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D',
  `session_token` varchar(255) DEFAULT NULL,
  `linked_id` int(10) UNSIGNED DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_account_id`, `file_as`, `email`, `username`, `password`, `profile_picture`, `locked`, `active`, `last_failed_login_attempt`, `failed_login_attempts`, `last_connection_date`, `password_expiry_date`, `reset_token`, `reset_token_expiry_date`, `receive_notification`, `two_factor_auth`, `otp`, `otp_expiry_date`, `failed_otp_attempts`, `last_password_change`, `account_lock_duration`, `last_password_reset`, `multiple_session`, `session_token`, `linked_id`, `created_date`, `last_log_by`) VALUES
(1, 'Digify Bot', 'digifybot@gmail.com', 'digifybot', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', NULL, 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20', NULL, NULL, NULL, 'aUIRg2jhRcYVcr0%2BiRDl98xjv81aR4Ux63bP%2BF2hQbE%3D', NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', NULL, NULL, NULL, NULL, NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', NULL, NULL, '2024-10-13 16:12:00', 1),
(2, 'Administrator', 'lawrenceagulto.317@gmail.com', 'ldagulto', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', NULL, 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20', '0000-00-00 00:00:00', '', '2024-10-23 14:09:19', 'aUIRg2jhRcYVcr0%2BiRDl98xjv81aR4Ux63bP%2BF2hQbE%3D', NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', NULL, NULL, NULL, NULL, NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', 'BfM3C3J5X7x3N%2B0un4fb1KhHrFmzxHAgm1tfvpTQK6s%3D', NULL, '2024-10-13 16:12:00', 1);

--
-- Triggers `user_account`
--
DROP TRIGGER IF EXISTS `user_account_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `user_account_trigger_insert` AFTER INSERT ON `user_account` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'User account created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('user_account', NEW.user_account_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `user_account_trigger_update`;
DELIMITER $$
CREATE TRIGGER `user_account_trigger_update` AFTER UPDATE ON `user_account` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'User account changed.<br/><br/>';

    IF NEW.file_as <> OLD.file_as THEN
        SET audit_log = CONCAT(audit_log, "File As: ", OLD.file_as, " -> ", NEW.file_as, "<br/>");
    END IF;

    IF NEW.email <> OLD.email THEN
        SET audit_log = CONCAT(audit_log, "Email: ", OLD.email, " -> ", NEW.email, "<br/>");
    END IF;

    IF NEW.username <> OLD.username THEN
        SET audit_log = CONCAT(audit_log, "Username: ", OLD.username, " -> ", NEW.username, "<br/>");
    END IF;

    IF NEW.last_failed_login_attempt <> OLD.last_failed_login_attempt THEN
        SET audit_log = CONCAT(audit_log, "Last Failed Login Attempt: ", OLD.last_failed_login_attempt, " -> ", NEW.last_failed_login_attempt, "<br/>");
    END IF;

    IF NEW.last_connection_date <> OLD.last_connection_date THEN
        SET audit_log = CONCAT(audit_log, "Last Connection Date: ", OLD.last_connection_date, " -> ", NEW.last_connection_date, "<br/>");
    END IF;

    IF NEW.last_password_change <> OLD.last_password_change THEN
        SET audit_log = CONCAT(audit_log, "Last Password Change: ", OLD.last_password_change, " -> ", NEW.last_password_change, "<br/>");
    END IF;

    IF NEW.last_password_reset <> OLD.last_password_reset THEN
        SET audit_log = CONCAT(audit_log, "Last Password Reset: ", OLD.last_password_reset, " -> ", NEW.last_password_reset, "<br/>");
    END IF;
    
    IF audit_log <> 'User account changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('user_account', NEW.user_account_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_module`
--
ALTER TABLE `app_module`
  ADD PRIMARY KEY (`app_module_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `app_module_index_app_module_id` (`app_module_id`),
  ADD KEY `app_module_index_menu_item_id` (`menu_item_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_log_id`),
  ADD KEY `audit_log_index_audit_log_id` (`audit_log_id`),
  ADD KEY `audit_log_index_table_name` (`table_name`),
  ADD KEY `audit_log_index_reference_id` (`reference_id`),
  ADD KEY `audit_log_index_changed_by` (`changed_by`);

--
-- Indexes for table `email_setting`
--
ALTER TABLE `email_setting`
  ADD PRIMARY KEY (`email_setting_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `email_setting_index_email_setting_id` (`email_setting_id`);

--
-- Indexes for table `internal_notes`
--
ALTER TABLE `internal_notes`
  ADD PRIMARY KEY (`internal_notes_id`),
  ADD KEY `internal_note_by` (`internal_note_by`),
  ADD KEY `internal_notes_index_internal_notes_id` (`internal_notes_id`),
  ADD KEY `internal_notes_index_table_name` (`table_name`),
  ADD KEY `internal_notes_index_reference_id` (`reference_id`);

--
-- Indexes for table `internal_notes_attachment`
--
ALTER TABLE `internal_notes_attachment`
  ADD PRIMARY KEY (`internal_notes_attachment_id`),
  ADD KEY `internal_notes_attachment_index_internal_notes_id` (`internal_notes_attachment_id`),
  ADD KEY `internal_notes_attachment_index_table_name` (`internal_notes_id`);

--
-- Indexes for table `menu_group`
--
ALTER TABLE `menu_group`
  ADD PRIMARY KEY (`menu_group_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `app_module_id` (`app_module_id`),
  ADD KEY `menu_group_index_menu_group_id` (`menu_group_id`);

--
-- Indexes for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD PRIMARY KEY (`menu_item_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `menu_item_index_menu_item_id` (`menu_item_id`),
  ADD KEY `menu_item_index_app_module_id` (`app_module_id`),
  ADD KEY `menu_item_index_parent_id` (`parent_id`);

--
-- Indexes for table `notification_setting`
--
ALTER TABLE `notification_setting`
  ADD PRIMARY KEY (`notification_setting_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `notification_setting_index_notification_setting_id` (`notification_setting_id`);

--
-- Indexes for table `notification_setting_email_template`
--
ALTER TABLE `notification_setting_email_template`
  ADD PRIMARY KEY (`notification_setting_email_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `notification_setting_email_index_notification_setting_email_id` (`notification_setting_email_id`),
  ADD KEY `notification_setting_email_index_notification_setting_id` (`notification_setting_id`);

--
-- Indexes for table `notification_setting_sms_template`
--
ALTER TABLE `notification_setting_sms_template`
  ADD PRIMARY KEY (`notification_setting_sms_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `notification_setting_sms_index_notification_setting_sms_id` (`notification_setting_sms_id`),
  ADD KEY `notification_setting_sms_index_notification_setting_id` (`notification_setting_id`);

--
-- Indexes for table `notification_setting_system_template`
--
ALTER TABLE `notification_setting_system_template`
  ADD PRIMARY KEY (`notification_setting_system_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `notification_setting_system_index_notification_setting_system_id` (`notification_setting_system_id`),
  ADD KEY `notification_setting_system_index_notification_setting_id` (`notification_setting_id`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`password_history_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `password_history_index_password_history_id` (`password_history_id`),
  ADD KEY `password_history_index_user_account_id` (`user_account_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `role_index_role_id` (`role_id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_permission_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `role_permission_index_role_permission_id` (`role_permission_id`),
  ADD KEY `role_permission_index_menu_item_id` (`menu_item_id`),
  ADD KEY `role_permission_index_role_id` (`role_id`);

--
-- Indexes for table `role_system_action_permission`
--
ALTER TABLE `role_system_action_permission`
  ADD PRIMARY KEY (`role_system_action_permission_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `role_system_action_permission_index_system_action_permission_id` (`role_system_action_permission_id`),
  ADD KEY `role_system_action_permission_index_system_action_id` (`system_action_id`),
  ADD KEY `role_system_action_permissionn_index_role_id` (`role_id`);

--
-- Indexes for table `role_user_account`
--
ALTER TABLE `role_user_account`
  ADD PRIMARY KEY (`role_user_account_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `role_user_account_index_role_user_account_id` (`role_user_account_id`),
  ADD KEY `role_user_account_permission_index_user_account_id` (`user_account_id`),
  ADD KEY `role_user_account_permissionn_index_role_id` (`role_id`);

--
-- Indexes for table `security_setting`
--
ALTER TABLE `security_setting`
  ADD PRIMARY KEY (`security_setting_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `security_setting_index_security_setting_id` (`security_setting_id`);

--
-- Indexes for table `system_action`
--
ALTER TABLE `system_action`
  ADD PRIMARY KEY (`system_action_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `system_action_index_system_action_id` (`system_action_id`);

--
-- Indexes for table `upload_setting`
--
ALTER TABLE `upload_setting`
  ADD PRIMARY KEY (`upload_setting_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `upload_setting_index_upload_setting_id` (`upload_setting_id`);

--
-- Indexes for table `upload_setting_file_extension`
--
ALTER TABLE `upload_setting_file_extension`
  ADD PRIMARY KEY (`upload_setting_file_extension_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `upload_setting_file_ext_index_upload_setting_file_extension_id` (`upload_setting_file_extension_id`),
  ADD KEY `upload_setting_file_ext_index_upload_setting_id` (`upload_setting_id`),
  ADD KEY `upload_setting_file_ext_index_file_extension_id` (`file_extension_id`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_account_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `user_account_index_user_account_id` (`user_account_id`),
  ADD KEY `user_account_index_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_module`
--
ALTER TABLE `app_module`
  MODIFY `app_module_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `email_setting`
--
ALTER TABLE `email_setting`
  MODIFY `email_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `internal_notes`
--
ALTER TABLE `internal_notes`
  MODIFY `internal_notes_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `internal_notes_attachment`
--
ALTER TABLE `internal_notes_attachment`
  MODIFY `internal_notes_attachment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_group`
--
ALTER TABLE `menu_group`
  MODIFY `menu_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `menu_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notification_setting`
--
ALTER TABLE `notification_setting`
  MODIFY `notification_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notification_setting_email_template`
--
ALTER TABLE `notification_setting_email_template`
  MODIFY `notification_setting_email_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notification_setting_sms_template`
--
ALTER TABLE `notification_setting_sms_template`
  MODIFY `notification_setting_sms_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_setting_system_template`
--
ALTER TABLE `notification_setting_system_template`
  MODIFY `notification_setting_system_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `password_history_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `role_permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `role_system_action_permission`
--
ALTER TABLE `role_system_action_permission`
  MODIFY `role_system_action_permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `role_user_account`
--
ALTER TABLE `role_user_account`
  MODIFY `role_user_account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `security_setting`
--
ALTER TABLE `security_setting`
  MODIFY `security_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `system_action`
--
ALTER TABLE `system_action`
  MODIFY `system_action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `upload_setting`
--
ALTER TABLE `upload_setting`
  MODIFY `upload_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `upload_setting_file_extension`
--
ALTER TABLE `upload_setting_file_extension`
  MODIFY `upload_setting_file_extension_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_module`
--
ALTER TABLE `app_module`
  ADD CONSTRAINT `app_module_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`changed_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `email_setting`
--
ALTER TABLE `email_setting`
  ADD CONSTRAINT `email_setting_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `internal_notes`
--
ALTER TABLE `internal_notes`
  ADD CONSTRAINT `internal_notes_ibfk_1` FOREIGN KEY (`internal_note_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `internal_notes_attachment`
--
ALTER TABLE `internal_notes_attachment`
  ADD CONSTRAINT `internal_notes_attachment_ibfk_1` FOREIGN KEY (`internal_notes_id`) REFERENCES `internal_notes` (`internal_notes_id`);

--
-- Constraints for table `menu_group`
--
ALTER TABLE `menu_group`
  ADD CONSTRAINT `menu_group_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `menu_group_ibfk_2` FOREIGN KEY (`app_module_id`) REFERENCES `app_module` (`app_module_id`);

--
-- Constraints for table `menu_item`
--
ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `menu_item_ibfk_2` FOREIGN KEY (`app_module_id`) REFERENCES `app_module` (`app_module_id`);

--
-- Constraints for table `notification_setting`
--
ALTER TABLE `notification_setting`
  ADD CONSTRAINT `notification_setting_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `notification_setting_email_template`
--
ALTER TABLE `notification_setting_email_template`
  ADD CONSTRAINT `notification_setting_email_template_ibfk_1` FOREIGN KEY (`notification_setting_id`) REFERENCES `notification_setting` (`notification_setting_id`),
  ADD CONSTRAINT `notification_setting_email_template_ibfk_2` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `notification_setting_sms_template`
--
ALTER TABLE `notification_setting_sms_template`
  ADD CONSTRAINT `notification_setting_sms_template_ibfk_1` FOREIGN KEY (`notification_setting_id`) REFERENCES `notification_setting` (`notification_setting_id`),
  ADD CONSTRAINT `notification_setting_sms_template_ibfk_2` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `notification_setting_system_template`
--
ALTER TABLE `notification_setting_system_template`
  ADD CONSTRAINT `notification_setting_system_template_ibfk_1` FOREIGN KEY (`notification_setting_id`) REFERENCES `notification_setting` (`notification_setting_id`),
  ADD CONSTRAINT `notification_setting_system_template_ibfk_2` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `password_history_ibfk_2` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `role_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `role_system_action_permission`
--
ALTER TABLE `role_system_action_permission`
  ADD CONSTRAINT `role_system_action_permission_ibfk_1` FOREIGN KEY (`system_action_id`) REFERENCES `system_action` (`system_action_id`),
  ADD CONSTRAINT `role_system_action_permission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `role_system_action_permission_ibfk_3` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
