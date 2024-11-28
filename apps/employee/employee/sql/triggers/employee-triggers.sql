DELIMITER //

DROP TRIGGER IF EXISTS employee_trigger_update//
CREATE TRIGGER employee_trigger_update
AFTER UPDATE ON employee
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Employee changed.<br/><br/>';

    IF NEW.employee_name <> OLD.employee_name THEN
        SET audit_log = CONCAT(audit_log, "Employee Name: ", OLD.employee_name, " -> ", NEW.employee_name, "<br/>");
    END IF;

    IF NEW.parent_employee_name <> OLD.parent_employee_name THEN
        SET audit_log = CONCAT(audit_log, "Parent Employee: ", OLD.parent_employee_name, " -> ", NEW.parent_employee_name, "<br/>");
    END IF;

    IF NEW.manager_name <> OLD.manager_name THEN
        SET audit_log = CONCAT(audit_log, "Manager: ", OLD.manager_name, " -> ", NEW.manager_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Employee changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('employee', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS employee_trigger_insert//
CREATE TRIGGER employee_trigger_insert
AFTER INSERT ON employee
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Employee created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('employee', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
END //