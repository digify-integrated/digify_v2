DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkSystemActionExist//
CREATE PROCEDURE checkSystemActionExist(
    IN p_system_action_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM system_action
    WHERE system_action_id = p_system_action_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getSystemAction//
CREATE PROCEDURE getSystemAction(
    IN p_system_action_id INT
)
BEGIN
	SELECT * FROM system_action
	WHERE system_action_id = p_system_action_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveSystemAction//
CREATE PROCEDURE saveSystemAction(
    IN p_system_action_id INT, 
    IN p_system_action_name VARCHAR(100), 
    IN p_system_action_description VARCHAR(200),
    IN p_last_log_by INT, 
    OUT p_new_system_action_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_system_action_id IS NULL OR NOT EXISTS (SELECT 1 FROM system_action WHERE system_action_id = p_system_action_id) THEN
        INSERT INTO system_action (system_action_name, system_action_description, last_log_by) 
        VALUES(p_system_action_name, p_system_action_description, p_last_log_by);
        
        SET p_new_system_action_id = LAST_INSERT_ID();
    ELSE
        UPDATE role_system_action_permission
        SET system_action_name = p_system_action_name,
            last_log_by = p_last_log_by
        WHERE system_action_id = p_system_action_id;
        
        UPDATE system_action
        SET system_action_name = p_system_action_name,
            system_action_description = p_system_action_description,
            last_log_by = p_last_log_by
        WHERE system_action_id = p_system_action_id;

        SET p_new_system_action_id = p_system_action_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteSystemAction//
CREATE PROCEDURE deleteSystemAction(
    IN p_system_action_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM role_system_action_permission WHERE system_action_id = p_system_action_id;
    DELETE FROM system_action WHERE system_action_id = p_system_action_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateSystemActionTable//
CREATE PROCEDURE generateSystemActionTable()
BEGIN
    SELECT system_action_id, system_action_name, system_action_description 
    FROM system_action
    ORDER BY system_action_id;
END //

DROP PROCEDURE IF EXISTS generateSystemActionOptions//
CREATE PROCEDURE generateSystemActionOptions()
BEGIN
    SELECT system_action_id, system_action_name 
    FROM system_action 
    ORDER BY system_action_name;
END //

DROP PROCEDURE IF EXISTS generateSystemActionAssignedRoleTable//
CREATE PROCEDURE generateSystemActionAssignedRoleTable(
    IN p_system_action_id INT
)
BEGIN
    SELECT role_system_action_permission_id, role_name, system_action_access 
    FROM role_system_action_permission
    WHERE system_action_id = p_system_action_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */