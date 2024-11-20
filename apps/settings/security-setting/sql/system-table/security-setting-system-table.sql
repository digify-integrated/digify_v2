/* Security Setting Table */

DROP TABLE IF EXISTS security_setting;
CREATE TABLE security_setting(
	security_setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	security_setting_name VARCHAR(100) NOT NULL,
	value VARCHAR(2000) NOT NULL,
    created_date DATETIME NOT NULL DEFAULT NOW(),
    last_log_by INT UNSIGNED NOT NULL,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX security_setting_index_security_setting_id ON security_setting(security_setting_id);

INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('Max Failed Login Attempt', 5, '1');
INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('Max Failed OTP Attempt', 5, '1');
INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('Default Forgot Password Link', 'http://localhost/digify/password-reset.php?id=', '1');
INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('Password Expiry Duration', 180, '1');
INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('Session Timeout Duration', 240, '1');
INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('OTP Duration', 5, '1');
INSERT INTO security_setting (security_setting_name, value, last_log_by) VALUES ('Reset Password Token Duration', 10, '1');

/* ----------------------------------------------------------------------------------------------------------------------------- */