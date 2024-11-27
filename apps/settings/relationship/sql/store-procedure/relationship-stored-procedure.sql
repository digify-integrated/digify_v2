DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkRelationshipExist//
CREATE PROCEDURE checkRelationshipExist(
    IN p_relationship_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM relationship
    WHERE relationship_id = p_relationship_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveRelationship//
CREATE PROCEDURE saveRelationship(
    IN p_relationship_id INT, 
    IN p_relationship_name VARCHAR(100), 
    IN p_last_log_by INT, 
    OUT p_new_relationship_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_relationship_id IS NULL OR NOT EXISTS (SELECT 1 FROM relationship WHERE relationship_id = p_relationship_id) THEN
        INSERT INTO relationship (relationship_name, last_log_by) 
        VALUES(p_relationship_name, p_last_log_by);
        
        SET p_new_relationship_id = LAST_INSERT_ID();
    ELSE
        UPDATE relationship
        SET relationship_name = p_relationship_name,
            last_log_by = p_last_log_by
        WHERE relationship_id = p_relationship_id;

        SET p_new_relationship_id = p_relationship_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteRelationship//
CREATE PROCEDURE deleteRelationship(
    IN p_relationship_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM relationship WHERE relationship_id = p_relationship_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getRelationship//
CREATE PROCEDURE getRelationship(
    IN p_relationship_id INT
)
BEGIN
	SELECT * FROM relationship
	WHERE relationship_id = p_relationship_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateRelationshipTable//
CREATE PROCEDURE generateRelationshipTable()
BEGIN
	SELECT relationship_id, relationship_name
    FROM relationship 
    ORDER BY relationship_id;
END //

DROP PROCEDURE IF EXISTS generateRelationshipOptions//
CREATE PROCEDURE generateRelationshipOptions()
BEGIN
	SELECT relationship_id, relationship_name 
    FROM relationship 
    ORDER BY relationship_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */