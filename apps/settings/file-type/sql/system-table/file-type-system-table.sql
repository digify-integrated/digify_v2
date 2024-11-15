/* File Type Table */

DROP TABLE IF EXISTS file_type;
CREATE TABLE file_type (
    file_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    file_type_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX file_type_index_file_type_id ON file_type(file_type_id);

INSERT INTO file_type (file_type_id, file_type_name, last_log_by) VALUES
(1, 'Audio', 1),
(2, 'Compressed', 1),
(3, 'Disk and Media', 1),
(4, 'Data and Database', 1),
(5, 'Email', 1),
(6, 'Executable', 1),
(7, 'Font', 1),
(8, 'Image', 1),
(9, 'Internet Related', 1),
(10, 'Presentation', 1),
(11, 'Spreadsheet', 1),
(12, 'System Related', 1),
(13, 'Video', 1),
(14, 'Word Processor', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */