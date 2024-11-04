/* Email Setting Table */

CREATE TABLE email_setting(
	email_setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	email_setting_name VARCHAR(100) NOT NULL,
	email_setting_description VARCHAR(200) NOT NULL,
	mail_host VARCHAR(100) NOT NULL,
	port VARCHAR(10) NOT NULL,
	smtp_auth INT(1) NOT NULL,
	smtp_auto_tls INT(1) NOT NULL,
	mail_username VARCHAR(200) NOT NULL,
	mail_password VARCHAR(250) NOT NULL,
	mail_encryption VARCHAR(20),
	mail_from_name VARCHAR(200),
	mail_from_email VARCHAR(200),
    created_date DATETIME NOT NULL DEFAULT NOW(),
    last_log_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX email_setting_index_email_setting_id ON email_setting(email_setting_id);

INSERT INTO email_setting (email_setting_name, email_setting_description, mail_host, port, smtp_auth, smtp_auto_tls, mail_username, mail_password, mail_encryption, mail_from_name, mail_from_email, last_log_by) VALUES ('Security Email Setting', '
Email setting for security emails.', 'smtp.hostinger.com', '465', '1', '0', 'cgmi-noreply@christianmotors.ph', 'UsDpF0dYRC6M9v0tT3MHq%2BlrRJu01%2Fb95Dq%2BAeCfu2Y%3D', 'ssl', 'cgmi-noreply@christianmotors.ph', 'cgmi-noreply@christianmotors.ph' , '1');

/* ----------------------------------------------------------------------------------------------------------------------------- */