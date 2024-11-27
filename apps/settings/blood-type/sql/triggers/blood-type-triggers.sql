DELIMITER //

DROP TRIGGER IF EXISTS blood_type_trigger_update//
CREATE TRIGGER blood_type_trigger_update
AFTER UPDATE ON blood_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Blood type changed.<br/><br/>';

    IF NEW.blood_type_name <> OLD.blood_type_name THEN
        SET audit_log = CONCAT(audit_log, "Blood Type Name: ", OLD.blood_type_name, " -> ", NEW.blood_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Blood type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('blood_type', NEW.blood_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS blood_type_trigger_insert//
CREATE TRIGGER blood_type_trigger_insert
AFTER INSERT ON blood_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Blood type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('blood_type', NEW.blood_type_id, audit_log, NEW.last_log_by, NOW());
END //