DELIMITER //

DROP TRIGGER IF EXISTS subscription_tier_trigger_update//
CREATE TRIGGER subscription_tier_trigger_update
AFTER UPDATE ON subscription_tier
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Subscription tier changed.<br/><br/>';

    IF NEW.subscription_tier_name <> OLD.subscription_tier_name THEN
        SET audit_log = CONCAT(audit_log, "Subscription Tier Name: ", OLD.subscription_tier_name, " -> ", NEW.subscription_tier_name, "<br/>");
    END IF;

    IF NEW.subscription_tier_description <> OLD.subscription_tier_description THEN
        SET audit_log = CONCAT(audit_log, "Subscription Tier Description: ", OLD.subscription_tier_description, " -> ", NEW.subscription_tier_description, "<br/>");
    END IF;

    IF NEW.order_sequence <> OLD.order_sequence THEN
        SET audit_log = CONCAT(audit_log, "Order Sequence: ", OLD.order_sequence, " -> ", NEW.order_sequence, "<br/>");
    END IF;
    
    IF audit_log <> 'Subscription tier changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('subscription_tier', NEW.subscription_tier_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS subscription_tier_trigger_insert//
CREATE TRIGGER subscription_tier_trigger_insert
AFTER INSERT ON subscription_tier
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Subscription tier created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('subscription_tier', NEW.subscription_tier_id, audit_log, NEW.last_log_by, NOW());
END //