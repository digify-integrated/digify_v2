/* Departure Reason Table */

DROP TABLE IF EXISTS departure_reason;
CREATE TABLE departure_reason (
    departure_reason_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    departure_reason_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX departure_reason_index_departure_reason_id ON departure_reason(departure_reason_id);

INSERT INTO departure_reason (departure_reason_name, last_log_by)
VALUES 
('Resigned', 1),
('Terminated', 1),
('Retired', 1),
('Laid Off', 1),
('End of Contract', 1),
('Redundancy', 1),
('Death', 1),
('Disability', 1),
('Pregnancy', 1),
('Maternity Leave', 1),
('Paternity Leave', 1),
('Study Leave', 1),
('Sabbatical', 1),
('Career Break', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */