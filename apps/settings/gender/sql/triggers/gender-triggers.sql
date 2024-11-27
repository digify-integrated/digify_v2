DELIMITER //

DROP TRIGGER IF EXISTS gender_trigger_update//
CREATE TRIGGER gender_trigger_update
AFTER UPDATE ON gender
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Gender changed.<br/><br/>';

    IF NEW.gender_name <> OLD.gender_name THEN
        SET audit_log = CONCAT(audit_log, "Gender Name: ", OLD.gender_name, " -> ", NEW.gender_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Gender changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('gender', NEW.gender_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS gender_trigger_insert//
CREATE TRIGGER gender_trigger_insert
AFTER INSERT ON gender
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Gender created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('gender', NEW.gender_id, audit_log, NEW.last_log_by, NOW());
END //