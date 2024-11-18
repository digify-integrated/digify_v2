DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkFileTypeExist//
CREATE PROCEDURE checkFileTypeExist(
    IN p_file_type_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM file_type
    WHERE file_type_id = p_file_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveFileType//
CREATE PROCEDURE saveFileType(
    IN p_file_type_id INT, 
    IN p_file_type_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_file_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_file_type_id IS NULL OR NOT EXISTS (SELECT 1 FROM file_type WHERE file_type_id = p_file_type_id) THEN
        INSERT INTO file_type (file_type_name, last_log_by) 
        VALUES(p_file_type_name, p_last_log_by);
        
        SET p_new_file_type_id = LAST_INSERT_ID();
    ELSE
        UPDATE file_extension
        SET file_type_name = p_file_type_name,
            last_log_by = p_last_log_by
        WHERE file_type_id = p_file_type_id;

        UPDATE file_type
        SET file_type_name = p_file_type_name,
            last_log_by = p_last_log_by
        WHERE file_type_id = p_file_type_id;

        SET p_new_file_type_id = p_file_type_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteFileType//
CREATE PROCEDURE deleteFileType(
    IN p_file_type_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM file_extension WHERE file_type_id = p_file_type_id;
    DELETE FROM file_type WHERE file_type_id = p_file_type_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getFileType//
CREATE PROCEDURE getFileType(
    IN p_file_type_id INT
)
BEGIN
	SELECT * FROM file_type
	WHERE file_type_id = p_file_type_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateFileTypeTable//
CREATE PROCEDURE generateFileTypeTable()
BEGIN
	SELECT file_type_id, file_type_name
    FROM file_type 
    ORDER BY file_type_id;
END //

DROP PROCEDURE IF EXISTS generateFileTypeOptions//
CREATE PROCEDURE generateFileTypeOptions()
BEGIN
	SELECT file_type_id, file_type_name 
    FROM file_type 
    ORDER BY file_type_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */