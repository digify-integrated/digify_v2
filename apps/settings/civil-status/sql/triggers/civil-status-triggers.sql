DELIMITER //

DROP TRIGGER IF EXISTS civil_status_trigger_update//
CREATE TRIGGER civil_status_trigger_update
AFTER UPDATE ON civil_status
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Civil status changed.<br/><br/>';

    IF NEW.civil_status_name <> OLD.civil_status_name THEN
        SET audit_log = CONCAT(audit_log, "Civil Status Name: ", OLD.civil_status_name, " -> ", NEW.civil_status_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Civil status changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('civil_status', NEW.civil_status_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS civil_status_trigger_insert//
CREATE TRIGGER civil_status_trigger_insert
AFTER INSERT ON civil_status
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Civil status created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('civil_status', NEW.civil_status_id, audit_log, NEW.last_log_by, NOW());
END //