DELIMITER //

DROP TRIGGER IF EXISTS subscriber_trigger_update//
CREATE TRIGGER subscriber_trigger_update
AFTER UPDATE ON subscriber
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Subscriber changed.<br/><br/>';

    IF NEW.subscriber_name <> OLD.subscriber_name THEN
        SET audit_log = CONCAT(audit_log, "Subscriber Name: ", OLD.subscriber_name, " -> ", NEW.subscriber_name, "<br/>");
    END IF;

    IF NEW.company_name <> OLD.company_name THEN
        SET audit_log = CONCAT(audit_log, "Company Name: ", OLD.company_name, " -> ", NEW.company_name, "<br/>");
    END IF;

    IF NEW.phone <> OLD.phone THEN
        SET audit_log = CONCAT(audit_log, "Phone: ", OLD.phone, " -> ", NEW.phone, "<br/>");
    END IF;

    IF NEW.email <> OLD.email THEN
        SET audit_log = CONCAT(audit_log, "Email: ", OLD.email, " -> ", NEW.email, "<br/>");
    END IF;

    IF NEW.subscriber_status <> OLD.subscriber_status THEN
        SET audit_log = CONCAT(audit_log, "Status: ", OLD.subscriber_status, " -> ", NEW.subscriber_status, "<br/>");
    END IF;

    IF NEW.subscription_tier_name <> OLD.subscription_tier_name THEN
        SET audit_log = CONCAT(audit_log, "Subscription Tier: ", OLD.subscription_tier_name, " -> ", NEW.subscription_tier_name, "<br/>");
    END IF;

    IF NEW.billing_cycle_name <> OLD.billing_cycle_name THEN
        SET audit_log = CONCAT(audit_log, "Billing Cycle: ", OLD.billing_cycle_name, " -> ", NEW.billing_cycle_name, "<br/>");
    END IF;
    
    IF audit_log <> 'Subscriber changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('subscriber', NEW.subscriber_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS subscriber_trigger_insert//
CREATE TRIGGER subscriber_trigger_insert
AFTER INSERT ON subscriber
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Subscriber created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('subscriber', NEW.subscriber_id, audit_log, NEW.last_log_by, NOW());
END //

DROP TRIGGER IF EXISTS subscription_trigger_update//
CREATE TRIGGER subscription_trigger_update
AFTER UPDATE ON subscription
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Subscription changed.<br/><br/>';

    IF NEW.subscription_start_date <> OLD.subscription_start_date THEN
        SET audit_log = CONCAT(audit_log, "Subscription Start Date: ", OLD.subscription_start_date, " -> ", NEW.subscription_start_date, "<br/>");
    END IF;

    IF NEW.subscription_end_date <> OLD.subscription_end_date THEN
        SET audit_log = CONCAT(audit_log, "Subscription End Date: ", OLD.subscription_end_date, " -> ", NEW.subscription_end_date, "<br/>");
    END IF;

    IF NEW.deactivation_date <> OLD.deactivation_date THEN
        SET audit_log = CONCAT(audit_log, "Deactivation Date: ", OLD.deactivation_date, " -> ", NEW.deactivation_date, "<br/>");
    END IF;

    IF NEW.grace_period <> OLD.grace_period THEN
        SET audit_log = CONCAT(audit_log, "Deactivation Date: ", OLD.grace_period, " Day(s) -> ", NEW.grace_period, " Day(s)<br/>");
    END IF;

    IF NEW.no_users <> OLD.no_users THEN
        SET audit_log = CONCAT(audit_log, "Number of Users: ", OLD.no_users, " Day(s) -> ", NEW.no_users, " Day(s)<br/>");
    END IF;

    IF NEW.remarks <> OLD.remarks THEN
        SET audit_log = CONCAT(audit_log, "Remarks: ", OLD.remarks, " Day(s) -> ", NEW.remarks, " Day(s)<br/>");
    END IF;
    
    IF audit_log <> 'Subscription changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('subscription', NEW.subscription_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS subscription_trigger_insert//
CREATE TRIGGER subscription_trigger_insert
AFTER INSERT ON subscription
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Subscription created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('subscription', NEW.subscription_id, audit_log, NEW.last_log_by, NOW());
END //