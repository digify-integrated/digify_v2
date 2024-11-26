/* Contact Information Type Table */

DROP TABLE IF EXISTS contact_information_type;
CREATE TABLE contact_information_type (
    contact_information_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    contact_information_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX contact_information_type_index_contact_information_type_id ON contact_information_type(contact_information_type_id);

INSERT INTO contact_information_type (contact_information_type_name, last_log_by)
VALUES 
('Personal', 1),
('Work', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */