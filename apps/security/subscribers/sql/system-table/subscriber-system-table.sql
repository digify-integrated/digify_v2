/* Subscription Code Table */

DROP TABLE IF EXISTS subscriber;

CREATE TABLE subscriber (
    subscriber_id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_name VARCHAR(500) NOT NULL,
    subscription_code TEXT NOT NULL,
    subscription_tier VARCHAR(500),
    billing_cycle VARCHAR(500),
    subscription_validity VARCHAR(500),
    no_users VARCHAR(500),
    subscription_status VARCHAR(500),
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX subscription_code_index_subscription_code_id ON subscription_code(subscription_code_id);
CREATE INDEX subscription_code_index_subscription_code ON subscription_code(subscription_code);
CREATE INDEX subscription_code_index_subscription_tier ON subscription_code(subscription_tier);
CREATE INDEX subscription_code_index_subscription_status ON subscription_code(subscription_status);
CREATE INDEX subscription_code_index_subscription_validity ON subscription_code(subscription_validity);
CREATE INDEX subscription_code_index_no_users ON subscription_code(no_users);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Subscription Code Table */

DROP TABLE IF EXISTS subscription_code;

CREATE TABLE subscription_code (
    subscription_code_id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id INT UNSIGNED NOT NULL,
    subscription_code TEXT NOT NULL,
    subscription_tier VARCHAR(500),
    billing_cycle VARCHAR(500),
    subscription_validity VARCHAR(500),
    no_users VARCHAR(500),
    subscription_status VARCHAR(500),
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (subscriber_id) REFERENCES subscriber(subscriber_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX subscription_code_index_subscription_code_id ON subscription_code(subscription_code_id);
CREATE INDEX subscription_code_index_subscription_code ON subscription_code(subscription_code);
CREATE INDEX subscription_code_index_subscription_tier ON subscription_code(subscription_tier);
CREATE INDEX subscription_code_index_subscription_status ON subscription_code(subscription_status);
CREATE INDEX subscription_code_index_subscription_validity ON subscription_code(subscription_validity);
CREATE INDEX subscription_code_index_no_users ON subscription_code(no_users);

/* ----------------------------------------------------------------------------------------------------------------------------- */