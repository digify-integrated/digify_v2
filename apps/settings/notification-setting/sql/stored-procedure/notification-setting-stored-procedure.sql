DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkNotificationSettingExist//
CREATE PROCEDURE checkNotificationSettingExist(
    IN p_notification_setting_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM notification_setting
    WHERE notification_setting_id = p_notification_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveNotificationSetting//
CREATE PROCEDURE saveNotificationSetting(
    IN p_notification_setting_id INT, 
    IN p_notification_setting_name VARCHAR(100), 
    IN p_notification_setting_description VARCHAR(200),
    IN p_last_log_by INT, 
    OUT p_new_notification_setting_id INT
)
BEGIN
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
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteNotificationSetting//
CREATE PROCEDURE deleteNotificationSetting(
    IN p_notification_setting_id INT
)
BEGIN
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
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getNotificationSetting//
CREATE PROCEDURE getNotificationSetting(
	IN p_notification_setting_id INT
)
BEGIN
	SELECT * FROM notification_setting
	WHERE notification_setting_id = p_notification_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateNotificationSettingTable//
CREATE PROCEDURE generateNotificationSettingTable()
BEGIN
	SELECT notification_setting_id, notification_setting_name, notification_setting_description
    FROM notification_setting 
    ORDER BY notification_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */