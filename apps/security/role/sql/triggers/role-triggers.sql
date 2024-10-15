DELIMITER //

DROP TRIGGER IF EXISTS role_trigger_update//
CREATE TRIGGER role_trigger_update
AFTER UPDATE ON role
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role changed.<br/><br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.role_description <> OLD.role_description THEN
        SET audit_log = CONCAT(audit_log, "Role Description: ", OLD.role_description, " -> ", NEW.role_description, "<br/>");
    END IF;
    
    IF audit_log <> 'Role changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role', NEW.role_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS role_trigger_insert//
CREATE TRIGGER role_trigger_insert
AFTER INSERT ON role
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role', NEW.role_id, audit_log, NEW.last_log_by, NOW());
END //

DROP TRIGGER IF EXISTS role_permission_trigger_update//
CREATE TRIGGER role_permission_trigger_update
AFTER UPDATE ON role_permission
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role permission changed.<br/><br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.menu_item_name <> OLD.menu_item_name THEN
        SET audit_log = CONCAT(audit_log, "Menu Item: ", OLD.menu_item_name, " -> ", NEW.menu_item_name, "<br/>");
    END IF;

    IF NEW.read_access <> OLD.read_access THEN
        SET audit_log = CONCAT(audit_log, "Read Access: ", OLD.read_access, " -> ", NEW.read_access, "<br/>");
    END IF;

    IF NEW.write_access <> OLD.write_access THEN
        SET audit_log = CONCAT(audit_log, "Write Access: ", OLD.write_access, " -> ", NEW.write_access, "<br/>");
    END IF;

    IF NEW.create_access <> OLD.create_access THEN
        SET audit_log = CONCAT(audit_log, "Create Access: ", OLD.create_access, " -> ", NEW.create_access, "<br/>");
    END IF;

    IF NEW.delete_access <> OLD.delete_access THEN
        SET audit_log = CONCAT(audit_log, "Delete Access: ", OLD.delete_access, " -> ", NEW.delete_access, "<br/>");
    END IF;

    IF NEW.import_access <> OLD.import_access THEN
        SET audit_log = CONCAT(audit_log, "Import Access: ", OLD.import_access, " -> ", NEW.import_access, "<br/>");
    END IF;

    IF NEW.export_access <> OLD.export_access THEN
        SET audit_log = CONCAT(audit_log, "Export Access: ", OLD.export_access, " -> ", NEW.export_access, "<br/>");
    END IF;

    IF NEW.log_notes_access <> OLD.log_notes_access THEN
        SET audit_log = CONCAT(audit_log, "Log Notes Access: ", OLD.log_notes_access, " -> ", NEW.log_notes_access, "<br/>");
    END IF;
    
    IF audit_log <> 'Role permission changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role_permission', NEW.role_permission_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS role_permission_trigger_insert//
CREATE TRIGGER role_permission_trigger_insert
AFTER INSERT ON role_permission
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role permission created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role_permission', NEW.role_permission_id, audit_log, NEW.last_log_by, NOW());
END //

DROP TRIGGER IF EXISTS role_system_action_permission_trigger_update//
CREATE TRIGGER role_system_action_permission_trigger_update
AFTER UPDATE ON role_system_action_permission
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role system action permission changed.<br/><br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.system_action_name <> OLD.system_action_name THEN
        SET audit_log = CONCAT(audit_log, "System Action: ", OLD.system_action_name, " -> ", NEW.system_action_name, "<br/>");
    END IF;

    IF NEW.system_action_access <> OLD.system_action_access THEN
        SET audit_log = CONCAT(audit_log, "System Action Access: ", OLD.system_action_access, " -> ", NEW.system_action_access, "<br/>");
    END IF;
    
    IF audit_log <> 'Role system action permission changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role_system_action_permission', NEW.role_system_action_permission_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS role_system_action_permission_trigger_insert//
CREATE TRIGGER role_system_action_permission_trigger_insert
AFTER INSERT ON role_system_action_permission
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role system action permission created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role_system_action_permission', NEW.role_system_action_permission_id, audit_log, NEW.last_log_by, NOW());
END //

DROP TRIGGER IF EXISTS role_user_account_trigger_update//
CREATE TRIGGER role_user_account_trigger_update
AFTER UPDATE ON role_user_account
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role user account changed. <br/>';

    IF NEW.role_name <> OLD.role_name THEN
        SET audit_log = CONCAT(audit_log, "Role Name: ", OLD.role_name, " -> ", NEW.role_name, "<br/>");
    END IF;

    IF NEW.file_as <> OLD.file_as THEN
        SET audit_log = CONCAT(audit_log, "User Account Name: ", OLD.file_as, " -> ", NEW.file_as, "<br/>");
    END IF;
    
    IF audit_log <> 'Role user account changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('role_user_account', NEW.role_user_account_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS role_user_account_trigger_insert//
CREATE TRIGGER role_user_account_trigger_insert
AFTER INSERT ON role_user_account
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Role user account created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('role_user_account', NEW.role_user_account_id, audit_log, NEW.last_log_by, NOW());
END //