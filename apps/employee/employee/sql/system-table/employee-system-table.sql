/* Employee Table */

DROP TABLE IF EXISTS employee;
CREATE TABLE employee (
    employee_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_name VARCHAR(100) NOT NULL,
    parent_employee_id INT,
    parent_employee_name VARCHAR(100),
    manager_id INT,
    manager_name VARCHAR(100),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_index_employee_id ON employee(employee_id);
CREATE INDEX employee_index_parent_employee_id ON employee(parent_employee_id);
CREATE INDEX employee_index_manager_id ON employee(manager_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */