DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkJobPositionExist//
CREATE PROCEDURE checkJobPositionExist(
    IN p_job_position_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM job_position
    WHERE job_position_id = p_job_position_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveJobPosition//
CREATE PROCEDURE saveJobPosition(
    IN p_job_position_id INT, 
    IN p_job_position_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_job_position_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_job_position_id IS NULL OR NOT EXISTS (SELECT 1 FROM job_position WHERE job_position_id = p_job_position_id) THEN
        INSERT INTO job_position (job_position_name, last_log_by) 
        VALUES(p_job_position_name, p_last_log_by);
        
        SET p_new_job_position_id = LAST_INSERT_ID();
    ELSE
        UPDATE job_position
        SET job_position_name = p_job_position_name,
            last_log_by = p_last_log_by
        WHERE job_position_id = p_job_position_id;

        SET p_new_job_position_id = p_job_position_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteJobPosition//
CREATE PROCEDURE deleteJobPosition(
    IN p_job_position_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM job_position WHERE job_position_id = p_job_position_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getJobPosition//
CREATE PROCEDURE getJobPosition(
    IN p_job_position_id INT
)
BEGIN
	SELECT * FROM job_position
	WHERE job_position_id = p_job_position_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateJobPositionTable//
CREATE PROCEDURE generateJobPositionTable()
BEGIN
	SELECT job_position_id, job_position_name
    FROM job_position 
    ORDER BY job_position_id;
END //

DROP PROCEDURE IF EXISTS generateJobPositionOptions//
CREATE PROCEDURE generateJobPositionOptions()
BEGIN
	SELECT job_position_id, job_position_name 
    FROM job_position 
    ORDER BY job_position_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */