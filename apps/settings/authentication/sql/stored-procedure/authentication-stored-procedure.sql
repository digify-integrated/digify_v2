DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkLoginCredentialsExist//
CREATE PROCEDURE checkLoginCredentialsExist(
    IN p_user_account_id INT,
    IN p_credentials VARCHAR(255)
)
BEGIN
    SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id = p_user_account_id
       OR username = BINARY p_credentials
       OR email = BINARY p_credentials;
END //

DROP PROCEDURE IF EXISTS checkAccessRights//
CREATE PROCEDURE checkAccessRights(IN p_user_account_id INT, IN p_menu_item_id INT, IN p_access_type VARCHAR(10))
BEGIN
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
END //

DROP PROCEDURE IF EXISTS checkSystemActionAccessRights//
CREATE PROCEDURE checkSystemActionAccessRights(
    IN p_user_account_id INT,
    IN p_system_action_id INT
)
BEGIN
    SELECT COUNT(role_id) AS total
    FROM role_system_action_permission 
    WHERE system_action_id = p_system_action_id 
    AND system_action_access = 1 
    AND role_id IN (SELECT role_id FROM role_user_account WHERE user_account_id = p_user_account_id);
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getLoginCredentials//
CREATE PROCEDURE getLoginCredentials(
    IN p_user_account_id INT,
    IN p_credentials VARCHAR(255)
)
BEGIN
    SELECT *
    FROM user_account
    WHERE user_account_id = p_user_account_id
       OR username = BINARY p_credentials
       OR email = BINARY p_credentials;
END //

DROP PROCEDURE IF EXISTS getPasswordHistory//
CREATE PROCEDURE getPasswordHistory(IN p_user_account_id INT)
BEGIN
    SELECT password 
    FROM password_history
    WHERE user_account_id = p_user_account_id;
END //

DROP PROCEDURE IF EXISTS getInternalNotesAttachment//
CREATE PROCEDURE getInternalNotesAttachment(
    IN p_internal_notes_id INT
)
BEGIN
	SELECT * FROM internal_notes_attachment
	WHERE internal_notes_id = p_internal_notes_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedure */

DROP PROCEDURE IF EXISTS insertLoginSession//
CREATE PROCEDURE insertLoginSession(
    IN p_user_account_id INT, 
    IN p_location VARCHAR(500), 
    IN p_login_status VARCHAR(50), 
    IN p_device VARCHAR(200),
    IN p_ip_address VARCHAR(50)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO login_session (user_account_id, location, login_status, device, ip_address) 
    VALUES(p_user_account_id, p_location, p_login_status, p_device, p_ip_address);
    
    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedure */

DROP PROCEDURE IF EXISTS updateLoginAttempt//
CREATE PROCEDURE updateLoginAttempt(
    IN p_user_account_id INT, 
    IN p_failed_login_attempts VARCHAR(255), 
    IN p_last_failed_login_attempt DATETIME
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateAccountLock//
CREATE PROCEDURE updateAccountLock(
    IN p_user_account_id INT, 
    IN p_locked VARCHAR(255), 
    IN p_account_lock_duration VARCHAR(255)
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateOTP//
CREATE PROCEDURE updateOTP(
    IN p_user_account_id INT, 
    IN p_otp VARCHAR(255), 
    IN p_otp_expiry_date VARCHAR(255), 
    IN p_failed_otp_attempts VARCHAR(255)
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateLastConnection//
CREATE PROCEDURE updateLastConnection(
    IN p_user_account_id INT, 
    IN p_session_token VARCHAR(255)
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateFailedOTPAttempts//
CREATE PROCEDURE updateFailedOTPAttempts(
    IN p_user_account_id INT,
    IN p_failed_otp_attempts VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;
    
    UPDATE user_account
    SET failed_otp_attempts = p_failed_otp_attempts
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateOTPAsExpired//
CREATE PROCEDURE updateOTPAsExpired(
    IN p_user_account_id INT,
    IN p_otp_expiry_date VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET otp_expiry_date = p_otp_expiry_date
    WHERE user_account_id = p_user_account_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateResetToken//
CREATE PROCEDURE updateResetToken(
    IN p_user_account_id INT, 
    IN p_reset_token VARCHAR(255), 
    IN p_reset_token_expiry_date VARCHAR(255)
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateUserPassword//
CREATE PROCEDURE updateUserPassword(
    IN p_user_account_id INT,
    IN p_password VARCHAR(255), 
    IN p_password_expiry_date VARCHAR(255), 
    IN p_locked VARCHAR(255), 
    IN p_failed_login_attempts VARCHAR(255), 
    IN p_account_lock_duration VARCHAR(255)
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateResetTokenAsExpired//
CREATE PROCEDURE updateResetTokenAsExpired(
    IN p_user_account_id INT,
    IN p_reset_token_expiry_date VARCHAR(255)
)
BEGIN
    UPDATE user_account
    SET reset_token_expiry_date = p_reset_token_expiry_date
    WHERE user_account_id = p_user_account_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Build Stored Procedure */

DROP PROCEDURE IF EXISTS buildAppModuleStack//
CREATE PROCEDURE buildAppModuleStack(
    IN p_user_account_id INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS buildMenuItem//
CREATE PROCEDURE buildMenuItem(
    IN p_user_account_id INT,
    IN p_app_module_id INT
)
BEGIN
    SELECT mi.menu_item_id, mi.menu_item_name, mi.menu_item_url, mi.parent_id, mi.app_module_id, mi.menu_item_icon
    FROM menu_item AS mi
    INNER JOIN role_permission AS mar ON mi.menu_item_id = mar.menu_item_id
    INNER JOIN role_user_account AS ru ON mar.role_id = ru.role_id
    WHERE mar.read_access = 1 AND ru.user_account_id = p_user_account_id AND mi.app_module_id = p_app_module_id
    ORDER BY mi.order_sequence;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

DROP PROCEDURE IF EXISTS generateLogNotes//
CREATE PROCEDURE generateLogNotes(
    IN p_table_name VARCHAR(255),
    IN p_reference_id INT
)
BEGIN
	SELECT log, changed_by, changed_at
    FROM audit_log
    WHERE table_name = p_table_name AND reference_id  = p_reference_id
    ORDER BY changed_at DESC;
END //


DROP PROCEDURE IF EXISTS buildBreadcrumb//
CREATE PROCEDURE buildBreadcrumb(IN pageID INT)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE current_id INT DEFAULT pageID;
    
    DECLARE menu_name VARCHAR(100);
    DECLARE menu_url VARCHAR(50);
    DECLARE parent INT;
    
    DECLARE breadcrumb_cursor CURSOR FOR
        SELECT menu_item_name, menu_item_url, parent_id
        FROM menu_item
        WHERE menu_item_id = current_id;
        
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    CREATE TEMPORARY TABLE IF NOT EXISTS BreadcrumbTrail (
        menu_item_name VARCHAR(100),
        menu_item_url VARCHAR(50)
    );
    
    OPEN breadcrumb_cursor;
    
    read_loop: LOOP
        FETCH breadcrumb_cursor INTO menu_name, menu_url, parent;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        IF current_id != pageID THEN
            INSERT INTO BreadcrumbTrail (menu_item_name, menu_item_url) 
            VALUES (menu_name, menu_url);
        END IF;

        SET current_id = parent;
        
        IF current_id IS NULL THEN
            LEAVE read_loop;
        END IF;
        
        CLOSE breadcrumb_cursor;
        OPEN breadcrumb_cursor;
    END LOOP read_loop;

    CLOSE breadcrumb_cursor;

    SELECT * FROM BreadcrumbTrail ORDER BY FIELD(menu_item_name, menu_item_name);

    DROP TEMPORARY TABLE BreadcrumbTrail;
END//