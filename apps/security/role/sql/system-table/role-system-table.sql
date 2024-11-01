/* Role Table */

DROP TABLE IF EXISTS role;
CREATE TABLE role(
	role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	role_name VARCHAR(100) NOT NULL,
	role_description VARCHAR(200) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX role_index_role_id ON role(role_id);

INSERT INTO role (role_name, role_description, last_log_by) VALUES ('Administrator', 'Full access to all features and data within the system. This role have similar access levels to the Admin but is not as powerful as the Super Admin.', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Role Permission Table */

DROP TABLE IF EXISTS role_permission;
CREATE TABLE role_permission(
	role_permission_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	role_id INT UNSIGNED NOT NULL,
	role_name VARCHAR(100) NOT NULL,
	menu_item_id INT UNSIGNED NOT NULL,
	menu_item_name VARCHAR(100) NOT NULL,
	read_access TINYINT(1) NOT NULL DEFAULT 0,
    write_access TINYINT(1) NOT NULL DEFAULT 0,
    create_access TINYINT(1) NOT NULL DEFAULT 0,
    delete_access TINYINT(1) NOT NULL DEFAULT 0,
    import_access TINYINT(1) NOT NULL DEFAULT 0,
    export_access TINYINT(1) NOT NULL DEFAULT 0,
    log_notes_access TINYINT(1) NOT NULL DEFAULT 0,
    date_assigned DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (menu_item_id) REFERENCES menu_item(menu_item_id),
    FOREIGN KEY (role_id) REFERENCES role(role_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX role_permission_index_role_permission_id ON role_permission(role_permission_id);
CREATE INDEX role_permission_index_menu_item_id ON role_permission(menu_item_id);
CREATE INDEX role_permission_index_role_id ON role_permission(role_id);

INSERT INTO role_permission (role_permission_id, role_id, role_name, menu_item_id, menu_item_name, read_access, write_access, create_access, delete_access, import_access, export_access, log_notes_access, last_log_by) VALUES
(1, 1, 'Administrator', 1, 'App Module', 1, 1, 1, 1, 1, 1, 1, 1),
(2, 1, 'Administrator', 2, 'General Settings', 1, 1, 1, 1, 1, 1, 1, 1),
(3, 1, 'Administrator', 3, 'Users & Companies', 1, 0, 0, 0, 0, 0, 0, 1),
(4, 1, 'Administrator', 4, 'User Account', 1, 1, 1, 1, 1, 1, 1, 1),
(5, 1, 'Administrator', 5, 'Company', 1, 1, 1, 1, 1, 1, 1, 1),
(6, 1, 'Administrator', 6, 'Role', 1, 1, 1, 1, 1, 1, 1, 1),
(7, 1, 'Administrator', 7, 'User Interface', 1, 0, 0, 0, 0, 0, 0, 1),
(8, 1, 'Administrator', 9, 'Menu Item', 1, 1, 1, 1, 1, 1, 1, 1),
(9, 1, 'Administrator', 10, 'System Action', 1, 1, 1, 1, 1, 1, 1, 1),
(10, 1, 'Administrator', 10, 'Subscription', 1, 0, 0, 0, 0, 0, 0, 1),
(11, 1, 'Administrator', 11, 'Subscription Code', 1, 1, 1, 1, 1, 1, 1, 1);


/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Role System Action Permission Table */

DROP TABLE IF EXISTS role_system_action_permission;
CREATE TABLE role_system_action_permission(
	role_system_action_permission_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	role_id INT UNSIGNED NOT NULL,
	role_name VARCHAR(100) NOT NULL,
	system_action_id INT UNSIGNED NOT NULL,
	system_action_name VARCHAR(100) NOT NULL,
	system_action_access TINYINT(1) NOT NULL DEFAULT 0,
    date_assigned DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (system_action_id) REFERENCES system_action(system_action_id),
    FOREIGN KEY (role_id) REFERENCES role(role_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX role_system_action_permission_index_system_action_permission_id ON role_system_action_permission(role_system_action_permission_id);
CREATE INDEX role_system_action_permission_index_system_action_id ON role_system_action_permission(system_action_id);
CREATE INDEX role_system_action_permissionn_index_role_id ON role_system_action_permission(role_id);

INSERT INTO `role_system_action_permission` (`role_system_action_permission_id`, `role_id`, `role_name`, `system_action_id`, `system_action_name`, `system_action_access`) VALUES
(1, 1, 'Administrator', 1, 'Update System Settings', 1),
(2, 1, 'Administrator', 2, 'Update Security Settings', 1),
(3, 1, 'Administrator', 3, 'Activate User Account', 1),
(4, 1, 'Administrator', 4, 'Deactivate User Account', 1),
(5, 1, 'Administrator', 5, 'Lock User Account', 1),
(6, 1, 'Administrator', 6, 'Unlock User Account', 1),
(7, 1, 'Administrator', 7, 'Add Role User Account', 1),
(8, 1, 'Administrator', 8, 'Delete Role User Account', 1),
(9, 1, 'Administrator', 9, 'Add Role Access', 1),
(10, 1, 'Administrator', 10, 'Update Role Access', 1),
(11, 1, 'Administrator', 11, 'Delete Role Access', 1),
(12, 1, 'Administrator', 12, 'Add Role System Action Access', 1),
(13, 1, 'Administrator', 13, 'Update Role System Action Access', 1),
(14, 1, 'Administrator', 14, 'Delete Role System Action Access', 1),
(15, 1, 'Administrator', 15, 'Add File Extension Access', 1),
(16, 1, 'Administrator', 16, 'Delete File Extension Access', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Role User Account Table */

DROP TABLE IF EXISTS role_user_account;
CREATE TABLE role_user_account(
	role_user_account_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	role_id INT UNSIGNED NOT NULL,
	role_name VARCHAR(100) NOT NULL,
	user_account_id INT UNSIGNED NOT NULL,
	file_as VARCHAR(300) NOT NULL,
    date_assigned DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (user_account_id) REFERENCES user_account(user_account_id),
    FOREIGN KEY (role_id) REFERENCES role(role_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX role_user_account_index_role_user_account_id ON role_user_account(role_user_account_id);
CREATE INDEX role_user_account_permission_index_user_account_id ON role_user_account(user_account_id);
CREATE INDEX role_user_account_permissionn_index_role_id ON role_user_account(role_id);

INSERT INTO role_user_account (role_id, role_name, user_account_id, file_as, last_log_by) VALUES (1, 'Administrator', 2, 'Administrator', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */