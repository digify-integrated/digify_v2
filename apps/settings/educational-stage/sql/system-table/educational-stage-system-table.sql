/* Educational Stage Table */

DROP TABLE IF EXISTS educational_stage;
CREATE TABLE educational_stage (
    educational_stage_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    educational_stage_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX educational_stage_index_educational_stage_id ON educational_stage(educational_stage_id);

INSERT INTO educational_stage (educational_stage_name, last_log_by)
VALUES 
('Primary Education', 1),
('Middle School', 1),
('High School', 1),
('Diploma', 1),
('Bachelor', 1),
('Master', 1),
('Doctorate', 1),
('Post-Doctorate', 1),
('Vocational Training', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */