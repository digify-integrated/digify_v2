DELIMITER //

DROP TRIGGER IF EXISTS bank_account_type_trigger_update//
CREATE TRIGGER bank_account_type_trigger_update
AFTER UPDATE ON bank_account_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank account type changed.<br/><br/>';

    IF NEW.bank_account_type_name <> OLD.bank_account_type_name THEN
        SET audit_log = CONCAT(audit_log, "Bank Account Type Name: ", OLD.bank_account_type_name, " -> ", NEW.bank_account_type_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Bank account type changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('bank_account_type', NEW.bank_account_type_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS bank_account_type_trigger_insert//
CREATE TRIGGER bank_account_type_trigger_insert
AFTER INSERT ON bank_account_type
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Bank account type created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('bank_account_type', NEW.bank_account_type_id, audit_log, NEW.last_log_by, NOW());
END //