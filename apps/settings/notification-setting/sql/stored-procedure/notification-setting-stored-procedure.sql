DELIMITER //

/* Check Stored Procedure */

CREATE PROCEDURE checkNotificationSettingExist(IN p_notification_setting_id INT)
BEGIN
	SELECT COUNT(*) AS total
    FROM notification_setting
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE checkEmailNotificationTemplateExist(IN p_notification_setting_id INT)
BEGIN
	SELECT COUNT(*) AS total
    FROM notification_setting_email_template
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE checkSystemNotificationTemplateExist(IN p_notification_setting_id INT)
BEGIN
	SELECT COUNT(*) AS total
    FROM notification_setting_system_template
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE checkSMSNotificationTemplateExist(IN p_notification_setting_id INT)
BEGIN
	SELECT COUNT(*) AS total
    FROM notification_setting_sms_template
    WHERE notification_setting_id = p_notification_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Insert Stored Procedure */

CREATE PROCEDURE insertNotificationSetting(IN p_notification_setting_name VARCHAR(100), IN p_notification_setting_description VARCHAR(200), IN p_last_log_by INT, OUT p_notification_setting_id INT)
BEGIN
    INSERT INTO notification_setting (notification_setting_name, notification_setting_description, last_log_by) 
	VALUES(p_notification_setting_name, p_notification_setting_description, p_last_log_by);
	
    SET p_notification_setting_id = LAST_INSERT_ID();
END //

CREATE PROCEDURE insertSystemNotificationTemplate(IN p_notification_setting_id INT, IN p_system_notification_title VARCHAR(200), IN p_system_notification_message VARCHAR(500), IN p_last_log_by INT)
BEGIN
    INSERT INTO notification_setting_system_template (notification_setting_id, system_notification_title, system_notification_message, last_log_by) 
	VALUES(p_notification_setting_id, p_system_notification_title, p_system_notification_message, p_last_log_by);
END //

CREATE PROCEDURE insertEmailNotificationTemplate(IN p_notification_setting_id INT, IN p_email_notification_subject VARCHAR(200), IN p_email_notification_body LONGTEXT, IN p_last_log_by INT)
BEGIN
    INSERT INTO notification_setting_email_template (notification_setting_id, email_notification_subject, email_notification_body, last_log_by) 
	VALUES(p_notification_setting_id, p_email_notification_subject, p_email_notification_body, p_last_log_by);
END //

CREATE PROCEDURE insertSMSNotificationTemplate(IN p_notification_setting_id INT, IN p_sms_notification_message VARCHAR(500), IN p_last_log_by INT)
BEGIN
    INSERT INTO notification_setting_sms_template (notification_setting_id, sms_notification_message, last_log_by) 
	VALUES(p_notification_setting_id, p_sms_notification_message, p_last_log_by);
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedure */

CREATE PROCEDURE updateNotificationSetting(IN p_notification_setting_id INT, IN p_notification_setting_name VARCHAR(100), IN p_notification_setting_description VARCHAR(200), IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting
    SET notification_setting_name = p_notification_setting_name,
        notification_setting_description = p_notification_setting_description,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE updateSystemNotificationChannelStatus(IN p_notification_setting_id INT, IN p_system_notification INT(1), IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting
    SET system_notification = p_system_notification,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE updateEmailNotificationChannelStatus(IN p_notification_setting_id INT, IN p_email_notification INT(1), IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting
    SET email_notification = p_email_notification,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE updateSMSNotificationChannelStatus(IN p_notification_setting_id INT, IN p_sms_notification INT(1), IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting
    SET sms_notification = p_sms_notification,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE updateSystemNotificationTemplate(IN p_notification_setting_id INT, IN p_system_notification_title VARCHAR(200), IN p_system_notification_message VARCHAR(500), IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting_system_template
    SET system_notification_title = p_system_notification_title,
        system_notification_message = p_system_notification_message,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE updateEmailNotificationTemplate(IN p_notification_setting_id INT, IN p_email_notification_subject VARCHAR(200), IN p_email_notification_body LONGTEXT, IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting_email_template
    SET email_notification_subject = p_email_notification_subject,
        email_notification_body = p_email_notification_body,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE updateSMSNotificationTemplate(IN p_notification_setting_id INT, IN p_sms_notification_message VARCHAR(500), IN p_last_log_by INT)
BEGIN
    UPDATE notification_setting_sms_template
    SET sms_notification_message = p_sms_notification_message,
        last_log_by = p_last_log_by
    WHERE notification_setting_id = p_notification_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

CREATE PROCEDURE deleteNotificationSetting(IN p_notification_setting_id INT)
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

CREATE PROCEDURE getNotificationSetting(IN p_notification_setting_id INT)
BEGIN
	SELECT * FROM notification_setting
	WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE getSystemNotificationTemplate(IN p_notification_setting_id INT)
BEGIN
	SELECT * FROM notification_setting_system_template
	WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE getEmailNotificationTemplate(IN p_notification_setting_id INT)
BEGIN
	SELECT * FROM notification_setting_email_template
	WHERE notification_setting_id = p_notification_setting_id;
END //

CREATE PROCEDURE getSMSNotificationTemplate(IN p_notification_setting_id INT)
BEGIN
	SELECT * FROM notification_setting_sms_template
	WHERE notification_setting_id = p_notification_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

CREATE PROCEDURE generateNotificationSettingTable()
BEGIN
    SELECT notification_setting_id, notification_setting_name, notification_setting_description 
    FROM notification_setting
    ORDER BY notification_setting_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */