DELIMITER //

/* Check Stored Procedure */

DROP PROCEDURE IF EXISTS checkEmployeeExist//
CREATE PROCEDURE checkEmployeeExist(
    IN p_employee_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM employee
    WHERE employee_id = p_employee_id;
END //

DROP PROCEDURE IF EXISTS checkEmployeeLanguageExist//
CREATE PROCEDURE checkEmployeeLanguageExist(
    IN p_employee_language_id INT
)
BEGIN
	SELECT COUNT(*) AS total
    FROM employee_language
    WHERE employee_language_id = p_employee_language_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Insert Stored Procedure */

DROP PROCEDURE IF EXISTS insertEmployee//
CREATE PROCEDURE insertEmployee(
    IN p_full_name VARCHAR(1000),
    IN p_first_name VARCHAR(300),
    IN p_middle_name VARCHAR(300),
    IN p_last_name VARCHAR(300),
    IN p_suffix VARCHAR(10),
    IN p_last_log_by INT, 
    OUT p_new_employee_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    INSERT INTO employee (full_name, first_name, middle_name, last_name, suffix, last_log_by) 
    VALUES(p_full_name, p_first_name, p_middle_name, p_last_name, p_suffix, p_last_log_by);
        
    SET p_new_employee_id = LAST_INSERT_ID();

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Update Stored Procedure */

DROP PROCEDURE IF EXISTS updateEmployeeImage//
CREATE PROCEDURE updateEmployeeImage(
	IN p_employee_id INT, 
	IN p_employee_image VARCHAR(500), 
	IN p_last_log_by INT
)
BEGIN
 	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET employee_image = p_employee_image,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeePINCode//
CREATE PROCEDURE updateEmployeePINCode(
    IN p_employee_id INT,
    IN p_pin_code VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET pin_code = p_pin_code,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeBadgeID//
CREATE PROCEDURE updateEmployeeBadgeID(
    IN p_employee_id INT,
    IN p_badge_id VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET badge_id = p_badge_id,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeePrivateEmail//
CREATE PROCEDURE updateEmployeePrivateEmail(
    IN p_employee_id INT,
    IN p_private_email VARCHAR(255),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET private_email = p_private_email,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeePrivatePhone//
CREATE PROCEDURE updateEmployeePrivatePhone(
    IN p_employee_id INT,
    IN p_private_phone VARCHAR(20),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET private_phone = p_private_phone,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeePrivateTelephone//
CREATE PROCEDURE updateEmployeePrivateTelephone(
    IN p_employee_id INT,
    IN p_private_telephone VARCHAR(20),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET private_telephone = p_private_telephone,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeNationality//
CREATE PROCEDURE updateEmployeeNationality(
    IN p_employee_id INT,
    IN p_nationality_id INT,
    IN p_nationality_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET nationality_id = p_nationality_id,
        nationality_name = p_nationality_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeGender//
CREATE PROCEDURE updateEmployeeGender(
    IN p_employee_id INT,
    IN p_gender_id INT,
    IN p_gender_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET gender_id = p_gender_id,
        gender_name = p_gender_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeBirthday//
CREATE PROCEDURE updateEmployeeBirthday(
    IN p_employee_id INT,
    IN p_birthday DATE,
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET birthday = p_birthday,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeePlaceOfBirth//
CREATE PROCEDURE updateEmployeePlaceOfBirth(
    IN p_employee_id INT,
    IN p_place_of_birth VARCHAR(1000),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET place_of_birth = p_place_of_birth,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeCompany//
CREATE PROCEDURE updateEmployeeCompany(
    IN p_employee_id INT,
    IN p_company_id INT,
    IN p_company_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET company_id = p_company_id,
        company_name = p_company_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeDepartment//
CREATE PROCEDURE updateEmployeeDepartment(
    IN p_employee_id INT,
    IN p_department_id INT,
    IN p_department_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET department_id = p_department_id,
        department_name = p_department_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeJobPosition//
CREATE PROCEDURE updateEmployeeJobPosition(
    IN p_employee_id INT,
    IN p_job_position_id INT,
    IN p_job_position_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET job_position_id = p_job_position_id,
        job_position_name = p_job_position_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeManager//
CREATE PROCEDURE updateEmployeeManager(
    IN p_employee_id INT,
    IN p_manager_id INT,
    IN p_manager_name VARCHAR(1000),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET manager_id = p_manager_id,
        manager_name = p_manager_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeTimeOffApprover//
CREATE PROCEDURE updateEmployeeTimeOffApprover(
    IN p_employee_id INT,
    IN p_time_off_approver_id INT,
    IN p_time_off_approver_name VARCHAR(1000),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET time_off_approver_id = p_time_off_approver_id,
        time_off_approver_name = p_time_off_approver_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeWorkLocation//
CREATE PROCEDURE updateEmployeeWorkLocation(
    IN p_employee_id INT,
    IN p_work_location_id INT,
    IN p_work_location_name VARCHAR(100),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET work_location_id = p_work_location_id,
        work_location_name = p_work_location_name,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeOnBoardDate//
CREATE PROCEDURE updateEmployeeOnBoardDate(
    IN p_employee_id INT,
    IN p_on_board_date DATE,
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET on_board_date = p_on_board_date,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeWorkEmail//
CREATE PROCEDURE updateEmployeeWorkEmail(
    IN p_employee_id INT,
    IN p_work_email VARCHAR(255),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET work_email = p_work_email,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeWorkPhone//
CREATE PROCEDURE updateEmployeeWorkPhone(
    IN p_employee_id INT,
    IN p_work_phone VARCHAR(20),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET work_phone = p_work_phone,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS updateEmployeeWorkTelephone//
CREATE PROCEDURE updateEmployeeWorkTelephone(
    IN p_employee_id INT,
    IN p_work_telephone VARCHAR(20),
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    UPDATE employee
    SET work_telephone = p_work_telephone,
        last_log_by = p_last_log_by
    WHERE employee_id = p_employee_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Save Stored Procedure */

DROP PROCEDURE IF EXISTS saveEmployeeLanguage//
CREATE PROCEDURE saveEmployeeLanguage(
    IN p_employee_language_id INT, 
    IN p_employee_id INT, 
    IN p_language_id INT, 
    IN p_language_name VARCHAR(100), 
    IN p_language_proficiency_id INT, 
    IN p_language_proficiency_name VARCHAR(100), 
    IN p_last_log_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    IF p_employee_language_id IS NULL OR NOT EXISTS (SELECT 1 FROM employee_language WHERE employee_language_id = p_employee_language_id) THEN
        INSERT INTO employee_language (employee_id, language_id, language_name, language_proficiency_id, language_proficiency_name, last_log_by) 
        VALUES(p_employee_id, p_language_id, p_language_name, p_language_proficiency_id, p_language_proficiency_name, p_last_log_by);
    ELSE
        UPDATE employee_language
        SET employee_id = p_employee_id,
            language_id = p_language_id,
            language_name = p_language_name,
            language_proficiency_id = p_language_proficiency_id,
            language_proficiency_name = p_language_proficiency_name,
            last_log_by = p_last_log_by
        WHERE employee_language_id = p_employee_language_id;
    END IF;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Delete Stored Procedure */

DROP PROCEDURE IF EXISTS deleteEmployee//
CREATE PROCEDURE deleteEmployee(
    IN p_employee_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM employee WHERE employee_id = p_employee_id;

    COMMIT;
END //

DROP PROCEDURE IF EXISTS deleteEmployeeLanguage//
CREATE PROCEDURE deleteEmployeeLanguage(
    IN p_employee_language_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
    END;

    START TRANSACTION;

    DELETE FROM employee_language WHERE employee_language_id = p_employee_language_id;

    COMMIT;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Get Stored Procedure */

DROP PROCEDURE IF EXISTS getEmployee//
CREATE PROCEDURE getEmployee(
    IN p_employee_id INT
)
BEGIN
	SELECT * FROM employee
	WHERE employee_id = p_employee_id;
END //

DROP PROCEDURE IF EXISTS getEmployeeLanguage//
CREATE PROCEDURE getEmployeeLanguage(
    IN p_employee_language_id INT
)
BEGIN
	SELECT * FROM employee_language
	WHERE employee_language_id = p_employee_language_id;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Generate Stored Procedure */

DROP PROCEDURE IF EXISTS generateEmployeeCard//
CREATE PROCEDURE generateEmployeeCard(
    IN p_search_value TEXT,
    IN p_filter_by_company TEXT,
    IN p_filter_by_department TEXT,
    IN p_filter_by_job_position TEXT,
    IN p_filter_by_employee_status TEXT,
    IN p_filter_by_work_location TEXT,
    IN p_filter_by_employment_type TEXT,
    IN p_filter_by_gender TEXT,
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    DECLARE query TEXT;
    DECLARE filter_conditions TEXT DEFAULT '';

    SET query = 'SELECT employee_id, employee_image, full_name, department_name, job_position_name, employment_status
                FROM employee WHERE 1';

    IF p_search_value IS NOT NULL AND p_search_value <> '' THEN
        SET query = CONCAT(query, ' AND (
            first_name LIKE ? OR
            middle_name LIKE ? OR
            last_name LIKE ? OR
            suffix LIKE ? OR
            department_name LIKE ? OR
            job_position_name LIKE ? OR
            employment_status LIKE ?
        )');
    END IF;

    IF p_filter_by_company IS NOT NULL AND p_filter_by_company <> '' THEN
        SET filter_conditions = CONCAT(filter_conditions, ' company_id IN (', p_filter_by_company, ')');
    END IF;

    IF p_filter_by_department IS NOT NULL AND p_filter_by_department <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' department_id IN (', p_filter_by_department, ')');
    END IF;

    IF p_filter_by_job_position IS NOT NULL AND p_filter_by_job_position <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' job_position_id IN (', p_filter_by_job_position, ')');
    END IF;

    IF p_filter_by_employee_status IS NOT NULL AND p_filter_by_employee_status <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' employment_status IN (', p_filter_by_employee_status, ')');
    END IF;

    IF p_filter_by_work_location IS NOT NULL AND p_filter_by_work_location <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' work_location_id IN (', p_filter_by_work_location, ')');
    END IF;

    IF p_filter_by_employment_type IS NOT NULL AND p_filter_by_employment_type <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' employment_type_id IN (', p_filter_by_employment_type, ')');
    END IF;

    IF p_filter_by_gender IS NOT NULL AND p_filter_by_gender <> '' THEN
        IF filter_conditions <> '' THEN
            SET filter_conditions = CONCAT(filter_conditions, ' AND ');
        END IF;

        SET filter_conditions = CONCAT(filter_conditions, ' gender_id IN (', p_filter_by_gender, ')');
    END IF;

    IF filter_conditions <> '' THEN
        SET query = CONCAT(query, ' WHERE ', filter_conditions);
    END IF;

    SET query = CONCAT(query, ' ORDER BY full_name LIMIT ?, ?;');

    PREPARE stmt FROM query;
    IF p_search_value IS NOT NULL AND p_search_value <> '' THEN
        EXECUTE stmt USING CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), CONCAT("%", p_search_value, "%"), p_offset, p_limit;
    ELSE
        EXECUTE stmt USING p_offset, p_limit;
    END IF;

    DEALLOCATE PREPARE stmt;
END //

DROP PROCEDURE IF EXISTS generateEmployeeLanguageList//
CREATE PROCEDURE generateEmployeeLanguageList(
    IN p_employee_id INT
)
BEGIN
	SELECT employee_language_id, language_name, language_proficiency_name
    FROM employee_language 
    WHERE employee_id = p_employee_id
    ORDER BY language_name;
END //

DROP PROCEDURE IF EXISTS generateEmployeeEducationalBackgroundList//
CREATE PROCEDURE generateEmployeeEducationalBackgroundList(
    IN p_employee_id INT
)
BEGIN
	SELECT employee_education_id, school, degree, field_of_study, start_month, start_year, end_month, end_year, activities_societies, education_description
    FROM employee_education 
    WHERE employee_id = p_employee_id
    ORDER BY 
    CASE 
        WHEN end_year IS NULL AND end_month IS NULL THEN 1  -- Ongoing education first
        ELSE 0
    END,
    COALESCE(end_year, start_year) DESC, 
    COALESCE(end_month, start_month) DESC;
END //

DROP PROCEDURE IF EXISTS generateEmployeeOptions//
CREATE PROCEDURE generateEmployeeOptions()
BEGIN
	SELECT employee_id, full_name
    FROM employee 
    ORDER BY full_name;
END //

DROP PROCEDURE IF EXISTS generateParentEmployeeOptions//
CREATE PROCEDURE generateParentEmployeeOptions(
    IN p_employee_id INT
)
BEGIN
	SELECT employee_id, full_name
    FROM employee 
    WHERE employee_id != p_employee_id
    ORDER BY full_name;
END //

/* ----------------------------------------------------------------------------------------------------------------------------- */