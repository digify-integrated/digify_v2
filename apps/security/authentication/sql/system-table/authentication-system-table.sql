/* Audit Log Table */

DROP TABLE IF EXISTS audit_log;
CREATE TABLE audit_log (
    audit_log_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    table_name VARCHAR(255) NOT NULL,
    reference_id INT NOT NULL,
    log TEXT NOT NULL,
    changed_by INT UNSIGNED NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (changed_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX audit_log_index_audit_log_id ON audit_log(audit_log_id);
CREATE INDEX audit_log_index_table_name ON audit_log(table_name);
CREATE INDEX audit_log_index_reference_id ON audit_log(reference_id);
CREATE INDEX audit_log_index_changed_by ON audit_log(changed_by);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* System Subscription Table */

DROP TABLE IF EXISTS system_subscription;

CREATE TABLE system_subscription (
    system_subscription_id INT AUTO_INCREMENT PRIMARY KEY,
    system_subscription_code TEXT NOT NULL,
    subscription_tier VARCHAR(500),
    billing_cycle VARCHAR(500),
    subscription_validity VARCHAR(500),
    no_users VARCHAR(500),
    subscription_status VARCHAR(500)
);

CREATE INDEX system_subscription_index_system_subscription_id ON system_subscription(system_subscription_id);
CREATE INDEX system_subscription_index_system_subscription_code ON system_subscription(system_subscription_code);
CREATE INDEX system_subscription_index_subscription_tier ON system_subscription(subscription_tier);
CREATE INDEX system_subscription_index_subscription_status ON system_subscription(subscription_status);
CREATE INDEX system_subscription_index_subscription_validity ON system_subscription(subscription_validity);
CREATE INDEX system_subscription_index_no_users ON system_subscription(no_users);

/* ----------------------------------------------------------------------------------------------------------------------------- */