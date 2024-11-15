/* Upload Setting Table */

DROP TABLE IF EXISTS upload_setting;
CREATE TABLE upload_setting(
	upload_setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	upload_setting_name VARCHAR(100) NOT NULL,
	upload_setting_description VARCHAR(200) NOT NULL,
	max_file_size DOUBLE NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX upload_setting_index_upload_setting_id ON upload_setting(upload_setting_id);

INSERT INTO upload_setting (upload_setting_id, upload_setting_name, upload_setting_description, max_file_size, last_log_by) VALUES
(1, 'App Logo', 'Sets the upload setting when uploading app logo.', 800, 1),
(2, 'Internal Notes Attachment', 'Sets the upload setting when uploading internal notes attachement.', 800, 1),
(3, 'Import File', 'Sets the upload setting when importing data.', 800, 2),
(4, 'User Account Profile Picture', 'Sets the upload setting when uploading user account profile picture.', 800, 1),
(5, 'Company Logo', 'Sets the upload setting when uploading company logo.', 800, 1);


/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Upload Setting File Extension Table */

DROP TABLE IF EXISTS upload_setting_file_extension;
CREATE TABLE upload_setting_file_extension(
    upload_setting_file_extension_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
	upload_setting_id INT UNSIGNED NOT NULL,
	upload_setting_name VARCHAR(100) NOT NULL,
	file_extension_id INT UNSIGNED NOT NULL,
	file_extension_name VARCHAR(100) NOT NULL,
	file_extension VARCHAR(10) NOT NULL,
	date_assigned DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX upload_setting_file_ext_index_upload_setting_file_extension_id ON upload_setting_file_extension(upload_setting_file_extension_id);
CREATE INDEX upload_setting_file_ext_index_upload_setting_id ON upload_setting_file_extension(upload_setting_id);
CREATE INDEX upload_setting_file_ext_index_file_extension_id ON upload_setting_file_extension(file_extension_id);

INSERT INTO upload_setting_file_extension (upload_setting_file_extension_id, upload_setting_id, upload_setting_name, file_extension_id, file_extension_name, file_extension, last_log_by) VALUES
(1, 1, 'App Logo', 63, 'PNG', 'png', 1),
(2, 1, 'App Logo', 61, 'JPG', 'jpg', 1),
(3, 1, 'App Logo', 62, 'JPEG', 'jpeg', 1),
(4, 2, 'Internal Notes Attachment', 63, 'PNG', 'png', 1),
(5, 2, 'Internal Notes Attachment', 61, 'JPG', 'jpg', 1),
(6, 2, 'Internal Notes Attachment', 62, 'JPEG', 'jpeg', 1),
(7, 2, 'Internal Notes Attachment', 127, 'PDF', 'pdf', 1),
(8, 2, 'Internal Notes Attachment', 125, 'DOC', 'doc', 1),
(9, 2, 'Internal Notes Attachment', 125, 'DOCX', 'docx', 1),
(10, 2, 'Internal Notes Attachment', 130, 'TXT', 'txt', 1),
(11, 2, 'Internal Notes Attachment', 92, 'XLS', 'xls', 1),
(12, 2, 'Internal Notes Attachment', 94, 'XLSX', 'xlsx', 1),
(13, 2, 'Internal Notes Attachment', 89, 'PPT', 'ppt', 1),
(14, 2, 'Internal Notes Attachment', 90, 'PPTX', 'pptx', 1),
(15, 3, 'Import File', 25, 'CSV', 'csv', 1),
(16, 4, 'User Account Profile Picture', 63, 'PNG', 'png', 1),
(17, 4, 'User Account Profile Picture', 61, 'JPG', 'jpg', 1),
(18, 4, 'User Account Profile Picture', 62, 'JPEG', 'jpeg', 1),
(19, 5, 'Company Logo', 63, 'PNG', 'png', 1),
(20, 5, 'Company Logo', 61, 'JPG', 'jpg', 1),
(21, 5, 'Company Logo', 62, 'JPEG', 'jpeg', 1);

/* ----------------------------------------------------------------------------------------------------------------------------- */