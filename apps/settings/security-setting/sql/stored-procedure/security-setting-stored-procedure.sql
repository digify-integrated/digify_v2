DELIMITER //

/* Update Procedure */

DROP PROCEDURE IF EXISTS updateSecuritySetting//
CREATE PROCEDURE updateSecuritySetting(
	IN p_security_setting_id INT, 
	IN p_value VARCHAR(2000), 
	IN p_last_log_by INT
)
BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE security_setting
    SET value = p_value,
        last_log_by = p_last_log_by
    WHERE security_setting_id = p_security_setting_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedures */

DROP PROCEDURE IF EXISTS getSecuritySetting//
CREATE PROCEDURE getSecuritySetting(
    IN p_security_setting_id INT
)
BEGIN
	SELECT * FROM security_setting
	WHERE security_setting_id = p_security_setting_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */