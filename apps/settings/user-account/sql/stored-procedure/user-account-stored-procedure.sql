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

DROP PROCEDURE IF EXISTS updateAppLogo//
CREATE PROCEDURE updateAppLogo(
	IN p_user_account_id INT, 
	IN p_app_logo VARCHAR(500), 
	IN p_last_log_by INT
)
BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE user_account
    SET app_logo = p_app_logo,
        last_log_by = p_last_log_by
    WHERE user_account_id = p_user_account_id;

    COMMIT;
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
    ORDER BY user_account_id;
END //

DROP PROCEDURE IF EXISTS generateUserAccountOptions//
CREATE PROCEDURE generateUserAccountOptions()
BEGIN
	SELECT user_account_id, user_account_name 
    FROM user_account 
    ORDER BY user_account_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */