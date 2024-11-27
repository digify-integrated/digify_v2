DELIMITER //

DROP TRIGGER IF EXISTS credential_type_trigger_update//
CREATE TRIGGER credential_type_trigger_update
AFTER UPDATE ON credential_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Credential type changed.<br/><br/>';

    IF NEW.credential_type_name <> OLD.credential_type_name THEN
        SET audit_log = CONCAT(audit_log, "Credential Type Name: ", OLD.credential_type_name, " -> ", NEW.credential_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Credential type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('credential_type', NEW.credential_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS credential_type_trigger_insert//
CREATE TRIGGER credential_type_trigger_insert
AFTER INSERT ON credential_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Credential type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('credential_type', NEW.credential_type_id, audit_log, NEW.last_log_by, NOW());
END //