/* Company Table */

DROP TABLE IF EXISTS work_location;
CREATE TABLE work_location(
	work_location_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	work_location_name VARCHAR(100) NOT NULL,
	address VARCHAR(1000),
	city_id INT UNSIGNED NOT NULL,
	city_name VARCHAR(100) NOT NULL,
	state_id INT UNSIGNED NOT NULL,
	state_name VARCHAR(100) NOT NULL,
	country_id INT UNSIGNED NOT NULL,
	country_name VARCHAR(100) NOT NULL,
	phone VARCHAR(20),
	telephone VARCHAR(20),
	email VARCHAR(255),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id),
    FOREIGN KEY (city_id) REFERENCES city(city_id),
    FOREIGN KEY (state_id) REFERENCES state(state_id),
    FOREIGN KEY (country_id) REFERENCES country(country_id)
);

CREATE INDEX work_location_index_work_location_id ON work_location(work_location_id);
CREATE INDEX work_location_index_city_id ON work_location(city_id);
CREATE INDEX work_location_index_state_id ON work_location(state_id);
CREATE INDEX work_location_index_country_id ON work_location(country_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */