/* Credential Type Table */

DROP TABLE IF EXISTS credential_type;
CREATE TABLE credential_type (
    credential_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    credential_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX credential_type_index_credential_type_id ON credential_type(credential_type_id);

INSERT INTO credential_type (credential_type_name, last_log_by) 
VALUES
('Passport', 1),
('Driver\'s License', 1),
('National ID', 1),
('SSS ID', 1),
('GSIS ID', 1),
('PhilHealth ID', 1),
('Postal ID', 1),
('Voter\'s ID', 1),
('Barangay ID', 1),
('Student ID', 1),
('PRC License', 1),
('Company ID', 1),
('Professional Certification', 1),
('Work Permit', 1),
('Medical License', 1),
('Teaching License', 1),
('Engineering License', 1),
('Bar Exam Certificate', 1),
('Visa', 1),
('Work Visa', 1),
('Immigration Card', 1),
('Marriage Certificate', 1),
('Birth Certificate', 1),
('Death Certificate', 1),
('Police Clearance', 1),
('NBI Clearance', 1),
('Barangay Clearance', 1),
('Travel Permit', 1),
('Employment Certificate', 1),
('Firearm License', 1),
('Business Permit', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */