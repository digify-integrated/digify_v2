DELIMITER //

DROP TRIGGER IF EXISTS department_trigger_update//
CREATE TRIGGER department_trigger_update
AFTER UPDATE ON department
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Department changed.<br/><br/>';

    IF NEW.department_name <> OLD.department_name THEN
        SET audit_log = CONCAT(audit_log, "Department Name: ", OLD.department_name, " -> ", NEW.department_name, "<br/>");
    END IF;

    IF NEW.parent_department_name <> OLD.parent_department_name THEN
        SET audit_log = CONCAT(audit_log, "Parent Department: ", OLD.parent_department_name, " -> ", NEW.parent_department_name, "<br/>");
    END IF;

    IF NEW.manager_name <> OLD.manager_name THEN
        SET audit_log = CONCAT(audit_log, "Manager: ", OLD.manager_name, " -> ", NEW.manager_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Department changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('department', NEW.department_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS department_trigger_insert//
CREATE TRIGGER department_trigger_insert
AFTER INSERT ON department
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Department created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('department', NEW.department_id, audit_log, NEW.last_log_by, NOW());
END //