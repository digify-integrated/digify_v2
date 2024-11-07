/* Users Table */
DROP TABLE IF EXISTS user_account;

CREATE TABLE user_account (
    user_account_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    file_as VARCHAR(300) NOT NULL,
    email VARCHAR(255),
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(500) NULL,
    phone VARCHAR(50),
    locked VARCHAR(255) NOT NULL DEFAULT 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0%3D',
    active VARCHAR(255) NOT NULL DEFAULT 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0%3D',
    last_failed_login_attempt DATETIME,
    failed_login_attempts VARCHAR(255),
    last_connection_date DATETIME,
    password_expiry_date VARCHAR(255) NOT NULL,
    reset_token VARCHAR(255),
    reset_token_expiry_date VARCHAR(255),
    receive_notification VARCHAR(255) NOT NULL DEFAULT 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D',
    two_factor_auth VARCHAR(255) NOT NULL DEFAULT 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D',
    otp VARCHAR(255),
    otp_expiry_date VARCHAR(255),
    failed_otp_attempts VARCHAR(255),
    last_password_change DATETIME,
    account_lock_duration VARCHAR(255),
    last_password_reset DATETIME,
    multiple_session VARCHAR(255) DEFAULT 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20%3D',
    session_token VARCHAR(255),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX user_account_index_user_account_id ON user_account(user_account_id);
CREATE INDEX user_account_index_email ON user_account(email);

INSERT INTO user_account (file_as, username, email, password, locked, active, password_expiry_date, two_factor_auth, last_log_by) VALUES ('Digify Bot', 'digifybot', 'digifybot@gmail.com', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20', 'aUIRg2jhRcYVcr0%2BiRDl98xjv81aR4Ux63bP%2BF2hQbE%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', '1');
INSERT INTO user_account (file_as, username, email, password, locked, active, password_expiry_date, two_factor_auth, last_log_by) VALUES ('Administrator', 'ldagulto', 'lawrenceagulto.317@gmail.com', 'Lu%2Be%2BRZfTv%2F3T0GR%2Fwes8QPJvE3Etx1p7tmryi74LNk%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', 'aVWoyO3aKYhOnVA8MwXfCaL4WrujDqvAPCHV3dY8F20', 'aUIRg2jhRcYVcr0%2BiRDl98xjv81aR4Ux63bP%2BF2hQbE%3D', 'WkgqlkcpSeEd7eWC8gl3iPwksfGbJYGy3VcisSyDeQ0', '1');

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Login Sessions Table */

DROP TABLE IF EXISTS login_sessions;

CREATE TABLE login_sessions (
    login_sessions_id INT AUTO_INCREMENT PRIMARY KEY,
    user_account_id INT UNSIGNED NOT NULL,
    location VARCHAR(500),
    device VARCHAR(200),
    ip_address VARCHAR(100),
    login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_account_id) REFERENCES user_account(user_account_id)
);

CREATE INDEX login_sessions_index_login_sessions_id ON login_sessions(login_sessions_id);
CREATE INDEX login_sessions_index_user_account_id ON login_sessions(user_account_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Password History Table */

DROP TABLE IF EXISTS password_history;

CREATE TABLE password_history (
    password_history_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    user_account_id INT UNSIGNED NOT NULL,
    password VARCHAR(255) NOT NULL,
    password_change_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (user_account_id) REFERENCES user_account(user_account_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX password_history_index_password_history_id ON password_history(password_history_id);
CREATE INDEX password_history_index_user_account_id ON password_history(user_account_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */