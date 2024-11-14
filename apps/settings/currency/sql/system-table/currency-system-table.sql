/* Currency Table */

DROP TABLE IF EXISTS currency;
CREATE TABLE currency(
	currency_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	currency_name VARCHAR(100) NOT NULL,
	symbol VARCHAR(5) NOT NULL,
	shorthand VARCHAR(10) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX currency_index_currency_id ON currency(currency_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */