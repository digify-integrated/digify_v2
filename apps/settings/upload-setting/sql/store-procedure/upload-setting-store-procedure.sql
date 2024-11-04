DELIMITER //

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