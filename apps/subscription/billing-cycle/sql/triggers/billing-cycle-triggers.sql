DELIMITER //

DROP TRIGGER IF EXISTS billing_cycle_trigger_update//
CREATE TRIGGER billing_cycle_trigger_update
AFTER UPDATE ON billing_cycle
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Billing cycle changed.<br/><br/>';

    IF NEW.billing_cycle_name <> OLD.billing_cycle_name THEN
        SET audit_log = CONCAT(audit_log, "Billing Cycle Name: ", OLD.billing_cycle_name, " -> ", NEW.billing_cycle_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Billing cycle changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('billing_cycle', NEW.billing_cycle_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS billing_cycle_trigger_insert//
CREATE TRIGGER billing_cycle_trigger_insert
AFTER INSERT ON billing_cycle
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Billing cycle created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('billing_cycle', NEW.billing_cycle_id, audit_log, NEW.last_log_by, NOW());
END //