DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkSubscriptionTierExist//
CREATE PROCEDURE checkSubscriptionTierExist(
    IN p_subscription_tier_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM subscription_tier
    WHERE subscription_tier_id = p_subscription_tier_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveSubscriptionTier//
CREATE PROCEDURE saveSubscriptionTier(
    IN p_subscription_tier_id INT, 
    IN p_subscription_tier_name VARCHAR(100), 
    IN p_subscription_tier_description VARCHAR(500),
    IN p_order_sequence TINYINT(10), 
    IN p_last_log_by INT, 
    OUT p_new_subscription_tier_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_subscription_tier_id IS NULL OR NOT EXISTS (SELECT 1 FROM subscription_tier WHERE subscription_tier_id = p_subscription_tier_id) THEN
        INSERT INTO subscription_tier (subscription_tier_name, subscription_tier_description, order_sequence, last_log_by) 
        VALUES(p_subscription_tier_name, p_subscription_tier_description, p_order_sequence, p_last_log_by);
        
        SET p_new_subscription_tier_id = LAST_INSERT_ID();
    ELSE
        UPDATE subscriber
        SET subscription_tier_name = p_subscription_tier_name,
            last_log_by = p_last_log_by
        WHERE subscription_tier_id = p_subscription_tier_id;

        UPDATE subscription_tier
        SET subscription_tier_name = p_subscription_tier_name,
            subscription_tier_description = p_subscription_tier_description,
            order_sequence = p_order_sequence,
            last_log_by = p_last_log_by
        WHERE subscription_tier_id = p_subscription_tier_id;

        SET p_new_subscription_tier_id = p_subscription_tier_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteSubscriptionTier//
CREATE PROCEDURE deleteSubscriptionTier(
    IN p_subscription_tier_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM subscription_tier WHERE subscription_tier_id = p_subscription_tier_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getSubscriptionTier//
CREATE PROCEDURE getSubscriptionTier(
    IN p_subscription_tier_id INT
)
BEGIN
	SELECT * FROM subscription_tier
	WHERE subscription_tier_id = p_subscription_tier_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateSubscriptionTierTable//
CREATE PROCEDURE generateSubscriptionTierTable()
BEGIN
	SELECT subscription_tier_id, subscription_tier_name, subscription_tier_description, order_sequence 
    FROM subscription_tier 
    ORDER BY subscription_tier_id;
END //

DROP PROCEDURE IF EXISTS generateSubscriptionTierOptions//
CREATE PROCEDURE generateSubscriptionTierOptions()
BEGIN
	SELECT subscription_tier_id, subscription_tier_name 
    FROM subscription_tier 
    ORDER BY subscription_tier_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */