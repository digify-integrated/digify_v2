/* Subscriber Table */

DROP TABLE IF EXISTS subscriber;
CREATE TABLE subscriber (
    subscriber_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    subscriber_name VARCHAR(500) NOT NULL,
    company_name VARCHAR(200),
    phone VARCHAR(50),
    email VARCHAR(255),
    subscriber_status VARCHAR(10) DEFAULT 'Active',
    subscription_tier_id INT UNSIGNED NOT NULL,
    subscription_tier_name VARCHAR(100) NOT NULL,
    billing_cycle_id INT UNSIGNED NOT NULL,
    billing_cycle_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (subscription_tier_id) REFERENCES subscription_tier(subscription_tier_id),
    FOREIGN KEY (billing_cycle_id) REFERENCES billing_cycle(billing_cycle_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX subscriber_index_subscriber_id ON subscriber(subscriber_id);
CREATE INDEX subscriber_index_subscriber_status ON subscriber(subscriber_status);
CREATE INDEX subscriber_index_subscription_tier_id ON subscriber(subscription_tier_id);
CREATE INDEX subscriber_index_billing_cycle_id ON subscriber(billing_cycle_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Subscription Code Table */

DROP TABLE IF EXISTS subscription;
CREATE TABLE subscription (
    subscription_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subscriber_id INT UNSIGNED NOT NULL,
    subscription_start_date DATE,
    subscription_end_date DATE,
    deactivation_date DATE,
    no_users INT NOT NULL,
    remarks VARCHAR(1000),
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (subscriber_id) REFERENCES subscriber(subscriber_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX subscription_index_subscription_id ON subscription(subscription_id);
CREATE INDEX subscription_index_subscriber_id ON subscription(subscriber_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */