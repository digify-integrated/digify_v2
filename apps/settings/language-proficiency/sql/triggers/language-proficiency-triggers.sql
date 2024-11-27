DELIMITER //

DROP TRIGGER IF EXISTS language_proficiency_trigger_update//
CREATE TRIGGER language_proficiency_trigger_update
AFTER UPDATE ON language_proficiency
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language proficiency changed.<br/><br/>';

    IF NEW.language_proficiency_name <> OLD.language_proficiency_name THEN
        SET audit_log = CONCAT(audit_log, "Language Proficiency Name: ", OLD.language_proficiency_name, " -> ", NEW.language_proficiency_name, "<br/>");
    END IF;

    IF NEW.language_proficiency_description <> OLD.language_proficiency_description THEN
        SET audit_log = CONCAT(audit_log, "Language Proficiency Description: ", OLD.language_proficiency_description, " -> ", NEW.language_proficiency_description, "<br/>");
    END IF;
    
    IF audit_log <> 'Language proficiency changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('language_proficiency', NEW.language_proficiency_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS language_proficiency_trigger_insert//
CREATE TRIGGER language_proficiency_trigger_insert
AFTER INSERT ON language_proficiency
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Language proficiency created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('language_proficiency', NEW.language_proficiency_id, audit_log, NEW.last_log_by, NOW());
END //