/* Bank Account Type Table */

DROP TABLE IF EXISTS bank_account_type;
CREATE TABLE bank_account_type (
    bank_account_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    bank_account_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX bank_account_type_index_bank_account_type_id ON bank_account_type(bank_account_type_id);

INSERT INTO bank_account_type (bank_account_type_name, last_log_by)
VALUES 
('Checking', 1),
('Savings', 1),
('Money Market', 1),
('Certificate of Deposit (CD)', 1),
('Individual Retirement Account (IRA)', 1),
('Business Checking', 1),
('Business Savings', 1),
('Business Money Market', 1),
('Business Certificate of Deposit (CD)', 1),
('Business Individual Retirement Account (IRA)', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */