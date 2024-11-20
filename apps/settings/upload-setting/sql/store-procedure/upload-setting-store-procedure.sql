DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkUploadSettingExist//
CREATE PROCEDURE checkUploadSettingExist(
    IN p_upload_setting_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM upload_setting
    WHERE upload_setting_id = p_upload_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveUploadSetting//
CREATE PROCEDURE saveUploadSetting(
    IN p_upload_setting_id INT, 
    IN p_upload_setting_name VARCHAR(100), 
    IN p_upload_setting_description VARCHAR(200), 
    IN p_max_file_size DOUBLE, 
    IN p_last_log_by INT, 
    OUT p_new_upload_setting_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_upload_setting_id IS NULL OR NOT EXISTS (SELECT 1 FROM upload_setting WHERE upload_setting_id = p_upload_setting_id) THEN
        INSERT INTO upload_setting (upload_setting_name, upload_setting_description, max_file_size, last_log_by) 
        VALUES(p_upload_setting_name, p_upload_setting_description, p_max_file_size, p_last_log_by);
        
        SET p_new_upload_setting_id = LAST_INSERT_ID();
    ELSE
        UPDATE upload_setting_file_extension
        SET upload_setting_name = p_upload_setting_name,
            last_log_by = p_last_log_by
        WHERE upload_setting_id = p_upload_setting_id;

        UPDATE upload_setting
        SET upload_setting_name = p_upload_setting_name,
        	upload_setting_description = p_upload_setting_description,
        	max_file_size = p_max_file_size,
            last_log_by = p_last_log_by
        WHERE upload_setting_id = p_upload_setting_id;

        SET p_new_upload_setting_id = p_upload_setting_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS insertUploadSettingFileExtension//
CREATE PROCEDURE insertUploadSettingFileExtension(
    IN p_upload_setting_id INT, 
    IN p_upload_setting_name VARCHAR(100), 
    IN p_file_extension_id INT, 
    IN p_file_extension_name VARCHAR(100), 
    IN p_file_extension VARCHAR(10), 
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO upload_setting_file_extension (upload_setting_id, upload_setting_name, file_extension_id, file_extension_name, file_extension, last_log_by) 
    VALUES(p_upload_setting_id, p_upload_setting_name, p_file_extension_id, p_file_extension_name, p_file_extension, p_last_log_by);

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteUploadSetting//
CREATE PROCEDURE deleteUploadSetting(
    IN p_upload_setting_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM upload_setting_file_extension WHERE upload_setting_id = p_upload_setting_id;
    DELETE FROM upload_setting WHERE upload_setting_id = p_upload_setting_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS deleteUploadSettingFileExtension//
CREATE PROCEDURE deleteUploadSettingFileExtension(
    IN p_upload_setting_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM upload_setting_file_extension WHERE upload_setting_id = p_upload_setting_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getUploadSetting//
CREATE PROCEDURE getUploadSetting(
	IN p_upload_setting_id INT
)
BEGIN
	SELECT * FROM upload_setting
	WHERE upload_setting_id = p_upload_setting_id;
END //

DROP PROCEDURE IF EXISTS getUploadSettingFileExtension//
CREATE PROCEDURE getUploadSettingFileExtension(
	IN p_upload_setting_id INT
)
BEGIN
	SELECT * FROM upload_setting_file_extension
	WHERE upload_setting_id = p_upload_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateUploadSettingTable//
CREATE PROCEDURE generateUploadSettingTable()
BEGIN
	SELECT upload_setting_id, upload_setting_name, upload_setting_description, max_file_size
    FROM upload_setting 
    ORDER BY upload_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */