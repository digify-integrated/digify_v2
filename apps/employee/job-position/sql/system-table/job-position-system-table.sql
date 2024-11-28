/* Job Position Table */

DROP TABLE IF EXISTS job_position;
CREATE TABLE job_position (
    job_position_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    job_position_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX job_position_index_job_position_id ON job_position(job_position_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */