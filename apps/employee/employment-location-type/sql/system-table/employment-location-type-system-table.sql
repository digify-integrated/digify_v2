/* Employment Location Type Table */

DROP TABLE IF EXISTS employment_location_type;
CREATE TABLE employment_location_type (
    employment_location_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employment_location_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employment_location_type_index_employment_location_type_id ON employment_location_type(employment_location_type_id);

INSERT INTO employment_location_type (employment_location_type_name, last_log_by)
VALUES 
('Head Office', 1),
('Branch Office', 1),
('Remote Work', 1),
('Client Site', 1),
('Factory', 1),
('Warehouse', 1),
('Retail Store', 1),
('Home Office', 1),
('Field Work', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */