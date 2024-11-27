DELIMITER //

DROP TRIGGER IF EXISTS religion_trigger_update//
CREATE TRIGGER religion_trigger_update
AFTER UPDATE ON religion
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Religion changed.<br/><br/>';

    IF NEW.religion_name <> OLD.religion_name THEN
        SET audit_log = CONCAT(audit_log, "Religion Name: ", OLD.religion_name, " -> ", NEW.religion_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Religion changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('religion', NEW.religion_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS religion_trigger_insert//
CREATE TRIGGER religion_trigger_insert
AFTER INSERT ON religion
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Religion created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('religion', NEW.religion_id, audit_log, NEW.last_log_by, NOW());
END //