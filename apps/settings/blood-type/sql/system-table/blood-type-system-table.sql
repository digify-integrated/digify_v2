/* Blood Type Table */

DROP TABLE IF EXISTS blood_type;
CREATE TABLE blood_type (
    blood_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    blood_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX blood_type_index_blood_type_id ON blood_type(blood_type_id);

INSERT INTO blood_type (blood_type_name, last_log_by)
VALUES 
('A+', 1),
('A-', 1),
('B+', 1),
('B-', 1),
('AB+', 1),
('AB-', 1),
('O+', 1),
('O-', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */