DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkEducationalStageExist//
CREATE PROCEDURE checkEducationalStageExist(
    IN p_educational_stage_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM educational_stage
    WHERE educational_stage_id = p_educational_stage_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveEducationalStage//
CREATE PROCEDURE saveEducationalStage(
    IN p_educational_stage_id INT, 
    IN p_educational_stage_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_educational_stage_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_educational_stage_id IS NULL OR NOT EXISTS (SELECT 1 FROM educational_stage WHERE educational_stage_id = p_educational_stage_id) THEN
        INSERT INTO educational_stage (educational_stage_name, last_log_by) 
        VALUES(p_educational_stage_name, p_last_log_by);
        
        SET p_new_educational_stage_id = LAST_INSERT_ID();
    ELSE
        UPDATE educational_stage
        SET educational_stage_name = p_educational_stage_name,
            last_log_by = p_last_log_by
        WHERE educational_stage_id = p_educational_stage_id;

        SET p_new_educational_stage_id = p_educational_stage_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteEducationalStage//
CREATE PROCEDURE deleteEducationalStage(
    IN p_educational_stage_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM educational_stage WHERE educational_stage_id = p_educational_stage_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getEducationalStage//
CREATE PROCEDURE getEducationalStage(
    IN p_educational_stage_id INT
)
BEGIN
	SELECT * FROM educational_stage
	WHERE educational_stage_id = p_educational_stage_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateEducationalStageTable//
CREATE PROCEDURE generateEducationalStageTable()
BEGIN
	SELECT educational_stage_id, educational_stage_name
    FROM educational_stage 
    ORDER BY educational_stage_id;
END //

DROP PROCEDURE IF EXISTS generateEducationalStageOptions//
CREATE PROCEDURE generateEducationalStageOptions()
BEGIN
	SELECT educational_stage_id, educational_stage_name 
    FROM educational_stage 
    ORDER BY educational_stage_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */