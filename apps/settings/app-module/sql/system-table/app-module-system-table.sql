/* App Module Table */

DROP TABLE IF EXISTS app_module;
CREATE TABLE app_module (
    app_module_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    app_module_name VARCHAR(100) NOT NULL,
    app_module_description VARCHAR(500) NOT NULL,
    app_logo VARCHAR(500),
    menu_item_id INT UNSIGNED NOT NULL,
    menu_item_name VARCHAR(100) NOT NULL,
    order_sequence TINYINT(10) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX app_module_index_app_module_id ON app_module(app_module_id);
CREATE INDEX app_module_index_menu_item_id ON app_module(menu_item_id);

INSERT INTO app_module (app_module_id, app_module_name, app_module_description, app_logo, menu_item_id, menu_item_name, order_sequence, last_log_by) VALUES
(1, 'Settings', 'Centralized management hub for comprehensive organizational oversight and control', '../settings/app-module/image/logo/1/Pboex.png', 1, 'App Module', 100, 1),
(2, 'Employee', 'Centralize employee information', '../settings/app-module/image/logo/2/FhZ0gHo.png', 24, 'Employee', 5, 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */