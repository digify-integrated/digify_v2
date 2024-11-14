/* Country Table */

DROP TABLE IF EXISTS country;
CREATE TABLE country(
	country_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	country_name VARCHAR(100) NOT NULL,
	country_code VARCHAR(10) NOT NULL,
	phone_code VARCHAR(10) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX country_index_country_id ON country(country_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */