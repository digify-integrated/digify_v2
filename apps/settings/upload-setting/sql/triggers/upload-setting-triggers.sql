DELIMITER //

CREATE TRIGGER upload_setting_trigger_update
AFTER UPDATE ON upload_setting
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Upload setting changed.<br/><br/>';

    IF NEW.upload_setting_name <> OLD.upload_setting_name THEN
        SET audit_log = CONCAT(audit_log, "Upload Setting Name: ", OLD.upload_setting_name, " -> ", NEW.upload_setting_name, "<br/>");
    END IF;

    IF NEW.upload_setting_description <> OLD.upload_setting_description THEN
        SET audit_log = CONCAT(audit_log, "Upload Setting Description: ", OLD.upload_setting_description, " -> ", NEW.upload_setting_description, "<br/>");
    END IF;

    IF NEW.max_file_size <> OLD.max_file_size THEN
        SET audit_log = CONCAT(audit_log, "Max File Size: ", OLD.max_file_size, " -> ", NEW.max_file_size, "<br/>");
    END IF;
    
    IF audit_log <> 'Upload setting changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('upload_setting', NEW.upload_setting_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

CREATE TRIGGER upload_setting_trigger_insert
AFTER INSERT ON upload_setting
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Upload Setting created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('upload_setting', NEW.upload_setting_id, audit_log, NEW.last_log_by, NOW());
END //

CREATE TRIGGER upload_setting_file_extension_trigger_insert
AFTER INSERT ON upload_setting_file_extension
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Upload Setting File Extension created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('upload_setting_file_extension', NEW.upload_setting_file_extension_id, audit_log, NEW.last_log_by, NOW());
END //