/* Employment Type Table */

DROP TABLE IF EXISTS employment_type;
CREATE TABLE employment_type (
    employment_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employment_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employment_type_index_employment_type_id ON employment_type(employment_type_id);

INSERT INTO employment_type (employment_type_name, last_log_by)
VALUES 
('Full-time', 1),
('Part-time', 1),
('Contract', 1),
('Internship', 1),
('Freelance', 1),
('Temporary', 1),
('Seasonal', 1),
('Apprenticeship', 1),
('Volunteer', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */