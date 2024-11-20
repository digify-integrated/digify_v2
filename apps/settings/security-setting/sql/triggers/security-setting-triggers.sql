DELIMITER //

CREATE TRIGGER security_setting_trigger_update
AFTER UPDATE ON security_setting
FOR EACH ROW
BEGIN
     DECLARE audit_log TEXT DEFAULT 'Security setting changed.<br/><br/>';

    IF NEW.security_setting_name <> OLD.security_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Security Setting Name: ", OLD.security_setting_name, " -> ", NEW.security_setting_name, "<br/>");
    END IF;

    IF NEW.value <> OLD.value THEN
        SET audit_log = CONCAT(audit_log, "Value: ", OLD.value, " -> ", NEW.value, "<br/>");
    END IF;
    
    IF audit_log <> 'Security setting changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('security_setting', NEW.security_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

CREATE TRIGGER security_setting_trigger_insert
AFTER INSERT ON security_setting
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Security setting created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('security_setting', NEW.security_setting_id, audit_log, NEW.last_log_by, NOW());
END //