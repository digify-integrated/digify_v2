/* Notification Setting Table */

CREATE TABLE notification_setting(
	notification_setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	notification_setting_name VARCHAR(100) NOT NULL,
	notification_setting_description VARCHAR(200) NOT NULL,
	system_notification INT(1) NOT NULL DEFAULT 1,
	email_notification INT(1) NOT NULL DEFAULT 0,
	sms_notification INT(1) NOT NULL DEFAULT 0,
    created_date DATETIME NOT NULL DEFAULT NOW(),
    last_log_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX notification_setting_index_notification_setting_id ON notification_setting(notification_setting_id);

INSERT INTO `notification_setting` (`notification_setting_id`, `notification_setting_name`, `notification_setting_description`, `system_notification`, `email_notification`, `sms_notification`, `last_log_by`) VALUES
(1, 'Login OTP', 'Notification setting for Login OTP received by the users.', 0, 1, 0, 1),
(2, 'Forgot Password', 'Notification setting when the user initiates forgot password.', 0, 1, 0, 1),
(3, 'Registration Verification', 'Notification setting when the user sign-up for an account.', 0, 1, 0, 1);

CREATE TABLE notification_setting_email_template(
	notification_setting_email_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	notification_setting_id INT UNSIGNED NOT NULL,
	email_notification_subject VARCHAR(200) NOT NULL,
	email_notification_body LONGTEXT NOT NULL,
	email_setting_id INT UNSIGNED NOT NULL,
	email_setting_name VARCHAR(100) NOT NULL,
    created_date DATETIME NOT NULL DEFAULT NOW(),
    last_log_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (notification_setting_id) REFERENCES notification_setting(notification_setting_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX notification_setting_email_index_notification_setting_email_id ON notification_setting_email_template(notification_setting_email_id);
CREATE INDEX notification_setting_email_index_notification_setting_id ON notification_setting_email_template(notification_setting_id);

INSERT INTO `notification_setting_email_template` (`notification_setting_email_id`, `notification_setting_id`, `email_notification_subject`, `email_notification_body`, `email_setting_id`, `email_setting_name`, `last_log_by`) VALUES
(1, 1, 'Login OTP - Secure Access to Your Account', '<p>To ensure the security of your account, we have generated a unique One-Time Password (OTP) for you to use during the login process. Please use the following OTP to access your account:</p>\n<p><br>OTP: <strong>#{OTP_CODE}</strong></p>\n<p><br>Please note that this OTP is valid for &nbsp;<strong>#{OTP_CODE_VALIDITY}</strong>. Once you have logged in successfully, we recommend enabling two-factor authentication for an added layer of security.<br>If you did not initiate this login or believe it was sent to you in error, please disregard this email and delete it immediately. Your account\'s security remains our utmost priority.</p>\n<p>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', 1),
(2, 2, 'Password Reset Request - Action Required', '<p>We received a request to reset your password. To proceed with the password reset, please follow the steps below:</p>\n<ol>\n<li>\n<p>Click on the following link to reset your password:&nbsp; <strong><a href=\"#{RESET_LINK}\">Password Reset Link</a></strong></p>\n</li>\n<li>\n<p>If you did not request this password reset, please ignore this email. Your account remains secure.</p>\n</li>\n</ol>\n<p>Please note that this link is time-sensitive and will expire after <strong>#{RESET_LINK_VALIDITY}</strong>. If you do not reset your password within this timeframe, you may need to request another password reset.</p>\n<p><br>If you did not initiate this password reset request or believe it was sent to you in error, please disregard this email and delete it immediately. Your account\'s security remains our utmost priority.<br><br>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', 1),
(3, 3, 'Sign Up Verification - Action Required', '<p>Thank you for registering! To complete your registration, please verify your email address by clicking the link below:</p>\n<p><a href=\"#{REGISTRATION_VERIFICATION_LINK}\">Click to verify your account</a></p>\n<p>Important: This link is time-sensitive and will expire after #{REGISTRATION_VERIFICATION_VALIDITY}. If you do not verify your email within this timeframe, you may need to request another verification link.</p>\n<p>If you did not register for an account with us, please ignore this email. Your account will not be activated.</p>\n<p>Note: This is an automatically generated email. Please do not reply to this address.</p>', 1, 'Security Email Setting', 1);

CREATE TABLE notification_setting_system_template(
	notification_setting_system_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	notification_setting_id INT UNSIGNED NOT NULL,
	system_notification_title VARCHAR(200) NOT NULL,
	system_notification_message VARCHAR(500) NOT NULL,
    created_date DATETIME NOT NULL DEFAULT NOW(),
    last_log_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (notification_setting_id) REFERENCES notification_setting(notification_setting_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX notification_setting_system_index_notification_setting_system_id ON notification_setting_system_template(notification_setting_system_id);
CREATE INDEX notification_setting_system_index_notification_setting_id ON notification_setting_system_template(notification_setting_id);

CREATE TABLE notification_setting_sms_template(
	notification_setting_sms_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	notification_setting_id INT UNSIGNED NOT NULL,
	sms_notification_message VARCHAR(500) NOT NULL,
    created_date DATETIME NOT NULL DEFAULT NOW(),
    last_log_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (notification_setting_id) REFERENCES notification_setting(notification_setting_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX notification_setting_sms_index_notification_setting_sms_id ON notification_setting_sms_template(notification_setting_sms_id);
CREATE INDEX notification_setting_sms_index_notification_setting_id ON notification_setting_sms_template(notification_setting_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */