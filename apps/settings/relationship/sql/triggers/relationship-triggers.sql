DELIMITER //

DROP TRIGGER IF EXISTS relationship_trigger_update//
CREATE TRIGGER relationship_trigger_update
AFTER UPDATE ON relationship
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Relationship changed.<br/><br/>';

    IF NEW.relationship_name <> OLD.relationship_name THEN
        SET audit_log = CONCAT(audit_log, "Relationship Name: ", OLD.relationship_name, " -> ", NEW.relationship_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Relationship changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('relationship', NEW.relationship_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS relationship_trigger_insert//
CREATE TRIGGER relationship_trigger_insert
AFTER INSERT ON relationship
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Relationship created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('relationship', NEW.relationship_id, audit_log, NEW.last_log_by, NOW());
END //