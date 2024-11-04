DELIMITER //

/* Check Stored Procedure */

CREATE PROCEDURE checkEmailSettingExist(IN p_email_setting_id INT)
BEGIN
	SELECT COUNT(*) AS total
    FROM email_setting
    WHERE email_setting_id = p_email_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Insert Stored Procedure */

CREATE PROCEDURE insertEmailSetting(IN p_email_setting_name VARCHAR(100), IN p_email_setting_description VARCHAR(200), IN p_mail_host VARCHAR(100), IN p_port VARCHAR(10), IN p_smtp_auth INT(1), IN p_smtp_auto_tls INT(1), IN p_mail_username VARCHAR(200), IN p_mail_password VARCHAR(250), IN p_mail_encryption VARCHAR(20), IN p_mail_from_name VARCHAR(200), IN p_mail_from_email VARCHAR(200), IN p_last_log_by INT, OUT p_email_setting_id INT)
BEGIN
    INSERT INTO email_setting (email_setting_name, email_setting_description, mail_host, port, smtp_auth, smtp_auto_tls, mail_username, mail_password, mail_encryption, mail_from_name, mail_from_email, last_log_by) 
	VALUES(p_email_setting_name, p_email_setting_description, p_mail_host, p_port, p_smtp_auth, p_smtp_auto_tls, p_mail_username, p_mail_password, p_mail_encryption, p_mail_from_name, p_mail_from_email, p_last_log_by);
	
    SET p_email_setting_id = LAST_INSERT_ID();
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedure */

CREATE PROCEDURE updateEmailSetting(IN p_email_setting_id INT, IN p_email_setting_name VARCHAR(100), IN p_email_setting_description VARCHAR(200), IN p_mail_host VARCHAR(100), IN p_port VARCHAR(10), IN p_smtp_auth INT(1), IN p_smtp_auto_tls INT(1), IN p_mail_username VARCHAR(200), IN p_mail_password VARCHAR(250), IN p_mail_encryption VARCHAR(20), IN p_mail_from_name VARCHAR(200), IN p_mail_from_email VARCHAR(200), IN p_last_log_by INT)
BEGIN
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
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

CREATE PROCEDURE deleteEmailSetting(IN p_email_setting_id INT)
BEGIN
   DELETE FROM email_setting WHERE email_setting_id = p_email_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

CREATE PROCEDURE getEmailSetting(IN p_email_setting_id INT)
BEGIN
	SELECT * FROM email_setting
    WHERE email_setting_id = p_email_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

CREATE PROCEDURE generateEmailSettingTable()
BEGIN
    SELECT email_setting_id, email_setting_name, email_setting_description 
    FROM email_setting
    ORDER BY email_setting_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */