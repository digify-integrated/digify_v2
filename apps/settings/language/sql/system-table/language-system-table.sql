/* Address Type Table */

DROP TABLE IF EXISTS language;
CREATE TABLE language (
    language_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    language_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX language_index_language_id ON language(language_id);

INSERT INTO language (language_name, last_log_by) VALUES
('Home Address', 1),
('Billing Address', 1),
('Mailing Address', 1),
('Shipping Address', 1),
('Work Address', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */