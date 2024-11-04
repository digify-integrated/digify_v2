DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkBillingCycleExist//
CREATE PROCEDURE checkBillingCycleExist(
    IN p_billing_cycle_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM billing_cycle
    WHERE billing_cycle_id = p_billing_cycle_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveBillingCycle//
CREATE PROCEDURE saveBillingCycle(
    IN p_billing_cycle_id INT, 
    IN p_billing_cycle_name VARCHAR(100),
    IN p_last_log_by INT, 
    OUT p_new_billing_cycle_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_billing_cycle_id IS NULL OR NOT EXISTS (SELECT 1 FROM billing_cycle WHERE billing_cycle_id = p_billing_cycle_id) THEN
        INSERT INTO billing_cycle (billing_cycle_name, last_log_by) 
        VALUES(p_billing_cycle_name, p_last_log_by);
        
        SET p_new_billing_cycle_id = LAST_INSERT_ID();
    ELSE
        UPDATE billing_cycle
        SET billing_cycle_name = p_billing_cycle_name,
            last_log_by = p_last_log_by
        WHERE billing_cycle_id = p_billing_cycle_id;

        SET p_new_billing_cycle_id = p_billing_cycle_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteBillingCycle//
CREATE PROCEDURE deleteBillingCycle(
    IN p_billing_cycle_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM billing_cycle WHERE billing_cycle_id = p_billing_cycle_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getBillingCycle//
CREATE PROCEDURE getBillingCycle(
    IN p_billing_cycle_id INT
)
BEGIN
	SELECT * FROM billing_cycle
	WHERE billing_cycle_id = p_billing_cycle_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateBillingCycleTable//
CREATE PROCEDURE generateBillingCycleTable()
BEGIN
	SELECT billing_cycle_id, billing_cycle_name
    FROM billing_cycle 
    ORDER BY billing_cycle_id;
END //

DROP PROCEDURE IF EXISTS generateBillingCycleOptions//
CREATE PROCEDURE generateBillingCycleOptions()
BEGIN
	SELECT billing_cycle_id, billing_cycle_name 
    FROM billing_cycle 
    ORDER BY billing_cycle_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */