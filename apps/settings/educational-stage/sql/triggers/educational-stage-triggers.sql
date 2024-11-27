DELIMITER //

DROP TRIGGER IF EXISTS educational_stage_trigger_update//
CREATE TRIGGER educational_stage_trigger_update
AFTER UPDATE ON educational_stage
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Educational stage changed.<br/><br/>';

    IF NEW.educational_stage_name <> OLD.educational_stage_name THEN
        SET audit_log = CONCAT(audit_log, "Educational Stage Name: ", OLD.educational_stage_name, " -> ", NEW.educational_stage_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Educational stage changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('educational_stage', NEW.educational_stage_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS educational_stage_trigger_insert//
CREATE TRIGGER educational_stage_trigger_insert
AFTER INSERT ON educational_stage
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Educational stage created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('educational_stage', NEW.educational_stage_id, audit_log, NEW.last_log_by, NOW());
END //