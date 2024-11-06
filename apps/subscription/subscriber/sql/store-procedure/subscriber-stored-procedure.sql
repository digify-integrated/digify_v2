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
    IN p_subscriber_name VARCHAR(500),
    IN p_company_name VARCHAR(200),
    IN p_phone VARCHAR(50),
    IN p_email VARCHAR(255),
    IN p_subscriber_status VARCHAR(10),
    IN p_subscription_tier_id INT,
    IN p_subscription_tier_name VARCHAR(100),
    IN p_billing_cycle_id INT,
    IN p_billing_cycle_name VARCHAR(100),
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
        INSERT INTO subscriber (subscriber_name, company_name, phone, email, subscription_tier_id, subscription_tier_name, billing_cycle_id, billing_cycle_name, last_log_by) 
        VALUES(p_subscriber_name, p_company_name, p_phone, p_email, p_subscription_tier_id, p_subscription_tier_name, p_billing_cycle_id, p_billing_cycle_name, p_last_log_by);
        
        SET p_new_subscriber_id = LAST_INSERT_ID();
    ELSE
        UPDATE subscriber
        SET subscriber_name = p_subscriber_name,
            company_name = p_company_name,
            phone = p_phone,
            email = p_email,
            subscriber_status = p_subscriber_status,
            subscription_tier_id = p_subscription_tier_id,
            subscription_tier_name = p_subscription_tier_name,
            billing_cycle_id = p_billing_cycle_id,
            billing_cycle_name = p_billing_cycle_name,
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

    DELETE FROM subscription WHERE subscriber_id = p_subscriber_id;
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
CREATE PROCEDURE generateSubscriberTable(
    IN p_filter_by_subscription_tier TEXT,
    IN p_filter_by_billing_cycle TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT subscriber_id, subscriber_name, company_name, phone, email, subscriber_status, subscription_tier_name, billing_cycle_name 
                FROM subscriber ';

    IF p_filter_by_subscription_tier IS NOT NULL AND p_filter_by_subscription_tier <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' subscription_tier_id IN (', p_filter_by_subscription_tier, ')');
    END IF;

    IF p_filter_by_billing_cycle IS NOT NULL AND p_filter_by_billing_cycle <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;
        SET filter_conditions = CONCAT(filter_conditions, ' billing_cycle_id IN (', p_filter_by_billing_cycle, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY subscriber_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateSubscriberOptions//
CREATE PROCEDURE generateSubscriberOptions()
BEGIN
	SELECT subscriber_id, subscriber_name 
    FROM subscriber 
    ORDER BY subscriber_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */