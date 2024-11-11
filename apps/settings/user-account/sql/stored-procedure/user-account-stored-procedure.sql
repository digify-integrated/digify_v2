DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkUserAccountExist//
CREATE PROCEDURE checkUserAccountExist(
    IN p_user_account_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id = p_user_account_id;
END //

DROP PROCEDURE IF EXISTS checkUserAccountUsernameExist//
CREATE PROCEDURE checkUserAccountUsernameExist(
    IN p_user_account_id INT,
    IN p_username VARCHAR(100)
)
BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id != p_user_account_id AND username = p_username;
END //

DROP PROCEDURE IF EXISTS checkUserAccountEmailExist//
CREATE PROCEDURE checkUserAccountEmailExist(
    IN p_user_account_id INT,
    IN p_email VARCHAR(255)
)
BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id != p_user_account_id AND email = p_email;
END //

DROP PROCEDURE IF EXISTS checkUserAccountPhoneExist//
CREATE PROCEDURE checkUserAccountPhoneExist(
    IN p_user_account_id INT,
    IN p_phone VARCHAR(50)
)
BEGIN
	SELECT COUNT(*) AS total
    FROM user_account
    WHERE user_account_id != p_user_account_id AND phone = p_phone;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Add Stored Procedure */

DROP PROCEDURE IF EXISTS addUserAccount//
CREATE PROCEDURE addUserAccount(
    IN p_file_as VARCHAR(300), 
    IN p_email VARCHAR(255), 
    IN p_username VARCHAR(100), 
    IN p_password VARCHAR(255),
    IN p_phone VARCHAR(50), 
    IN p_password_expiry_date VARCHAR(255), 
    IN p_last_log_by INT, 
    OUT p_new_user_account_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO user_account (file_as, email, username, password, phone, password_expiry_date, last_log_by) 
    VALUES(p_file_as, p_email, p_username, p_password, p_phone, p_password_expiry_date, p_last_log_by);
        
    SET p_new_user_account_id = LAST_INSERT_ID();

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Procedure */

DROP PROCEDURE IF EXISTS updateUserAccountFullName//
CREATE PROCEDURE updateUserAccountFullName(
	IN p_user_account_id INT, 
	IN p_file_as VARCHAR(300), 
	IN p_last_log_by INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateUserAccountUsername//
CREATE PROCEDURE updateUserAccountUsername(
	IN p_user_account_id INT, 
	IN p_username VARCHAR(100), 
	IN p_last_log_by INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateUserAccountEmailAddress//
CREATE PROCEDURE updateUserAccountEmailAddress(
	IN p_user_account_id INT, 
	IN p_email VARCHAR(255),
	IN p_last_log_by INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateUserAccountPhone//
CREATE PROCEDURE updateUserAccountPhone(
	IN p_user_account_id INT, 
	IN p_phone VARCHAR(50),
	IN p_last_log_by INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateUserAccountPassword//
CREATE PROCEDURE updateUserAccountPassword(
	IN p_user_account_id INT, 
	IN p_password VARCHAR(255),
    IN p_password_expiry_date VARCHAR(255), 
	IN p_last_log_by INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateProfilePicture//
CREATE PROCEDURE updateProfilePicture(
	IN p_user_account_id INT, 
	IN p_profile_picture VARCHAR(500), 
	IN p_last_log_by INT
)
BEGIN
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
END //

DROP PROCEDURE IF EXISTS updateTwoFactorAuthenticationStatus//
CREATE PROCEDURE updateTwoFactorAuthenticationStatus(
    IN p_user_account_id INT,
    IN p_two_factor_auth VARCHAR(255),
    IN p_last_log_by INT
)
BEGIN
    UPDATE user_account
    SET two_factor_auth = p_two_factor_auth,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;
END //

DROP PROCEDURE IF EXISTS updateMultipleLoginSessionsStatus//
CREATE PROCEDURE updateMultipleLoginSessionsStatus(IN p_user_account_id INT,
    IN p_multiple_session VARCHAR(255),
    IN p_last_log_by INT
)
BEGIN
    UPDATE user_account
    SET multiple_session = p_multiple_session,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;
END //

DROP PROCEDURE IF EXISTS updateUserAccountStatus//
CREATE PROCEDURE updateUserAccountStatus(
    IN p_user_account_id INT,
    IN p_active VARCHAR(255),
    IN p_last_log_by INT
)
BEGIN
    UPDATE user_account
    SET active = p_active,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;
END //

DROP PROCEDURE IF EXISTS updateUserAccountLock//
CREATE PROCEDURE updateUserAccountLock(
    IN p_user_account_id INT,
    IN p_locked VARCHAR(255),
    IN p_account_lock_duration VARCHAR(255),
    IN p_last_log_by INT
    )
BEGIN
	UPDATE user_account 
    SET locked = p_locked, account_lock_duration = p_account_lock_duration 
    WHERE user_account_id = p_user_account_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteUserAccount//
CREATE PROCEDURE deleteUserAccount(
    IN p_user_account_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_user_account WHERE user_account_id = p_user_account_id;
    DELETE FROM user_account WHERE user_account_id = p_user_account_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getUserAccount//
CREATE PROCEDURE getUserAccount(
    IN p_user_account_id INT
)
BEGIN
	SELECT * FROM user_account
	WHERE user_account_id = p_user_account_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateUserAccountTable//
CREATE PROCEDURE generateUserAccountTable()
BEGIN
	SELECT user_account_id, file_as, username, email, profile_picture, locked, active, password_expiry_date, last_connection_date 
    FROM user_account
    WHERE user_account_id NOT IN (1, 2)
    ORDER BY user_account_id;
END //

DROP PROCEDURE IF EXISTS generateUserAccountOptions//
CREATE PROCEDURE generateUserAccountOptions()
BEGIN
	SELECT user_account_id, user_account_name 
    FROM user_account 
    ORDER BY user_account_name;
END //

DROP PROCEDURE IF EXISTS generateUserAccountLoginSession//
CREATE PROCEDURE generateUserAccountLoginSession(
    IN p_user_account_id INT
)
BEGIN
	SELECT location, login_status, device, ip_address, login_date 
    FROM login_session
    WHERE user_account_id = p_user_account_id
    ORDER BY login_date DESC;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */