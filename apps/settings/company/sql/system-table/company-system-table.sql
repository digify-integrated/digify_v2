/* Company Table */

DROP TABLE IF EXISTS company;
CREATE TABLE company(
	company_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	company_name VARCHAR(100) NOT NULL,
	company_logo VARCHAR(500),
	address VARCHAR(1000),
	city_id INT UNSIGNED NOT NULL,
	city_name VARCHAR(100) NOT NULL,
	state_id INT UNSIGNED NOT NULL,
	state_name VARCHAR(100) NOT NULL,
	country_id INT UNSIGNED NOT NULL,
	country_name VARCHAR(100) NOT NULL,
	tax_id VARCHAR(100),
	currency_id INT UNSIGNED,
	currency_name VARCHAR(100),
	phone VARCHAR(20),
	telephone VARCHAR(20),
	email VARCHAR(255),
	website VARCHAR(255),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id),
    FOREIGN KEY (city_id) REFERENCES city(city_id),
    FOREIGN KEY (state_id) REFERENCES state(state_id),
    FOREIGN KEY (country_id) REFERENCES country(country_id)
);

CREATE INDEX company_index_company_id ON company(company_id);
CREATE INDEX company_index_city_id ON company(city_id);
CREATE INDEX company_index_state_id ON company(state_id);
CREATE INDEX company_index_country_id ON company(country_id);
CREATE INDEX company_index_currency_id ON company(currency_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */