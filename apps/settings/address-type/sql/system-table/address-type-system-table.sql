/* Address Type Table */

DROP TABLE IF EXISTS address_type;
CREATE TABLE address_type (
    address_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    address_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX address_type_index_address_type_id ON address_type(address_type_id);

INSERT INTO address_type (address_type_name, last_log_by) VALUES
('Home Address', 1),
('Billing Address', 1),
('Mailing Address', 1),
('Shipping Address', 1),
('Work Address', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */