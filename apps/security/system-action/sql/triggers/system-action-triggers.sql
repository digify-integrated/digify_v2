DELIMITER //

DROP TRIGGER IF EXISTS system_action_trigger_update//
CREATE TRIGGER system_action_trigger_update
AFTER UPDATE ON system_action
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'System action changed.<br/><br/>';

    IF NEW.system_action_name <> OLD.system_action_name THEN
        SET audit_log = CONCAT(audit_log, "System Action Name: ", OLD.system_action_name, " -> ", NEW.system_action_name, "<br/>");
    END IF;

    IF NEW.system_action_description <> OLD.system_action_description THEN
        SET audit_log = CONCAT(audit_log, "System Action Description: ", OLD.system_action_description, " -> ", NEW.system_action_description, "<br/>");
    END IF;
    
    IF audit_log <> 'System action changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('system_action', NEW.system_action_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS system_action_trigger_insert//
CREATE TRIGGER system_action_trigger_insert
AFTER INSERT ON system_action
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'System action created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('system_action', NEW.system_action_id, audit_log, NEW.last_log_by, NOW());
END //