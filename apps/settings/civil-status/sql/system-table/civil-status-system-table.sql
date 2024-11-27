/* Civil Status Table */

DROP TABLE IF EXISTS civil_status;
CREATE TABLE civil_status (
    civil_status_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    civil_status_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX civil_status_index_civil_status_id ON civil_status(civil_status_id);

INSERT INTO civil_status (civil_status_name, last_log_by)
VALUES 
('Single', 1),
('Married', 1),
('Divorced', 1),
('Widowed', 1),
('Separated', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */