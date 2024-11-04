/* Subscription Tier Table */

DROP TABLE IF EXISTS subscription_tier;
CREATE TABLE subscription_tier (
    subscription_tier_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    subscription_tier_name VARCHAR(100) NOT NULL,
    subscription_tier_description VARCHAR(500) NOT NULL,
    order_sequence TINYINT(10) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX subscription_tier_index_subscription_tier_id ON subscription_tier(subscription_tier_id);

INSERT INTO subscription_tier (subscription_tier_name, subscription_tier_description, order_sequence, last_log_by) VALUES
('LaunchPad', 'A solid foundation for startups ready to take off, featuring essential tools for online presence.', 1, 1),
('Accelerator', 'Aimed at propelling businesses forward with enhanced features for growth and engagement.', 2, 1),
('Elevate', 'A powerful suite for businesses focused on optimizing their operations and driving performance.', 3, 1),
('Infinity', 'A one-time investment for perpetual access to a comprehensive solution that adapts with your business needs.', 4, 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */