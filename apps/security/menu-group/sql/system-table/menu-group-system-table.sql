/* Menu Group Table */

DROP TABLE IF EXISTS menu_group;
CREATE TABLE menu_group (
    menu_group_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    menu_group_name VARCHAR(100) NOT NULL,
    app_module_id INT UNSIGNED NOT NULL,
    app_module_name VARCHAR(100) NOT NULL,
    order_sequence TINYINT(10) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id),
    FOREIGN KEY (app_module_id) REFERENCES app_module(app_module_id)
);

CREATE INDEX menu_group_index_menu_group_id ON menu_group(menu_group_id);

INSERT INTO menu_group (menu_group_id, menu_group_name, app_module_id, app_module_name, order_sequence, last_log_by) VALUES
(1, 'Technical', 1, 'Settings', 100, 2),
(2, 'Administration', 1, 'Settings', 5, 2);

/* ----------------------------------------------------------------------------------------------------------------------------- */