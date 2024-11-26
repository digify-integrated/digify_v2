DELIMITER //

DROP TRIGGER IF EXISTS bank_trigger_update//
CREATE TRIGGER bank_trigger_update
AFTER UPDATE ON bank
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank changed.<br/><br/>';

    IF NEW.bank_name <> OLD.bank_name THEN
        SET audit_log = CONCAT(audit_log, "Bank Name: ", OLD.bank_name, " -> ", NEW.bank_name, "<br/>");
    END IF;

    IF NEW.bank_identifier_code <> OLD.bank_identifier_code THEN
        SET audit_log = CONCAT(audit_log, "Bank Identifier Code: ", OLD.bank_identifier_code, " -> ", NEW.bank_identifier_code, "<br/>");
    END IF;
    
    IF audit_log <> 'Bank changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('bank', NEW.bank_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS bank_trigger_insert//
CREATE TRIGGER bank_trigger_insert
AFTER INSERT ON bank
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('bank', NEW.bank_id, audit_log, NEW.last_log_by, NOW());
END //