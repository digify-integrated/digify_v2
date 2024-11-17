DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkFileExtensionExist//
CREATE PROCEDURE checkFileExtensionExist(
    IN p_file_extension_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM file_extension
    WHERE file_extension_id = p_file_extension_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveFileExtension//
CREATE PROCEDURE saveFileExtension(
    IN p_file_extension_id INT, 
    IN p_file_extension_name VARCHAR(100), 
    IN p_file_extension VARCHAR(10), 
    IN p_file_type_id INT, 
    IN p_file_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_file_extension_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_file_extension_id IS NULL OR NOT EXISTS (SELECT 1 FROM file_extension WHERE file_extension_id = p_file_extension_id) THEN
        INSERT INTO file_extension (file_extension_name, file_extension, file_type_id, file_type_name, last_log_by) 
        VALUES(p_file_extension_name, p_file_extension, p_file_type_id, p_file_type_name, p_last_log_by);
        
        SET p_new_file_extension_id = LAST_INSERT_ID();
    ELSE
        UPDATE file_extension
        SET file_extension_name = p_file_extension_name,
            file_extension = p_file_extension,
            file_type_id = p_file_type_id,
            file_type_name = p_file_type_name,
            last_log_by = p_last_log_by
        WHERE file_extension_id = p_file_extension_id;

        SET p_new_file_extension_id = p_file_extension_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteFileExtension//
CREATE PROCEDURE deleteFileExtension(
    IN p_file_extension_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM file_extension WHERE file_extension_id = p_file_extension_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getFileExtension//
CREATE PROCEDURE getFileExtension(
    IN p_file_extension_id INT
)
BEGIN
	SELECT * FROM file_extension
	WHERE file_extension_id = p_file_extension_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateFileExtensionTable//
CREATE PROCEDURE generateFileExtensionTable(
    IN p_filter_by_file_type TEXT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT file_extension_id, file_extension_name, file_extension, file_type_name 
                FROM file_extension ';

    IF p_filter_by_file_type IS NOT NULL AND p_filter_by_file_type <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' file_type_id IN (', p_filter_by_file_type, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY file_extension_name');

    PREPARE stmt FROM query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateFileExtensionOptions//
CREATE PROCEDURE generateFileExtensionOptions()
BEGIN
	SELECT file_extension_id, file_extension_name, file_extension
    FROM file_extension 
    ORDER BY file_extension_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */