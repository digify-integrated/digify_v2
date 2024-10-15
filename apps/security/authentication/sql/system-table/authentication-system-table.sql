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

/* Internal Notes Table */

DROP TABLE IF EXISTS internal_notes;
CREATE TABLE internal_notes (
    internal_notes_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    table_name VARCHAR(255) NOT NULL,
    reference_id INT NOT NULL,
    internal_note VARCHAR(5000) NOT NULL,
    internal_note_by INT UNSIGNED NOT NULL,
    internal_note_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (internal_note_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX internal_notes_index_internal_notes_id ON internal_notes(internal_notes_id);
CREATE INDEX internal_notes_index_table_name ON internal_notes(table_name);
CREATE INDEX internal_notes_index_reference_id ON internal_notes(reference_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Internal Notes Attachment Table */

DROP TABLE IF EXISTS internal_notes_attachment;
CREATE TABLE internal_notes_attachment (
    internal_notes_attachment_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    internal_notes_id INT UNSIGNED NOT NULL,
    attachment_file_name VARCHAR(500) NOT NULL,
    attachment_file_size DOUBLE NOT NULL,
    attachment_path_file VARCHAR(500) NOT NULL,
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (internal_notes_id) REFERENCES internal_notes(internal_notes_id)
);

CREATE INDEX internal_notes_attachment_index_internal_notes_id ON internal_notes_attachment(internal_notes_attachment_id);
CREATE INDEX internal_notes_attachment_index_table_name ON internal_notes_attachment(internal_notes_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */