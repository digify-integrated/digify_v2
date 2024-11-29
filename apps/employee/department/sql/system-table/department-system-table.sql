/* Department Table */

DROP TABLE IF EXISTS department;
CREATE TABLE department (
    department_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    department_name VARCHAR(100) NOT NULL,
    parent_department_id INT,
    parent_department_name VARCHAR(100),
    manager_id INT,
    manager_name VARCHAR(1000),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX department_index_department_id ON department(department_id);
CREATE INDEX department_index_parent_department_id ON department(parent_department_id);
CREATE INDEX department_index_manager_id ON department(manager_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */