-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 10:28 AM
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
DROP PROCEDURE IF EXISTS `addUserAccount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `addUserAccount` (IN `p_file_as` VARCHAR(300), IN `p_email` VARCHAR(255), IN `p_username` VARCHAR(100), IN `p_password` VARCHAR(255), IN `p_phone` VARCHAR(50), IN `p_password_expiry_date` VARCHAR(255), IN `p_last_log_by` INT, OUT `p_new_user_account_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO user_account (file_as, email, username, password, phone, password_expiry_date, last_log_by) 
    VALUES(p_file_as, p_email, p_username, p_password, p_phone, p_password_expiry_date, p_last_log_by);
        
    SET p_new_user_account_id = LAST_INSERT_ID();

    COMMIT;
END$$

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

DROP PROCEDURE IF EXISTS `buildBreadcrumb`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `buildBreadcrumb` (IN `pageID` INT)   BEGIN
    -- Temporary table to store breadcrumb trail
    DECLARE done INT DEFAULT FALSE;
    DECLARE current_id INT DEFAULT pageID;
    
    DECLARE menu_name VARCHAR(100);
    DECLARE menu_url VARCHAR(50);
    DECLARE parent INT;
    
    -- Cursor to fetch the current page and its parent
    DECLARE breadcrumb_cursor CURSOR FOR
        SELECT menu_item_name, menu_item_url, parent_id
        FROM menu_item
        WHERE menu_item_id = current_id;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Temporary table to hold breadcrumb data
    CREATE TEMPORARY TABLE IF NOT EXISTS BreadcrumbTrail (
        menu_item_name VARCHAR(100),
        menu_item_url VARCHAR(50)
    );
    
    -- Open the cursor to start fetching data
    OPEN breadcrumb_cursor;
    
    -- Loop to trace the breadcrumb trail upwards
    read_loop: LOOP
        FETCH breadcrumb_cursor INTO menu_name, menu_url, parent;
        
        -- If no more data is found, exit the loop
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Skip inserting the current page ID (pageID) into the breadcrumb trail
        IF current_id != pageID THEN
            -- Insert the breadcrumb into the temporary table
            INSERT INTO BreadcrumbTrail (menu_item_name, menu_item_url) 
            VALUES (menu_name, menu_url);
        END IF;

        -- Set the current_id to the parent to trace upwards
        SET current_id = parent;
        
        -- If there's no parent, exit the loop
        IF current_id IS NULL THEN
            LEAVE read_loop;
        END IF;
        
        -- Reopen the cursor to fetch the next parent
        CLOSE breadcrumb_cursor;
        OPEN breadcrumb_cursor;
    END LOOP read_loop;

    -- Close the cursor once the loop is done
    CLOSE breadcrumb_cursor;

    -- Select the breadcrumb trail in the correct order (in reverse)
    SELECT * FROM BreadcrumbTrail ORDER BY FIELD(menu_item_name, menu_item_name);

    -- Clean up by dropping the temporary table
    DROP TEMPORARY TABLE BreadcrumbTrail;
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

DROP PROCEDURE IF EXISTS `checkAddressTypeExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkAddressTypeExist` (IN `p_address_type_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM address_type
    WHERE address_type_id = p_address_type_id;
END$$

DROP PROCEDURE IF EXISTS `checkAppModuleExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkAppModuleExist` (IN `p_app_module_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM app_module
    WHERE app_module_id = p_app_module_id;
END$$

DROP PROCEDURE IF EXISTS `checkBankAccountTypeExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkBankAccountTypeExist` (IN `p_bank_account_type_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM bank_account_type
    WHERE bank_account_type_id = p_bank_account_type_id;
END$$

DROP PROCEDURE IF EXISTS `checkBankExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkBankExist` (IN `p_bank_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM bank
    WHERE bank_id = p_bank_id;
END$$

DROP PROCEDURE IF EXISTS `checkBillingCycleExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkBillingCycleExist` (IN `p_billing_cycle_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM billing_cycle
    WHERE billing_cycle_id = p_billing_cycle_id;
END$$

DROP PROCEDURE IF EXISTS `checkBloodTypeExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkBloodTypeExist` (IN `p_blood_type_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM blood_type
    WHERE blood_type_id = p_blood_type_id;
END$$

DROP PROCEDURE IF EXISTS `checkCityExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkCityExist` (IN `p_city_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM city
    WHERE city_id = p_city_id;
END$$

DROP PROCEDURE IF EXISTS `checkCivilStatusExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkCivilStatusExist` (IN `p_civil_status_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM civil_status
    WHERE civil_status_id = p_civil_status_id;
END$$

DROP PROCEDURE IF EXISTS `checkCompanyExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkCompanyExist` (IN `p_company_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM company
    WHERE company_id = p_company_id;
END$$

DROP PROCEDURE IF EXISTS `checkContactInformationTypeExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkContactInformationTypeExist` (IN `p_contact_information_type_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM contact_information_type
    WHERE contact_information_type_id = p_contact_information_type_id;
END$$

DROP PROCEDURE IF EXISTS `checkCountryExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkCountryExist` (IN `p_country_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM country
    WHERE country_id = p_country_id;
END$$

DROP PROCEDURE IF EXISTS `checkCredentialTypeExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkCredentialTypeExist` (IN `p_credential_type_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM credential_type
    WHERE credential_type_id = p_credential_type_id;
END$$

DROP PROCEDURE IF EXISTS `checkCurrencyExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkCurrencyExist` (IN `p_currency_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM currency
    WHERE currency_id = p_currency_id;
END$$

DROP PROCEDURE IF EXISTS `checkDepartmentExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkDepartmentExist` (IN `p_department_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM department
    WHERE department_id = p_department_id;
END$$

DROP PROCEDURE IF EXISTS `checkEducationalStageExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkEducationalStageExist` (IN `p_educational_stage_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM educational_stage
    WHERE educational_stage_id = p_educational_stage_id;
END$$

DROP PROCEDURE IF EXISTS `checkEmailSettingExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkEmailSettingExist` (IN `p_email_setting_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM email_setting
    WHERE email_setting_id = p_email_setting_id;
END$$

DROP PROCEDURE IF EXISTS `checkFileExtensionExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkFileExtensionExist` (IN `p_file_extension_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM file_extension
    WHERE file_extension_id = p_file_extension_id;
END$$

DROP PROCEDURE IF EXISTS `checkFileTypeExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkFileTypeExist` (IN `p_file_type_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM file_type
    WHERE file_type_id = p_file_type_id;
END$$

DROP PROCEDURE IF EXISTS `checkGenderExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkGenderExist` (IN `p_gender_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM gender
    WHERE gender_id = p_gender_id;
END$$

DROP PROCEDURE IF EXISTS `checkLanguageExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkLanguageExist` (IN `p_language_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM language
    WHERE language_id = p_language_id;
END$$

DROP PROCEDURE IF EXISTS `checkLanguageProficiencyExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkLanguageProficiencyExist` (IN `p_language_proficiency_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM language_proficiency
    WHERE language_proficiency_id = p_language_proficiency_id;
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

DROP PROCEDURE IF EXISTS `checkNotificationSettingExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkNotificationSettingExist` (IN `p_notification_setting_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM notification_setting
    WHERE notification_setting_id = p_notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `checkRelationshipExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkRelationshipExist` (IN `p_relationship_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM relationship
    WHERE relationship_id = p_relationship_id;
END$$

DROP PROCEDURE IF EXISTS `checkReligionExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkReligionExist` (IN `p_religion_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM religion
    WHERE religion_id = p_religion_id;
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

DROP PROCEDURE IF EXISTS `checkStateExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkStateExist` (IN `p_state_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM state
    WHERE state_id = p_state_id;
END$$

DROP PROCEDURE IF EXISTS `checkSubscriberExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkSubscriberExist` (IN `p_subscriber_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM subscriber
    WHERE subscriber_id = p_subscriber_id;
END$$

DROP PROCEDURE IF EXISTS `checkSubscriptionTierExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkSubscriptionTierExist` (IN `p_subscription_tier_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM subscription_tier
    WHERE subscription_tier_id = p_subscription_tier_id;
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

DROP PROCEDURE IF EXISTS `checkUploadSettingExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkUploadSettingExist` (IN `p_upload_setting_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM upload_setting
    WHERE upload_setting_id = p_upload_setting_id;
END$$

DROP PROCEDURE IF EXISTS `checkUserAccountEmailExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkUserAccountEmailExist` (IN `p_user_account_id` INT, IN `p_email` VARCHAR(255))   BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id != p_user_account_id AND email = p_email;
END$$

DROP PROCEDURE IF EXISTS `checkUserAccountExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkUserAccountExist` (IN `p_user_account_id` INT)   BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `checkUserAccountPhoneExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkUserAccountPhoneExist` (IN `p_user_account_id` INT, IN `p_phone` VARCHAR(50))   BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id != p_user_account_id AND phone = p_phone;
END$$

DROP PROCEDURE IF EXISTS `checkUserAccountUsernameExist`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `checkUserAccountUsernameExist` (IN `p_user_account_id` INT, IN `p_username` VARCHAR(100))   BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id != p_user_account_id AND username = p_username;
END$$

DROP PROCEDURE IF EXISTS `deleteAddressType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteAddressType` (IN `p_address_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM address_type WHERE address_type_id = p_address_type_id;

    COMMIT;
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

DROP PROCEDURE IF EXISTS `deleteBank`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteBank` (IN `p_bank_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM bank WHERE bank_id = p_bank_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteBankAccountType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteBankAccountType` (IN `p_bank_account_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM bank_account_type WHERE bank_account_type_id = p_bank_account_type_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteBillingCycle`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteBillingCycle` (IN `p_billing_cycle_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM billing_cycle WHERE billing_cycle_id = p_billing_cycle_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteBloodType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteBloodType` (IN `p_blood_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM blood_type WHERE blood_type_id = p_blood_type_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteCity`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCity` (IN `p_city_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM city WHERE city_id = p_city_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteCivilStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCivilStatus` (IN `p_civil_status_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM civil_status WHERE civil_status_id = p_civil_status_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteCompany`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCompany` (IN `p_company_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM company WHERE company_id = p_company_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteContactInformationType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteContactInformationType` (IN `p_contact_information_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM contact_information_type WHERE contact_information_type_id = p_contact_information_type_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteCountry`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCountry` (IN `p_country_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM state WHERE country_id = p_country_id;
    DELETE FROM city WHERE country_id = p_country_id;
    DELETE FROM country WHERE country_id = p_country_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteCredentialType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCredentialType` (IN `p_credential_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM credential_type WHERE credential_type_id = p_credential_type_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteCurrency`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCurrency` (IN `p_currency_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM currency WHERE currency_id = p_currency_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteDepartment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteDepartment` (IN `p_department_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM department WHERE department_id = p_department_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteEducationalStage`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteEducationalStage` (IN `p_educational_stage_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM educational_stage WHERE educational_stage_id = p_educational_stage_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteEmailSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteEmailSetting` (IN `p_email_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM email_setting WHERE email_setting_id = p_email_setting_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteFileExtension`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteFileExtension` (IN `p_file_extension_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM file_extension WHERE file_extension_id = p_file_extension_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteFileType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteFileType` (IN `p_file_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    /*DELETE FROM file_extension WHERE file_type_id = p_file_type_id;*/
    DELETE FROM file_type WHERE file_type_id = p_file_type_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteGender`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteGender` (IN `p_gender_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM gender WHERE gender_id = p_gender_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteLanguage`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLanguage` (IN `p_language_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM language WHERE language_id = p_language_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteLanguageProficiency`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteLanguageProficiency` (IN `p_language_proficiency_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM language_proficiency WHERE language_proficiency_id = p_language_proficiency_id;

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

DROP PROCEDURE IF EXISTS `deleteNotificationSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteNotificationSetting` (IN `p_notification_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM notification_setting_email_template WHERE notification_setting_id = p_notification_setting_id;
    DELETE FROM notification_setting_system_template WHERE notification_setting_id = p_notification_setting_id;
    DELETE FROM notification_setting_sms_template WHERE notification_setting_id = p_notification_setting_id;
    DELETE FROM notification_setting WHERE notification_setting_id = p_notification_setting_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteRelationship`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteRelationship` (IN `p_relationship_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM relationship WHERE relationship_id = p_relationship_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteReligion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteReligion` (IN `p_religion_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM religion WHERE religion_id = p_religion_id;

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

DROP PROCEDURE IF EXISTS `deleteState`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteState` (IN `p_state_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM city WHERE state_id = p_state_id;
    DELETE FROM state WHERE state_id = p_state_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteSubscriber`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteSubscriber` (IN `p_subscriber_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM subscription WHERE subscriber_id = p_subscriber_id;
    DELETE FROM subscriber WHERE subscriber_id = p_subscriber_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteSubscriptionTier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteSubscriptionTier` (IN `p_subscription_tier_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM subscription_tier WHERE subscription_tier_id = p_subscription_tier_id;

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

DROP PROCEDURE IF EXISTS `deleteUploadSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUploadSetting` (IN `p_upload_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM upload_setting_file_extension WHERE upload_setting_id = p_upload_setting_id;
    DELETE FROM upload_setting WHERE upload_setting_id = p_upload_setting_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteUploadSettingFileExtension`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUploadSettingFileExtension` (IN `p_upload_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM upload_setting_file_extension WHERE upload_setting_id = p_upload_setting_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `deleteUserAccount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUserAccount` (IN `p_user_account_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_user_account WHERE user_account_id = p_user_account_id;
    DELETE FROM user_account WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `exportData`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `exportData` (IN `p_table_name` VARCHAR(255), IN `p_columns` TEXT, IN `p_ids` TEXT)   BEGIN
    SET @sql = CONCAT('SELECT ', p_columns, ' FROM ', p_table_name, ' WHERE ', p_table_name, '_id IN (', p_ids, ')');

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateAddressTypeOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateAddressTypeOptions` ()   BEGIN
	SELECT address_type_id, address_type_name 
    FROM address_type 
    ORDER BY address_type_name;
END$$

DROP PROCEDURE IF EXISTS `generateAddressTypeTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateAddressTypeTable` ()   BEGIN
	SELECT address_type_id, address_type_name
    FROM address_type 
    ORDER BY address_type_id;
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

DROP PROCEDURE IF EXISTS `generateBankAccountTypeOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBankAccountTypeOptions` ()   BEGIN
	SELECT bank_account_type_id, bank_account_type_name 
    FROM bank_account_type 
    ORDER BY bank_account_type_name;
END$$

DROP PROCEDURE IF EXISTS `generateBankAccountTypeTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBankAccountTypeTable` ()   BEGIN
	SELECT bank_account_type_id, bank_account_type_name
    FROM bank_account_type 
    ORDER BY bank_account_type_id;
END$$

DROP PROCEDURE IF EXISTS `generateBankOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBankOptions` ()   BEGIN
    SELECT bank_id, bank_name
    FROM bank 
    ORDER BY bank_name;
END$$

DROP PROCEDURE IF EXISTS `generateBankTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBankTable` ()   BEGIN
    SELECT bank_id, bank_name, bank_identifier_code FROM bank;
END$$

DROP PROCEDURE IF EXISTS `generateBillingCycleOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBillingCycleOptions` ()   BEGIN
	SELECT billing_cycle_id, billing_cycle_name 
    FROM billing_cycle 
    ORDER BY billing_cycle_name;
END$$

DROP PROCEDURE IF EXISTS `generateBillingCycleTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBillingCycleTable` ()   BEGIN
	SELECT billing_cycle_id, billing_cycle_name
    FROM billing_cycle 
    ORDER BY billing_cycle_id;
END$$

DROP PROCEDURE IF EXISTS `generateBloodTypeOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBloodTypeOptions` ()   BEGIN
	SELECT blood_type_id, blood_type_name 
    FROM blood_type 
    ORDER BY blood_type_name;
END$$

DROP PROCEDURE IF EXISTS `generateBloodTypeTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateBloodTypeTable` ()   BEGIN
	SELECT blood_type_id, blood_type_name
    FROM blood_type 
    ORDER BY blood_type_id;
END$$

DROP PROCEDURE IF EXISTS `generateCityOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCityOptions` ()   BEGIN
    SELECT city_id, city_name, state_name, country_name
    FROM city 
    ORDER BY city_name;
END$$

DROP PROCEDURE IF EXISTS `generateCityTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCityTable` (IN `p_filter_by_state` TEXT, IN `p_filter_by_country` TEXT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `generateCivilStatusOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCivilStatusOptions` ()   BEGIN
	SELECT civil_status_id, civil_status_name 
    FROM civil_status 
    ORDER BY civil_status_name;
END$$

DROP PROCEDURE IF EXISTS `generateCivilStatusTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCivilStatusTable` ()   BEGIN
	SELECT civil_status_id, civil_status_name
    FROM civil_status 
    ORDER BY civil_status_id;
END$$

DROP PROCEDURE IF EXISTS `generateCompanyOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCompanyOptions` ()   BEGIN
	SELECT company_id, company_name 
    FROM company 
    ORDER BY company_name;
END$$

DROP PROCEDURE IF EXISTS `generateCompanyTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCompanyTable` (IN `p_filter_by_city` TEXT, IN `p_filter_by_state` TEXT, IN `p_filter_by_country` TEXT, IN `p_filter_by_currency` TEXT)   BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT company_id, company_name, company_logo, address, city_name, state_name, country_name 
                FROM company ';

    IF p_filter_by_city IS NOT NULL AND p_filter_by_city <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' city_id IN (', p_filter_by_city, ')');
    END IF;

    IF p_filter_by_state IS NOT NULL AND p_filter_by_state <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
         SET filter_conditions = CONCAT(filter_conditions, ' state_id IN (', p_filter_by_state, ')');
    END IF;

    IF p_filter_by_country IS NOT NULL AND p_filter_by_country <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
         SET filter_conditions = CONCAT(filter_conditions, ' country_id IN (', p_filter_by_country, ')');
    END IF;

    IF p_filter_by_currency IS NOT NULL AND p_filter_by_currency <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
         SET filter_conditions = CONCAT(filter_conditions, ' currency_id IN (', p_filter_by_currency, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY company_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateContactInformationTypeOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateContactInformationTypeOptions` ()   BEGIN
	SELECT contact_information_type_id, contact_information_type_name 
    FROM contact_information_type 
    ORDER BY contact_information_type_name;
END$$

DROP PROCEDURE IF EXISTS `generateContactInformationTypeTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateContactInformationTypeTable` ()   BEGIN
	SELECT contact_information_type_id, contact_information_type_name
    FROM contact_information_type 
    ORDER BY contact_information_type_id;
END$$

DROP PROCEDURE IF EXISTS `generateCountryOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCountryOptions` ()   BEGIN
    SELECT country_id, country_name 
    FROM country 
    ORDER BY country_name;
END$$

DROP PROCEDURE IF EXISTS `generateCountryTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCountryTable` ()   BEGIN
    SELECT country_id, country_name, country_code, phone_code 
    FROM country
    ORDER BY country_id;
END$$

DROP PROCEDURE IF EXISTS `generateCredentialTypeOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCredentialTypeOptions` ()   BEGIN
	SELECT credential_type_id, credential_type_name 
    FROM credential_type 
    ORDER BY credential_type_name;
END$$

DROP PROCEDURE IF EXISTS `generateCredentialTypeTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCredentialTypeTable` ()   BEGIN
	SELECT credential_type_id, credential_type_name
    FROM credential_type 
    ORDER BY credential_type_id;
END$$

DROP PROCEDURE IF EXISTS `generateCurrencyOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCurrencyOptions` ()   BEGIN
    SELECT currency_id, currency_name, symbol
    FROM currency 
    ORDER BY currency_name;
END$$

DROP PROCEDURE IF EXISTS `generateCurrencyTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCurrencyTable` ()   BEGIN
    SELECT currency_id, currency_name, symbol, shorthand 
    FROM currency
    ORDER BY currency_id;
END$$

DROP PROCEDURE IF EXISTS `generateDepartmentOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateDepartmentOptions` ()   BEGIN
	SELECT department_id, department_name
    FROM department 
    ORDER BY department_name;
END$$

DROP PROCEDURE IF EXISTS `generateDepartmentTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateDepartmentTable` (IN `p_filter_by_parent_department` TEXT, IN `p_filter_by_manager` TEXT)   BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT department_id, department_name, parent_department_name, manager_name
                FROM department ';

    IF p_filter_by_parent_department IS NOT NULL AND p_filter_by_parent_department <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' parent_department_id IN (', p_filter_by_parent_department, ')');
    END IF;

    IF p_filter_by_manager IS NOT NULL AND p_filter_by_manager <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' manager_id IN (', p_filter_by_manager, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY department_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateEducationalStageOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateEducationalStageOptions` ()   BEGIN
	SELECT educational_stage_id, educational_stage_name 
    FROM educational_stage 
    ORDER BY educational_stage_name;
END$$

DROP PROCEDURE IF EXISTS `generateEducationalStageTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateEducationalStageTable` ()   BEGIN
	SELECT educational_stage_id, educational_stage_name
    FROM educational_stage 
    ORDER BY educational_stage_id;
END$$

DROP PROCEDURE IF EXISTS `generateEmailSettingOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateEmailSettingOptions` ()   BEGIN
	SELECT email_setting_id, email_setting_name 
    FROM email_setting 
    ORDER BY email_setting_name;
END$$

DROP PROCEDURE IF EXISTS `generateEmailSettingTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateEmailSettingTable` ()   BEGIN
	SELECT email_setting_id, email_setting_name, email_setting_description
    FROM email_setting 
    ORDER BY email_setting_id;
END$$

DROP PROCEDURE IF EXISTS `generateExportOption`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateExportOption` (IN `p_databasename` VARCHAR(500), IN `p_table_name` VARCHAR(500))   BEGIN
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_schema = p_databasename 
    AND table_name = p_table_name
    ORDER BY ordinal_position;
END$$

DROP PROCEDURE IF EXISTS `generateFileExtensionOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateFileExtensionOptions` ()   BEGIN
	SELECT file_extension_id, file_extension_name, file_extension
    FROM file_extension 
    ORDER BY file_extension_name;
END$$

DROP PROCEDURE IF EXISTS `generateFileExtensionTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateFileExtensionTable` (IN `p_filter_by_file_type` TEXT)   BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT file_extension_id, file_extension_name, file_extension, file_type_name 
                FROM file_extension ';

    IF p_filter_by_file_type IS NOT NULL AND p_filter_by_file_type <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' file_type_id IN (', p_filter_by_file_type, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY file_extension_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateFileTypeOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateFileTypeOptions` ()   BEGIN
	SELECT file_type_id, file_type_name 
    FROM file_type 
    ORDER BY file_type_name;
END$$

DROP PROCEDURE IF EXISTS `generateFileTypeTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateFileTypeTable` ()   BEGIN
	SELECT file_type_id, file_type_name
    FROM file_type 
    ORDER BY file_type_id;
END$$

DROP PROCEDURE IF EXISTS `generateGenderOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateGenderOptions` ()   BEGIN
	SELECT gender_id, gender_name 
    FROM gender 
    ORDER BY gender_name;
END$$

DROP PROCEDURE IF EXISTS `generateGenderTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateGenderTable` ()   BEGIN
	SELECT gender_id, gender_name
    FROM gender 
    ORDER BY gender_id;
END$$

DROP PROCEDURE IF EXISTS `generateInternalNotes`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateInternalNotes` (IN `p_table_name` VARCHAR(255), IN `p_reference_id` INT)   BEGIN
	SELECT internal_notes_id, internal_note, internal_note_by, internal_note_date
    FROM internal_notes
    WHERE table_name = p_table_name AND reference_id  = p_reference_id
    ORDER BY internal_note_date DESC;
END$$

DROP PROCEDURE IF EXISTS `generateLanguageOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateLanguageOptions` ()   BEGIN
	SELECT language_id, language_name 
    FROM language 
    ORDER BY language_name;
END$$

DROP PROCEDURE IF EXISTS `generateLanguageProficiencyOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateLanguageProficiencyOptions` ()   BEGIN
    SELECT language_proficiency_id, language_proficiency_name 
    FROM language_proficiency 
    ORDER BY language_proficiency_name;
END$$

DROP PROCEDURE IF EXISTS `generateLanguageProficiencyTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateLanguageProficiencyTable` ()   BEGIN
    SELECT language_proficiency_id, language_proficiency_name, language_proficiency_description 
    FROM language_proficiency
    ORDER BY language_proficiency_id;
END$$

DROP PROCEDURE IF EXISTS `generateLanguageTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateLanguageTable` ()   BEGIN
	SELECT language_id, language_name
    FROM language 
    ORDER BY language_id;
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

DROP PROCEDURE IF EXISTS `generateNotificationSettingTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateNotificationSettingTable` ()   BEGIN
	SELECT notification_setting_id, notification_setting_name, notification_setting_description
    FROM notification_setting 
    ORDER BY notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `generateParentDepartmentOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateParentDepartmentOptions` (IN `p_department_id` INT)   BEGIN
	SELECT department_id, department_name
    FROM department 
    WHERE department_id != p_department_id
    ORDER BY department_name;
END$$

DROP PROCEDURE IF EXISTS `generateRelationshipOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRelationshipOptions` ()   BEGIN
	SELECT relationship_id, relationship_name 
    FROM relationship 
    ORDER BY relationship_name;
END$$

DROP PROCEDURE IF EXISTS `generateRelationshipTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateRelationshipTable` ()   BEGIN
	SELECT relationship_id, relationship_name
    FROM relationship 
    ORDER BY relationship_id;
END$$

DROP PROCEDURE IF EXISTS `generateReligionOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateReligionOptions` ()   BEGIN
	SELECT religion_id, religion_name 
    FROM religion 
    ORDER BY religion_name;
END$$

DROP PROCEDURE IF EXISTS `generateReligionTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateReligionTable` ()   BEGIN
	SELECT religion_id, religion_name
    FROM religion 
    ORDER BY religion_id;
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

DROP PROCEDURE IF EXISTS `generateStateOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateStateOptions` ()   BEGIN
    SELECT state_id, state_name 
    FROM state 
    ORDER BY state_name;
END$$

DROP PROCEDURE IF EXISTS `generateStateTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateStateTable` (IN `p_filter_by_country` TEXT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `generateSubscriberOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSubscriberOptions` ()   BEGIN
	SELECT subscriber_id, subscriber_name 
    FROM subscriber 
    ORDER BY subscriber_name;
END$$

DROP PROCEDURE IF EXISTS `generateSubscriberTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSubscriberTable` (IN `p_filter_by_subscription_tier` TEXT, IN `p_filter_by_billing_cycle` TEXT)   BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT subscriber_id, subscriber_name, company_name, phone, email, subscriber_status, subscription_tier_name, billing_cycle_name 
                FROM subscriber ';

    IF p_filter_by_subscription_tier IS NOT NULL AND p_filter_by_subscription_tier <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' subscription_tier_id IN (', p_filter_by_subscription_tier, ')');
    END IF;

    IF p_filter_by_billing_cycle IS NOT NULL AND p_filter_by_billing_cycle <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
        SET filter_conditions = CONCAT(filter_conditions, ' billing_cycle_id IN (', p_filter_by_billing_cycle, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY subscriber_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `generateSubscriptionTierOptions`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSubscriptionTierOptions` ()   BEGIN
	SELECT subscription_tier_id, subscription_tier_name 
    FROM subscription_tier 
    ORDER BY subscription_tier_name;
END$$

DROP PROCEDURE IF EXISTS `generateSubscriptionTierTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateSubscriptionTierTable` ()   BEGIN
	SELECT subscription_tier_id, subscription_tier_name, subscription_tier_description, order_sequence 
    FROM subscription_tier 
    ORDER BY subscription_tier_id;
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

DROP PROCEDURE IF EXISTS `generateTables`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateTables` (IN `p_database_name` VARCHAR(255))   BEGIN
	SELECT table_name FROM information_schema.tables WHERE table_schema = p_database_name;
END$$

DROP PROCEDURE IF EXISTS `generateUploadSettingTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateUploadSettingTable` ()   BEGIN
	SELECT upload_setting_id, upload_setting_name, upload_setting_description, max_file_size
    FROM upload_setting 
    ORDER BY upload_setting_id;
END$$

DROP PROCEDURE IF EXISTS `generateUserAccountLoginSession`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateUserAccountLoginSession` (IN `p_user_account_id` INT)   BEGIN
	SELECT location, login_status, device, ip_address, login_date 
    FROM login_session
    WHERE user_account_id = p_user_account_id
    ORDER BY login_date DESC;
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

DROP PROCEDURE IF EXISTS `generateUserAccountTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateUserAccountTable` ()   BEGIN
	SELECT user_account_id, file_as, username, email, profile_picture, locked, active, password_expiry_date, last_connection_date 
    FROM user_account
    /*WHERE user_account_id NOT IN (1, 2)*/
    ORDER BY user_account_id;
END$$

DROP PROCEDURE IF EXISTS `getAddressType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAddressType` (IN `p_address_type_id` INT)   BEGIN
	SELECT * FROM address_type
	WHERE address_type_id = p_address_type_id;
END$$

DROP PROCEDURE IF EXISTS `getAppModule`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAppModule` (IN `p_app_module_id` INT)   BEGIN
	SELECT * FROM app_module
	WHERE app_module_id = p_app_module_id;
END$$

DROP PROCEDURE IF EXISTS `getBank`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBank` (IN `p_bank_id` INT)   BEGIN
	SELECT * FROM bank
	WHERE bank_id = p_bank_id;
END$$

DROP PROCEDURE IF EXISTS `getBankAccountType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBankAccountType` (IN `p_bank_account_type_id` INT)   BEGIN
	SELECT * FROM bank_account_type
	WHERE bank_account_type_id = p_bank_account_type_id;
END$$

DROP PROCEDURE IF EXISTS `getBillingCycle`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBillingCycle` (IN `p_billing_cycle_id` INT)   BEGIN
	SELECT * FROM billing_cycle
	WHERE billing_cycle_id = p_billing_cycle_id;
END$$

DROP PROCEDURE IF EXISTS `getBloodType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getBloodType` (IN `p_blood_type_id` INT)   BEGIN
	SELECT * FROM blood_type
	WHERE blood_type_id = p_blood_type_id;
END$$

DROP PROCEDURE IF EXISTS `getCity`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCity` (IN `p_city_id` INT)   BEGIN
	SELECT * FROM city
	WHERE city_id = p_city_id;
END$$

DROP PROCEDURE IF EXISTS `getCivilStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCivilStatus` (IN `p_civil_status_id` INT)   BEGIN
	SELECT * FROM civil_status
	WHERE civil_status_id = p_civil_status_id;
END$$

DROP PROCEDURE IF EXISTS `getCompany`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCompany` (IN `p_company_id` INT)   BEGIN
	SELECT * FROM company
	WHERE company_id = p_company_id;
END$$

DROP PROCEDURE IF EXISTS `getContactInformationType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getContactInformationType` (IN `p_contact_information_type_id` INT)   BEGIN
	SELECT * FROM contact_information_type
	WHERE contact_information_type_id = p_contact_information_type_id;
END$$

DROP PROCEDURE IF EXISTS `getCountry`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCountry` (IN `p_country_id` INT)   BEGIN
	SELECT * FROM country
	WHERE country_id = p_country_id;
END$$

DROP PROCEDURE IF EXISTS `getCredentialType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCredentialType` (IN `p_credential_type_id` INT)   BEGIN
	SELECT * FROM credential_type
	WHERE credential_type_id = p_credential_type_id;
END$$

DROP PROCEDURE IF EXISTS `getCurrency`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getCurrency` (IN `p_currency_id` INT)   BEGIN
	SELECT * FROM currency
	WHERE currency_id = p_currency_id;
END$$

DROP PROCEDURE IF EXISTS `getDepartment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getDepartment` (IN `p_department_id` INT)   BEGIN
	SELECT * FROM department
	WHERE department_id = p_department_id;
END$$

DROP PROCEDURE IF EXISTS `getEducationalStage`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getEducationalStage` (IN `p_educational_stage_id` INT)   BEGIN
	SELECT * FROM educational_stage
	WHERE educational_stage_id = p_educational_stage_id;
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

DROP PROCEDURE IF EXISTS `getFileExtension`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFileExtension` (IN `p_file_extension_id` INT)   BEGIN
	SELECT * FROM file_extension
	WHERE file_extension_id = p_file_extension_id;
END$$

DROP PROCEDURE IF EXISTS `getFileType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getFileType` (IN `p_file_type_id` INT)   BEGIN
	SELECT * FROM file_type
	WHERE file_type_id = p_file_type_id;
END$$

DROP PROCEDURE IF EXISTS `getGender`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getGender` (IN `p_gender_id` INT)   BEGIN
	SELECT * FROM gender
	WHERE gender_id = p_gender_id;
END$$

DROP PROCEDURE IF EXISTS `getInternalNotesAttachment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getInternalNotesAttachment` (IN `p_internal_notes_id` INT)   BEGIN
	SELECT * FROM internal_notes_attachment
	WHERE internal_notes_id = p_internal_notes_id;
END$$

DROP PROCEDURE IF EXISTS `getLanguage`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguage` (IN `p_language_id` INT)   BEGIN
	SELECT * FROM language
	WHERE language_id = p_language_id;
END$$

DROP PROCEDURE IF EXISTS `getLanguageProficiency`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getLanguageProficiency` (IN `p_language_proficiency_id` INT)   BEGIN
	SELECT * FROM language_proficiency
	WHERE language_proficiency_id = p_language_proficiency_id;
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

DROP PROCEDURE IF EXISTS `getNotificationSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getNotificationSetting` (IN `p_notification_setting_id` INT)   BEGIN
	SELECT * FROM notification_setting
	WHERE notification_setting_id = p_notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getNotificationSettingEmailTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getNotificationSettingEmailTemplate` (IN `p_notification_setting_id` INT)   BEGIN
	SELECT * FROM notification_setting_email_template
	WHERE notification_setting_id = p_notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getNotificationSettingSMSTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getNotificationSettingSMSTemplate` (IN `p_notification_setting_id` INT)   BEGIN
	SELECT * FROM notification_setting_sms_template
	WHERE notification_setting_id = p_notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getNotificationSettingSystemTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getNotificationSettingSystemTemplate` (IN `p_notification_setting_id` INT)   BEGIN
	SELECT * FROM notification_setting_system_template
	WHERE notification_setting_id = p_notification_setting_id;
END$$

DROP PROCEDURE IF EXISTS `getPasswordHistory`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getPasswordHistory` (IN `p_user_account_id` INT)   BEGIN
    SELECT password 
    FROM password_history
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `getRelationship`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getRelationship` (IN `p_relationship_id` INT)   BEGIN
	SELECT * FROM relationship
	WHERE relationship_id = p_relationship_id;
END$$

DROP PROCEDURE IF EXISTS `getReligion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getReligion` (IN `p_religion_id` INT)   BEGIN
	SELECT * FROM religion
	WHERE religion_id = p_religion_id;
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

DROP PROCEDURE IF EXISTS `getState`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getState` (IN `p_state_id` INT)   BEGIN
	SELECT * FROM state
	WHERE state_id = p_state_id;
END$$

DROP PROCEDURE IF EXISTS `getSubscriber`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSubscriber` (IN `p_subscriber_id` INT)   BEGIN
	SELECT * FROM subscriber
	WHERE subscriber_id = p_subscriber_id;
END$$

DROP PROCEDURE IF EXISTS `getSubscriptionTier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSubscriptionTier` (IN `p_subscription_tier_id` INT)   BEGIN
	SELECT * FROM subscription_tier
	WHERE subscription_tier_id = p_subscription_tier_id;
END$$

DROP PROCEDURE IF EXISTS `getSystemAction`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getSystemAction` (IN `p_system_action_id` INT)   BEGIN
	SELECT * FROM system_action
	WHERE system_action_id = p_system_action_id;
END$$

DROP PROCEDURE IF EXISTS `getTotalProductCost`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getTotalProductCost` (IN `p_product_id` INT)   BEGIN
	SELECT SUM(expense_amount) AS expense_amount FROM product_expense
    WHERE product_id = p_product_id;
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

DROP PROCEDURE IF EXISTS `getUserAccount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getUserAccount` (IN `p_user_account_id` INT)   BEGIN
	SELECT * FROM user_account
	WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `insertLoginSession`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertLoginSession` (IN `p_user_account_id` INT, IN `p_location` VARCHAR(500), IN `p_login_status` VARCHAR(50), IN `p_device` VARCHAR(200), IN `p_ip_address` VARCHAR(50))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO login_session (user_account_id, location, login_status, device, ip_address) 
    VALUES(p_user_account_id, p_location, p_login_status, p_device, p_ip_address);
    
    COMMIT;
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

DROP PROCEDURE IF EXISTS `insertUploadSettingFileExtension`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `insertUploadSettingFileExtension` (IN `p_upload_setting_id` INT, IN `p_upload_setting_name` VARCHAR(100), IN `p_file_extension_id` INT, IN `p_file_extension_name` VARCHAR(100), IN `p_file_extension` VARCHAR(10), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO upload_setting_file_extension (upload_setting_id, upload_setting_name, file_extension_id, file_extension_name, file_extension, last_log_by) 
    VALUES(p_upload_setting_id, p_upload_setting_name, p_file_extension_id, p_file_extension_name, p_file_extension, p_last_log_by);

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveAddressType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveAddressType` (IN `p_address_type_id` INT, IN `p_address_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_address_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_address_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM address_type WHERE address_type_id = p_address_type_id) THEN
        INSERT INTO address_type (address_type_name, last_log_by) 
        VALUES(p_address_type_name, p_last_log_by);
        
        SET p_new_address_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE address_type
        SET address_type_name = p_address_type_name,
            last_log_by = p_last_log_by
        WHERE address_type_id = p_address_type_id;

        SET p_new_address_type_id = p_address_type_id;
    END IF;

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

DROP PROCEDURE IF EXISTS `saveBank`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveBank` (IN `p_bank_id` INT, IN `p_bank_name` VARCHAR(100), IN `p_bank_identifier_code` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_bank_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_bank_id IS NULL OR NOT EXISTS (SELECT 1 FROM bank WHERE bank_id = p_bank_id) THEN
        INSERT INTO bank (bank_name, bank_identifier_code, last_log_by) 
        VALUES(p_bank_name, p_bank_identifier_code, p_last_log_by);
        
        SET p_new_bank_id = LAST_INSERT_ID();
    ELSE        
        UPDATE bank
        SET bank_name = p_bank_name,
            bank_identifier_code = p_bank_identifier_code,
            last_log_by = p_last_log_by
        WHERE bank_id = p_bank_id;

        SET p_new_bank_id = p_bank_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveBankAccountType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveBankAccountType` (IN `p_bank_account_type_id` INT, IN `p_bank_account_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_bank_account_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_bank_account_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM bank_account_type WHERE bank_account_type_id = p_bank_account_type_id) THEN
        INSERT INTO bank_account_type (bank_account_type_name, last_log_by) 
        VALUES(p_bank_account_type_name, p_last_log_by);
        
        SET p_new_bank_account_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE bank_account_type
        SET bank_account_type_name = p_bank_account_type_name,
            last_log_by = p_last_log_by
        WHERE bank_account_type_id = p_bank_account_type_id;

        SET p_new_bank_account_type_id = p_bank_account_type_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveBillingCycle`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveBillingCycle` (IN `p_billing_cycle_id` INT, IN `p_billing_cycle_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_billing_cycle_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_billing_cycle_id IS NULL OR NOT EXISTS (SELECT 1 FROM billing_cycle WHERE billing_cycle_id = p_billing_cycle_id) THEN
        INSERT INTO billing_cycle (billing_cycle_name, last_log_by) 
        VALUES(p_billing_cycle_name, p_last_log_by);
        
        SET p_new_billing_cycle_id = LAST_INSERT_ID();
    ELSE
        UPDATE subscriber
        SET billing_cycle_name = p_billing_cycle_name,
            last_log_by = p_last_log_by
        WHERE billing_cycle_id = p_billing_cycle_id;

        UPDATE billing_cycle
        SET billing_cycle_name = p_billing_cycle_name,
            last_log_by = p_last_log_by
        WHERE billing_cycle_id = p_billing_cycle_id;

        SET p_new_billing_cycle_id = p_billing_cycle_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveBloodType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveBloodType` (IN `p_blood_type_id` INT, IN `p_blood_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_blood_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_blood_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM blood_type WHERE blood_type_id = p_blood_type_id) THEN
        INSERT INTO blood_type (blood_type_name, last_log_by) 
        VALUES(p_blood_type_name, p_last_log_by);
        
        SET p_new_blood_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE blood_type
        SET blood_type_name = p_blood_type_name,
            last_log_by = p_last_log_by
        WHERE blood_type_id = p_blood_type_id;

        SET p_new_blood_type_id = p_blood_type_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveCity`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveCity` (IN `p_city_id` INT, IN `p_city_name` VARCHAR(100), IN `p_state_id` INT, IN `p_state_name` VARCHAR(100), IN `p_country_id` INT, IN `p_country_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_city_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `saveCivilStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveCivilStatus` (IN `p_civil_status_id` INT, IN `p_civil_status_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_civil_status_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_civil_status_id IS NULL OR NOT EXISTS (SELECT 1 FROM civil_status WHERE civil_status_id = p_civil_status_id) THEN
        INSERT INTO civil_status (civil_status_name, last_log_by) 
        VALUES(p_civil_status_name, p_last_log_by);
        
        SET p_new_civil_status_id = LAST_INSERT_ID();
    ELSE
        UPDATE civil_status
        SET civil_status_name = p_civil_status_name,
            last_log_by = p_last_log_by
        WHERE civil_status_id = p_civil_status_id;

        SET p_new_civil_status_id = p_civil_status_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveCompany`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveCompany` (IN `p_company_id` INT, IN `p_company_name` VARCHAR(100), IN `p_address` VARCHAR(1000), IN `p_city_id` INT, IN `p_city_name` VARCHAR(100), IN `p_state_id` INT, IN `p_state_name` VARCHAR(100), IN `p_country_id` INT, IN `p_country_name` VARCHAR(100), IN `p_tax_id` VARCHAR(100), IN `p_currency_id` INT, IN `p_currency_name` VARCHAR(100), IN `p_phone` VARCHAR(20), IN `p_telephone` VARCHAR(20), IN `p_email` VARCHAR(255), IN `p_website` VARCHAR(255), IN `p_last_log_by` INT, OUT `p_new_company_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_company_id IS NULL OR NOT EXISTS (SELECT 1 FROM company WHERE company_id = p_company_id) THEN
        INSERT INTO company (company_name, address, city_id, city_name, state_id, state_name, country_id, country_name, tax_id, currency_id, currency_name, phone, telephone, email, website, last_log_by) 
        VALUES(p_company_name, p_address, p_city_id, p_city_name, p_state_id, p_state_name, p_country_id, p_country_name, p_tax_id, p_currency_id, p_currency_name, p_phone, p_telephone, p_email, p_website, p_last_log_by);
        
        SET p_new_company_id = LAST_INSERT_ID();
    ELSE
        UPDATE company
        SET company_name = p_company_name,
            address = p_address,
            city_id = p_city_id,
            city_name = p_city_name,
            state_id = p_state_id,
            state_name = p_state_name,
            country_id = p_country_id,
            country_name = p_country_name,
            tax_id = p_tax_id,
            currency_id = p_currency_id,
            currency_name = p_currency_name,
            phone = p_phone,
            telephone = p_telephone,
            email = p_email,
            website = p_website,
            last_log_by = p_last_log_by
        WHERE company_id = p_company_id;

        SET p_new_company_id = p_company_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveContactInformationType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveContactInformationType` (IN `p_contact_information_type_id` INT, IN `p_contact_information_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_contact_information_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_contact_information_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM contact_information_type WHERE contact_information_type_id = p_contact_information_type_id) THEN
        INSERT INTO contact_information_type (contact_information_type_name, last_log_by) 
        VALUES(p_contact_information_type_name, p_last_log_by);
        
        SET p_new_contact_information_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE contact_information_type
        SET contact_information_type_name = p_contact_information_type_name,
            last_log_by = p_last_log_by
        WHERE contact_information_type_id = p_contact_information_type_id;

        SET p_new_contact_information_type_id = p_contact_information_type_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveCountry`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveCountry` (IN `p_country_id` INT, IN `p_country_name` VARCHAR(100), IN `p_country_code` VARCHAR(10), IN `p_phone_code` VARCHAR(10), IN `p_last_log_by` INT, OUT `p_new_country_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_country_id IS NULL OR NOT EXISTS (SELECT 1 FROM country WHERE country_id = p_country_id) THEN
        INSERT INTO country (country_name, country_code, phone_code, last_log_by) 
        VALUES(p_country_name, p_country_code, p_phone_code, p_last_log_by);
        
        SET p_new_country_id = LAST_INSERT_ID();
    ELSE
        UPDATE state
        SET country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE country_id = p_country_id;

        UPDATE city
        SET country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE country_id = p_country_id;
        
        UPDATE country
        SET country_name = p_country_name,
            country_code = p_country_code,
            phone_code = p_phone_code,
            last_log_by = p_last_log_by
        WHERE country_id = p_country_id;

        SET p_new_country_id = p_country_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveCredentialType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveCredentialType` (IN `p_credential_type_id` INT, IN `p_credential_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_credential_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_credential_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM credential_type WHERE credential_type_id = p_credential_type_id) THEN
        INSERT INTO credential_type (credential_type_name, last_log_by) 
        VALUES(p_credential_type_name, p_last_log_by);
        
        SET p_new_credential_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE credential_type
        SET credential_type_name = p_credential_type_name,
            last_log_by = p_last_log_by
        WHERE credential_type_id = p_credential_type_id;

        SET p_new_credential_type_id = p_credential_type_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveCurrency`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveCurrency` (IN `p_currency_id` INT, IN `p_currency_name` VARCHAR(100), IN `p_symbol` VARCHAR(5), IN `p_shorthand` VARCHAR(10), IN `p_last_log_by` INT, OUT `p_new_currency_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_currency_id IS NULL OR NOT EXISTS (SELECT 1 FROM currency WHERE currency_id = p_currency_id) THEN
        INSERT INTO currency (currency_name, symbol, shorthand, last_log_by) 
        VALUES(p_currency_name, p_symbol, p_shorthand, p_last_log_by);
        
        SET p_new_currency_id = LAST_INSERT_ID();
    ELSE        
        UPDATE currency
        SET currency_name = p_currency_name,
            symbol = p_symbol,
            shorthand = p_shorthand,
            last_log_by = p_last_log_by
        WHERE currency_id = p_currency_id;

        SET p_new_currency_id = p_currency_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveDepartment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveDepartment` (IN `p_department_id` INT, IN `p_department_name` VARCHAR(100), IN `p_parent_department_id` INT, IN `p_parent_department_name` VARCHAR(100), IN `p_manager_id` INT, IN `p_manager_name` VARCHAR(500), IN `p_last_log_by` INT, OUT `p_new_department_id` INT)   BEGIN
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
END$$

DROP PROCEDURE IF EXISTS `saveEducationalStage`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveEducationalStage` (IN `p_educational_stage_id` INT, IN `p_educational_stage_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_educational_stage_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_educational_stage_id IS NULL OR NOT EXISTS (SELECT 1 FROM educational_stage WHERE educational_stage_id = p_educational_stage_id) THEN
        INSERT INTO educational_stage (educational_stage_name, last_log_by) 
        VALUES(p_educational_stage_name, p_last_log_by);
        
        SET p_new_educational_stage_id = LAST_INSERT_ID();
    ELSE
        UPDATE educational_stage
        SET educational_stage_name = p_educational_stage_name,
            last_log_by = p_last_log_by
        WHERE educational_stage_id = p_educational_stage_id;

        SET p_new_educational_stage_id = p_educational_stage_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveEmailSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveEmailSetting` (IN `p_email_setting_id` INT, IN `p_email_setting_name` VARCHAR(100), IN `p_email_setting_description` VARCHAR(200), IN `p_mail_host` VARCHAR(100), IN `p_port` VARCHAR(100), IN `p_smtp_auth` INT(1), IN `p_smtp_auto_tls` INT(1), IN `p_mail_username` VARCHAR(200), IN `p_mail_password` VARCHAR(250), IN `p_mail_encryption` VARCHAR(20), IN `p_mail_from_name` VARCHAR(200), IN `p_mail_from_email` VARCHAR(200), IN `p_last_log_by` INT, OUT `p_new_email_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_email_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM email_setting WHERE email_setting_id = p_email_setting_id) THEN
        INSERT INTO email_setting (email_setting_name, email_setting_description, mail_host, port, smtp_auth, smtp_auto_tls, mail_username, mail_password, mail_encryption, mail_from_name, mail_from_email, last_log_by) 
        VALUES(p_email_setting_name, p_email_setting_description, p_mail_host, p_port, p_smtp_auth, p_smtp_auto_tls, p_mail_username, p_mail_password, p_mail_encryption, p_mail_from_name, p_mail_from_email, p_last_log_by);
        
        SET p_new_email_setting_id = LAST_INSERT_ID();
    ELSE
        UPDATE email_setting
        SET email_setting_name = p_email_setting_name,
        	email_setting_description = p_email_setting_description,
        	mail_host = p_mail_host,
        	port = p_port,
        	smtp_auth = p_smtp_auth,
        	smtp_auto_tls = p_smtp_auto_tls,
        	mail_username = p_mail_username,
        	mail_password = p_mail_password,
        	mail_encryption = p_mail_encryption,
        	mail_from_name = p_mail_from_name,
        	mail_from_email = p_mail_from_email,
            last_log_by = p_last_log_by
        WHERE email_setting_id = p_email_setting_id;

        SET p_new_email_setting_id = p_email_setting_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveFileExtension`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveFileExtension` (IN `p_file_extension_id` INT, IN `p_file_extension_name` VARCHAR(100), IN `p_file_extension` VARCHAR(10), IN `p_file_type_id` INT, IN `p_file_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_file_extension_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_file_extension_id IS NULL OR NOT EXISTS (SELECT 1 FROM file_extension WHERE file_extension_id = p_file_extension_id) THEN
        INSERT INTO file_extension (file_extension_name, file_extension, file_type_id, file_type_name, last_log_by) 
        VALUES(p_file_extension_name, p_file_extension, p_file_type_id, p_file_type_name, p_last_log_by);
        
        SET p_new_file_extension_id = LAST_INSERT_ID();
    ELSE
        UPDATE file_extension
        SET file_extension_name = p_file_extension_name,
            file_extension = p_file_extension,
            file_type_id = p_file_type_id,
            file_type_name = p_file_type_name,
            last_log_by = p_last_log_by
        WHERE file_extension_id = p_file_extension_id;

        SET p_new_file_extension_id = p_file_extension_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveFileType`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveFileType` (IN `p_file_type_id` INT, IN `p_file_type_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_file_type_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_file_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM file_type WHERE file_type_id = p_file_type_id) THEN
        INSERT INTO file_type (file_type_name, last_log_by) 
        VALUES(p_file_type_name, p_last_log_by);
        
        SET p_new_file_type_id = LAST_INSERT_ID();
    ELSE
        /*UPDATE file_extension
        SET file_type_name = p_file_type_name,
            last_log_by = p_last_log_by
        WHERE file_type_id = p_file_type_id;*/

        UPDATE file_type
        SET file_type_name = p_file_type_name,
            last_log_by = p_last_log_by
        WHERE file_type_id = p_file_type_id;

        SET p_new_file_type_id = p_file_type_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveGender`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveGender` (IN `p_gender_id` INT, IN `p_gender_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_gender_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_gender_id IS NULL OR NOT EXISTS (SELECT 1 FROM gender WHERE gender_id = p_gender_id) THEN
        INSERT INTO gender (gender_name, last_log_by) 
        VALUES(p_gender_name, p_last_log_by);
        
        SET p_new_gender_id = LAST_INSERT_ID();
    ELSE
        UPDATE gender
        SET gender_name = p_gender_name,
            last_log_by = p_last_log_by
        WHERE gender_id = p_gender_id;

        SET p_new_gender_id = p_gender_id;
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

DROP PROCEDURE IF EXISTS `saveLanguage`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveLanguage` (IN `p_language_id` INT, IN `p_language_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_language_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_language_id IS NULL OR NOT EXISTS (SELECT 1 FROM language WHERE language_id = p_language_id) THEN
        INSERT INTO language (language_name, last_log_by) 
        VALUES(p_language_name, p_last_log_by);
        
        SET p_new_language_id = LAST_INSERT_ID();
    ELSE
        UPDATE language
        SET language_name = p_language_name,
            last_log_by = p_last_log_by
        WHERE language_id = p_language_id;

        SET p_new_language_id = p_language_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveLanguageProficiency`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveLanguageProficiency` (IN `p_language_proficiency_id` INT, IN `p_language_proficiency_name` VARCHAR(100), IN `p_language_proficiency_description` VARCHAR(200), IN `p_last_log_by` INT, OUT `p_new_language_proficiency_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_language_proficiency_id IS NULL OR NOT EXISTS (SELECT 1 FROM language_proficiency WHERE language_proficiency_id = p_language_proficiency_id) THEN
        INSERT INTO language_proficiency (language_proficiency_name, language_proficiency_description, last_log_by) 
        VALUES(p_language_proficiency_name, p_language_proficiency_description, p_last_log_by);
        
        SET p_new_language_proficiency_id = LAST_INSERT_ID();
    ELSE        
        UPDATE language_proficiency
        SET language_proficiency_name = p_language_proficiency_name,
            language_proficiency_description = p_language_proficiency_description,
            last_log_by = p_last_log_by
        WHERE language_proficiency_id = p_language_proficiency_id;

        SET p_new_language_proficiency_id = p_language_proficiency_id;
    END IF;

    COMMIT;
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

DROP PROCEDURE IF EXISTS `saveNotificationSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveNotificationSetting` (IN `p_notification_setting_id` INT, IN `p_notification_setting_name` VARCHAR(100), IN `p_notification_setting_description` VARCHAR(200), IN `p_last_log_by` INT, OUT `p_new_notification_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_notification_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM notification_setting WHERE notification_setting_id = p_notification_setting_id) THEN
        INSERT INTO notification_setting (notification_setting_name, notification_setting_description, last_log_by) 
        VALUES(p_notification_setting_name, p_notification_setting_description, p_last_log_by);
        
        SET p_new_notification_setting_id = LAST_INSERT_ID();
    ELSE
        UPDATE notification_setting
        SET notification_setting_name = p_notification_setting_name,
        	notification_setting_description = p_notification_setting_description,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;

        SET p_new_notification_setting_id = p_notification_setting_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveRelationship`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveRelationship` (IN `p_relationship_id` INT, IN `p_relationship_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_relationship_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_relationship_id IS NULL OR NOT EXISTS (SELECT 1 FROM relationship WHERE relationship_id = p_relationship_id) THEN
        INSERT INTO relationship (relationship_name, last_log_by) 
        VALUES(p_relationship_name, p_last_log_by);
        
        SET p_new_relationship_id = LAST_INSERT_ID();
    ELSE
        UPDATE relationship
        SET relationship_name = p_relationship_name,
            last_log_by = p_last_log_by
        WHERE relationship_id = p_relationship_id;

        SET p_new_relationship_id = p_relationship_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveReligion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveReligion` (IN `p_religion_id` INT, IN `p_religion_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_religion_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_religion_id IS NULL OR NOT EXISTS (SELECT 1 FROM religion WHERE religion_id = p_religion_id) THEN
        INSERT INTO religion (religion_name, last_log_by) 
        VALUES(p_religion_name, p_last_log_by);
        
        SET p_new_religion_id = LAST_INSERT_ID();
    ELSE
        UPDATE religion
        SET religion_name = p_religion_name,
            last_log_by = p_last_log_by
        WHERE religion_id = p_religion_id;

        SET p_new_religion_id = p_religion_id;
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

DROP PROCEDURE IF EXISTS `saveState`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveState` (IN `p_state_id` INT, IN `p_state_name` VARCHAR(100), IN `p_country_id` INT, IN `p_country_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_state_id` INT)   BEGIN
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
        

        UPDATE state
        SET state_name = p_state_name,
            country_id = p_country_id,
            country_name = p_country_name,
            last_log_by = p_last_log_by
        WHERE state_id = p_state_id;

        SET p_new_state_id = p_state_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveSubscriber`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveSubscriber` (IN `p_subscriber_id` INT, IN `p_subscriber_name` VARCHAR(500), IN `p_company_name` VARCHAR(200), IN `p_phone` VARCHAR(50), IN `p_email` VARCHAR(255), IN `p_subscriber_status` VARCHAR(10), IN `p_subscription_tier_id` INT, IN `p_subscription_tier_name` VARCHAR(100), IN `p_billing_cycle_id` INT, IN `p_billing_cycle_name` VARCHAR(100), IN `p_last_log_by` INT, OUT `p_new_subscriber_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_subscriber_id IS NULL OR NOT EXISTS (SELECT 1 FROM subscriber WHERE subscriber_id = p_subscriber_id) THEN
        INSERT INTO subscriber (subscriber_name, company_name, phone, email, subscription_tier_id, subscription_tier_name, billing_cycle_id, billing_cycle_name, last_log_by) 
        VALUES(p_subscriber_name, p_company_name, p_phone, p_email, p_subscription_tier_id, p_subscription_tier_name, p_billing_cycle_id, p_billing_cycle_name, p_last_log_by);
        
        SET p_new_subscriber_id = LAST_INSERT_ID();
    ELSE
        UPDATE subscription
        SET subscriber_name = p_subscriber_name,
            last_log_by = p_last_log_by
        WHERE subscriber_id = p_subscriber_id;

        UPDATE subscriber
        SET subscriber_name = p_subscriber_name,
            company_name = p_company_name,
            phone = p_phone,
            email = p_email,
            subscriber_status = p_subscriber_status,
            subscription_tier_id = p_subscription_tier_id,
            subscription_tier_name = p_subscription_tier_name,
            billing_cycle_id = p_billing_cycle_id,
            billing_cycle_name = p_billing_cycle_name,
            last_log_by = p_last_log_by
        WHERE subscriber_id = p_subscriber_id;

        SET p_new_subscriber_id = p_subscriber_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `saveSubscriptionTier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveSubscriptionTier` (IN `p_subscription_tier_id` INT, IN `p_subscription_tier_name` VARCHAR(100), IN `p_subscription_tier_description` VARCHAR(500), IN `p_order_sequence` TINYINT(10), IN `p_last_log_by` INT, OUT `p_new_subscription_tier_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_subscription_tier_id IS NULL OR NOT EXISTS (SELECT 1 FROM subscription_tier WHERE subscription_tier_id = p_subscription_tier_id) THEN
        INSERT INTO subscription_tier (subscription_tier_name, subscription_tier_description, order_sequence, last_log_by) 
        VALUES(p_subscription_tier_name, p_subscription_tier_description, p_order_sequence, p_last_log_by);
        
        SET p_new_subscription_tier_id = LAST_INSERT_ID();
    ELSE
        UPDATE subscriber
        SET subscription_tier_name = p_subscription_tier_name,
            last_log_by = p_last_log_by
        WHERE subscription_tier_id = p_subscription_tier_id;

        UPDATE subscription_tier
        SET subscription_tier_name = p_subscription_tier_name,
            subscription_tier_description = p_subscription_tier_description,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE subscription_tier_id = p_subscription_tier_id;

        SET p_new_subscription_tier_id = p_subscription_tier_id;
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

DROP PROCEDURE IF EXISTS `saveUploadSetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveUploadSetting` (IN `p_upload_setting_id` INT, IN `p_upload_setting_name` VARCHAR(100), IN `p_upload_setting_description` VARCHAR(200), IN `p_max_file_size` DOUBLE, IN `p_last_log_by` INT, OUT `p_new_upload_setting_id` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_upload_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM upload_setting WHERE upload_setting_id = p_upload_setting_id) THEN
        INSERT INTO upload_setting (upload_setting_name, upload_setting_description, max_file_size, last_log_by) 
        VALUES(p_upload_setting_name, p_upload_setting_description, p_max_file_size, p_last_log_by);
        
        SET p_new_upload_setting_id = LAST_INSERT_ID();
    ELSE
        UPDATE upload_setting_file_extension
        SET upload_setting_name = p_upload_setting_name,
            last_log_by = p_last_log_by
        WHERE upload_setting_id = p_upload_setting_id;

        UPDATE upload_setting
        SET upload_setting_name = p_upload_setting_name,
        	upload_setting_description = p_upload_setting_description,
        	max_file_size = p_max_file_size,
            last_log_by = p_last_log_by
        WHERE upload_setting_id = p_upload_setting_id;

        SET p_new_upload_setting_id = p_upload_setting_id;
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

DROP PROCEDURE IF EXISTS `updateCompanyLogo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCompanyLogo` (IN `p_company_id` INT, IN `p_company_logo` VARCHAR(500), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE company
    SET company_logo = p_company_logo,
        last_log_by = p_last_log_by
    WHERE company_id = p_company_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateEmailNotificationTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateEmailNotificationTemplate` (IN `p_notification_setting_id` INT, IN `p_email_notification_subject` VARCHAR(200), IN `p_email_notification_body` LONGTEXT, IN `p_email_setting_id` INT, IN `p_email_setting_name` VARCHAR(100), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_notification_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM notification_setting_email_template WHERE notification_setting_id = p_notification_setting_id) THEN
        INSERT INTO notification_setting_email_template (notification_setting_id, email_notification_subject, email_notification_body, email_setting_id, email_setting_name, last_log_by) 
        VALUES(p_notification_setting_id, p_email_notification_subject, p_email_notification_body, p_email_setting_id, p_email_setting_name, p_last_log_by);
    ELSE
        UPDATE notification_setting_email_template
        SET email_notification_subject = p_email_notification_subject,
        	email_notification_body = p_email_notification_body,
        	email_setting_id = p_email_setting_id,
        	email_setting_name = p_email_setting_name,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;
    END IF;

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
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateLastConnection` (IN `p_user_account_id` INT, IN `p_session_token` VARCHAR(255))   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    
    UPDATE user_account
    SET session_token = p_session_token, 
        last_connection_date = NOW()
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

DROP PROCEDURE IF EXISTS `updateMultipleLoginSessionsStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateMultipleLoginSessionsStatus` (IN `p_user_account_id` INT, IN `p_multiple_session` VARCHAR(255), IN `p_last_log_by` INT)   BEGIN
    UPDATE user_account
    SET multiple_session = p_multiple_session,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `updateNotificationChannel`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateNotificationChannel` (IN `p_notification_setting_id` INT, IN `p_notification_channel` VARCHAR(10), IN `p_notification_channel_value` INT(1), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_notification_channel = 'system' THEN
        UPDATE notification_setting
        SET system_notification = p_notification_channel_value,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;
    ELSEIF p_notification_channel = 'email' THEN
        UPDATE notification_setting
        SET email_notification = p_notification_channel_value,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;
    ELSE
        UPDATE notification_setting
        SET sms_notification = p_notification_channel_value,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;
    END IF;

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

DROP PROCEDURE IF EXISTS `updateProfilePicture`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProfilePicture` (IN `p_user_account_id` INT, IN `p_profile_picture` VARCHAR(500), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET profile_picture = p_profile_picture,
        last_log_by = p_last_log_by
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

DROP PROCEDURE IF EXISTS `updateSecuritySetting`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateSecuritySetting` (IN `p_security_setting_id` INT, IN `p_value` VARCHAR(2000), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE security_setting
    SET value = p_value,
        last_log_by = p_last_log_by
    WHERE security_setting_id = p_security_setting_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateSMSNotificationTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateSMSNotificationTemplate` (IN `p_notification_setting_id` INT, IN `p_sms_notification_message` VARCHAR(500), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_notification_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM notification_setting_sms_template WHERE notification_setting_id = p_notification_setting_id) THEN
        INSERT INTO notification_setting_sms_template (notification_setting_id, sms_notification_message, last_log_by) 
        VALUES(p_notification_setting_id, p_sms_notification_message, p_last_log_by);
    ELSE
        UPDATE notification_setting_sms_template
        SET sms_notification_message = p_sms_notification_message,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateSystemNotificationTemplate`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateSystemNotificationTemplate` (IN `p_notification_setting_id` INT, IN `p_system_notification_title` VARCHAR(200), IN `p_system_notification_message` VARCHAR(200), IN `p_last_log_by` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_notification_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM notification_setting_system_template WHERE notification_setting_id = p_notification_setting_id) THEN
        INSERT INTO notification_setting_system_template (notification_setting_id, system_notification_title, system_notification_message, last_log_by) 
        VALUES(p_notification_setting_id, p_system_notification_title, p_system_notification_message, p_last_log_by);
    ELSE
        UPDATE notification_setting_system_template
        SET system_notification_title = p_system_notification_title,
        	system_notification_message = p_system_notification_message,
            last_log_by = p_last_log_by
        WHERE notification_setting_id = p_notification_setting_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateTwoFactorAuthenticationStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateTwoFactorAuthenticationStatus` (IN `p_user_account_id` INT, IN `p_two_factor_auth` VARCHAR(255), IN `p_last_log_by` INT)   BEGIN
    UPDATE user_account
    SET two_factor_auth = p_two_factor_auth,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountEmailAddress`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountEmailAddress` (IN `p_user_account_id` INT, IN `p_email` VARCHAR(255), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET email = p_email,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountFullName`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountFullName` (IN `p_user_account_id` INT, IN `p_file_as` VARCHAR(300), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET file_as = p_file_as,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountLock`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountLock` (IN `p_user_account_id` INT, IN `p_locked` VARCHAR(255), IN `p_account_lock_duration` VARCHAR(255), IN `p_last_log_by` INT)   BEGIN
	UPDATE user_account 
    SET locked = p_locked, account_lock_duration = p_account_lock_duration 
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountPassword`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountPassword` (IN `p_user_account_id` INT, IN `p_password` VARCHAR(255), IN `p_password_expiry_date` VARCHAR(255), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET password = p_password,
        password_expiry_date = p_password_expiry_date,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountPhone`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountPhone` (IN `p_user_account_id` INT, IN `p_phone` VARCHAR(50), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET phone = p_phone,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountStatus`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountStatus` (IN `p_user_account_id` INT, IN `p_active` VARCHAR(255), IN `p_last_log_by` INT)   BEGIN
    UPDATE user_account
    SET active = p_active,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;
END$$

DROP PROCEDURE IF EXISTS `updateUserAccountUsername`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `updateUserAccountUsername` (IN `p_user_account_id` INT, IN `p_username` VARCHAR(100), IN `p_last_log_by` INT)   BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET username = p_username,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;

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
-- Table structure for table `address_type`
--

DROP TABLE IF EXISTS `address_type`;
CREATE TABLE `address_type` (
  `address_type_id` int(10) UNSIGNED NOT NULL,
  `address_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `address_type`
--

INSERT INTO `address_type` (`address_type_id`, `address_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'Home Address', '2024-11-26 17:03:35', 1),
(2, 'Billing Address', '2024-11-26 17:03:35', 1),
(3, 'Mailing Address', '2024-11-26 17:03:35', 1),
(4, 'Shipping Address', '2024-11-26 17:03:35', 1),
(5, 'Work Address', '2024-11-26 17:03:35', 1);

--
-- Triggers `address_type`
--
DROP TRIGGER IF EXISTS `address_type_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `address_type_trigger_insert` AFTER INSERT ON `address_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Address type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('address_type', NEW.address_type_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `address_type_trigger_update`;
DELIMITER $$
CREATE TRIGGER `address_type_trigger_update` AFTER UPDATE ON `address_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Address type changed.<br/><br/>';

    IF NEW.address_type_name <> OLD.address_type_name THEN
        SET audit_log = CONCAT(audit_log, "Address Type Name: ", OLD.address_type_name, " -> ", NEW.address_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Address type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('address_type', NEW.address_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
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
(1, 'Settings', 'Centralized management hub for comprehensive organizational oversight and control', '../settings/app-module/image/logo/1/Pboex.png', 1, 'App Module', 100, '2024-11-25 15:12:14', 1),
(2, 'Employee', 'Centralize employee information', '../settings/app-module/image/logo/2/Jiwn.png', 24, 'Employee', 5, '2024-11-25 15:12:14', 2);

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
(1, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-01 18:50:30 -> 2024-11-03 19:24:29<br/>', 1, '2024-11-03 19:24:29', '2024-11-03 19:24:29'),
(2, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-03 19:24:29 -> 2024-11-04 08:47:35<br/>', 1, '2024-11-04 08:47:35', '2024-11-04 08:47:35'),
(3, 'subscription_tier', 5, 'Subscription tier created.', 2, '2024-11-04 12:32:25', '2024-11-04 12:32:25'),
(4, 'subscription_tier', 5, 'Subscription tier changed.<br/><br/>Subscription Tier Name: asd -> 123123<br/>Subscription Tier Description: asd -> asd123123<br/>Order Sequence: 12 -> 127<br/>', 2, '2024-11-04 12:32:37', '2024-11-04 12:32:37'),
(5, 'subscription_tier', 6, 'Subscription tier created.', 2, '2024-11-04 12:59:21', '2024-11-04 12:59:21'),
(6, 'subscription_tier', 6, 'Subscription tier created.', 2, '2024-11-04 13:43:02', '2024-11-04 13:43:02'),
(7, 'subscription_tier', 6, 'Subscription tier created.', 2, '2024-11-04 13:43:23', '2024-11-04 13:43:23'),
(8, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-04 08:47:35 -> 2024-11-05 10:28:59<br/>', 1, '2024-11-05 10:28:59', '2024-11-05 10:28:59'),
(9, 'billing_cycle', 5, 'Billing cycle created.', 2, '2024-11-05 11:00:05', '2024-11-05 11:00:05'),
(10, 'billing_cycle', 5, 'Billing cycle changed.<br/><br/>Billing Cycle Name: test -> tests<br/>', 2, '2024-11-05 11:00:07', '2024-11-05 11:00:07'),
(11, 'billing_cycle', 5, 'Billing cycle changed.<br/><br/>Billing Cycle Name: tests -> testsss<br/>', 2, '2024-11-05 11:00:14', '2024-11-05 11:00:14'),
(12, 'billing_cycle', 6, 'Billing cycle created.', 2, '2024-11-05 11:00:19', '2024-11-05 11:00:19'),
(13, 'billing_cycle', 5, 'Billing cycle created.', 2, '2024-11-05 11:00:53', '2024-11-05 11:00:53'),
(14, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-05 10:28:59 -> 2024-11-06 13:02:30<br/>', 1, '2024-11-06 13:02:30', '2024-11-06 13:02:30'),
(15, 'subscriber', 1, 'Subscriber changed.<br/><br/>Subscriber Name: asd -> asdasdasd<br/>Company Name: asd -> asdasdas<br/>Phone: asd -> asddasd<br/>Email: asd@gmail.com -> asasdasdasdd@gmail.com<br/>Status: Active -> Inactive<br/>Subscription Tier: Accelerator -> Infinity<br/>Billing Cycle: Yearly -> Monthly<br/>', 2, '2024-11-06 16:15:59', '2024-11-06 16:15:59'),
(16, 'subscriber', 2, 'Subscriber created.', 2, '2024-11-06 16:19:20', '2024-11-06 16:19:20'),
(17, 'subscriber', 2, 'Subscriber changed.<br/><br/>Subscriber Name: asd -> asdasdasd<br/>Company Name: dasda -> dasdaasdasd<br/>Phone: asda -> asdaasdasd<br/>Email: sdasd@gmail.com -> sdasdasdasdasd@gmail.com<br/>Status: Active -> Inactive<br/>Subscription Tier: LaunchPad -> Infinity<br/>Billing Cycle: Monthly -> Yearly<br/>', 2, '2024-11-06 16:22:39', '2024-11-06 16:22:39'),
(18, 'subscriber', 1, 'Subscriber created.', 2, '2024-11-06 16:29:22', '2024-11-06 16:29:22'),
(19, 'subscriber', 2, 'Subscriber created.', 2, '2024-11-06 16:29:22', '2024-11-06 16:29:22'),
(20, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-06 13:02:30 -> 2024-11-07 08:44:17<br/>', 1, '2024-11-07 08:44:17', '2024-11-07 08:44:17'),
(21, 'subscriber', 1, 'Subscriber changed.<br/><br/>Status: Inactive -> Active<br/>Subscription Tier: Infinity -> Accelerator<br/>', 2, '2024-11-07 10:18:20', '2024-11-07 10:18:20'),
(22, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-08 08:29:37 -> 2024-11-08 08:29:58<br/>', 1, '2024-11-08 08:29:58', '2024-11-08 08:29:58'),
(23, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-08 08:29:58 -> 2024-11-08 08:30:17<br/>', 1, '2024-11-08 08:30:17', '2024-11-08 08:30:17'),
(24, 'user_account', 3, 'User account created.', 2, '2024-11-08 10:06:59', '2024-11-08 10:06:59'),
(25, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-08 08:30:17 -> 2024-11-09 17:09:11<br/>', 1, '2024-11-09 17:09:11', '2024-11-09 17:09:11'),
(26, 'user_account', 2, 'User account changed.<br/><br/>File As: Administrator -> Administrators<br/>', 2, '2024-11-09 20:05:29', '2024-11-09 20:05:29'),
(27, 'user_account', 2, 'User account changed.<br/><br/>File As: Administrators -> Administrator<br/>', 2, '2024-11-09 20:05:34', '2024-11-09 20:05:34'),
(28, 'user_account', 2, 'User account changed.<br/><br/>Username: ldagulto -> ldagultos<br/>', 2, '2024-11-09 20:06:28', '2024-11-09 20:06:28'),
(29, 'user_account', 2, 'User account changed.<br/><br/>Username: ldagultos -> ldagulto<br/>', 2, '2024-11-09 20:06:38', '2024-11-09 20:06:38'),
(30, 'user_account', 2, 'User account changed.<br/><br/>Email: lawrenceagulto.317s@gmail.com -> lawrenceagulto.317@gmail.com<br/>', 2, '2024-11-09 20:08:05', '2024-11-09 20:08:05'),
(31, 'user_account', 2, 'User account changed.<br/><br/>Phone: 09399108659 -> 093991086599<br/>', 2, '2024-11-09 20:16:25', '2024-11-09 20:16:25'),
(32, 'user_account', 2, 'User account changed.<br/><br/>Phone: 093991086599 -> 09399108659<br/>', 2, '2024-11-09 20:16:31', '2024-11-09 20:16:31'),
(33, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-09 17:09:11 -> 2024-11-11 17:57:48<br/>', 2, '2024-11-11 17:57:48', '2024-11-11 17:57:48'),
(34, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 17:57:48 -> 2024-11-11 17:58:14<br/>', 2, '2024-11-11 17:58:14', '2024-11-11 17:58:14'),
(35, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 17:58:14 -> 2024-11-11 18:01:52<br/>', 2, '2024-11-11 18:01:52', '2024-11-11 18:01:52'),
(36, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:01:52 -> 2024-11-11 18:08:01<br/>', 2, '2024-11-11 18:08:01', '2024-11-11 18:08:01'),
(37, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:08:01 -> 2024-11-11 18:10:47<br/>', 2, '2024-11-11 18:10:47', '2024-11-11 18:10:47'),
(38, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:10:47 -> 2024-11-11 18:13:44<br/>', 2, '2024-11-11 18:13:44', '2024-11-11 18:13:44'),
(39, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:13:44 -> 2024-11-11 18:14:42<br/>', 2, '2024-11-11 18:14:42', '2024-11-11 18:14:42'),
(40, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:14:42 -> 2024-11-11 18:16:07<br/>', 2, '2024-11-11 18:16:07', '2024-11-11 18:16:07'),
(41, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:16:07 -> 2024-11-11 18:19:47<br/>', 2, '2024-11-11 18:19:47', '2024-11-11 18:19:47'),
(42, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:19:47 -> 2024-11-11 18:20:47<br/>', 2, '2024-11-11 18:20:47', '2024-11-11 18:20:47'),
(43, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:20:47 -> 2024-11-11 18:21:55<br/>', 2, '2024-11-11 18:21:55', '2024-11-11 18:21:55'),
(44, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:21:55 -> 2024-11-11 18:34:39<br/>', 2, '2024-11-11 18:34:39', '2024-11-11 18:34:39'),
(45, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 18:34:39 -> 2024-11-11 20:07:41<br/>', 2, '2024-11-11 20:07:41', '2024-11-11 20:07:41'),
(46, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 20:07:41 -> 2024-11-11 20:11:39<br/>', 2, '2024-11-11 20:11:39', '2024-11-11 20:11:39'),
(47, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-11 20:11:39 -> 2024-11-12 14:17:42<br/>', 2, '2024-11-12 14:17:42', '2024-11-12 14:17:42'),
(48, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-12 14:17:42 -> 2024-11-13 09:03:49<br/>', 2, '2024-11-13 09:03:49', '2024-11-13 09:03:49'),
(49, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-13 09:03:49 -> 2024-11-13 11:24:08<br/>', 2, '2024-11-13 11:24:08', '2024-11-13 11:24:08'),
(50, 'user_account', 2, 'User account changed.<br/><br/>File As: Administrator -> Administrators<br/>', 2, '2024-11-13 11:51:43', '2024-11-13 11:51:43'),
(51, 'user_account', 2, 'User account changed.<br/><br/>File As: Administrators -> asd<br/>', 2, '2024-11-13 11:51:57', '2024-11-13 11:51:57'),
(52, 'user_account', 2, 'User account changed.<br/><br/>File As: asd -> asdasd<br/>', 2, '2024-11-13 11:52:18', '2024-11-13 11:52:18'),
(53, 'user_account', 2, 'User account changed.<br/><br/>File As: asdasd -> Administrator<br/>', 2, '2024-11-13 11:53:09', '2024-11-13 11:53:09'),
(54, 'user_account', 2, 'User account changed.<br/><br/>File As: Administrator -> Administrators<br/>', 2, '2024-11-13 11:53:38', '2024-11-13 11:53:38'),
(55, 'user_account', 2, 'User account changed.<br/><br/>File As: Administrators -> Administrator<br/>', 2, '2024-11-13 11:53:43', '2024-11-13 11:53:43'),
(56, 'user_account', 2, 'User account changed.<br/><br/>Username: ldagulto -> ldagultos<br/>', 2, '2024-11-13 11:56:33', '2024-11-13 11:56:33'),
(57, 'user_account', 2, 'User account changed.<br/><br/>Username: ldagultos -> cgmibot<br/>', 2, '2024-11-13 11:56:39', '2024-11-13 11:56:39'),
(58, 'user_account', 2, 'User account changed.<br/><br/>Username: cgmibot -> ldagulto<br/>', 2, '2024-11-13 11:56:47', '2024-11-13 11:56:47'),
(59, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-13 11:24:08 -> 2024-11-14 08:50:03<br/>', 2, '2024-11-14 08:50:03', '2024-11-14 08:50:03'),
(60, 'country', 1, 'Country created.', 2, '2024-11-14 14:32:11', '2024-11-14 14:32:11'),
(61, 'country', 1, 'Country changed.<br/><br/>Phone Code:  -> phoneCode<br/>', 2, '2024-11-14 14:32:41', '2024-11-14 14:32:41'),
(62, 'country', 2, 'Country created.', 2, '2024-11-14 14:33:11', '2024-11-14 14:33:11'),
(63, 'country', 3, 'Country created.', 2, '2024-11-14 14:33:16', '2024-11-14 14:33:16'),
(64, 'country', 3, 'Country changed.<br/><br/>Country Name: test -> test2<br/>Country Code: test -> test2<br/>Phone Code: test -> test2<br/>', 2, '2024-11-14 14:33:21', '2024-11-14 14:33:21'),
(65, 'country', 1, 'Country created.', 2, '2024-11-14 14:35:47', '2024-11-14 14:35:47'),
(66, 'country', 2, 'Country created.', 2, '2024-11-14 14:35:47', '2024-11-14 14:35:47'),
(67, 'country', 4, 'Country created.', 2, '2024-11-14 14:36:45', '2024-11-14 14:36:45'),
(68, 'state', 1, 'State changed.<br/><br/>State Name: test2 -> test22<br/>', 2, '2024-11-14 15:52:52', '2024-11-14 15:52:52'),
(69, 'state', 2, 'State created.', 2, '2024-11-14 15:53:13', '2024-11-14 15:53:13'),
(70, 'state', 3, 'State created.', 2, '2024-11-14 15:53:20', '2024-11-14 15:53:20'),
(71, 'country', 5, 'Country created.', 2, '2024-11-14 15:56:02', '2024-11-14 15:56:02'),
(72, 'country', 4, 'Country changed.<br/><br/>Country Name: Philippines -> Philippine<br/>', 2, '2024-11-14 15:56:13', '2024-11-14 15:56:13'),
(73, 'country', 4, 'Country changed.<br/><br/>Country Name: Philippine -> Philippines<br/>', 2, '2024-11-14 15:56:19', '2024-11-14 15:56:19'),
(74, 'state', 2, 'State changed.<br/><br/>Country: Philippines -> Japan<br/>', 2, '2024-11-14 15:56:30', '2024-11-14 15:56:30'),
(75, 'state', 4, 'State created.', 2, '2024-11-14 15:58:30', '2024-11-14 15:58:30'),
(76, 'city', 1, 'City created.', 2, '2024-11-14 16:39:35', '2024-11-14 16:39:35'),
(77, 'city', 1, 'City changed.<br/><br/>City Name: test -> test2<br/>', 2, '2024-11-14 16:39:41', '2024-11-14 16:39:41'),
(78, 'city', 2, 'City created.', 2, '2024-11-14 16:39:48', '2024-11-14 16:39:48'),
(79, 'city', 3, 'City created.', 2, '2024-11-14 16:39:53', '2024-11-14 16:39:53'),
(80, 'city', 1, 'City created.', 2, '2024-11-14 16:42:41', '2024-11-14 16:42:41'),
(81, 'city', 2, 'City created.', 2, '2024-11-14 16:42:41', '2024-11-14 16:42:41'),
(82, 'state', 4, 'State changed.<br/><br/>Country: Philippines -> Philippine<br/>', 2, '2024-11-14 16:44:34', '2024-11-14 16:44:34'),
(83, 'city', 1, 'City changed.<br/><br/>Country: Philippines -> Philippine<br/>', 2, '2024-11-14 16:44:34', '2024-11-14 16:44:34'),
(84, 'city', 2, 'City changed.<br/><br/>Country: Philippines -> Philippine<br/>', 2, '2024-11-14 16:44:34', '2024-11-14 16:44:34'),
(85, 'country', 4, 'Country changed.<br/><br/>Country Name: Philippines -> Philippine<br/>', 2, '2024-11-14 16:44:34', '2024-11-14 16:44:34'),
(86, 'city', 1, 'City changed.<br/><br/>State: Nueva Ecija -> Nueva Ecijas<br/>', 2, '2024-11-14 16:44:48', '2024-11-14 16:44:48'),
(87, 'city', 2, 'City changed.<br/><br/>State: Nueva Ecija -> Nueva Ecijas<br/>', 2, '2024-11-14 16:44:48', '2024-11-14 16:44:48'),
(88, 'state', 4, 'State changed.<br/><br/>State Name: Nueva Ecija -> Nueva Ecijas<br/>', 2, '2024-11-14 16:44:48', '2024-11-14 16:44:48'),
(89, 'state', 4, 'State changed.<br/><br/>Country: Philippine -> Philippines<br/>', 2, '2024-11-14 16:44:57', '2024-11-14 16:44:57'),
(90, 'city', 1, 'City changed.<br/><br/>Country: Philippine -> Philippines<br/>', 2, '2024-11-14 16:44:57', '2024-11-14 16:44:57'),
(91, 'city', 2, 'City changed.<br/><br/>Country: Philippine -> Philippines<br/>', 2, '2024-11-14 16:44:57', '2024-11-14 16:44:57'),
(92, 'country', 4, 'Country changed.<br/><br/>Country Name: Philippine -> Philippines<br/>', 2, '2024-11-14 16:44:57', '2024-11-14 16:44:57'),
(93, 'city', 1, 'City changed.<br/><br/>State: Nueva Ecijas -> Nueva Ecija<br/>', 2, '2024-11-14 16:44:59', '2024-11-14 16:44:59'),
(94, 'city', 2, 'City changed.<br/><br/>State: Nueva Ecijas -> Nueva Ecija<br/>', 2, '2024-11-14 16:44:59', '2024-11-14 16:44:59'),
(95, 'state', 4, 'State changed.<br/><br/>State Name: Nueva Ecijas -> Nueva Ecija<br/>', 2, '2024-11-14 16:44:59', '2024-11-14 16:44:59'),
(96, 'currency', 1, 'Currency created.', 2, '2024-11-14 17:06:35', '2024-11-14 17:06:35'),
(97, 'currency', 1, 'Currency changed.<br/><br/>Currency Name: test -> testasdasd<br/>Symbol: P -> Pasd<br/>Shorthand: asdasd -> asdasdasd<br/>', 2, '2024-11-14 17:06:41', '2024-11-14 17:06:41'),
(98, 'currency', 2, 'Currency created.', 2, '2024-11-14 17:06:49', '2024-11-14 17:06:49'),
(99, 'currency', 3, 'Currency created.', 2, '2024-11-14 17:06:55', '2024-11-14 17:06:55'),
(100, 'currency', 1, 'Currency created.', 2, '2024-11-14 17:08:08', '2024-11-14 17:08:08'),
(101, 'currency', 2, 'Currency created.', 2, '2024-11-14 17:08:08', '2024-11-14 17:08:08'),
(102, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-14 08:50:03 -> 2024-11-15 08:50:55<br/>', 2, '2024-11-15 08:50:55', '2024-11-15 08:50:55'),
(103, 'company', 1, 'Company created.', 2, '2024-11-15 15:43:37', '2024-11-15 15:43:37'),
(104, 'company', 1, 'Company changed.<br/><br/>Tax ID:  -> asdads<br/>', 2, '2024-11-15 15:59:40', '2024-11-15 15:59:40'),
(105, 'company', 2, 'Company created.', 2, '2024-11-15 16:06:23', '2024-11-15 16:06:23'),
(106, 'company', 2, 'Company changed.<br/><br/>Tax ID:  -> asdas<br/>', 2, '2024-11-15 16:06:47', '2024-11-15 16:06:47'),
(107, 'company', 1, 'Company changed.<br/><br/>Company Name: test -> testtest<br/>Address: test -> testteste<br/>City: test -> test2<br/>Tax ID: asdads -> asdadsteetet<br/>Currency: test -> testasdasd<br/>Phone: test -> testetet<br/>Telephone: test -> testetete<br/>Email: tes -> testete<br/>Website: tes -> testete<br/>', 2, '2024-11-15 16:13:17', '2024-11-15 16:13:17'),
(108, 'company', 1, 'Company changed.<br/><br/>Email: testete -> testete@gmail.com<br/>', 2, '2024-11-15 16:14:03', '2024-11-15 16:14:03'),
(109, 'company', 2, 'Company created.', 2, '2024-11-15 16:18:52', '2024-11-15 16:18:52'),
(110, 'file_type', 15, 'File type created.', 2, '2024-11-15 17:05:13', '2024-11-15 17:05:13'),
(111, 'file_type', 15, 'File type changed.<br/><br/>File Type Name: test -> testtest<br/>', 2, '2024-11-15 17:05:25', '2024-11-15 17:05:25'),
(112, 'file_type', 1, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(113, 'file_type', 2, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(114, 'file_type', 3, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(115, 'file_type', 4, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(116, 'file_type', 5, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(117, 'file_type', 6, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(118, 'file_type', 7, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(119, 'file_type', 8, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(120, 'file_type', 9, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(121, 'file_type', 10, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(122, 'file_type', 11, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(123, 'file_type', 12, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(124, 'file_type', 13, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(125, 'file_type', 14, 'File type created.', 1, '2024-11-15 17:06:25', '2024-11-15 17:06:25'),
(126, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-15 08:50:55 -> 2024-11-17 12:52:56<br/>', 2, '2024-11-17 12:52:56', '2024-11-17 12:52:56'),
(127, 'file_extension', 132, 'File extension created.', 1, '2024-11-17 14:48:56', '2024-11-17 14:48:56'),
(128, 'file_extension', 133, 'File extension created.', 2, '2024-11-17 14:58:22', '2024-11-17 14:58:22'),
(129, 'file_extension', 133, 'File extension changed.<br/><br/>File Extension Name: test -> test2<br/>File Extension: test -> test22<br/>File Type: Compressed -> Audio<br/>', 2, '2024-11-17 14:58:32', '2024-11-17 14:58:32'),
(130, 'file_extension', 1, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(131, 'file_extension', 2, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(132, 'file_extension', 3, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(133, 'file_extension', 4, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(134, 'file_extension', 5, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(135, 'file_extension', 6, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(136, 'file_extension', 7, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(137, 'file_extension', 11, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(138, 'file_extension', 12, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(139, 'file_extension', 13, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(140, 'file_extension', 14, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(141, 'file_extension', 15, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(142, 'file_extension', 16, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(143, 'file_extension', 20, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(144, 'file_extension', 21, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(145, 'file_extension', 22, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(146, 'file_extension', 25, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(147, 'file_extension', 26, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(148, 'file_extension', 27, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(149, 'file_extension', 28, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(150, 'file_extension', 29, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(151, 'file_extension', 30, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(152, 'file_extension', 31, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(153, 'file_extension', 35, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(154, 'file_extension', 36, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(155, 'file_extension', 37, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(156, 'file_extension', 38, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(157, 'file_extension', 39, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(158, 'file_extension', 40, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(159, 'file_extension', 41, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(160, 'file_extension', 43, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(161, 'file_extension', 44, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(162, 'file_extension', 45, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(163, 'file_extension', 46, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(164, 'file_extension', 47, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(165, 'file_extension', 48, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(166, 'file_extension', 49, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(167, 'file_extension', 50, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(168, 'file_extension', 51, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(169, 'file_extension', 53, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(170, 'file_extension', 54, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(171, 'file_extension', 55, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(172, 'file_extension', 57, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(173, 'file_extension', 58, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(174, 'file_extension', 59, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(175, 'file_extension', 60, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(176, 'file_extension', 61, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(177, 'file_extension', 62, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(178, 'file_extension', 63, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(179, 'file_extension', 64, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(180, 'file_extension', 65, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(181, 'file_extension', 70, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(182, 'file_extension', 71, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(183, 'file_extension', 72, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(184, 'file_extension', 73, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(185, 'file_extension', 74, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(186, 'file_extension', 75, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(187, 'file_extension', 76, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(188, 'file_extension', 77, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(189, 'file_extension', 78, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(190, 'file_extension', 79, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(191, 'file_extension', 80, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(192, 'file_extension', 81, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(193, 'file_extension', 82, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(194, 'file_extension', 83, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(195, 'file_extension', 84, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(196, 'file_extension', 86, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(197, 'file_extension', 87, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(198, 'file_extension', 88, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(199, 'file_extension', 89, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(200, 'file_extension', 90, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(201, 'file_extension', 91, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(202, 'file_extension', 95, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(203, 'file_extension', 96, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(204, 'file_extension', 97, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(205, 'file_extension', 98, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(206, 'file_extension', 99, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(207, 'file_extension', 100, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(208, 'file_extension', 101, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(209, 'file_extension', 102, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(210, 'file_extension', 103, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(211, 'file_extension', 104, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(212, 'file_extension', 105, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(213, 'file_extension', 106, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(214, 'file_extension', 109, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(215, 'file_extension', 110, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(216, 'file_extension', 111, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(217, 'file_extension', 112, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(218, 'file_extension', 113, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(219, 'file_extension', 114, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(220, 'file_extension', 115, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(221, 'file_extension', 116, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(222, 'file_extension', 117, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(223, 'file_extension', 118, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(224, 'file_extension', 119, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(225, 'file_extension', 120, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(226, 'file_extension', 125, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(227, 'file_extension', 126, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(228, 'file_extension', 127, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(229, 'file_extension', 128, 'File extension created.', 1, '2024-11-17 14:59:48', '2024-11-17 14:59:48'),
(230, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-17 12:52:56 -> 2024-11-18 12:07:42<br/>', 2, '2024-11-18 12:07:42', '2024-11-18 12:07:42'),
(231, 'upload_setting', 6, 'Upload setting created.', 2, '2024-11-18 17:00:10', '2024-11-18 17:00:10'),
(232, 'upload_setting', 6, 'Upload setting changed.<br/><br/>Upload Setting Name: asdasd -> asdasdasd<br/>Upload Setting Description: asdasd -> asdasdasdasdas<br/>Max File Size: 123 -> 123123123<br/>', 2, '2024-11-18 17:00:14', '2024-11-18 17:00:14'),
(233, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-18 12:07:42 -> 2024-11-20 13:24:32<br/>', 2, '2024-11-20 13:24:32', '2024-11-20 13:24:32'),
(234, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-20 13:24:32 -> 2024-11-21 14:08:58<br/>', 2, '2024-11-21 14:08:58', '2024-11-21 14:08:58'),
(235, 'security_setting', 6, 'Security setting changed.<br/><br/>Value: 5 -> 6<br/>', 2, '2024-11-22 09:17:27', '2024-11-22 09:17:27'),
(236, 'security_setting', 6, 'Security setting changed.<br/><br/>Value: 6 -> 5<br/>', 2, '2024-11-22 09:17:29', '2024-11-22 09:17:29'),
(237, 'security_setting', 3, 'Security setting changed.<br/><br/>Value: http://localhost/digify/password-reset.php?id= -> http://localhost/digify_v2/password-reset.php?id=<br/>', 2, '2024-11-22 09:18:16', '2024-11-22 09:18:16'),
(238, 'email_setting', 1, 'Email setting changed.<br/><br/>Email Setting Description: \r\nEmail setting for security emails. -> Email setting for security emails.<br/>SMTP Authentication: 1 -> 0<br/>Mail From Name: cgmi-noreply@christianmotors.ph -> 1<br/>Mail From Email: cgmi-noreply@christianmotors.ph -> 0<br/>', 2, '2024-11-22 12:20:27', '2024-11-22 12:20:27'),
(239, 'email_setting', 1, 'Email setting changed.<br/><br/>Email Setting Name: Security Email Setting -> Security Email Settings<br/>', 2, '2024-11-22 12:20:30', '2024-11-22 12:20:30'),
(240, 'email_setting', 1, 'Email setting changed.<br/><br/>Email Setting Name: Security Email Settings -> Security Email Setting<br/>', 2, '2024-11-22 12:20:32', '2024-11-22 12:20:32'),
(241, 'email_setting', 1, 'Email setting changed.<br/><br/>Email Setting Description: \r\nEmail setting for security emails. -> Email setting for security emails.<br/>Mail From Name: cgmi-noreply@christianmotors.ph -> cgmi-noreply@christianmotors.phs<br/>Mail From Email: cgmi-noreply@christianmotors.ph -> cgmi-noreply@christianmotors.phs<br/>', 2, '2024-11-22 12:22:30', '2024-11-22 12:22:30'),
(242, 'email_setting', 1, 'Email setting changed.<br/><br/>Mail From Name: cgmi-noreply@christianmotors.phs -> cgmi-noreply@christianmotors.ph<br/>Mail From Email: cgmi-noreply@christianmotors.phs -> cgmi-noreply@christianmotors.ph<br/>', 2, '2024-11-22 12:22:41', '2024-11-22 12:22:41'),
(243, 'email_setting', 2, 'Email setting created.', 2, '2024-11-22 12:27:01', '2024-11-22 12:27:01'),
(244, 'email_setting', 2, 'Email setting changed.<br/><br/>Email Setting Name: test -> testtest<br/>Email Setting Description: test -> testtest<br/>Host: test -> testtest<br/>Port: test -> testtest<br/>SMTP Authentication: 0 -> 1<br/>SMTP Auto TLS: 0 -> 1<br/>Mail Username: test -> testtest<br/>Mail Encryption: none -> ssl<br/>Mail From Name: test -> testtest<br/>Mail From Email: test -> testtest<br/>', 2, '2024-11-22 12:27:18', '2024-11-22 12:27:18'),
(245, 'email_setting', 1, 'Email setting created.', 2, '2024-11-22 12:28:32', '2024-11-22 12:28:32'),
(246, 'notification_setting', 4, 'Notification setting created.', 2, '2024-11-22 15:29:03', '2024-11-22 15:29:03'),
(247, 'notification_setting', 4, 'Notification setting changed.<br/><br/>Notification Setting Name: test -> test2<br/>Notification Setting Description: test -> test2<br/>', 2, '2024-11-23 16:21:56', '2024-11-23 16:21:56'),
(248, 'notification_setting', 4, 'Notification setting changed.<br/><br/>Email Notification: 0 -> 1<br/>', 2, '2024-11-23 17:58:27', '2024-11-23 17:58:27'),
(249, 'notification_setting', 4, 'Notification setting changed.<br/><br/>SMS Notification: 0 -> 1<br/>', 2, '2024-11-23 17:58:28', '2024-11-23 17:58:28'),
(250, 'notification_setting', 4, 'Notification setting changed.<br/><br/>Email Notification: 1 -> 0<br/>', 2, '2024-11-23 18:07:31', '2024-11-23 18:07:31'),
(251, 'notification_setting', 4, 'Notification setting changed.<br/><br/>Email Notification: 0 -> 1<br/>', 2, '2024-11-23 18:07:38', '2024-11-23 18:07:38'),
(252, 'notification_setting', 4, 'Notification setting changed.<br/><br/>Email Notification: 1 -> 0<br/>', 2, '2024-11-23 18:13:42', '2024-11-23 18:13:42'),
(253, 'notification_setting', 4, 'Notification setting changed.<br/><br/>Email Notification: 0 -> 1<br/>', 2, '2024-11-23 18:14:45', '2024-11-23 18:14:45'),
(254, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-21 14:08:58 -> 2024-11-24 13:26:06<br/>', 2, '2024-11-24 13:26:06', '2024-11-24 13:26:06'),
(255, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-24 13:26:06 -> 2024-11-24 17:44:11<br/>', 2, '2024-11-24 17:44:11', '2024-11-24 17:44:11'),
(256, 'notification_setting_sms_template', 4, 'SMS notification template created.', 2, '2024-11-24 19:35:16', '2024-11-24 19:35:16'),
(257, 'notification_setting_email_template', 4, 'Email notification template created.', 2, '2024-11-24 19:35:28', '2024-11-24 19:35:28'),
(258, 'notification_setting_system_template', 4, 'System notification template created.', 2, '2024-11-24 19:36:08', '2024-11-24 19:36:08'),
(259, 'notification_setting_email_template', 4, 'Email notification template changed.<br/><br/>Email Notification Body: asdasdasd -> asdasdasdasdasd<br/>', 2, '2024-11-24 19:39:06', '2024-11-24 19:39:06'),
(260, 'notification_setting_email_template', 4, 'Email notification template changed.<br/><br/>Email Notification Body: asdasdasdasdasd -> <p><em><span style=\"text-decoration: underline;\"><strong>asdasdasdasdasd</strong></span></em></p><br/>', 2, '2024-11-24 19:40:51', '2024-11-24 19:40:51'),
(261, 'notification_setting_email_template', 4, 'Email notification template changed.<br/><br/>Email Notification Body: <p><em><span style=\"text-decoration: underline;\"><strong>asdasdasdasdasd</strong></span></em></p> -> <p><em><span style=\"text-decoration: underline;\"><strong>asdasdasdasdasdasdasdasd</strong></span></em></p><br/>', 2, '2024-11-24 19:41:08', '2024-11-24 19:41:08'),
(262, 'notification_setting_email_template', 4, 'Email notification template changed.<br/><br/>Email Notification Body: <p><em><span style=\"text-decoration: underline;\"><strong>asdasdasdasdasdasdasdasd</strong></span></em></p> -> <p><em><span style=\"text-decoration: underline;\"><strong>aasdasdasd</strong></span></em></p><br/>', 2, '2024-11-24 19:41:20', '2024-11-24 19:41:20'),
(263, 'user_account', 2, 'User account changed.<br/><br/>Last Connection Date: 2024-11-24 17:44:11 -> 2024-11-25 11:44:20<br/>', 2, '2024-11-25 11:44:20', '2024-11-25 11:44:20'),
(264, 'bank', 46, 'Bank created.', 2, '2024-11-26 16:09:13', '2024-11-26 16:09:13'),
(265, 'bank', 46, 'Bank changed.<br/><br/>Bank Name: test -> testtest<br/>Bank Identifier Code: test -> testtest<br/>', 2, '2024-11-26 16:09:16', '2024-11-26 16:09:16'),
(266, 'bank', 46, 'Bank changed.<br/><br/>Bank Name: testtest -> testtesttest<br/>Bank Identifier Code: testtest -> testtesttest<br/>', 2, '2024-11-26 16:09:21', '2024-11-26 16:09:21'),
(267, 'bank', 1, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(268, 'bank', 2, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(269, 'bank', 3, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(270, 'bank', 4, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(271, 'bank', 5, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(272, 'bank', 6, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(273, 'bank', 7, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(274, 'bank', 8, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(275, 'bank', 9, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(276, 'bank', 10, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(277, 'bank', 11, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(278, 'bank', 12, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(279, 'bank', 13, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(280, 'bank', 14, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(281, 'bank', 15, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(282, 'bank', 16, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(283, 'bank', 17, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(284, 'bank', 18, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(285, 'bank', 19, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(286, 'bank', 20, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(287, 'bank', 21, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(288, 'bank', 22, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(289, 'bank', 23, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(290, 'bank', 24, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(291, 'bank', 25, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(292, 'bank', 26, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(293, 'bank', 27, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(294, 'bank', 28, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(295, 'bank', 29, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(296, 'bank', 30, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(297, 'bank', 31, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(298, 'bank', 32, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(299, 'bank', 33, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(300, 'bank', 34, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(301, 'bank', 35, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(302, 'bank', 36, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(303, 'bank', 37, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(304, 'bank', 38, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(305, 'bank', 39, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(306, 'bank', 40, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(307, 'bank', 41, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(308, 'bank', 42, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(309, 'bank', 43, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(310, 'bank', 44, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(311, 'bank', 45, 'Bank created.', 1, '2024-11-26 16:10:26', '2024-11-26 16:10:26'),
(312, 'bank_account_type', 11, 'Bank account type created.', 2, '2024-11-26 16:25:39', '2024-11-26 16:25:39'),
(313, 'bank_account_type', 11, 'Bank account type changed.<br/><br/>Bank Account Type Name: test -> testtest<br/>', 2, '2024-11-26 16:25:42', '2024-11-26 16:25:42'),
(314, 'bank_account_type', 1, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(315, 'bank_account_type', 2, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(316, 'bank_account_type', 3, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(317, 'bank_account_type', 4, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(318, 'bank_account_type', 5, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(319, 'bank_account_type', 6, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(320, 'bank_account_type', 7, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(321, 'bank_account_type', 8, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(322, 'bank_account_type', 9, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(323, 'bank_account_type', 10, 'Bank account type created.', 1, '2024-11-26 16:26:11', '2024-11-26 16:26:11'),
(324, 'address_type', 6, 'Address type created.', 2, '2024-11-26 17:05:59', '2024-11-26 17:05:59'),
(325, 'address_type', 6, 'Address type changed.<br/><br/>Address Type Name: test -> testtest<br/>', 2, '2024-11-26 17:06:02', '2024-11-26 17:06:02'),
(326, 'address_type', 1, 'Address type created.', 1, '2024-11-26 17:06:56', '2024-11-26 17:06:56'),
(327, 'address_type', 2, 'Address type created.', 1, '2024-11-26 17:06:56', '2024-11-26 17:06:56'),
(328, 'address_type', 3, 'Address type created.', 1, '2024-11-26 17:06:56', '2024-11-26 17:06:56'),
(329, 'address_type', 4, 'Address type created.', 1, '2024-11-26 17:06:56', '2024-11-26 17:06:56'),
(330, 'address_type', 5, 'Address type created.', 1, '2024-11-26 17:06:56', '2024-11-26 17:06:56'),
(331, 'contact_information_type', 3, 'Contact information type created.', 2, '2024-11-26 17:22:39', '2024-11-26 17:22:39'),
(332, 'contact_information_type', 3, 'Contact information type changed.<br/><br/>Contact Information Type Name: test -> testtest<br/>', 2, '2024-11-26 17:22:45', '2024-11-26 17:22:45'),
(333, 'contact_information_type', 1, 'Contact information type created.', 1, '2024-11-26 17:23:08', '2024-11-26 17:23:08'),
(334, 'contact_information_type', 2, 'Contact information type created.', 1, '2024-11-26 17:23:08', '2024-11-26 17:23:08'),
(335, 'language', 152, 'Language created.', 2, '2024-11-27 11:25:00', '2024-11-27 11:25:00'),
(336, 'language', 152, 'Language changed.<br/><br/>Language Name: asdasd -> asdasdasdasd<br/>', 2, '2024-11-27 11:25:07', '2024-11-27 11:25:07'),
(337, 'language', 1, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(338, 'language', 2, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(339, 'language', 3, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(340, 'language', 4, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(341, 'language', 5, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(342, 'language', 6, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(343, 'language', 7, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(344, 'language', 8, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(345, 'language', 9, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(346, 'language', 10, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(347, 'language', 11, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(348, 'language', 12, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(349, 'language', 13, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(350, 'language', 14, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(351, 'language', 15, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(352, 'language', 16, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(353, 'language', 17, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(354, 'language', 18, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(355, 'language', 19, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(356, 'language', 20, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(357, 'language', 21, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(358, 'language', 22, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(359, 'language', 23, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(360, 'language', 24, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(361, 'language', 25, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(362, 'language', 26, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(363, 'language', 27, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(364, 'language', 28, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(365, 'language', 29, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(366, 'language', 30, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(367, 'language', 31, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(368, 'language', 32, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(369, 'language', 33, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(370, 'language', 34, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(371, 'language', 35, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(372, 'language', 36, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(373, 'language', 37, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(374, 'language', 38, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(375, 'language', 39, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(376, 'language', 40, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(377, 'language', 41, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(378, 'language', 42, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(379, 'language', 43, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(380, 'language', 44, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(381, 'language', 45, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(382, 'language', 46, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(383, 'language', 47, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(384, 'language', 48, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(385, 'language', 49, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(386, 'language', 50, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(387, 'language', 51, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(388, 'language', 52, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(389, 'language', 53, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(390, 'language', 54, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(391, 'language', 55, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(392, 'language', 56, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(393, 'language', 57, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(394, 'language', 58, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(395, 'language', 59, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(396, 'language', 60, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(397, 'language', 61, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(398, 'language', 62, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(399, 'language', 63, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(400, 'language', 64, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(401, 'language', 65, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(402, 'language', 66, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(403, 'language', 67, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(404, 'language', 68, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(405, 'language', 69, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(406, 'language', 70, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(407, 'language', 71, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(408, 'language', 72, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(409, 'language', 73, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(410, 'language', 74, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(411, 'language', 75, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(412, 'language', 76, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(413, 'language', 77, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(414, 'language', 78, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(415, 'language', 79, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(416, 'language', 80, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(417, 'language', 81, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(418, 'language', 82, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(419, 'language', 83, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(420, 'language', 84, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(421, 'language', 85, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(422, 'language', 86, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(423, 'language', 87, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(424, 'language', 88, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15');
INSERT INTO `audit_log` (`audit_log_id`, `table_name`, `reference_id`, `log`, `changed_by`, `changed_at`, `created_date`) VALUES
(425, 'language', 89, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(426, 'language', 90, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(427, 'language', 91, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(428, 'language', 92, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(429, 'language', 93, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(430, 'language', 94, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(431, 'language', 95, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(432, 'language', 96, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(433, 'language', 97, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(434, 'language', 98, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(435, 'language', 99, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(436, 'language', 100, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(437, 'language', 101, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(438, 'language', 102, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(439, 'language', 103, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(440, 'language', 104, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(441, 'language', 105, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(442, 'language', 106, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(443, 'language', 107, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(444, 'language', 108, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(445, 'language', 109, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(446, 'language', 110, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(447, 'language', 111, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(448, 'language', 112, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(449, 'language', 113, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(450, 'language', 114, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(451, 'language', 115, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(452, 'language', 116, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(453, 'language', 117, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(454, 'language', 118, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(455, 'language', 119, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(456, 'language', 120, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(457, 'language', 121, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(458, 'language', 122, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(459, 'language', 123, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(460, 'language', 124, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(461, 'language', 125, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(462, 'language', 126, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(463, 'language', 127, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(464, 'language', 128, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(465, 'language', 129, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(466, 'language', 130, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(467, 'language', 131, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(468, 'language', 132, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(469, 'language', 133, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(470, 'language', 134, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(471, 'language', 135, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(472, 'language', 136, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(473, 'language', 137, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(474, 'language', 138, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(475, 'language', 139, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(476, 'language', 140, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(477, 'language', 141, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(478, 'language', 142, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(479, 'language', 143, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(480, 'language', 144, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(481, 'language', 145, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(482, 'language', 146, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(483, 'language', 147, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(484, 'language', 148, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(485, 'language', 149, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(486, 'language', 150, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(487, 'language', 151, 'Language created.', 1, '2024-11-27 11:26:15', '2024-11-27 11:26:15'),
(488, 'language_proficiency', 7, 'Language proficiency created.', 2, '2024-11-27 11:53:00', '2024-11-27 11:53:00'),
(489, 'language_proficiency', 7, 'Language proficiency changed.<br/><br/>Language Proficiency Name: test -> testtest<br/>Language Proficiency Description: test -> testtest<br/>', 2, '2024-11-27 11:53:04', '2024-11-27 11:53:04'),
(490, 'language_proficiency', 1, 'Language proficiency created.', 1, '2024-11-27 11:53:27', '2024-11-27 11:53:27'),
(491, 'language_proficiency', 2, 'Language proficiency created.', 1, '2024-11-27 11:53:27', '2024-11-27 11:53:27'),
(492, 'language_proficiency', 3, 'Language proficiency created.', 1, '2024-11-27 11:53:27', '2024-11-27 11:53:27'),
(493, 'language_proficiency', 4, 'Language proficiency created.', 1, '2024-11-27 11:53:27', '2024-11-27 11:53:27'),
(494, 'language_proficiency', 5, 'Language proficiency created.', 1, '2024-11-27 11:53:27', '2024-11-27 11:53:27'),
(495, 'language_proficiency', 6, 'Language proficiency created.', 1, '2024-11-27 11:53:27', '2024-11-27 11:53:27'),
(496, 'blood_type', 9, 'Blood type created.', 2, '2024-11-27 12:07:53', '2024-11-27 12:07:53'),
(497, 'blood_type', 9, 'Blood type changed.<br/><br/>Blood Type Name: asasdasdasdasdasd -> asasdasdasdasdasdasdasd<br/>', 2, '2024-11-27 12:08:22', '2024-11-27 12:08:22'),
(498, 'blood_type', 1, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(499, 'blood_type', 2, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(500, 'blood_type', 3, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(501, 'blood_type', 4, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(502, 'blood_type', 5, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(503, 'blood_type', 6, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(504, 'blood_type', 7, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(505, 'blood_type', 8, 'Blood type created.', 1, '2024-11-27 12:08:46', '2024-11-27 12:08:46'),
(506, 'civil_status', 6, 'Civil status created.', 2, '2024-11-27 12:27:13', '2024-11-27 12:27:13'),
(507, 'civil_status', 6, 'Civil status changed.<br/><br/>Civil Status Name: asdasd -> asdasdasdasdasd<br/>', 2, '2024-11-27 12:27:16', '2024-11-27 12:27:16'),
(508, 'civil_status', 1, 'Civil status created.', 1, '2024-11-27 12:27:37', '2024-11-27 12:27:37'),
(509, 'civil_status', 2, 'Civil status created.', 1, '2024-11-27 12:27:37', '2024-11-27 12:27:37'),
(510, 'civil_status', 3, 'Civil status created.', 1, '2024-11-27 12:27:37', '2024-11-27 12:27:37'),
(511, 'civil_status', 4, 'Civil status created.', 1, '2024-11-27 12:27:37', '2024-11-27 12:27:37'),
(512, 'civil_status', 5, 'Civil status created.', 1, '2024-11-27 12:27:37', '2024-11-27 12:27:37'),
(513, 'educational_stage', 10, 'Educational stage created.', 2, '2024-11-27 14:09:29', '2024-11-27 14:09:29'),
(514, 'educational_stage', 10, 'Educational stage changed.<br/><br/>Educational Stage Name: test -> testtest<br/>', 2, '2024-11-27 14:09:32', '2024-11-27 14:09:32'),
(515, 'educational_stage', 1, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(516, 'educational_stage', 2, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(517, 'educational_stage', 3, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(518, 'educational_stage', 4, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(519, 'educational_stage', 5, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(520, 'educational_stage', 6, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(521, 'educational_stage', 7, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(522, 'educational_stage', 8, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(523, 'educational_stage', 9, 'Educational stage created.', 1, '2024-11-27 14:12:02', '2024-11-27 14:12:02'),
(524, 'gender', 3, 'Gender created.', 2, '2024-11-27 14:24:12', '2024-11-27 14:24:12'),
(525, 'gender', 3, 'Gender changed.<br/><br/>Gender Name: asdasd -> asdasdasdasdasd<br/>', 2, '2024-11-27 14:24:17', '2024-11-27 14:24:17'),
(526, 'gender', 1, 'Gender created.', 1, '2024-11-27 14:24:43', '2024-11-27 14:24:43'),
(527, 'gender', 2, 'Gender created.', 1, '2024-11-27 14:24:43', '2024-11-27 14:24:43'),
(528, 'credential_type', 32, 'Credential type created.', 2, '2024-11-27 14:51:28', '2024-11-27 14:51:28'),
(529, 'credential_type', 32, 'Credential type changed.<br/><br/>Credential Type Name: asdasd -> aasdasdasdasd<br/>', 2, '2024-11-27 14:51:33', '2024-11-27 14:51:33'),
(530, 'credential_type', 1, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(531, 'credential_type', 2, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(532, 'credential_type', 3, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(533, 'credential_type', 4, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(534, 'credential_type', 5, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(535, 'credential_type', 6, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(536, 'credential_type', 7, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(537, 'credential_type', 8, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(538, 'credential_type', 9, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(539, 'credential_type', 10, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(540, 'credential_type', 11, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(541, 'credential_type', 12, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(542, 'credential_type', 13, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(543, 'credential_type', 14, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(544, 'credential_type', 15, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(545, 'credential_type', 16, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(546, 'credential_type', 17, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(547, 'credential_type', 18, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(548, 'credential_type', 19, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(549, 'credential_type', 20, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(550, 'credential_type', 21, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(551, 'credential_type', 22, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(552, 'credential_type', 23, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(553, 'credential_type', 24, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(554, 'credential_type', 25, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(555, 'credential_type', 26, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(556, 'credential_type', 27, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(557, 'credential_type', 28, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(558, 'credential_type', 29, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(559, 'credential_type', 30, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(560, 'credential_type', 31, 'Credential type created.', 1, '2024-11-27 14:51:56', '2024-11-27 14:51:56'),
(561, 'relationship', 19, 'Relationship created.', 2, '2024-11-27 15:02:04', '2024-11-27 15:02:04'),
(562, 'relationship', 19, 'Relationship changed.<br/><br/>Relationship Name: asd -> asdasdasd<br/>', 2, '2024-11-27 15:02:08', '2024-11-27 15:02:08'),
(563, 'relationship', 1, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(564, 'relationship', 2, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(565, 'relationship', 3, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(566, 'relationship', 4, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(567, 'relationship', 5, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(568, 'relationship', 6, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(569, 'relationship', 7, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(570, 'relationship', 8, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(571, 'relationship', 9, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(572, 'relationship', 10, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(573, 'relationship', 11, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(574, 'relationship', 12, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(575, 'relationship', 13, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(576, 'relationship', 14, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(577, 'relationship', 15, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(578, 'relationship', 16, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(579, 'relationship', 17, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(580, 'relationship', 18, 'Relationship created.', 1, '2024-11-27 15:02:31', '2024-11-27 15:02:31'),
(581, 'religion', 21, 'Religion created.', 2, '2024-11-27 15:17:26', '2024-11-27 15:17:26'),
(582, 'religion', 21, 'Religion changed.<br/><br/>Religion Name: asasdasd -> asasdasdasdasdasd<br/>', 2, '2024-11-27 15:17:30', '2024-11-27 15:17:30'),
(583, 'religion', 1, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(584, 'religion', 2, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(585, 'religion', 3, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(586, 'religion', 4, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(587, 'religion', 5, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(588, 'religion', 6, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(589, 'religion', 7, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(590, 'religion', 8, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(591, 'religion', 9, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(592, 'religion', 10, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(593, 'religion', 11, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(594, 'religion', 12, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(595, 'religion', 13, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(596, 'religion', 14, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(597, 'religion', 15, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(598, 'religion', 16, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(599, 'religion', 17, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(600, 'religion', 18, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(601, 'religion', 19, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(602, 'religion', 20, 'Religion created.', 1, '2024-11-27 15:17:59', '2024-11-27 15:17:59'),
(603, 'department', 1, 'Department created.', 2, '2024-11-27 16:50:57', '2024-11-27 16:50:57'),
(604, 'department', 1, 'Department changed.<br/><br/>Department Name: asdasd -> asdasdasdasdasd<br/>', 2, '2024-11-27 16:51:44', '2024-11-27 16:51:44'),
(605, 'department', 2, 'Department created.', 2, '2024-11-27 16:58:22', '2024-11-27 16:58:22'),
(606, 'department', 2, 'Department changed.<br/><br/>Department Name: asdasdasd -> asdasdasdasdasdasd<br/>', 2, '2024-11-27 17:03:47', '2024-11-27 17:03:47'),
(607, 'department', 2, 'Department changed.<br/><br/>Department Name: asdasdasdasdasdasd -> asdasdasdasdasdasdasdasdasd<br/>', 2, '2024-11-27 17:24:19', '2024-11-27 17:24:19'),
(608, 'department', 2, 'Department changed.<br/><br/>Department Name: asdasdasdasdasdasdasdasdasd -> asdasdasdasdasdasdasdasdasdasdasdasdasd<br/>', 2, '2024-11-27 17:24:47', '2024-11-27 17:24:47'),
(609, 'department', 2, 'Department changed.<br/><br/>Parent Department:  -> asdasdasdasdasd<br/>', 2, '2024-11-27 17:28:28', '2024-11-27 17:28:28');

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

DROP TABLE IF EXISTS `bank`;
CREATE TABLE `bank` (
  `bank_id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_identifier_code` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`bank_id`, `bank_name`, `bank_identifier_code`, `created_date`, `last_log_by`) VALUES
(1, 'Banco de Oro (BDO)', '010530667', '2024-11-26 15:52:20', 1),
(2, 'Metrobank', '010269996', '2024-11-26 15:52:20', 1),
(3, 'Land Bank of the Philippines', '010350025', '2024-11-26 15:52:20', 1),
(4, 'Bank of the Philippine Islands (BPI)', '010040018', '2024-11-26 15:52:20', 1),
(5, 'Philippine National Bank (PNB)', '010080010', '2024-11-26 15:52:20', 1),
(6, 'Security Bank', '010140015', '2024-11-26 15:52:20', 1),
(7, 'UnionBank of the Philippines', '010419995', '2024-11-26 15:52:20', 1),
(8, 'Development Bank of the Philippines (DBP)', '010590018', '2024-11-26 15:52:20', 1),
(9, 'EastWest Bank', '010620014', '2024-11-26 15:52:20', 1),
(10, 'China Banking Corporation (Chinabank)', '010100013', '2024-11-26 15:52:20', 1),
(11, 'RCBC (Rizal Commercial Banking Corporation)', '010280014', '2024-11-26 15:52:20', 1),
(12, 'Maybank Philippines', '010220016', '2024-11-26 15:52:20', 1),
(13, 'Bank of America', 'BOFAUS3N', '2024-11-26 15:52:20', 1),
(14, 'JPMorgan Chase', 'CHASUS33', '2024-11-26 15:52:20', 1),
(15, 'Wells Fargo', 'WFBIUS6W', '2024-11-26 15:52:20', 1),
(16, 'Citibank', 'CITIUS33', '2024-11-26 15:52:20', 1),
(17, 'U.S. Bank', 'USBKUS44', '2024-11-26 15:52:20', 1),
(18, 'Bank of New York Mellon', 'BKONYUS33', '2024-11-26 15:52:20', 1),
(19, 'State Street Corporation', 'SSTTUS33', '2024-11-26 15:52:20', 1),
(20, 'Goldman Sachs', 'GOLDUS33', '2024-11-26 15:52:20', 1),
(21, 'Morgan Stanley', 'MSNYUS33', '2024-11-26 15:52:20', 1),
(22, 'Capital One', 'COWNUS33', '2024-11-26 15:52:20', 1),
(23, 'PNC Financial Services Group', 'PNCCUS33', '2024-11-26 15:52:20', 1),
(24, 'Truist Financial Corporation', 'TRUIUS33', '2024-11-26 15:52:20', 1),
(25, 'Charles Schwab Corporation', 'SCHWUS33', '2024-11-26 15:52:20', 1),
(26, 'Ally Financial', 'ALLYUS33', '2024-11-26 15:52:20', 1),
(27, 'TD Bank', 'TDUSUS33', '2024-11-26 15:52:20', 1),
(28, 'Fifth Third Bank', 'FTBCUS3J', '2024-11-26 15:52:20', 1),
(29, 'KeyBank', 'KEYBUS33', '2024-11-26 15:52:20', 1),
(30, 'Huntington Bancshares', 'HBANUS33', '2024-11-26 15:52:20', 1),
(31, 'Regions Financial Corporation', 'RGNSUS33', '2024-11-26 15:52:20', 1),
(32, 'M&T Bank', 'MANTUS33', '2024-11-26 15:52:20', 1),
(33, 'SunTrust Banks', 'STBAUS33', '2024-11-26 15:52:20', 1),
(34, 'BB&T Corporation', 'BBTUS33', '2024-11-26 15:52:20', 1),
(35, 'Emirates NBD', 'EBILAEAD', '2024-11-26 15:52:20', 1),
(36, 'First Abu Dhabi Bank', 'NBADAEAAXXX', '2024-11-26 15:52:20', 1),
(37, 'Abu Dhabi Commercial Bank', 'ADCBAEAAXXX', '2024-11-26 15:52:20', 1),
(38, 'Dubai Islamic Bank', 'DIBAEAAXXX', '2024-11-26 15:52:20', 1),
(39, 'Mashreq Bank', 'BOMLAEAD', '2024-11-26 15:52:20', 1),
(40, 'Union National Bank', 'UNBAEAAXXX', '2024-11-26 15:52:20', 1),
(41, 'Rakbank', 'RAKAEAAXXX', '2024-11-26 15:52:20', 1),
(42, 'Commercial Bank of Dubai', 'CBDAEAAXXX', '2024-11-26 15:52:20', 1),
(43, 'Emirates Islamic Bank', 'EIILAEAD', '2024-11-26 15:52:20', 1),
(44, 'Ajman Bank', 'AJBLAEAD', '2024-11-26 15:52:20', 1),
(45, 'Sharjah Islamic Bank', 'SIBAEAAXXX', '2024-11-26 15:52:20', 1);

--
-- Triggers `bank`
--
DROP TRIGGER IF EXISTS `bank_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `bank_trigger_insert` AFTER INSERT ON `bank` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('bank', NEW.bank_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `bank_trigger_update`;
DELIMITER $$
CREATE TRIGGER `bank_trigger_update` AFTER UPDATE ON `bank` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank changed.<br/><br/>';

    IF NEW.bank_name <> OLD.bank_name THEN
        SET audit_log = CONCAT(audit_log, "Bank Name: ", OLD.bank_name, " -> ", NEW.bank_name, "<br/>");
    END IF;

    IF NEW.bank_identifier_code <> OLD.bank_identifier_code THEN
        SET audit_log = CONCAT(audit_log, "Bank Identifier Code: ", OLD.bank_identifier_code, " -> ", NEW.bank_identifier_code, "<br/>");
    END IF;
    
    IF audit_log <> 'Bank changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('bank', NEW.bank_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bank_account_type`
--

DROP TABLE IF EXISTS `bank_account_type`;
CREATE TABLE `bank_account_type` (
  `bank_account_type_id` int(10) UNSIGNED NOT NULL,
  `bank_account_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_account_type`
--

INSERT INTO `bank_account_type` (`bank_account_type_id`, `bank_account_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'Checking', '2024-11-26 16:23:13', 1),
(2, 'Savings', '2024-11-26 16:23:13', 1),
(3, 'Money Market', '2024-11-26 16:23:13', 1),
(4, 'Certificate of Deposit (CD)', '2024-11-26 16:23:13', 1),
(5, 'Individual Retirement Account (IRA)', '2024-11-26 16:23:13', 1),
(6, 'Business Checking', '2024-11-26 16:23:13', 1),
(7, 'Business Savings', '2024-11-26 16:23:13', 1),
(8, 'Business Money Market', '2024-11-26 16:23:13', 1),
(9, 'Business Certificate of Deposit (CD)', '2024-11-26 16:23:13', 1),
(10, 'Business Individual Retirement Account (IRA)', '2024-11-26 16:23:13', 1);

--
-- Triggers `bank_account_type`
--
DROP TRIGGER IF EXISTS `bank_account_type_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `bank_account_type_trigger_insert` AFTER INSERT ON `bank_account_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank account type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('bank_account_type', NEW.bank_account_type_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `bank_account_type_trigger_update`;
DELIMITER $$
CREATE TRIGGER `bank_account_type_trigger_update` AFTER UPDATE ON `bank_account_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank account type changed.<br/><br/>';

    IF NEW.bank_account_type_name <> OLD.bank_account_type_name THEN
        SET audit_log = CONCAT(audit_log, "Bank Account Type Name: ", OLD.bank_account_type_name, " -> ", NEW.bank_account_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Bank account type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('bank_account_type', NEW.bank_account_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `blood_type`
--

DROP TABLE IF EXISTS `blood_type`;
CREATE TABLE `blood_type` (
  `blood_type_id` int(10) UNSIGNED NOT NULL,
  `blood_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_type`
--

INSERT INTO `blood_type` (`blood_type_id`, `blood_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'A+', '2024-11-27 12:07:24', 1),
(2, 'A-', '2024-11-27 12:07:24', 1),
(3, 'B+', '2024-11-27 12:07:24', 1),
(4, 'B-', '2024-11-27 12:07:24', 1),
(5, 'AB+', '2024-11-27 12:07:24', 1),
(6, 'AB-', '2024-11-27 12:07:24', 1),
(7, 'O+', '2024-11-27 12:07:24', 1),
(8, 'O-', '2024-11-27 12:07:24', 1);

--
-- Triggers `blood_type`
--
DROP TRIGGER IF EXISTS `blood_type_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `blood_type_trigger_insert` AFTER INSERT ON `blood_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Blood type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('blood_type', NEW.blood_type_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `blood_type_trigger_update`;
DELIMITER $$
CREATE TRIGGER `blood_type_trigger_update` AFTER UPDATE ON `blood_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Blood type changed.<br/><br/>';

    IF NEW.blood_type_name <> OLD.blood_type_name THEN
        SET audit_log = CONCAT(audit_log, "Blood Type Name: ", OLD.blood_type_name, " -> ", NEW.blood_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Blood type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('blood_type', NEW.blood_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `city_id` int(10) UNSIGNED NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_id`, `city_name`, `state_id`, `state_name`, `country_id`, `country_name`, `created_date`, `last_log_by`) VALUES
(1, 'test2', 4, 'Nueva Ecija', 4, 'Philippines', '2024-11-14 16:39:35', 2),
(2, 'test', 4, 'Nueva Ecija', 4, 'Philippines', '2024-11-14 16:39:48', 2);

--
-- Triggers `city`
--
DROP TRIGGER IF EXISTS `city_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `city_trigger_insert` AFTER INSERT ON `city` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'City created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('city', NEW.city_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `city_trigger_update`;
DELIMITER $$
CREATE TRIGGER `city_trigger_update` AFTER UPDATE ON `city` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'City changed.<br/><br/>';

    IF NEW.city_name <> OLD.city_name THEN
        SET audit_log = CONCAT(audit_log, "City Name: ", OLD.city_name, " -> ", NEW.city_name, "<br/>");
    END IF;

    IF NEW.state_name <> OLD.state_name THEN
        SET audit_log = CONCAT(audit_log, "State: ", OLD.state_name, " -> ", NEW.state_name, "<br/>");
    END IF;

    IF NEW.country_name <> OLD.country_name THEN
        SET audit_log = CONCAT(audit_log, "Country: ", OLD.country_name, " -> ", NEW.country_name, "<br/>");
    END IF;
    
    IF audit_log <> 'City changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('city', NEW.city_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `civil_status`
--

DROP TABLE IF EXISTS `civil_status`;
CREATE TABLE `civil_status` (
  `civil_status_id` int(10) UNSIGNED NOT NULL,
  `civil_status_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `civil_status`
--

INSERT INTO `civil_status` (`civil_status_id`, `civil_status_name`, `created_date`, `last_log_by`) VALUES
(1, 'Single', '2024-11-27 12:26:56', 1),
(2, 'Married', '2024-11-27 12:26:56', 1),
(3, 'Divorced', '2024-11-27 12:26:56', 1),
(4, 'Widowed', '2024-11-27 12:26:56', 1),
(5, 'Separated', '2024-11-27 12:26:56', 1);

--
-- Triggers `civil_status`
--
DROP TRIGGER IF EXISTS `civil_status_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `civil_status_trigger_insert` AFTER INSERT ON `civil_status` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Civil status created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('civil_status', NEW.civil_status_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `civil_status_trigger_update`;
DELIMITER $$
CREATE TRIGGER `civil_status_trigger_update` AFTER UPDATE ON `civil_status` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Civil status changed.<br/><br/>';

    IF NEW.civil_status_name <> OLD.civil_status_name THEN
        SET audit_log = CONCAT(audit_log, "Civil Status Name: ", OLD.civil_status_name, " -> ", NEW.civil_status_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Civil status changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('civil_status', NEW.civil_status_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `company_id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_logo` varchar(500) DEFAULT NULL,
  `address` varchar(1000) DEFAULT NULL,
  `city_id` int(10) UNSIGNED NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `state_id` int(10) UNSIGNED NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `currency_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `company_name`, `company_logo`, `address`, `city_id`, `city_name`, `state_id`, `state_name`, `country_id`, `country_name`, `tax_id`, `currency_id`, `currency_name`, `phone`, `telephone`, `email`, `website`, `created_date`, `last_log_by`) VALUES
(2, 'test', '../settings/company/image/logo/2/yOjC.png', 'test', 2, 'test', 4, 'Nueva Ecija', 4, 'Philippines', 'asdas', 2, 'test', 'test', 'test', 'test', 'test', '2024-11-15 16:06:23', 2);

--
-- Triggers `company`
--
DROP TRIGGER IF EXISTS `company_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `company_trigger_insert` AFTER INSERT ON `company` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Company created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('company', NEW.company_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `company_trigger_update`;
DELIMITER $$
CREATE TRIGGER `company_trigger_update` AFTER UPDATE ON `company` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Company changed.<br/><br/>';

    IF NEW.company_name <> OLD.company_name THEN
        SET audit_log = CONCAT(audit_log, "Company Name: ", OLD.company_name, " -> ", NEW.company_name, "<br/>");
    END IF;

    IF NEW.address <> OLD.address THEN
        SET audit_log = CONCAT(audit_log, "Address: ", OLD.address, " -> ", NEW.address, "<br/>");
    END IF;

    IF NEW.city_name <> OLD.city_name THEN
        SET audit_log = CONCAT(audit_log, "City: ", OLD.city_name, " -> ", NEW.city_name, "<br/>");
    END IF;

    IF NEW.state_name <> OLD.state_name THEN
        SET audit_log = CONCAT(audit_log, "State: ", OLD.state_name, " -> ", NEW.state_name, "<br/>");
    END IF;

    IF NEW.country_name <> OLD.country_name THEN
        SET audit_log = CONCAT(audit_log, "Country: ", OLD.country_name, " -> ", NEW.country_name, "<br/>");
    END IF;

    IF NEW.tax_id <> OLD.tax_id THEN
        SET audit_log = CONCAT(audit_log, "Tax ID: ", OLD.tax_id, " -> ", NEW.tax_id, "<br/>");
    END IF;

    IF NEW.currency_name <> OLD.currency_name THEN
        SET audit_log = CONCAT(audit_log, "Currency: ", OLD.currency_name, " -> ", NEW.currency_name, "<br/>");
    END IF;

    IF NEW.phone <> OLD.phone THEN
        SET audit_log = CONCAT(audit_log, "Phone: ", OLD.phone, " -> ", NEW.phone, "<br/>");
    END IF;

    IF NEW.telephone <> OLD.telephone THEN
        SET audit_log = CONCAT(audit_log, "Telephone: ", OLD.telephone, " -> ", NEW.telephone, "<br/>");
    END IF;

    IF NEW.email <> OLD.email THEN
        SET audit_log = CONCAT(audit_log, "Email: ", OLD.email, " -> ", NEW.email, "<br/>");
    END IF;

    IF NEW.website <> OLD.website THEN
        SET audit_log = CONCAT(audit_log, "Website: ", OLD.website, " -> ", NEW.website, "<br/>");
    END IF;
    
    IF audit_log <> 'Company changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('company', NEW.company_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `contact_information_type`
--

DROP TABLE IF EXISTS `contact_information_type`;
CREATE TABLE `contact_information_type` (
  `contact_information_type_id` int(10) UNSIGNED NOT NULL,
  `contact_information_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_information_type`
--

INSERT INTO `contact_information_type` (`contact_information_type_id`, `contact_information_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'Personal', '2024-11-26 17:22:04', 1),
(2, 'Work', '2024-11-26 17:22:04', 1);

--
-- Triggers `contact_information_type`
--
DROP TRIGGER IF EXISTS `contact_information_type_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `contact_information_type_trigger_insert` AFTER INSERT ON `contact_information_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Contact information type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('contact_information_type', NEW.contact_information_type_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `contact_information_type_trigger_update`;
DELIMITER $$
CREATE TRIGGER `contact_information_type_trigger_update` AFTER UPDATE ON `contact_information_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Contact information type changed.<br/><br/>';

    IF NEW.contact_information_type_name <> OLD.contact_information_type_name THEN
        SET audit_log = CONCAT(audit_log, "Contact Information Type Name: ", OLD.contact_information_type_name, " -> ", NEW.contact_information_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Contact information type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('contact_information_type', NEW.contact_information_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `country_id` int(10) UNSIGNED NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `phone_code` varchar(10) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `country_name`, `country_code`, `phone_code`, `created_date`, `last_log_by`) VALUES
(4, 'Philippines', 'PH', '+63', '2024-11-14 14:36:45', 2),
(5, 'Japan', 'JP', '+43', '2024-11-14 15:56:02', 2);

--
-- Triggers `country`
--
DROP TRIGGER IF EXISTS `country_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `country_trigger_insert` AFTER INSERT ON `country` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Country created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('country', NEW.country_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `country_trigger_update`;
DELIMITER $$
CREATE TRIGGER `country_trigger_update` AFTER UPDATE ON `country` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Country changed.<br/><br/>';

    IF NEW.country_name <> OLD.country_name THEN
        SET audit_log = CONCAT(audit_log, "Country Name: ", OLD.country_name, " -> ", NEW.country_name, "<br/>");
    END IF;

    IF NEW.country_code <> OLD.country_code THEN
        SET audit_log = CONCAT(audit_log, "Country Code: ", OLD.country_code, " -> ", NEW.country_code, "<br/>");
    END IF;

    IF NEW.phone_code <> OLD.phone_code THEN
        SET audit_log = CONCAT(audit_log, "Phone Code: ", OLD.phone_code, " -> ", NEW.phone_code, "<br/>");
    END IF;
    
    IF audit_log <> 'Country changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('country', NEW.country_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `credential_type`
--

DROP TABLE IF EXISTS `credential_type`;
CREATE TABLE `credential_type` (
  `credential_type_id` int(10) UNSIGNED NOT NULL,
  `credential_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `credential_type`
--

INSERT INTO `credential_type` (`credential_type_id`, `credential_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'Passport', '2024-11-27 14:51:12', 1),
(2, 'Driver\'s License', '2024-11-27 14:51:12', 1),
(3, 'National ID', '2024-11-27 14:51:12', 1),
(4, 'SSS ID', '2024-11-27 14:51:12', 1),
(5, 'GSIS ID', '2024-11-27 14:51:12', 1),
(6, 'PhilHealth ID', '2024-11-27 14:51:12', 1),
(7, 'Postal ID', '2024-11-27 14:51:12', 1),
(8, 'Voter\'s ID', '2024-11-27 14:51:12', 1),
(9, 'Barangay ID', '2024-11-27 14:51:12', 1),
(10, 'Student ID', '2024-11-27 14:51:12', 1),
(11, 'PRC License', '2024-11-27 14:51:12', 1),
(12, 'Company ID', '2024-11-27 14:51:12', 1),
(13, 'Professional Certification', '2024-11-27 14:51:12', 1),
(14, 'Work Permit', '2024-11-27 14:51:12', 1),
(15, 'Medical License', '2024-11-27 14:51:12', 1),
(16, 'Teaching License', '2024-11-27 14:51:12', 1),
(17, 'Engineering License', '2024-11-27 14:51:12', 1),
(18, 'Bar Exam Certificate', '2024-11-27 14:51:12', 1),
(19, 'Visa', '2024-11-27 14:51:12', 1),
(20, 'Work Visa', '2024-11-27 14:51:12', 1),
(21, 'Immigration Card', '2024-11-27 14:51:12', 1),
(22, 'Marriage Certificate', '2024-11-27 14:51:12', 1),
(23, 'Birth Certificate', '2024-11-27 14:51:12', 1),
(24, 'Death Certificate', '2024-11-27 14:51:12', 1),
(25, 'Police Clearance', '2024-11-27 14:51:12', 1),
(26, 'NBI Clearance', '2024-11-27 14:51:12', 1),
(27, 'Barangay Clearance', '2024-11-27 14:51:12', 1),
(28, 'Travel Permit', '2024-11-27 14:51:12', 1),
(29, 'Employment Certificate', '2024-11-27 14:51:12', 1),
(30, 'Firearm License', '2024-11-27 14:51:12', 1),
(31, 'Business Permit', '2024-11-27 14:51:12', 1);

--
-- Triggers `credential_type`
--
DROP TRIGGER IF EXISTS `credential_type_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `credential_type_trigger_insert` AFTER INSERT ON `credential_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Credential type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('credential_type', NEW.credential_type_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `credential_type_trigger_update`;
DELIMITER $$
CREATE TRIGGER `credential_type_trigger_update` AFTER UPDATE ON `credential_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Credential type changed.<br/><br/>';

    IF NEW.credential_type_name <> OLD.credential_type_name THEN
        SET audit_log = CONCAT(audit_log, "Credential Type Name: ", OLD.credential_type_name, " -> ", NEW.credential_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Credential type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('credential_type', NEW.credential_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency` (
  `currency_id` int(10) UNSIGNED NOT NULL,
  `currency_name` varchar(100) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `shorthand` varchar(10) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currency`
--

INSERT INTO `currency` (`currency_id`, `currency_name`, `symbol`, `shorthand`, `created_date`, `last_log_by`) VALUES
(1, 'testasdasd', 'Pasd', 'asdasdasd', '2024-11-14 17:06:35', 2),
(2, 'test', 'test', 'asdasd', '2024-11-14 17:06:49', 2);

--
-- Triggers `currency`
--
DROP TRIGGER IF EXISTS `currency_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `currency_trigger_insert` AFTER INSERT ON `currency` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Currency created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('currency', NEW.currency_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `currency_trigger_update`;
DELIMITER $$
CREATE TRIGGER `currency_trigger_update` AFTER UPDATE ON `currency` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Currency changed.<br/><br/>';

    IF NEW.currency_name <> OLD.currency_name THEN
        SET audit_log = CONCAT(audit_log, "Currency Name: ", OLD.currency_name, " -> ", NEW.currency_name, "<br/>");
    END IF;

    IF NEW.symbol <> OLD.symbol THEN
        SET audit_log = CONCAT(audit_log, "Symbol: ", OLD.symbol, " -> ", NEW.symbol, "<br/>");
    END IF;

    IF NEW.shorthand <> OLD.shorthand THEN
        SET audit_log = CONCAT(audit_log, "Shorthand: ", OLD.shorthand, " -> ", NEW.shorthand, "<br/>");
    END IF;
    
    IF audit_log <> 'Currency changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('currency', NEW.currency_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE `department` (
  `department_id` int(10) UNSIGNED NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `parent_department_id` int(11) DEFAULT NULL,
  `parent_department_name` varchar(100) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `manager_name` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`, `parent_department_id`, `parent_department_name`, `manager_id`, `manager_name`, `created_date`, `last_log_by`) VALUES
(1, 'asdasdasdasdasd', 2, 'asdasdasdasdasdasd', 0, '', '2024-11-27 16:50:57', 2),
(2, 'asdasdasdasdasdasdasdasdasdasdasdasdasd', 1, 'asdasdasdasdasd', 0, '', '2024-11-27 16:58:22', 2);

--
-- Triggers `department`
--
DROP TRIGGER IF EXISTS `department_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `department_trigger_insert` AFTER INSERT ON `department` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Department created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('department', NEW.department_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `department_trigger_update`;
DELIMITER $$
CREATE TRIGGER `department_trigger_update` AFTER UPDATE ON `department` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Department changed.<br/><br/>';

    IF NEW.department_name <> OLD.department_name THEN
        SET audit_log = CONCAT(audit_log, "Department Name: ", OLD.department_name, " -> ", NEW.department_name, "<br/>");
    END IF;

    IF NEW.parent_department_name <> OLD.parent_department_name THEN
        SET audit_log = CONCAT(audit_log, "Parent Department: ", OLD.parent_department_name, " -> ", NEW.parent_department_name, "<br/>");
    END IF;

    IF NEW.manager_name <> OLD.manager_name THEN
        SET audit_log = CONCAT(audit_log, "Manager: ", OLD.manager_name, " -> ", NEW.manager_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Department changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('department', NEW.department_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `educational_stage`
--

DROP TABLE IF EXISTS `educational_stage`;
CREATE TABLE `educational_stage` (
  `educational_stage_id` int(10) UNSIGNED NOT NULL,
  `educational_stage_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `educational_stage`
--

INSERT INTO `educational_stage` (`educational_stage_id`, `educational_stage_name`, `created_date`, `last_log_by`) VALUES
(1, 'Primary Education', '2024-11-27 14:04:06', 1),
(2, 'Middle School', '2024-11-27 14:04:06', 1),
(3, 'High School', '2024-11-27 14:04:06', 1),
(4, 'Diploma', '2024-11-27 14:04:06', 1),
(5, 'Bachelor', '2024-11-27 14:04:06', 1),
(6, 'Master', '2024-11-27 14:04:06', 1),
(7, 'Doctorate', '2024-11-27 14:04:06', 1),
(8, 'Post-Doctorate', '2024-11-27 14:04:06', 1),
(9, 'Vocational Training', '2024-11-27 14:04:06', 1);

--
-- Triggers `educational_stage`
--
DROP TRIGGER IF EXISTS `educational_stage_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `educational_stage_trigger_insert` AFTER INSERT ON `educational_stage` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Educational stage created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('educational_stage', NEW.educational_stage_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `educational_stage_trigger_update`;
DELIMITER $$
CREATE TRIGGER `educational_stage_trigger_update` AFTER UPDATE ON `educational_stage` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Educational stage changed.<br/><br/>';

    IF NEW.educational_stage_name <> OLD.educational_stage_name THEN
        SET audit_log = CONCAT(audit_log, "Educational Stage Name: ", OLD.educational_stage_name, " -> ", NEW.educational_stage_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Educational stage changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('educational_stage', NEW.educational_stage_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

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
(1, 'Security Email Setting', 'Email setting for security emails.', 'smtp.hostinger.com', '465', 1, 0, 'cgmi-noreply@christianmotors.ph', 'ZQ9hTsv10HUdptkRyJqyc8xICLKW9GsU9WPOxXzTE5U%3D', 'ssl', 'cgmi-noreply@christianmotors.ph', 'cgmi-noreply@christianmotors.ph', '2024-11-22 12:22:26', 2);

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
-- Table structure for table `file_extension`
--

DROP TABLE IF EXISTS `file_extension`;
CREATE TABLE `file_extension` (
  `file_extension_id` int(10) UNSIGNED NOT NULL,
  `file_extension_name` varchar(100) NOT NULL,
  `file_extension` varchar(10) NOT NULL,
  `file_type_id` int(11) NOT NULL,
  `file_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `file_extension`
--

INSERT INTO `file_extension` (`file_extension_id`, `file_extension_name`, `file_extension`, `file_type_id`, `file_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'AIF', 'aif', 1, 'Audio', '2024-11-17 14:36:26', 1),
(2, 'CDA', 'cda', 1, 'Audio', '2024-11-17 14:36:26', 1),
(3, 'MID', 'mid', 1, 'Audio', '2024-11-17 14:36:26', 1),
(4, 'MIDI', 'midi', 1, 'Audio', '2024-11-17 14:36:26', 1),
(5, 'MP3', 'mp3', 1, 'Audio', '2024-11-17 14:36:26', 1),
(6, 'MPA', 'mpa', 1, 'Audio', '2024-11-17 14:36:26', 1),
(7, 'OGG', 'ogg', 1, 'Audio', '2024-11-17 14:36:26', 1),
(11, '7Z', '7z', 2, 'Compressed', '2024-11-17 14:36:26', 1),
(12, 'ARJ', 'arj', 2, 'Compressed', '2024-11-17 14:36:26', 1),
(13, 'DEB', 'deb', 2, 'Compressed', '2024-11-17 14:36:26', 1),
(14, 'PKG', 'pkg', 2, 'Compressed', '2024-11-17 14:36:26', 1),
(15, 'RAR', 'rar', 2, 'Compressed', '2024-11-17 14:36:26', 1),
(16, 'RPM', 'rpm', 2, 'Compressed', '2024-11-17 14:36:26', 1),
(20, 'BIN', 'bin', 3, 'Disk and Media', '2024-11-17 14:36:26', 1),
(21, 'DMG', 'dmg', 3, 'Disk and Media', '2024-11-17 14:36:26', 1),
(22, 'ISO', 'iso', 3, 'Disk and Media', '2024-11-17 14:36:26', 1),
(25, 'CSV', 'csv', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(26, 'DAT', 'dat', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(27, 'DB', 'db', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(28, 'DBF', 'dbf', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(29, 'LOG', 'log', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(30, 'MDB', 'mdb', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(31, 'SAV', 'sav', 4, 'Data and Database', '2024-11-17 14:36:26', 1),
(35, 'EMAIL', 'email', 5, 'Email', '2024-11-17 14:36:26', 1),
(36, 'EML', 'eml', 5, 'Email', '2024-11-17 14:36:26', 1),
(37, 'EMLX', 'emlx', 5, 'Email', '2024-11-17 14:36:26', 1),
(38, 'MSG', 'msg', 5, 'Email', '2024-11-17 14:36:26', 1),
(39, 'OFT', 'oft', 5, 'Email', '2024-11-17 14:36:26', 1),
(40, 'OST', 'ost', 5, 'Email', '2024-11-17 14:36:26', 1),
(41, 'PST', 'pst', 5, 'Email', '2024-11-17 14:36:26', 1),
(43, 'APK', 'apk', 6, 'Executable', '2024-11-17 14:36:26', 1),
(44, 'BAT', 'bat', 6, 'Executable', '2024-11-17 14:36:26', 1),
(45, 'BIN', 'bin', 6, 'Executable', '2024-11-17 14:36:26', 1),
(46, 'CGI', 'cgi', 6, 'Executable', '2024-11-17 14:36:26', 1),
(47, 'PL', 'pl', 6, 'Executable', '2024-11-17 14:36:26', 1),
(48, 'COM', 'com', 6, 'Executable', '2024-11-17 14:36:26', 1),
(49, 'EXE', 'exe', 6, 'Executable', '2024-11-17 14:36:26', 1),
(50, 'GADGET', 'gadget', 6, 'Executable', '2024-11-17 14:36:26', 1),
(51, 'JAR', 'jar', 6, 'Executable', '2024-11-17 14:36:26', 1),
(53, 'FNT', 'fnt', 7, 'Font', '2024-11-17 14:36:26', 1),
(54, 'FON', 'fon', 7, 'Font', '2024-11-17 14:36:26', 1),
(55, 'OTF', 'otf', 7, 'Font', '2024-11-17 14:36:26', 1),
(57, 'AI', 'ai', 8, 'Image', '2024-11-17 14:36:26', 1),
(58, 'BMP', 'bmp', 8, 'Image', '2024-11-17 14:36:26', 1),
(59, 'GIF', 'gif', 8, 'Image', '2024-11-17 14:36:26', 1),
(60, 'ICO', 'ico', 8, 'Image', '2024-11-17 14:36:26', 1),
(61, 'JPG', 'jpg', 8, 'Image', '2024-11-17 14:36:26', 1),
(62, 'JPEG', 'jpeg', 8, 'Image', '2024-11-17 14:36:26', 1),
(63, 'PNG', 'png', 8, 'Image', '2024-11-17 14:36:26', 1),
(64, 'PS', 'ps', 8, 'Image', '2024-11-17 14:36:26', 1),
(65, 'PSD', 'psd', 8, 'Image', '2024-11-17 14:36:26', 1),
(70, 'ASP', 'asp', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(71, 'ASPX', 'aspx', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(72, 'CER', 'cer', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(73, 'CFM', 'cfm', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(74, 'CGI', 'cgi', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(75, 'PL', 'pl', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(76, 'CSS', 'css', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(77, 'HTM', 'htm', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(78, 'HTML', 'html', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(79, 'JS', 'js', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(80, 'JSP', 'jsp', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(81, 'PART', 'part', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(82, 'PHP', 'php', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(83, 'PY', 'py', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(84, 'RSS', 'rss', 9, 'Internet Related', '2024-11-17 14:36:26', 1),
(86, 'KEY', 'key', 10, 'Presentation', '2024-11-17 14:36:26', 1),
(87, 'ODP', 'odp', 10, 'Presentation', '2024-11-17 14:36:26', 1),
(88, 'PPS', 'pps', 10, 'Presentation', '2024-11-17 14:36:26', 1),
(89, 'PPT', 'ppt', 10, 'Presentation', '2024-11-17 14:36:26', 1),
(90, 'PPTX', 'pptx', 10, 'Presentation', '2024-11-17 14:36:26', 1),
(91, 'ODS', 'ods', 11, 'Spreadsheet', '2024-11-17 14:36:26', 1),
(95, 'BAK', 'bak', 12, 'System Related', '2024-11-17 14:36:26', 1),
(96, 'CAB', 'cab', 12, 'System Related', '2024-11-17 14:36:26', 1),
(97, 'CFG', 'cfg', 12, 'System Related', '2024-11-17 14:36:26', 1),
(98, 'CPL', 'cpl', 12, 'System Related', '2024-11-17 14:36:26', 1),
(99, 'CUR', 'cur', 12, 'System Related', '2024-11-17 14:36:26', 1),
(100, 'DLL', 'dll', 12, 'System Related', '2024-11-17 14:36:26', 1),
(101, 'DMP', 'dmp', 12, 'System Related', '2024-11-17 14:36:26', 1),
(102, 'DRV', 'drv', 12, 'System Related', '2024-11-17 14:36:26', 1),
(103, 'ICNS', 'icns', 12, 'System Related', '2024-11-17 14:36:26', 1),
(104, 'INI', 'ini', 12, 'System Related', '2024-11-17 14:36:26', 1),
(105, 'LNK', 'lnk', 12, 'System Related', '2024-11-17 14:36:26', 1),
(106, 'MSI', 'msi', 12, 'System Related', '2024-11-17 14:36:26', 1),
(109, '3G2', '3g2', 13, 'Video', '2024-11-17 14:36:26', 1),
(110, '3GP', '3gp', 13, 'Video', '2024-11-17 14:36:26', 1),
(111, 'AVI', 'avi', 13, 'Video', '2024-11-17 14:36:26', 1),
(112, 'FLV', 'flv', 13, 'Video', '2024-11-17 14:36:26', 1),
(113, 'H264', 'h264', 13, 'Video', '2024-11-17 14:36:26', 1),
(114, 'M4V', 'm4v', 13, 'Video', '2024-11-17 14:36:26', 1),
(115, 'MKV', 'mkv', 13, 'Video', '2024-11-17 14:36:26', 1),
(116, 'MOV', 'mov', 13, 'Video', '2024-11-17 14:36:26', 1),
(117, 'MP4', 'mp4', 13, 'Video', '2024-11-17 14:36:26', 1),
(118, 'MPG', 'mpg', 13, 'Video', '2024-11-17 14:36:26', 1),
(119, 'MPEG', 'mpeg', 13, 'Video', '2024-11-17 14:36:26', 1),
(120, 'RM', 'rm', 13, 'Video', '2024-11-17 14:36:26', 1),
(125, 'DOC', 'doc', 14, 'Word Processor', '2024-11-17 14:36:26', 1),
(126, 'DOCX', 'docx', 14, 'Word Processor', '2024-11-17 14:36:26', 1),
(127, 'PDF', 'pdf', 14, 'Word Processor', '2024-11-17 14:36:26', 1),
(128, 'RTF', 'rtf', 14, 'Word Processor', '2024-11-17 14:36:26', 1);

--
-- Triggers `file_extension`
--
DROP TRIGGER IF EXISTS `file_extension_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `file_extension_trigger_insert` AFTER INSERT ON `file_extension` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'File extension created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('file_extension', NEW.file_extension_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `file_extension_trigger_update`;
DELIMITER $$
CREATE TRIGGER `file_extension_trigger_update` AFTER UPDATE ON `file_extension` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'File extension changed.<br/><br/>';

    IF NEW.file_extension_name <> OLD.file_extension_name THEN
        SET audit_log = CONCAT(audit_log, "File Extension Name: ", OLD.file_extension_name, " -> ", NEW.file_extension_name, "<br/>");
    END IF;

    IF NEW.file_extension <> OLD.file_extension THEN
        SET audit_log = CONCAT(audit_log, "File Extension: ", OLD.file_extension, " -> ", NEW.file_extension, "<br/>");
    END IF;

    IF NEW.file_type_name <> OLD.file_type_name THEN
        SET audit_log = CONCAT(audit_log, "File Type: ", OLD.file_type_name, " -> ", NEW.file_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'File extension changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('file_extension', NEW.file_extension_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `file_type`
--

DROP TABLE IF EXISTS `file_type`;
CREATE TABLE `file_type` (
  `file_type_id` int(10) UNSIGNED NOT NULL,
  `file_type_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `file_type`
--

INSERT INTO `file_type` (`file_type_id`, `file_type_name`, `created_date`, `last_log_by`) VALUES
(1, 'Audio', '2024-11-15 17:04:49', 1),
(2, 'Compressed', '2024-11-15 17:04:49', 1),
(3, 'Disk and Media', '2024-11-15 17:04:49', 1),
(4, 'Data and Database', '2024-11-15 17:04:49', 1),
(5, 'Email', '2024-11-15 17:04:49', 1),
(6, 'Executable', '2024-11-15 17:04:49', 1),
(7, 'Font', '2024-11-15 17:04:49', 1),
(8, 'Image', '2024-11-15 17:04:49', 1),
(9, 'Internet Related', '2024-11-15 17:04:49', 1),
(10, 'Presentation', '2024-11-15 17:04:49', 1),
(11, 'Spreadsheet', '2024-11-15 17:04:49', 1),
(12, 'System Related', '2024-11-15 17:04:49', 1),
(13, 'Video', '2024-11-15 17:04:49', 1),
(14, 'Word Processor', '2024-11-15 17:04:49', 1);

--
-- Triggers `file_type`
--
DROP TRIGGER IF EXISTS `file_type_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `file_type_trigger_insert` AFTER INSERT ON `file_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'File type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('file_type', NEW.file_type_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `file_type_trigger_update`;
DELIMITER $$
CREATE TRIGGER `file_type_trigger_update` AFTER UPDATE ON `file_type` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'File type changed.<br/><br/>';

    IF NEW.file_type_name <> OLD.file_type_name THEN
        SET audit_log = CONCAT(audit_log, "File Type Name: ", OLD.file_type_name, " -> ", NEW.file_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'File type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('file_type', NEW.file_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `gender`
--

DROP TABLE IF EXISTS `gender`;
CREATE TABLE `gender` (
  `gender_id` int(10) UNSIGNED NOT NULL,
  `gender_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gender`
--

INSERT INTO `gender` (`gender_id`, `gender_name`, `created_date`, `last_log_by`) VALUES
(1, 'Male', '2024-11-27 14:22:15', 1),
(2, 'Female', '2024-11-27 14:22:15', 1);

--
-- Triggers `gender`
--
DROP TRIGGER IF EXISTS `gender_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `gender_trigger_insert` AFTER INSERT ON `gender` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Gender created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('gender', NEW.gender_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `gender_trigger_update`;
DELIMITER $$
CREATE TRIGGER `gender_trigger_update` AFTER UPDATE ON `gender` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Gender changed.<br/><br/>';

    IF NEW.gender_name <> OLD.gender_name THEN
        SET audit_log = CONCAT(audit_log, "Gender Name: ", OLD.gender_name, " -> ", NEW.gender_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Gender changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('gender', NEW.gender_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `language_id` int(10) UNSIGNED NOT NULL,
  `language_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`language_id`, `language_name`, `created_date`, `last_log_by`) VALUES
(1, 'Afrikaans', '2024-11-27 11:24:20', 1),
(2, 'Amharic', '2024-11-27 11:24:20', 1),
(3, 'Arabic', '2024-11-27 11:24:20', 1),
(4, 'Assamese', '2024-11-27 11:24:20', 1),
(5, 'Azerbaijani', '2024-11-27 11:24:20', 1),
(6, 'Belarusian', '2024-11-27 11:24:20', 1),
(7, 'Bulgarian', '2024-11-27 11:24:20', 1),
(8, 'Bhojpuri', '2024-11-27 11:24:20', 1),
(9, 'Bengali', '2024-11-27 11:24:20', 1),
(10, 'Bosnian', '2024-11-27 11:24:20', 1),
(11, 'Catalan, Valencian', '2024-11-27 11:24:20', 1),
(12, 'Cebuano', '2024-11-27 11:24:20', 1),
(13, 'Czech', '2024-11-27 11:24:20', 1),
(14, 'Danish', '2024-11-27 11:24:20', 1),
(15, 'German', '2024-11-27 11:24:20', 1),
(16, 'English', '2024-11-27 11:24:20', 1),
(17, 'Ewe', '2024-11-27 11:24:20', 1),
(18, 'Greek, Modern', '2024-11-27 11:24:20', 1),
(19, 'Spanish', '2024-11-27 11:24:20', 1),
(20, 'Estonian', '2024-11-27 11:24:20', 1),
(21, 'Basque', '2024-11-27 11:24:20', 1),
(22, 'Persian', '2024-11-27 11:24:20', 1),
(23, 'Fula', '2024-11-27 11:24:20', 1),
(24, 'Finnish', '2024-11-27 11:24:20', 1),
(25, 'French', '2024-11-27 11:24:20', 1),
(26, 'Irish', '2024-11-27 11:24:20', 1),
(27, 'Galician', '2024-11-27 11:24:20', 1),
(28, 'Guarani', '2024-11-27 11:24:20', 1),
(29, 'Gujarati', '2024-11-27 11:24:20', 1),
(30, 'Hausa', '2024-11-27 11:24:20', 1),
(31, 'Haitian Creole', '2024-11-27 11:24:20', 1),
(32, 'Hebrew (modern)', '2024-11-27 11:24:20', 1),
(33, 'Hindi', '2024-11-27 11:24:20', 1),
(34, 'Chhattisgarhi', '2024-11-27 11:24:20', 1),
(35, 'Croatian', '2024-11-27 11:24:20', 1),
(36, 'Hungarian', '2024-11-27 11:24:20', 1),
(37, 'Armenian', '2024-11-27 11:24:20', 1),
(38, 'Indonesian', '2024-11-27 11:24:20', 1),
(39, 'Igbo', '2024-11-27 11:24:20', 1),
(40, 'Icelandic', '2024-11-27 11:24:20', 1),
(41, 'Italian', '2024-11-27 11:24:20', 1),
(42, 'Japanese', '2024-11-27 11:24:20', 1),
(43, 'Syro-Palestinian Sign Language', '2024-11-27 11:24:20', 1),
(44, 'Javanese', '2024-11-27 11:24:20', 1),
(45, 'Georgian', '2024-11-27 11:24:20', 1),
(46, 'Kikuyu', '2024-11-27 11:24:20', 1),
(47, 'Kyrgyz', '2024-11-27 11:24:20', 1),
(48, 'Kuanyama', '2024-11-27 11:24:20', 1),
(49, 'Kazakh', '2024-11-27 11:24:20', 1),
(50, 'Khmer', '2024-11-27 11:24:20', 1),
(51, 'Kannada', '2024-11-27 11:24:20', 1),
(52, 'Korean', '2024-11-27 11:24:20', 1),
(53, 'Krio', '2024-11-27 11:24:20', 1),
(54, 'Kashmiri', '2024-11-27 11:24:20', 1),
(55, 'Kurdish', '2024-11-27 11:24:20', 1),
(56, 'Latin', '2024-11-27 11:24:20', 1),
(57, 'Lithuanian', '2024-11-27 11:24:20', 1),
(58, 'Luxembourgish', '2024-11-27 11:24:20', 1),
(59, 'Latvian', '2024-11-27 11:24:20', 1),
(60, 'Magahi', '2024-11-27 11:24:20', 1),
(61, 'Maithili', '2024-11-27 11:24:20', 1),
(62, 'Malagasy', '2024-11-27 11:24:20', 1),
(63, 'Macedonian', '2024-11-27 11:24:20', 1),
(64, 'Malayalam', '2024-11-27 11:24:20', 1),
(65, 'Mongolian', '2024-11-27 11:24:20', 1),
(66, 'Marathi (Marāṭhī)', '2024-11-27 11:24:20', 1),
(67, 'Malay', '2024-11-27 11:24:20', 1),
(68, 'Maltese', '2024-11-27 11:24:20', 1),
(69, 'Burmese', '2024-11-27 11:24:20', 1),
(70, 'Nepali', '2024-11-27 11:24:20', 1),
(71, 'Dutch', '2024-11-27 11:24:20', 1),
(72, 'Norwegian', '2024-11-27 11:24:20', 1),
(73, 'Oromo', '2024-11-27 11:24:20', 1),
(74, 'Odia', '2024-11-27 11:24:20', 1),
(75, 'Oromo', '2024-11-27 11:24:20', 1),
(76, 'Panjabi, Punjabi', '2024-11-27 11:24:20', 1),
(77, 'Polish', '2024-11-27 11:24:20', 1),
(78, 'Pashto', '2024-11-27 11:24:20', 1),
(79, 'Portuguese', '2024-11-27 11:24:20', 1),
(80, 'Rundi', '2024-11-27 11:24:20', 1),
(81, 'Romanian, Moldavian, Moldovan', '2024-11-27 11:24:20', 1),
(82, 'Russian', '2024-11-27 11:24:20', 1),
(83, 'Kinyarwanda', '2024-11-27 11:24:20', 1),
(84, 'Sindhi', '2024-11-27 11:24:20', 1),
(85, 'Argentine Sign Language', '2024-11-27 11:24:20', 1),
(86, 'Brazilian Sign Language', '2024-11-27 11:24:20', 1),
(87, 'Chinese Sign Language', '2024-11-27 11:24:20', 1),
(88, 'Colombian Sign Language', '2024-11-27 11:24:20', 1),
(89, 'German Sign Language', '2024-11-27 11:24:20', 1),
(90, 'Algerian Sign Language', '2024-11-27 11:24:20', 1),
(91, 'Ecuadorian Sign Language', '2024-11-27 11:24:20', 1),
(92, 'Spanish Sign Language', '2024-11-27 11:24:20', 1),
(93, 'Ethiopian Sign Language', '2024-11-27 11:24:20', 1),
(94, 'French Sign Language', '2024-11-27 11:24:20', 1),
(95, 'British Sign Language', '2024-11-27 11:24:20', 1),
(96, 'Ghanaian Sign Language', '2024-11-27 11:24:20', 1),
(97, 'Irish Sign Language', '2024-11-27 11:24:20', 1),
(98, 'Indopakistani Sign Language', '2024-11-27 11:24:20', 1),
(99, 'Persian Sign Language', '2024-11-27 11:24:20', 1),
(100, 'Italian Sign Language', '2024-11-27 11:24:20', 1),
(101, 'Japanese Sign Language', '2024-11-27 11:24:20', 1),
(102, 'Kenyan Sign Language', '2024-11-27 11:24:20', 1),
(103, 'Korean Sign Language', '2024-11-27 11:24:20', 1),
(104, 'Moroccan Sign Language', '2024-11-27 11:24:20', 1),
(105, 'Mexican Sign Language', '2024-11-27 11:24:20', 1),
(106, 'Malaysian Sign Language', '2024-11-27 11:24:20', 1),
(107, 'Philippine Sign Language', '2024-11-27 11:24:20', 1),
(108, 'Polish Sign Language', '2024-11-27 11:24:20', 1),
(109, 'Portuguese Sign Language', '2024-11-27 11:24:20', 1),
(110, 'Russian Sign Language', '2024-11-27 11:24:20', 1),
(111, 'Saudi Arabian Sign Language', '2024-11-27 11:24:20', 1),
(112, 'El Salvadoran Sign Language', '2024-11-27 11:24:20', 1),
(113, 'Turkish Sign Language', '2024-11-27 11:24:20', 1),
(114, 'Tanzanian Sign Language', '2024-11-27 11:24:20', 1),
(115, 'Ukrainian Sign Language', '2024-11-27 11:24:20', 1),
(116, 'American Sign Language', '2024-11-27 11:24:20', 1),
(117, 'South African Sign Language', '2024-11-27 11:24:20', 1),
(118, 'Zimbabwe Sign Language', '2024-11-27 11:24:20', 1),
(119, 'Sinhala, Sinhalese', '2024-11-27 11:24:20', 1),
(120, 'Slovak', '2024-11-27 11:24:20', 1),
(121, 'Saraiki', '2024-11-27 11:24:20', 1),
(122, 'Slovene', '2024-11-27 11:24:20', 1),
(123, 'Shona', '2024-11-27 11:24:20', 1),
(124, 'Somali', '2024-11-27 11:24:20', 1),
(125, 'Albanian', '2024-11-27 11:24:20', 1),
(126, 'Serbian', '2024-11-27 11:24:20', 1),
(127, 'Swati', '2024-11-27 11:24:20', 1),
(128, 'Sunda', '2024-11-27 11:24:20', 1),
(129, 'Swedish', '2024-11-27 11:24:20', 1),
(130, 'Swahili', '2024-11-27 11:24:20', 1),
(131, 'Sylheti', '2024-11-27 11:24:20', 1),
(132, 'Tagalog', '2024-11-27 11:24:20', 1),
(133, 'Tamil', '2024-11-27 11:24:20', 1),
(134, 'Telugu', '2024-11-27 11:24:20', 1),
(135, 'Thai', '2024-11-27 11:24:20', 1),
(136, 'Tibetan', '2024-11-27 11:24:20', 1),
(137, 'Tigrinya', '2024-11-27 11:24:20', 1),
(138, 'Turkmen', '2024-11-27 11:24:20', 1),
(139, 'Tswana', '2024-11-27 11:24:20', 1),
(140, 'Turkish', '2024-11-27 11:24:20', 1),
(141, 'Uyghur', '2024-11-27 11:24:20', 1),
(142, 'Ukrainian', '2024-11-27 11:24:20', 1),
(143, 'Urdu', '2024-11-27 11:24:20', 1),
(144, 'Uzbek', '2024-11-27 11:24:20', 1),
(145, 'Vietnamese', '2024-11-27 11:24:20', 1),
(146, 'Xhosa', '2024-11-27 11:24:20', 1),
(147, 'Yiddish', '2024-11-27 11:24:20', 1),
(148, 'Yoruba', '2024-11-27 11:24:20', 1),
(149, 'Cantonese', '2024-11-27 11:24:20', 1),
(150, 'Chinese', '2024-11-27 11:24:20', 1),
(151, 'Zulu', '2024-11-27 11:24:20', 1);

--
-- Triggers `language`
--
DROP TRIGGER IF EXISTS `language_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `language_trigger_insert` AFTER INSERT ON `language` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('language', NEW.language_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `language_trigger_update`;
DELIMITER $$
CREATE TRIGGER `language_trigger_update` AFTER UPDATE ON `language` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language changed.<br/><br/>';

    IF NEW.language_name <> OLD.language_name THEN
        SET audit_log = CONCAT(audit_log, "Language Name: ", OLD.language_name, " -> ", NEW.language_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Language changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('language', NEW.language_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `language_proficiency`
--

DROP TABLE IF EXISTS `language_proficiency`;
CREATE TABLE `language_proficiency` (
  `language_proficiency_id` int(10) UNSIGNED NOT NULL,
  `language_proficiency_name` varchar(100) NOT NULL,
  `language_proficiency_description` varchar(200) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `language_proficiency`
--

INSERT INTO `language_proficiency` (`language_proficiency_id`, `language_proficiency_name`, `language_proficiency_description`, `created_date`, `last_log_by`) VALUES
(1, 'Native', 'Fluent in the language, spoken at home', '2024-11-27 11:50:38', 1),
(2, 'Fluent', 'Able to communicate effectively and accurately in most formal and informal conversations', '2024-11-27 11:50:38', 1),
(3, 'Advanced', 'Able to communicate effectively and accurately in most formal and informal conversations, with some difficulty in complex situations', '2024-11-27 11:50:38', 1),
(4, 'Intermediate', 'Able to communicate in everyday situations, with some difficulty in formal conversations', '2024-11-27 11:50:38', 1),
(5, 'Basic', 'Able to communicate in very basic situations, with difficulty in everyday conversations', '2024-11-27 11:50:38', 1),
(6, 'Non-proficient', 'No knowledge of the language', '2024-11-27 11:50:38', 1);

--
-- Triggers `language_proficiency`
--
DROP TRIGGER IF EXISTS `language_proficiency_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `language_proficiency_trigger_insert` AFTER INSERT ON `language_proficiency` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language proficiency created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('language_proficiency', NEW.language_proficiency_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `language_proficiency_trigger_update`;
DELIMITER $$
CREATE TRIGGER `language_proficiency_trigger_update` AFTER UPDATE ON `language_proficiency` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language proficiency changed.<br/><br/>';

    IF NEW.language_proficiency_name <> OLD.language_proficiency_name THEN
        SET audit_log = CONCAT(audit_log, "Language Proficiency Name: ", OLD.language_proficiency_name, " -> ", NEW.language_proficiency_name, "<br/>");
    END IF;

    IF NEW.language_proficiency_description <> OLD.language_proficiency_description THEN
        SET audit_log = CONCAT(audit_log, "Language Proficiency Description: ", OLD.language_proficiency_description, " -> ", NEW.language_proficiency_description, "<br/>");
    END IF;
    
    IF audit_log <> 'Language proficiency changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('language_proficiency', NEW.language_proficiency_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `login_session`
--

DROP TABLE IF EXISTS `login_session`;
CREATE TABLE `login_session` (
  `login_session_id` int(11) NOT NULL,
  `user_account_id` int(10) UNSIGNED NOT NULL,
  `location` varchar(500) NOT NULL,
  `login_status` varchar(50) NOT NULL,
  `device` varchar(200) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `login_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_session`
--

INSERT INTO `login_session` (`login_session_id`, `user_account_id`, `location`, `login_status`, `device`, `ip_address`, `login_date`) VALUES
(1, 2, 'Unknown', 'Ok', 'Chrome - Windows PC', '::1', '2024-11-11 17:58:14'),
(2, 2, 'Makati City, PH', 'Ok', 'Chrome - Windows PC', '112.207.178.12', '2024-11-11 18:01:53'),
(3, 2, 'Makati City, PH', 'Ok', 'Chrome - K - Android', '112.207.178.12', '2024-11-11 18:08:02'),
(4, 2, 'Makati City, PH', 'Ok', 'K - Android', '112.207.178.12', '2024-11-11 18:10:48'),
(5, 2, 'Makati City, PH', 'Ok', 'K) - Android', '112.207.178.12', '2024-11-11 18:13:44'),
(6, 2, 'Makati City, PH', 'Ok', 'K - Android', '112.207.178.12', '2024-11-11 18:14:42'),
(7, 2, 'Makati City, PH', 'Ok', 'Opera - Linux', '112.207.178.12', '2024-11-11 18:16:08'),
(8, 2, 'Makati City, PH', 'Ok', 'Opera - Linux', '112.207.178.12', '2024-11-11 18:19:48'),
(9, 2, 'Makati City, PH', 'Ok', 'Opera - Android', '112.207.178.12', '2024-11-11 18:20:48'),
(10, 2, 'Makati City, PH', 'Ok', 'Opera - Windows', '112.207.178.12', '2024-11-11 18:21:56'),
(11, 2, 'Makati City, PH', 'Ok', 'Opera - Windows', '112.207.178.12', '2024-11-11 18:34:39'),
(12, 2, 'Makati City, PH', 'Ok', 'Opera - Windows', '112.207.178.12', '2024-11-11 20:07:41'),
(13, 2, 'Makati City, PH', 'Ok', 'Opera - Windows', '112.207.178.12', '2024-11-11 20:11:39'),
(14, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-12 14:17:42'),
(15, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-13 09:03:49'),
(16, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-13 11:24:08'),
(17, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-14 08:50:03'),
(18, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-15 08:50:55'),
(19, 2, 'Tunasan, PH', 'Ok', 'Opera - Windows', '112.208.177.211', '2024-11-17 12:52:56'),
(20, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-18 12:07:42'),
(21, 2, 'Cabanatuan City, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-20 13:24:32'),
(22, 2, 'Manila, PH', 'Ok', 'Opera - Windows', '124.106.204.254', '2024-11-21 14:08:58'),
(23, 2, 'Tunasan, PH', 'Ok', 'Opera - Windows', '112.208.177.211', '2024-11-24 13:26:06'),
(24, 2, 'Tunasan, PH', 'Ok', 'Opera - Windows', '112.208.177.211', '2024-11-24 17:44:11'),
(25, 2, 'Tunasan, PH', 'Ok', 'Opera - Windows', '112.208.177.211', '2024-11-25 11:44:20');

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
(1, 'App Module', 'app-module.php', '', 1, 'Settings', 0, '', 'app_module', 1, '2024-11-14 11:44:44', 2),
(2, 'Settings', '', '', 1, 'Settings', 0, '', '', 80, '2024-11-14 11:44:44', 2),
(3, 'Users & Companies', '', '', 1, 'Settings', 0, '', '', 21, '2024-11-14 11:44:44', 2),
(4, 'User Account', 'user-account.php', 'ki-outline ki-user', 1, 'Settings', 3, 'Users & Companies', 'user_account', 21, '2024-11-14 11:44:44', 2),
(5, 'Company', 'company.php', 'ki-outline ki-shop', 1, 'Settings', 3, 'Users & Companies', 'company', 3, '2024-11-14 11:44:44', 2),
(6, 'Role', 'role.php', '', 1, 'Settings', NULL, NULL, 'role', 3, '2024-11-14 11:44:44', 2),
(7, 'User Interface', '', '', 1, 'Settings', NULL, NULL, '', 16, '2024-11-14 11:44:44', 2),
(8, 'Menu Item', 'menu-item.php', 'ki-outline ki-data', 1, 'Settings', 7, 'User Interface', 'menu_item', 2, '2024-11-14 11:44:44', 2),
(9, 'System Action', 'system-action.php', 'ki-outline ki-key-square', 1, 'Settings', 7, 'User Interface', 'system_action', 3, '2024-11-14 11:44:44', 2),
(10, 'Account Settings', 'account-settings.php', '', 1, 'Settings', NULL, NULL, NULL, 127, '2024-11-14 11:44:44', 2),
(11, 'Configurations', '', '', 1, 'Settings', 0, '', '', 50, '2024-11-14 11:49:18', 2),
(12, 'Localization', '', 'ki-outline ki-compass', 1, 'Settings', 11, 'Configurations', '', 12, '2024-11-14 11:56:25', 2),
(13, 'Country', 'country.php', '', 1, 'Settings', 12, 'Localization', 'country', 3, '2024-11-14 11:57:15', 2),
(14, 'State', 'state.php', '', 1, 'Settings', 12, 'Localization', '', 19, '2024-11-14 12:13:03', 2),
(15, 'City', 'city.php', '', 1, 'Settings', 12, 'Localization', 'city', 3, '2024-11-14 12:14:05', 2),
(16, 'Currency', 'currency.php', '', 1, 'Settings', 12, 'Localization', 'currency', 3, '2024-11-14 12:16:32', 2),
(17, 'Data Classification', '', 'ki-outline ki-file-up', 1, 'Settings', 11, 'Configurations', '', 4, '2024-11-15 16:41:47', 2),
(18, 'File Type', 'file-type.php', '', 1, 'Settings', 17, 'Data Classification', 'file_type', 6, '2024-11-15 16:42:51', 2),
(19, 'File Extension', 'file-extension.php', '', 1, 'Settings', 17, 'Data Classification', 'file_extension', 6, '2024-11-15 16:43:31', 2),
(20, 'Upload Setting', 'upload-setting.php', 'ki-outline ki-exit-up', 1, 'Settings', 2, 'Settings', 'upload_setting', 21, '2024-11-18 14:42:34', 2),
(21, 'Security Setting', 'security-setting.php', 'ki-outline ki-lock', 1, 'Settings', 2, 'Settings', 'security_setting', 19, '2024-11-20 16:48:34', 2),
(22, 'Email Setting', 'email-setting.php', 'ki-outline ki-sms', 1, 'Settings', 2, 'Settings', 'email_setting', 5, '2024-11-22 10:39:27', 2),
(23, 'Notification Setting', 'notification-setting.php', 'ki-outline ki-notification', 1, 'Settings', 2, 'Settings', 'notification_setting', 14, '2024-11-22 14:29:29', 2),
(24, 'Employee', 'employee.php', '', 2, 'Employee', 0, '', '', 1, '2024-11-25 14:34:33', 2),
(25, 'Banking', '', 'ki-outline ki-bank', 1, 'Settings', 11, 'Configurations', '', 2, '2024-11-25 15:14:27', 2),
(26, 'Bank', 'bank.php', '', 1, 'Settings', 25, 'Banking', 'bank', 1, '2024-11-25 15:14:59', 2),
(27, 'Bank Account Type', 'bank-account-type.php', '', 1, 'Settings', 25, 'Banking', 'bank_account_type', 2, '2024-11-25 15:15:23', 2),
(28, 'Contact Information', '', 'ki-outline ki-address-book', 1, 'Settings', 11, 'Configurations', '', 3, '2024-11-25 15:18:29', 2),
(29, 'Address Type', 'address-type.php', '', 1, 'Settings', 28, 'Contact Information', 'address_type', 1, '2024-11-25 15:19:04', 2),
(30, 'Contact Information Type', 'contact-information-type.php', 'ki-outline ki-abstract', 1, 'Settings', 28, 'Contact Information', 'contact_information_type', 3, '2024-11-25 15:19:57', 2),
(31, 'Language Settings', '', 'ki-outline ki-note-2', 1, 'Settings', 11, 'Configurations', '', 12, '2024-11-25 15:23:17', 2),
(32, 'Language', 'language.php', '', 1, 'Settings', 31, 'Language Settings', 'language', 1, '2024-11-25 15:23:44', 2),
(33, 'Language Proficiency', 'language-proficiency.php', '', 1, 'Settings', 31, 'Language Settings', 'language_proficiency', 2, '2024-11-25 15:24:19', 2),
(34, 'Profile Attribute', '', 'ki-outline ki-people', 1, 'Settings', 11, 'Configurations', '', 16, '2024-11-25 15:27:17', 2),
(35, 'Blood Type', 'blood-type.php', '', 1, 'Settings', 34, 'Profile Attribute', 'blood_type', 2, '2024-11-25 15:27:50', 2),
(36, 'Civil Status', 'civil-status.php', '', 1, 'Settings', 34, 'Profile Attribute', 'civil_status', 3, '2024-11-25 15:28:20', 2),
(37, 'Educational Stage', 'educational-stage.php', '', 1, 'Settings', 34, 'Profile Attribute', 'educational_stage', 5, '2024-11-25 15:28:53', 2),
(38, 'Gender', 'gender.php', '', 1, 'Settings', 34, 'Profile Attribute', 'gender', 7, '2024-11-25 15:29:25', 2),
(39, 'Credential Type', 'credential-type.php', '', 1, 'Settings', 34, 'Profile Attribute', 'credential_type', 3, '2024-11-25 15:30:00', 2),
(40, 'Relationship', 'relationship.php', '', 1, 'Settings', 34, 'Profile Attribute', 'relationship', 18, '2024-11-25 15:30:39', 2),
(41, 'Religion', 'religion.php', '', 1, 'Settings', 34, 'Profile Attribute', 'religion', 19, '2024-11-25 15:31:21', 2),
(42, 'HR Configurations', '', '', 2, 'Employee', NULL, '', '', 99, '2024-11-25 15:33:34', 2),
(43, 'Department', 'department.php', 'ki-outline ki-data', 2, 'Employee', 42, 'HR Configurations', '', 4, '2024-11-25 15:36:29', 2),
(44, 'Departure Reason', 'departure-reason.php', 'ki-outline ki-user-square', 2, 'Employee', 42, 'HR Configurations', '', 4, '2024-11-25 15:38:31', 2),
(45, 'Employment Location Type', 'employment-location-type.php', 'ki-outline ki-route', 2, 'Employee', 42, 'HR Configurations', '', 5, '2024-11-25 15:39:48', 2),
(46, 'Employment Type', 'employment-type.php', 'ki-outline ki-briefcase', 2, 'Employee', 42, 'HR Configurations', '', 5, '2024-11-25 15:40:40', 2),
(47, 'Job Position', 'job-position.php', 'ki-outline ki-questionnaire-tablet', 2, 'Employee', 42, 'HR Configurations', '', 10, '2024-11-25 15:42:05', 2),
(48, 'Work Location', 'work-location.php', 'ki-outline ki-geolocation', 2, 'Employee', 42, 'HR Configurations', '', 23, '2024-11-25 15:43:23', 2),
(49, 'Work Schedule Type', 'work-schedule-type.php', 'ki-outline ki-brifecase-timer', 2, 'Employee', 42, 'HR Configurations', '', 23, '2024-11-25 15:45:02', 2),
(50, 'Work Schedule', 'work-schedule.php', '', 2, 'Employee', 0, '', '', 23, '2024-11-25 15:45:48', 2);

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
(3, 'Registration Verification', 'Notification setting when the user sign-up for an account.', 0, 1, 0, '2024-10-13 16:15:08', 1),
(4, 'test2', 'test2', 1, 1, 1, '2024-11-22 15:29:03', 2);

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
(3, 3, 'Sign Up Verification - Action Required', '<p>Thank you for registering! To complete your registration, please verify your email address by clicking the link below:</p>\n<p><a href=\"#{REGISTRATION_VERIFICATION_LINK}\">Click to verify your account</a></p>\n<p>Important: This link is time-sensitive and will expire after #{REGISTRATION_VERIFICATION_VALIDITY}. If you do not verify your email within this timeframe, you may need to request another verification link.</p>\n<p>If you did not register for an account with us, please ignore this email. Your account will not be activated.</p>\n<p>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', '2024-10-13 16:15:08', 1),
(4, 4, 'asdasdasd', '<p><em><span style=\"text-decoration: underline;\"><strong>aasdasdasd</strong></span></em></p>', 1, 'Security Email Setting', '2024-11-24 19:35:28', 2);

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
-- Dumping data for table `notification_setting_sms_template`
--

INSERT INTO `notification_setting_sms_template` (`notification_setting_sms_id`, `notification_setting_id`, `sms_notification_message`, `created_date`, `last_log_by`) VALUES
(1, 4, 'asdasd', '2024-11-24 19:35:16', 2);

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
-- Dumping data for table `notification_setting_system_template`
--

INSERT INTO `notification_setting_system_template` (`notification_setting_system_id`, `notification_setting_id`, `system_notification_title`, `system_notification_message`, `created_date`, `last_log_by`) VALUES
(1, 4, 'asdasdas', 'asdasd', '2024-11-24 19:36:08', 2);

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
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
CREATE TABLE `relationship` (
  `relationship_id` int(10) UNSIGNED NOT NULL,
  `relationship_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `relationship`
--

INSERT INTO `relationship` (`relationship_id`, `relationship_name`, `created_date`, `last_log_by`) VALUES
(1, 'Father', '2024-11-27 15:01:02', 1),
(2, 'Mother', '2024-11-27 15:01:02', 1),
(3, 'Husband', '2024-11-27 15:01:02', 1),
(4, 'Wife', '2024-11-27 15:01:02', 1),
(5, 'Son', '2024-11-27 15:01:02', 1),
(6, 'Daughter', '2024-11-27 15:01:02', 1),
(7, 'Brother', '2024-11-27 15:01:02', 1),
(8, 'Sister', '2024-11-27 15:01:02', 1),
(9, 'Grandfather', '2024-11-27 15:01:02', 1),
(10, 'Grandmother', '2024-11-27 15:01:02', 1),
(11, 'Grandson', '2024-11-27 15:01:02', 1),
(12, 'Granddaughter', '2024-11-27 15:01:02', 1),
(13, 'Uncle', '2024-11-27 15:01:02', 1),
(14, 'Aunt', '2024-11-27 15:01:02', 1),
(15, 'Nephew', '2024-11-27 15:01:02', 1),
(16, 'Niece', '2024-11-27 15:01:02', 1),
(17, 'Cousin', '2024-11-27 15:01:02', 1),
(18, 'Friend', '2024-11-27 15:01:02', 1);

--
-- Triggers `relationship`
--
DROP TRIGGER IF EXISTS `relationship_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `relationship_trigger_insert` AFTER INSERT ON `relationship` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Relationship created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('relationship', NEW.relationship_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `relationship_trigger_update`;
DELIMITER $$
CREATE TRIGGER `relationship_trigger_update` AFTER UPDATE ON `relationship` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Relationship changed.<br/><br/>';

    IF NEW.relationship_name <> OLD.relationship_name THEN
        SET audit_log = CONCAT(audit_log, "Relationship Name: ", OLD.relationship_name, " -> ", NEW.relationship_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Relationship changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('relationship', NEW.relationship_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `religion`
--

DROP TABLE IF EXISTS `religion`;
CREATE TABLE `religion` (
  `religion_id` int(10) UNSIGNED NOT NULL,
  `religion_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `religion`
--

INSERT INTO `religion` (`religion_id`, `religion_name`, `created_date`, `last_log_by`) VALUES
(1, 'Christianity', '2024-11-27 15:16:36', 1),
(2, 'Islam', '2024-11-27 15:16:36', 1),
(3, 'Hinduism', '2024-11-27 15:16:36', 1),
(4, 'Buddhism', '2024-11-27 15:16:36', 1),
(5, 'Judaism', '2024-11-27 15:16:36', 1),
(6, 'Sikhism', '2024-11-27 15:16:36', 1),
(7, 'Atheism', '2024-11-27 15:16:36', 1),
(8, 'Agnosticism', '2024-11-27 15:16:36', 1),
(9, 'Baháʼí', '2024-11-27 15:16:36', 1),
(10, 'Confucianism', '2024-11-27 15:16:36', 1),
(11, 'Shinto', '2024-11-27 15:16:36', 1),
(12, 'Taoism', '2024-11-27 15:16:36', 1),
(13, 'Zoroastrianism', '2024-11-27 15:16:36', 1),
(14, 'Jainism', '2024-11-27 15:16:36', 1),
(15, 'Spiritualism', '2024-11-27 15:16:36', 1),
(16, 'Paganism', '2024-11-27 15:16:36', 1),
(17, 'Rastafarianism', '2024-11-27 15:16:36', 1),
(18, 'Unitarian Universalism', '2024-11-27 15:16:36', 1),
(19, 'Scientology', '2024-11-27 15:16:36', 1),
(20, 'Druze', '2024-11-27 15:16:36', 1);

--
-- Triggers `religion`
--
DROP TRIGGER IF EXISTS `religion_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `religion_trigger_insert` AFTER INSERT ON `religion` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Religion created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('religion', NEW.religion_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `religion_trigger_update`;
DELIMITER $$
CREATE TRIGGER `religion_trigger_update` AFTER UPDATE ON `religion` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'Religion changed.<br/><br/>';

    IF NEW.religion_name <> OLD.religion_name THEN
        SET audit_log = CONCAT(audit_log, "Religion Name: ", OLD.religion_name, " -> ", NEW.religion_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Religion changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('religion', NEW.religion_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END
$$
DELIMITER ;

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
(1, 'Administrator', 'Full access to all features and data within the system. This role have similar access levels to the Admin but is not as powerful as the Super Admin.', '2024-11-07 10:43:23', 1);

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
(1, 1, 'Administrator', 1, 'App Module', 1, 1, 1, 1, 1, 1, 1, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(2, 1, 'Administrator', 2, 'Settings', 1, 0, 0, 0, 0, 0, 0, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(3, 1, 'Administrator', 3, 'Users & Companies', 1, 0, 0, 0, 0, 0, 0, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 1),
(4, 1, 'Administrator', 4, 'User Account', 1, 1, 1, 1, 1, 1, 1, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(5, 1, 'Administrator', 5, 'Company', 1, 1, 1, 1, 1, 1, 1, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(6, 1, 'Administrator', 6, 'Role', 1, 1, 1, 1, 1, 1, 1, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(7, 1, 'Administrator', 7, 'User Interface', 1, 0, 0, 0, 0, 0, 0, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 1),
(8, 1, 'Administrator', 8, 'Menu Item', 1, 1, 1, 1, 1, 1, 1, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(9, 1, 'Administrator', 9, 'System Action', 1, 1, 1, 1, 1, 1, 1, '2024-11-07 10:43:23', '2024-11-07 10:43:23', 2),
(19, 1, 'Administrator', 10, 'Account Settings', 1, 1, 0, 0, 0, 0, 1, '2024-11-12 15:33:52', '2024-11-12 15:33:52', 2),
(20, 1, 'Administrator', 11, 'Configurations', 1, 0, 0, 0, 0, 0, 0, '2024-11-14 11:49:21', '2024-11-14 11:49:21', 2),
(21, 1, 'Administrator', 12, 'Localization', 1, 0, 0, 0, 0, 0, 0, '2024-11-14 11:56:29', '2024-11-14 11:56:29', 2),
(22, 1, 'Administrator', 13, 'Country', 1, 1, 1, 1, 1, 1, 1, '2024-11-14 11:57:23', '2024-11-14 11:57:23', 2),
(23, 1, 'Administrator', 14, 'State', 1, 1, 1, 1, 1, 1, 1, '2024-11-14 12:13:08', '2024-11-14 12:13:08', 2),
(24, 1, 'Administrator', 15, 'City', 1, 1, 1, 1, 1, 1, 1, '2024-11-14 12:14:09', '2024-11-14 12:14:09', 2),
(25, 1, 'Administrator', 16, 'Currency', 1, 1, 1, 1, 1, 1, 0, '2024-11-14 12:16:35', '2024-11-14 12:16:35', 2),
(26, 1, 'Administrator', 17, 'Data Classification', 1, 0, 0, 0, 0, 0, 0, '2024-11-15 16:41:51', '2024-11-15 16:41:51', 2),
(27, 1, 'Administrator', 18, 'File Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-15 16:42:56', '2024-11-15 16:42:56', 2),
(28, 1, 'Administrator', 19, 'File Extension', 1, 1, 1, 1, 1, 1, 1, '2024-11-15 16:43:35', '2024-11-15 16:43:35', 2),
(29, 1, 'Administrator', 20, 'Upload Setting', 1, 1, 1, 1, 1, 1, 1, '2024-11-18 14:42:39', '2024-11-18 14:42:39', 2),
(30, 1, 'Administrator', 21, 'Security Setting', 1, 1, 0, 0, 0, 0, 0, '2024-11-20 16:48:41', '2024-11-20 16:48:41', 2),
(31, 1, 'Administrator', 22, 'Email Setting', 1, 1, 1, 1, 1, 1, 1, '2024-11-22 10:39:32', '2024-11-22 10:39:32', 2),
(32, 1, 'Administrator', 23, 'Notification Setting', 1, 1, 1, 1, 1, 1, 1, '2024-11-22 14:29:34', '2024-11-22 14:29:34', 2),
(33, 1, 'Administrator', 24, 'Employee', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 14:34:49', '2024-11-25 14:34:49', 2),
(34, 1, 'Administrator', 26, 'Bank', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:15:41', '2024-11-25 15:15:41', 2),
(35, 1, 'Administrator', 27, 'Bank Account Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:15:59', '2024-11-25 15:15:59', 2),
(36, 1, 'Administrator', 25, 'Banking', 1, 0, 0, 0, 0, 0, 0, '2024-11-25 15:16:18', '2024-11-25 15:16:18', 2),
(37, 1, 'Administrator', 28, 'Contact Information', 1, 0, 0, 0, 0, 0, 0, '2024-11-25 15:18:32', '2024-11-25 15:18:32', 2),
(38, 1, 'Administrator', 29, 'Address Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:19:09', '2024-11-25 15:19:09', 2),
(39, 1, 'Administrator', 30, 'Contact Information Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:20:01', '2024-11-25 15:20:01', 2),
(40, 1, 'Administrator', 31, 'Language Settings', 1, 0, 0, 0, 0, 0, 0, '2024-11-25 15:23:22', '2024-11-25 15:23:22', 2),
(41, 1, 'Administrator', 32, 'Language', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:23:47', '2024-11-25 15:23:47', 2),
(42, 1, 'Administrator', 33, 'Language Proficiency', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:24:26', '2024-11-25 15:24:26', 2),
(43, 1, 'Administrator', 34, 'Profile Attribute', 1, 0, 0, 0, 0, 0, 0, '2024-11-25 15:27:20', '2024-11-25 15:27:20', 2),
(44, 1, 'Administrator', 35, 'Blood Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:27:54', '2024-11-25 15:27:54', 2),
(45, 1, 'Administrator', 36, 'Civil Status', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:28:25', '2024-11-25 15:28:25', 2),
(46, 1, 'Administrator', 37, 'Educational Stage', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:28:58', '2024-11-25 15:28:58', 2),
(47, 1, 'Administrator', 38, 'Gender', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:29:29', '2024-11-25 15:29:29', 2),
(48, 1, 'Administrator', 39, 'Credential Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:30:04', '2024-11-25 15:30:04', 2),
(49, 1, 'Administrator', 40, 'Relationship', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:30:43', '2024-11-25 15:30:43', 2),
(50, 1, 'Administrator', 41, 'Religion', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:31:25', '2024-11-25 15:31:25', 2),
(51, 1, 'Administrator', 42, 'HR Configurations', 1, 0, 0, 0, 0, 0, 0, '2024-11-25 15:33:39', '2024-11-25 15:33:39', 2),
(52, 1, 'Administrator', 43, 'Department', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:36:33', '2024-11-25 15:36:33', 2),
(53, 1, 'Administrator', 44, 'Departure Reason', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:38:35', '2024-11-25 15:38:35', 2),
(54, 1, 'Administrator', 45, 'Employment Location Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:39:52', '2024-11-25 15:39:52', 2),
(55, 1, 'Administrator', 46, 'Employment Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:40:45', '2024-11-25 15:40:45', 2),
(56, 1, 'Administrator', 47, 'Job Position', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:42:09', '2024-11-25 15:42:09', 2),
(57, 1, 'Administrator', 48, 'Work Location', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:43:27', '2024-11-25 15:43:27', 2),
(58, 1, 'Administrator', 49, 'Work Schedule Type', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:45:07', '2024-11-25 15:45:07', 2),
(59, 1, 'Administrator', 50, 'Work Schedule', 1, 1, 1, 1, 1, 1, 1, '2024-11-25 15:45:53', '2024-11-25 15:45:53', 2);

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
  `last_log_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_system_action_permission`
--

INSERT INTO `role_system_action_permission` (`role_system_action_permission_id`, `role_id`, `role_name`, `system_action_id`, `system_action_name`, `system_action_access`, `date_assigned`, `created_date`, `last_log_by`) VALUES
(1, 1, 'Administrator', 1, 'Activate User Account', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(2, 1, 'Administrator', 2, 'Deactivate User Account', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(3, 1, 'Administrator', 3, 'Lock User Account', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(4, 1, 'Administrator', 4, 'Unlock User Account', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(5, 1, 'Administrator', 5, 'Add Role User Account', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(6, 1, 'Administrator', 6, 'Delete Role User Account', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(7, 1, 'Administrator', 7, 'Add Role Access', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(8, 1, 'Administrator', 8, 'Update Role Access', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(9, 1, 'Administrator', 9, 'Delete Role Access', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(10, 1, 'Administrator', 10, 'Add Role System Action Access', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(11, 1, 'Administrator', 11, 'Update Role System Action Access', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL),
(12, 1, 'Administrator', 12, 'Delete Role System Action Access', 1, '2024-11-26 13:51:42', '2024-11-26 13:51:42', NULL);

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
(2, 1, 'Administrator', 2, 'Administrator', '2024-11-11 10:34:23', '2024-11-11 10:34:23', 1);

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
(3, 'Default Forgot Password Link', 'http://localhost/digify_v2/password-reset.php?id=', '2024-10-13 16:13:00', 2),
(4, 'Password Expiry Duration', '180', '2024-10-13 16:13:00', 1),
(5, 'Session Timeout Duration', '240', '2024-10-13 16:13:00', 1),
(6, 'OTP Duration', '5', '2024-10-13 16:13:00', 2),
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
-- Table structure for table `state`
--

DROP TABLE IF EXISTS `state`;
CREATE TABLE `state` (
  `state_id` int(10) UNSIGNED NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `state_name`, `country_id`, `country_name`, `created_date`, `last_log_by`) VALUES
(4, 'Nueva Ecija', 4, 'Philippines', '2024-11-14 15:58:30', 2);

--
-- Triggers `state`
--
DROP TRIGGER IF EXISTS `state_trigger_insert`;
DELIMITER $$
CREATE TRIGGER `state_trigger_insert` AFTER INSERT ON `state` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'State created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('state', NEW.state_id, audit_log, NEW.last_log_by, NOW());
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `state_trigger_update`;
DELIMITER $$
CREATE TRIGGER `state_trigger_update` AFTER UPDATE ON `state` FOR EACH ROW BEGIN
    DECLARE audit_log TEXT DEFAULT 'State changed.<br/><br/>';

    IF NEW.state_name <> OLD.state_name THEN
        SET audit_log = CONCAT(audit_log, "State Name: ", OLD.state_name, " -> ", NEW.state_name, "<br/>");
    END IF;

    IF NEW.country_name <> OLD.country_name THEN
        SET audit_log = CONCAT(audit_log, "Country: ", OLD.country_name, " -> ", NEW.country_name, "<br/>");
    END IF;
    
    IF audit_log <> 'State changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('state', NEW.state_id, audit_log, NEW.last_log_by, NOW());
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
(1, 'Activate User Account', 'Access to activate the user account.', '2024-11-26 13:51:56', 1),
(2, 'Deactivate User Account', 'Access to deactivate the user account.', '2024-11-26 13:51:56', 1),
(3, 'Lock User Account', 'Access to lock the user account.', '2024-11-26 13:51:56', 1),
(4, 'Unlock User Account', 'Access to unlock the user account.', '2024-11-26 13:51:56', 1),
(5, 'Add Role User Account', 'Access to assign roles to user account.', '2024-11-26 13:51:56', 1),
(6, 'Delete Role User Account', 'Access to delete roles to user account.', '2024-11-26 13:51:56', 1),
(7, 'Add Role Access', 'Access to add role access.', '2024-11-26 13:51:56', 1),
(8, 'Update Role Access', 'Access to update role access.', '2024-11-26 13:51:56', 1),
(9, 'Delete Role Access', 'Access to delete role access.', '2024-11-26 13:51:56', 1),
(10, 'Add Role System Action Access', 'Access to add the role system action access.', '2024-11-26 13:51:56', 1),
(11, 'Update Role System Action Access', 'Access to update the role system action access.', '2024-11-26 13:51:56', 1),
(12, 'Delete Role System Action Access', 'Access to delete the role system action access.', '2024-11-26 13:51:56', 1);

-- --------------------------------------------------------

--
-- Table structure for table `system_subscription`
--

DROP TABLE IF EXISTS `system_subscription`;
CREATE TABLE `system_subscription` (
  `system_subscription_id` int(11) NOT NULL,
  `system_subscription_code` text NOT NULL,
  `subscription_tier` varchar(500) DEFAULT NULL,
  `billing_cycle` varchar(500) DEFAULT NULL,
  `subscription_validity` varchar(500) DEFAULT NULL,
  `no_users` varchar(500) DEFAULT NULL,
  `subscription_status` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'App Logo', 'Sets the upload setting when uploading app logo.', 800, '2024-11-18 17:01:34', 1),
(2, 'Internal Notes Attachment', 'Sets the upload setting when uploading internal notes attachement.', 800, '2024-11-18 17:01:34', 1),
(3, 'Import File', 'Sets the upload setting when importing data.', 800, '2024-11-18 17:01:34', 2),
(4, 'User Account Profile Picture', 'Sets the upload setting when uploading user account profile picture.', 800, '2024-11-18 17:01:34', 1),
(5, 'Company Logo', 'Sets the upload setting when uploading company logo.', 800, '2024-11-18 17:01:34', 1);

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
(1, 1, 'App Logo', 63, 'PNG', 'png', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(2, 1, 'App Logo', 61, 'JPG', 'jpg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(3, 1, 'App Logo', 62, 'JPEG', 'jpeg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(4, 2, 'Internal Notes Attachment', 63, 'PNG', 'png', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(5, 2, 'Internal Notes Attachment', 61, 'JPG', 'jpg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(6, 2, 'Internal Notes Attachment', 62, 'JPEG', 'jpeg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(7, 2, 'Internal Notes Attachment', 127, 'PDF', 'pdf', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(8, 2, 'Internal Notes Attachment', 125, 'DOC', 'doc', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(9, 2, 'Internal Notes Attachment', 125, 'DOCX', 'docx', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(10, 2, 'Internal Notes Attachment', 130, 'TXT', 'txt', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(11, 2, 'Internal Notes Attachment', 92, 'XLS', 'xls', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(12, 2, 'Internal Notes Attachment', 94, 'XLSX', 'xlsx', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(13, 2, 'Internal Notes Attachment', 89, 'PPT', 'ppt', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(14, 2, 'Internal Notes Attachment', 90, 'PPTX', 'pptx', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(15, 3, 'Import File', 25, 'CSV', 'csv', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(16, 4, 'User Account Profile Picture', 63, 'PNG', 'png', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(17, 4, 'User Account Profile Picture', 61, 'JPG', 'jpg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(18, 4, 'User Account Profile Picture', 62, 'JPEG', 'jpeg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(19, 5, 'Company Logo', 63, 'PNG', 'png', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(20, 5, 'Company Logo', 61, 'JPG', 'jpg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1),
(21, 5, 'Company Logo', 62, 'JPEG', 'jpeg', '2024-11-18 17:01:34', '2024-11-18 17:01:34', 1);

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
  `phone` varchar(50) DEFAULT NULL,
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
  `created_date` datetime DEFAULT current_timestamp(),
  `last_log_by` int(10) UNSIGNED DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_account_id`, `file_as`, `email`, `username`, `password`, `profile_picture`, `phone`, `locked`, `active`, `last_failed_login_attempt`, `failed_login_attempts`, `last_connection_date`, `password_expiry_date`, `reset_token`, `reset_token_expiry_date`, `receive_notification`, `two_factor_auth`, `otp`, `otp_expiry_date`, `failed_otp_attempts`, `last_password_change`, `account_lock_duration`, `last_password_reset`, `multiple_session`, `session_token`, `created_date`, `last_log_by`) VALUES
(1, 'Digify Bot', 'digifybot@gmail.com', 'digifybot', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', NULL, NULL, 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', 'hgS2I4DCVvc958Llg2PKCHdKnnfSLJu1zrJUL4SG0NI%3D', NULL, NULL, NULL, 'aUIRg2jhRcYVcr0%2BiRDl98xjv81aR4Ux63bP%2BF2hQbE%3D', NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', NULL, NULL, NULL, NULL, NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', NULL, '2024-11-07 14:09:59', 2),
(2, 'Administrator', 'lawrenceagulto.317@gmail.com', 'ldagulto', 'SMg7mIbHqD17ZNzk4pUSHKxR2Nfkv8wVWoIhOMauCpA%3D', '../settings/user-account/profile_picture/2/TOzfy.png', '09399108659', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20', '0000-00-00 00:00:00', '', '2024-11-25 11:44:20', 'IdZyoPwFg7Zx6PdFQXTLnK4GDFGM%2F5%2B538NQXWe0fRw%3D', NULL, NULL, 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D', 'KhYNEpk%2BfHBo7mnUZcNgkjIE4glzNH0tuertF2JjmgQ%3D', 'gXp3Xx315Z6mD5poPARBwk6LYfK1qH63jB14fwJVKys%3D', 'q3JpeTjLIph%2B43%2BzoWKSkp9sBJSwJQ2llzgDQXMG%2B5vVUhOOsArBjGo5a83MG7mh', 'DjTtk1lGlRza%2FA7zImkKgcjJJL%2FRT3XlgPhcbRx%2BfnM%3D', NULL, NULL, NULL, 'obZjVWYuZ2bMQotHXebKUp9kMtZzPxCtWBJ1%2BLbJKfU%3D', 'MYOQcjJ5EPGwBwwF9ry7pXNWjpDiRv%2F%2Ff3PP3yJvus4%3D', '2024-11-07 14:09:59', 2);

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

    IF NEW.phone <> OLD.phone THEN
        SET audit_log = CONCAT(audit_log, "Phone: ", OLD.phone, " -> ", NEW.phone, "<br/>");
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
-- Indexes for table `address_type`
--
ALTER TABLE `address_type`
  ADD PRIMARY KEY (`address_type_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `address_type_index_address_type_id` (`address_type_id`);

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
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `bank_index_bank_id` (`bank_id`);

--
-- Indexes for table `bank_account_type`
--
ALTER TABLE `bank_account_type`
  ADD PRIMARY KEY (`bank_account_type_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `bank_account_type_index_bank_account_type_id` (`bank_account_type_id`);

--
-- Indexes for table `blood_type`
--
ALTER TABLE `blood_type`
  ADD PRIMARY KEY (`blood_type_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `blood_type_index_blood_type_id` (`blood_type_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `city_index_city_id` (`city_id`),
  ADD KEY `city_index_state_id` (`state_id`),
  ADD KEY `city_index_country_id` (`country_id`);

--
-- Indexes for table `civil_status`
--
ALTER TABLE `civil_status`
  ADD PRIMARY KEY (`civil_status_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `civil_status_index_civil_status_id` (`civil_status_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `company_index_company_id` (`company_id`),
  ADD KEY `company_index_city_id` (`city_id`),
  ADD KEY `company_index_state_id` (`state_id`),
  ADD KEY `company_index_country_id` (`country_id`),
  ADD KEY `company_index_currency_id` (`currency_id`);

--
-- Indexes for table `contact_information_type`
--
ALTER TABLE `contact_information_type`
  ADD PRIMARY KEY (`contact_information_type_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `contact_information_type_index_contact_information_type_id` (`contact_information_type_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`country_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `country_index_country_id` (`country_id`);

--
-- Indexes for table `credential_type`
--
ALTER TABLE `credential_type`
  ADD PRIMARY KEY (`credential_type_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `credential_type_index_credential_type_id` (`credential_type_id`);

--
-- Indexes for table `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`currency_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `currency_index_currency_id` (`currency_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `department_index_department_id` (`department_id`),
  ADD KEY `department_index_parent_department_id` (`parent_department_id`),
  ADD KEY `department_index_manager_id` (`manager_id`);

--
-- Indexes for table `educational_stage`
--
ALTER TABLE `educational_stage`
  ADD PRIMARY KEY (`educational_stage_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `educational_stage_index_educational_stage_id` (`educational_stage_id`);

--
-- Indexes for table `email_setting`
--
ALTER TABLE `email_setting`
  ADD PRIMARY KEY (`email_setting_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `email_setting_index_email_setting_id` (`email_setting_id`);

--
-- Indexes for table `file_extension`
--
ALTER TABLE `file_extension`
  ADD PRIMARY KEY (`file_extension_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `file_extension_index_file_extension_id` (`file_extension_id`),
  ADD KEY `file_extension_index_file_type_id` (`file_type_id`);

--
-- Indexes for table `file_type`
--
ALTER TABLE `file_type`
  ADD PRIMARY KEY (`file_type_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `file_type_index_file_type_id` (`file_type_id`);

--
-- Indexes for table `gender`
--
ALTER TABLE `gender`
  ADD PRIMARY KEY (`gender_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `gender_index_gender_id` (`gender_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`language_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `language_index_language_id` (`language_id`);

--
-- Indexes for table `language_proficiency`
--
ALTER TABLE `language_proficiency`
  ADD PRIMARY KEY (`language_proficiency_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `language_proficiency_index_language_proficiency_id` (`language_proficiency_id`);

--
-- Indexes for table `login_session`
--
ALTER TABLE `login_session`
  ADD PRIMARY KEY (`login_session_id`),
  ADD KEY `login_session_index_login_session_id` (`login_session_id`),
  ADD KEY `login_session_index_user_account_id` (`user_account_id`);

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
-- Indexes for table `relationship`
--
ALTER TABLE `relationship`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `relationship_index_relationship_id` (`relationship_id`);

--
-- Indexes for table `religion`
--
ALTER TABLE `religion`
  ADD PRIMARY KEY (`religion_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `religion_index_religion_id` (`religion_id`);

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
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`state_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `state_index_state_id` (`state_id`),
  ADD KEY `state_index_country_id` (`country_id`);

--
-- Indexes for table `system_action`
--
ALTER TABLE `system_action`
  ADD PRIMARY KEY (`system_action_id`),
  ADD KEY `last_log_by` (`last_log_by`),
  ADD KEY `system_action_index_system_action_id` (`system_action_id`);

--
-- Indexes for table `system_subscription`
--
ALTER TABLE `system_subscription`
  ADD PRIMARY KEY (`system_subscription_id`),
  ADD KEY `system_subscription_index_system_subscription_id` (`system_subscription_id`),
  ADD KEY `system_subscription_index_system_subscription_code` (`system_subscription_code`(768)),
  ADD KEY `system_subscription_index_subscription_tier` (`subscription_tier`),
  ADD KEY `system_subscription_index_subscription_status` (`subscription_status`),
  ADD KEY `system_subscription_index_subscription_validity` (`subscription_validity`),
  ADD KEY `system_subscription_index_no_users` (`no_users`);

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
-- AUTO_INCREMENT for table `address_type`
--
ALTER TABLE `address_type`
  MODIFY `address_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `app_module`
--
ALTER TABLE `app_module`
  MODIFY `app_module_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=610;

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `bank_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `bank_account_type`
--
ALTER TABLE `bank_account_type`
  MODIFY `bank_account_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `blood_type`
--
ALTER TABLE `blood_type`
  MODIFY `blood_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `civil_status`
--
ALTER TABLE `civil_status`
  MODIFY `civil_status_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_information_type`
--
ALTER TABLE `contact_information_type`
  MODIFY `contact_information_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `country_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `credential_type`
--
ALTER TABLE `credential_type`
  MODIFY `credential_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `currency`
--
ALTER TABLE `currency`
  MODIFY `currency_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `educational_stage`
--
ALTER TABLE `educational_stage`
  MODIFY `educational_stage_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `email_setting`
--
ALTER TABLE `email_setting`
  MODIFY `email_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `file_extension`
--
ALTER TABLE `file_extension`
  MODIFY `file_extension_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `file_type`
--
ALTER TABLE `file_type`
  MODIFY `file_type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `gender`
--
ALTER TABLE `gender`
  MODIFY `gender_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `language_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `language_proficiency`
--
ALTER TABLE `language_proficiency`
  MODIFY `language_proficiency_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `login_session`
--
ALTER TABLE `login_session`
  MODIFY `login_session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `menu_group`
--
ALTER TABLE `menu_group`
  MODIFY `menu_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_item`
--
ALTER TABLE `menu_item`
  MODIFY `menu_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `notification_setting`
--
ALTER TABLE `notification_setting`
  MODIFY `notification_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification_setting_email_template`
--
ALTER TABLE `notification_setting_email_template`
  MODIFY `notification_setting_email_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notification_setting_sms_template`
--
ALTER TABLE `notification_setting_sms_template`
  MODIFY `notification_setting_sms_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notification_setting_system_template`
--
ALTER TABLE `notification_setting_system_template`
  MODIFY `notification_setting_system_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `password_history_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `relationship`
--
ALTER TABLE `relationship`
  MODIFY `relationship_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `religion`
--
ALTER TABLE `religion`
  MODIFY `religion_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `role_permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `role_system_action_permission`
--
ALTER TABLE `role_system_action_permission`
  MODIFY `role_system_action_permission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `role_user_account`
--
ALTER TABLE `role_user_account`
  MODIFY `role_user_account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `security_setting`
--
ALTER TABLE `security_setting`
  MODIFY `security_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `state_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `system_action`
--
ALTER TABLE `system_action`
  MODIFY `system_action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `system_subscription`
--
ALTER TABLE `system_subscription`
  MODIFY `system_subscription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `upload_setting`
--
ALTER TABLE `upload_setting`
  MODIFY `upload_setting_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `upload_setting_file_extension`
--
ALTER TABLE `upload_setting_file_extension`
  MODIFY `upload_setting_file_extension_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address_type`
--
ALTER TABLE `address_type`
  ADD CONSTRAINT `address_type_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

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
-- Constraints for table `bank`
--
ALTER TABLE `bank`
  ADD CONSTRAINT `bank_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `bank_account_type`
--
ALTER TABLE `bank_account_type`
  ADD CONSTRAINT `bank_account_type_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `blood_type`
--
ALTER TABLE `blood_type`
  ADD CONSTRAINT `blood_type_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `city_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `city_ibfk_2` FOREIGN KEY (`state_id`) REFERENCES `state` (`state_id`),
  ADD CONSTRAINT `city_ibfk_3` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`);

--
-- Constraints for table `civil_status`
--
ALTER TABLE `civil_status`
  ADD CONSTRAINT `civil_status_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `company_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `city` (`city_id`),
  ADD CONSTRAINT `company_ibfk_3` FOREIGN KEY (`state_id`) REFERENCES `state` (`state_id`),
  ADD CONSTRAINT `company_ibfk_4` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`);

--
-- Constraints for table `contact_information_type`
--
ALTER TABLE `contact_information_type`
  ADD CONSTRAINT `contact_information_type_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `country`
--
ALTER TABLE `country`
  ADD CONSTRAINT `country_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `credential_type`
--
ALTER TABLE `credential_type`
  ADD CONSTRAINT `credential_type_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `currency`
--
ALTER TABLE `currency`
  ADD CONSTRAINT `currency_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `educational_stage`
--
ALTER TABLE `educational_stage`
  ADD CONSTRAINT `educational_stage_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `email_setting`
--
ALTER TABLE `email_setting`
  ADD CONSTRAINT `email_setting_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `file_extension`
--
ALTER TABLE `file_extension`
  ADD CONSTRAINT `file_extension_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `file_type`
--
ALTER TABLE `file_type`
  ADD CONSTRAINT `file_type_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `gender`
--
ALTER TABLE `gender`
  ADD CONSTRAINT `gender_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `language`
--
ALTER TABLE `language`
  ADD CONSTRAINT `language_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `language_proficiency`
--
ALTER TABLE `language_proficiency`
  ADD CONSTRAINT `language_proficiency_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `login_session`
--
ALTER TABLE `login_session`
  ADD CONSTRAINT `login_session_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`user_account_id`);

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
-- Constraints for table `relationship`
--
ALTER TABLE `relationship`
  ADD CONSTRAINT `relationship_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `religion`
--
ALTER TABLE `religion`
  ADD CONSTRAINT `religion_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `role_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`menu_item_id`),
  ADD CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `role_permission_ibfk_3` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `role_system_action_permission`
--
ALTER TABLE `role_system_action_permission`
  ADD CONSTRAINT `role_system_action_permission_ibfk_1` FOREIGN KEY (`system_action_id`) REFERENCES `system_action` (`system_action_id`),
  ADD CONSTRAINT `role_system_action_permission_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `role_system_action_permission_ibfk_3` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `role_user_account`
--
ALTER TABLE `role_user_account`
  ADD CONSTRAINT `role_user_account_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `role_user_account_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`),
  ADD CONSTRAINT `role_user_account_ibfk_3` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `state`
--
ALTER TABLE `state`
  ADD CONSTRAINT `state_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`),
  ADD CONSTRAINT `state_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`);

--
-- Constraints for table `system_action`
--
ALTER TABLE `system_action`
  ADD CONSTRAINT `system_action_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `upload_setting`
--
ALTER TABLE `upload_setting`
  ADD CONSTRAINT `upload_setting_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`last_log_by`) REFERENCES `user_account` (`user_account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
