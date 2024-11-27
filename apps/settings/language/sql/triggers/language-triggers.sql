DELIMITER //

DROP TRIGGER IF EXISTS language_trigger_update//
CREATE TRIGGER language_trigger_update
AFTER UPDATE ON language
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language changed.<br/><br/>';

    IF NEW.language_name <> OLD.language_name THEN
        SET audit_log = CONCAT(audit_log, "Language Name: ", OLD.language_name, " -> ", NEW.language_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Language changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('language', NEW.language_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS language_trigger_insert//
CREATE TRIGGER language_trigger_insert
AFTER INSERT ON language
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('language', NEW.language_id, audit_log, NEW.last_log_by, NOW());
END //