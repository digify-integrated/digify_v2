DELIMITER //

DROP TRIGGER IF EXISTS address_type_trigger_update//
CREATE TRIGGER address_type_trigger_update
AFTER UPDATE ON address_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Address type changed.<br/><br/>';

    IF NEW.address_type_name <> OLD.address_type_name THEN
        SET audit_log = CONCAT(audit_log, "Address Type Name: ", OLD.address_type_name, " -> ", NEW.address_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Address type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('address_type', NEW.address_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS address_type_trigger_insert//
CREATE TRIGGER address_type_trigger_insert
AFTER INSERT ON address_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Address type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('address_type', NEW.address_type_id, audit_log, NEW.last_log_by, NOW());
END //