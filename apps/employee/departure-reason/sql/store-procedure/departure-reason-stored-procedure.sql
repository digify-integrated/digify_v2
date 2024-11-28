DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkDepartureReasonExist//
CREATE PROCEDURE checkDepartureReasonExist(
    IN p_departure_reason_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM departure_reason
    WHERE departure_reason_id = p_departure_reason_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveDepartureReason//
CREATE PROCEDURE saveDepartureReason(
    IN p_departure_reason_id INT, 
    IN p_departure_reason_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_departure_reason_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_departure_reason_id IS NULL OR NOT EXISTS (SELECT 1 FROM departure_reason WHERE departure_reason_id = p_departure_reason_id) THEN
        INSERT INTO departure_reason (departure_reason_name, last_log_by) 
        VALUES(p_departure_reason_name, p_last_log_by);
        
        SET p_new_departure_reason_id = LAST_INSERT_ID();
    ELSE
        UPDATE departure_reason
        SET departure_reason_name = p_departure_reason_name,
            last_log_by = p_last_log_by
        WHERE departure_reason_id = p_departure_reason_id;

        SET p_new_departure_reason_id = p_departure_reason_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteDepartureReason//
CREATE PROCEDURE deleteDepartureReason(
    IN p_departure_reason_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM departure_reason WHERE departure_reason_id = p_departure_reason_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getDepartureReason//
CREATE PROCEDURE getDepartureReason(
    IN p_departure_reason_id INT
)
BEGIN
	SELECT * FROM departure_reason
	WHERE departure_reason_id = p_departure_reason_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateDepartureReasonTable//
CREATE PROCEDURE generateDepartureReasonTable()
BEGIN
	SELECT departure_reason_id, departure_reason_name
    FROM departure_reason 
    ORDER BY departure_reason_id;
END //

DROP PROCEDURE IF EXISTS generateDepartureReasonOptions//
CREATE PROCEDURE generateDepartureReasonOptions()
BEGIN
	SELECT departure_reason_id, departure_reason_name 
    FROM departure_reason 
    ORDER BY departure_reason_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */