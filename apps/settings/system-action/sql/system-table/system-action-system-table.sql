/* System Action Table */

DROP TABLE IF EXISTS system_action;
CREATE TABLE system_action(
	system_action_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	system_action_name VARCHAR(100) NOT NULL,
	system_action_description VARCHAR(200) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX system_action_index_system_action_id ON system_action(system_action_id);

INSERT INTO `system_action` (`system_action_id`, `system_action_name`, `system_action_description`) VALUES
(1, 'Activate User Account', 'Access to activate the user account.'),
(2, 'Deactivate User Account', 'Access to deactivate the user account.'),
(3, 'Lock User Account', 'Access to lock the user account.'),
(4, 'Unlock User Account', 'Access to unlock the user account.'),
(5, 'Add Role User Account', 'Access to assign roles to user account.'),
(6, 'Delete Role User Account', 'Access to delete roles to user account.'),
(7, 'Add Role Access', 'Access to add role access.'),
(8, 'Update Role Access', 'Access to update role access.'),
(9, 'Delete Role Access', 'Access to delete role access.'),
(10, 'Add Role System Action Access', 'Access to add the role system action access.'),
(11, 'Update Role System Action Access', 'Access to update the role system action access.'),
(12, 'Delete Role System Action Access', 'Access to delete the role system action access.');

/* ----------------------------------------------------------------------------------------------------------------------------- */