DELIMITER //

DROP TRIGGER IF EXISTS departure_reason_trigger_update//
CREATE TRIGGER departure_reason_trigger_update
AFTER UPDATE ON departure_reason
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Departure reason changed.<br/><br/>';

    IF NEW.departure_reason_name <> OLD.departure_reason_name THEN
        SET audit_log = CONCAT(audit_log, "Departure Reason Name: ", OLD.departure_reason_name, " -> ", NEW.departure_reason_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Departure reason changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('departure_reason', NEW.departure_reason_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS departure_reason_trigger_insert//
CREATE TRIGGER departure_reason_trigger_insert
AFTER INSERT ON departure_reason
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Departure reason created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('departure_reason', NEW.departure_reason_id, audit_log, NEW.last_log_by, NOW());
END //