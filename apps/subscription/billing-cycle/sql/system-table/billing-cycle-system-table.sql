/* Billing Cycle Table */

DROP TABLE IF EXISTS billing_cycle;
CREATE TABLE billing_cycle (
    billing_cycle_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    billing_cycle_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX billing_cycle_index_billing_cycle_id ON billing_cycle(billing_cycle_id);

INSERT INTO billing_cycle (billing_cycle_name, last_log_by) VALUES
('Monthly', 1),
('Yearly', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */