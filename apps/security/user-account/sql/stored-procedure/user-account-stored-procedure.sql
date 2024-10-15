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

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveUserAccount//
CREATE PROCEDURE saveUserAccount(
    IN p_user_account_id INT, 
    IN p_user_account_name VARCHAR(100), 
    IN p_user_account_description VARCHAR(500), 
    IN p_menu_item_id INT, 
    IN p_menu_item_name VARCHAR(100), 
    IN p_order_sequence TINYINT(10), 
    IN p_last_log_by INT, 
    OUT p_new_user_account_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_user_account_id IS NULL OR NOT EXISTS (SELECT 1 FROM user_account WHERE user_account_id = p_user_account_id) THEN
        INSERT INTO user_account (user_account_name, user_account_description, menu_item_id, menu_item_name, order_sequence, last_log_by) 
        VALUES(p_user_account_name, p_user_account_description, p_menu_item_id, p_menu_item_name, p_order_sequence, p_last_log_by);
        
        SET p_new_user_account_id = LAST_INSERT_ID();
    ELSE
        UPDATE user_account
        SET user_account_name = p_user_account_name,
            user_account_description = p_user_account_description,
            menu_item_id = p_menu_item_id,
            menu_item_name = p_menu_item_name,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE user_account_id = p_user_account_id;
        
        UPDATE menu_group
        SET user_account_name = p_user_account_name,
            last_log_by = p_last_log_by
        WHERE user_account_id = p_user_account_id;

        UPDATE menu_item
        SET user_account_name = p_user_account_name,
            last_log_by = p_last_log_by
        WHERE user_account_id = p_user_account_id;

        SET p_new_user_account_id = p_user_account_id;
    END IF;

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