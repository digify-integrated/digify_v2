DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkSubscriberExist//
CREATE PROCEDURE checkSubscriberExist(
    IN p_subscriber_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM subscriber
    WHERE subscriber_id = p_subscriber_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveSubscriber//
CREATE PROCEDURE saveSubscriber(
    IN p_subscriber_id INT, 
    IN p_subscriber_name VARCHAR(100),
    IN p_last_log_by INT, 
    OUT p_new_subscriber_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_subscriber_id IS NULL OR NOT EXISTS (SELECT 1 FROM subscriber WHERE subscriber_id = p_subscriber_id) THEN
        INSERT INTO subscriber (subscriber_name, last_log_by) 
        VALUES(p_subscriber_name, p_last_log_by);
        
        SET p_new_subscriber_id = LAST_INSERT_ID();
    ELSE
        UPDATE subscriber
        SET subscriber_name = p_subscriber_name,
            last_log_by = p_last_log_by
        WHERE subscriber_id = p_subscriber_id;

        SET p_new_subscriber_id = p_subscriber_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteSubscriber//
CREATE PROCEDURE deleteSubscriber(
    IN p_subscriber_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM subscriber WHERE subscriber_id = p_subscriber_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getSubscriber//
CREATE PROCEDURE getSubscriber(
    IN p_subscriber_id INT
)
BEGIN
	SELECT * FROM subscriber
	WHERE subscriber_id = p_subscriber_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateSubscriberTable//
CREATE PROCEDURE generateSubscriberTable()
BEGIN
	SELECT subscriber_id, subscriber_name
    FROM subscriber 
    ORDER BY subscriber_id;
END //

DROP PROCEDURE IF EXISTS generateSubscriberOptions//
CREATE PROCEDURE generateSubscriberOptions()
BEGIN
	SELECT subscriber_id, subscriber_name 
    FROM subscriber 
    ORDER BY subscriber_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */