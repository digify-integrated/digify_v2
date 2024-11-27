/* Gender Table */

DROP TABLE IF EXISTS gender;
CREATE TABLE gender (
    gender_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    gender_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX gender_index_gender_id ON gender(gender_id);

INSERT INTO gender (gender_name, last_log_by)
VALUES 
('Male', 1),
('Female', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */