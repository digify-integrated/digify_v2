/* Relationship Table */

DROP TABLE IF EXISTS relationship;
CREATE TABLE relationship (
    relationship_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    relationship_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX relationship_index_relationship_id ON relationship(relationship_id);

INSERT INTO relationship (relationship_name, last_log_by)
VALUES 
('Father', 1),
('Mother', 1),
('Husband', 1),
('Wife', 1),
('Son', 1),
('Daughter', 1),
('Brother', 1),
('Sister', 1),
('Grandfather', 1),
('Grandmother', 1),
('Grandson', 1),
('Granddaughter', 1),
('Uncle', 1),
('Aunt', 1),
('Nephew', 1),
('Niece', 1),
('Cousin', 1),
('Friend', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */