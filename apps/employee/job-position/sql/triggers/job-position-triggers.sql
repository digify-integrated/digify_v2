DELIMITER //

DROP TRIGGER IF EXISTS job_position_trigger_update//
CREATE TRIGGER job_position_trigger_update
AFTER UPDATE ON job_position
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Job position changed.<br/><br/>';

    IF NEW.job_position_name <> OLD.job_position_name THEN
        SET audit_log = CONCAT(audit_log, "Job Position Name: ", OLD.job_position_name, " -> ", NEW.job_position_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Job position changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('job_position', NEW.job_position_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS job_position_trigger_insert//
CREATE TRIGGER job_position_trigger_insert
AFTER INSERT ON job_position
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Job position created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('job_position', NEW.job_position_id, audit_log, NEW.last_log_by, NOW());
END //