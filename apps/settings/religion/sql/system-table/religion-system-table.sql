/* Religion Table */

DROP TABLE IF EXISTS religion;
CREATE TABLE religion (
    religion_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    religion_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX religion_index_religion_id ON religion(religion_id);

INSERT INTO religion (religion_name, last_log_by)
VALUES
('Christianity', 1),
('Islam', 1),
('Hinduism', 1),
('Buddhism', 1),
('Judaism', 1),
('Sikhism', 1),
('Atheism', 1),
('Agnosticism', 1),
('Baháʼí', 1),
('Confucianism', 1),
('Shinto', 1),
('Taoism', 1),
('Zoroastrianism', 1),
('Jainism', 1),
('Spiritualism', 1),
('Paganism', 1),
('Rastafarianism', 1),
('Unitarian Universalism', 1),
('Scientology', 1),
('Druze', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */