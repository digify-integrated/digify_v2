/* Menu Item Table */

DROP TABLE IF EXISTS menu_item;
CREATE TABLE menu_item (
    menu_item_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    menu_item_name VARCHAR(100) NOT NULL,
    menu_item_url VARCHAR(50),
	menu_item_icon VARCHAR(50),
    app_module_id INT UNSIGNED NOT NULL,
    app_module_name VARCHAR(100) NOT NULL,
	parent_id INT UNSIGNED,
    parent_name VARCHAR(100),
    table_name VARCHAR(100),
    order_sequence TINYINT(10) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id),
    FOREIGN KEY (app_module_id) REFERENCES app_module(app_module_id)
);

CREATE INDEX menu_item_index_menu_item_id ON menu_item(menu_item_id);
CREATE INDEX menu_item_index_app_module_id ON menu_item(app_module_id);
CREATE INDEX menu_item_index_parent_id ON menu_item(parent_id);

INSERT INTO menu_item (menu_item_id, menu_item_name, menu_item_url, menu_item_icon, app_module_id, app_module_name, parent_id, parent_name, table_name, order_sequence, last_log_by) VALUES
(1, 'App Module', 'app-module.php', '', 1, 'Settings', 0, '', 'app_module', 1, 2),
(2, 'General Settings', 'general-settings.php', '', 1, 'Settings', 0, '', '', 7, 2),
(3, 'Users & Companies', '', '', 1, 'Settings', 0, '', '', 21, 2),
(4, 'User Account', 'user-account.php', 'ki-outline ki-user', 1, 'Settings', 3, 'Users & Companies', 'user_account', 21, 2),
(5, 'Company', 'company.php', 'ki-outline ki-shop', 1, 'Settings', 3, 'Users & Companies', 'company', 3, 2),
(6, 'Role', 'role.php', '', 1, 'Settings', NULL, NULL, 'role', 3, 2),
(7, 'User Interface', '', '', 1, 'Settings', NULL, NULL, '', 16, 2),
(8, 'Menu Item', 'menu-item.php', 'ki-outline ki-data', 1, 'Settings', 7, 'User Interface', 'menu_item', 2, 2),
(9, 'System Action', 'system-action.php', 'ki-outline ki-key-square', 1, 'Settings', 7, 'User Interface', 'system_action', 2, 2);

/* ----------------------------------------------------------------------------------------------------------------------------- */