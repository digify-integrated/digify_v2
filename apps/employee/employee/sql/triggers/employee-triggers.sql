DELIMITER //

DROP TRIGGER IF EXISTS employee_trigger_update//
CREATE TRIGGER employee_trigger_update
AFTER UPDATE ON employee
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Employee changed.<br/><br/>';

    IF NEW.full_name <> OLD.full_name THEN
        SET audit_log = CONCAT(audit_log, "Full Name: ", OLD.full_name, " -> ", NEW.full_name, "<br/>");
    END IF;

    IF NEW.first_name <> OLD.first_name THEN
        SET audit_log = CONCAT(audit_log, "First Name: ", OLD.first_name, " -> ", NEW.first_name, "<br/>");
    END IF;

    IF NEW.middle_name <> OLD.middle_name THEN
        SET audit_log = CONCAT(audit_log, "Middle Name: ", OLD.middle_name, " -> ", NEW.middle_name, "<br/>");
    END IF;

    IF NEW.last_name <> OLD.last_name THEN
        SET audit_log = CONCAT(audit_log, "Last Name: ", OLD.last_name, " -> ", NEW.last_name, "<br/>");
    END IF;

    IF NEW.suffix <> OLD.suffix THEN
        SET audit_log = CONCAT(audit_log, "Suffix: ", OLD.suffix, " -> ", NEW.suffix, "<br/>");
    END IF;

    IF NEW.nickname <> OLD.nickname THEN
        SET audit_log = CONCAT(audit_log, "Nickname: ", OLD.nickname, " -> ", NEW.nickname, "<br/>");
    END IF;

    IF NEW.private_address <> OLD.private_address THEN
        SET audit_log = CONCAT(audit_log, "Private Address: ", OLD.private_address, " -> ", NEW.private_address, "<br/>");
    END IF;

    IF NEW.private_address_city_name <> OLD.private_address_city_name THEN
        SET audit_log = CONCAT(audit_log, "Private Address City: ", OLD.private_address_city_name, " -> ", NEW.private_address_city_name, "<br/>");
    END IF;

    IF NEW.private_address_state_name <> OLD.private_address_state_name THEN
        SET audit_log = CONCAT(audit_log, "Private Address State: ", OLD.private_address_state_name, " -> ", NEW.private_address_state_name, "<br/>");
    END IF;

    IF NEW.private_address_country_name <> OLD.private_address_country_name THEN
        SET audit_log = CONCAT(audit_log, "Private Address Country: ", OLD.private_address_country_name, " -> ", NEW.private_address_country_name, "<br/>");
    END IF;

    IF NEW.private_phone <> OLD.private_phone THEN
        SET audit_log = CONCAT(audit_log, "Private Phone: ", OLD.private_phone, " -> ", NEW.private_phone, "<br/>");
    END IF;

    IF NEW.private_telephone <> OLD.private_telephone THEN
        SET audit_log = CONCAT(audit_log, "Private Telephone: ", OLD.private_telephone, " -> ", NEW.private_telephone, "<br/>");
    END IF;

    IF NEW.private_email <> OLD.private_email THEN
        SET audit_log = CONCAT(audit_log, "Private Email: ", OLD.private_email, " -> ", NEW.private_email, "<br/>");
    END IF;

    IF NEW.civil_status_name <> OLD.civil_status_name THEN
        SET audit_log = CONCAT(audit_log, "Civil Status: ", OLD.civil_status_name, " -> ", NEW.civil_status_name, "<br/>");
    END IF;

    IF NEW.dependents <> OLD.dependents THEN
        SET audit_log = CONCAT(audit_log, "Dependents: ", OLD.dependents, " -> ", NEW.dependents, "<br/>");
    END IF;

    IF NEW.nationality_name <> OLD.nationality_name THEN
        SET audit_log = CONCAT(audit_log, "Nationality: ", OLD.nationality_name, " -> ", NEW.nationality_name, "<br/>");
    END IF;

    IF NEW.gender_name <> OLD.gender_name THEN
        SET audit_log = CONCAT(audit_log, "Gender: ", OLD.gender_name, " -> ", NEW.gender_name, "<br/>");
    END IF;

    IF NEW.religion_name <> OLD.religion_name THEN
        SET audit_log = CONCAT(audit_log, "Religion: ", OLD.religion_name, " -> ", NEW.religion_name, "<br/>");
    END IF;

    IF NEW.blood_type_name <> OLD.blood_type_name THEN
        SET audit_log = CONCAT(audit_log, "Blood Type: ", OLD.blood_type_name, " -> ", NEW.blood_type_name, "<br/>");
    END IF;

    IF NEW.birthday <> OLD.birthday THEN
        SET audit_log = CONCAT(audit_log, "Birthday: ", OLD.birthday, " -> ", NEW.birthday, "<br/>");
    END IF;

    IF NEW.place_of_birth <> OLD.place_of_birth THEN
        SET audit_log = CONCAT(audit_log, "Place of Birth: ", OLD.place_of_birth, " -> ", NEW.place_of_birth, "<br/>");
    END IF;

    IF NEW.home_work_distance <> OLD.home_work_distance THEN
        SET audit_log = CONCAT(audit_log, "Home-Work Distance: ", OLD.home_work_distance, " km -> ", NEW.home_work_distance, " km<br/>");
    END IF;

    IF NEW.height <> OLD.height THEN
        SET audit_log = CONCAT(audit_log, "Height: ", OLD.height, " cm -> ", NEW.height, " cm<br/>");
    END IF;

    IF NEW.weight <> OLD.weight THEN
        SET audit_log = CONCAT(audit_log, "Weight: ", OLD.weight, " kg -> ", NEW.weight, " kg<br/>");
    END IF;

    IF NEW.employment_status <> OLD.employment_status THEN
        SET audit_log = CONCAT(audit_log, "Employment Status: ", OLD.employment_status, " -> ", NEW.employment_status, "<br/>");
    END IF;

    IF NEW.company_name <> OLD.company_name THEN
        SET audit_log = CONCAT(audit_log, "Company: ", OLD.company_name, " -> ", NEW.company_name, "<br/>");
    END IF;

    IF NEW.department_name <> OLD.department_name THEN
        SET audit_log = CONCAT(audit_log, "Department: ", OLD.department_name, " -> ", NEW.department_name, "<br/>");
    END IF;

    IF NEW.job_position_name <> OLD.job_position_name THEN
        SET audit_log = CONCAT(audit_log, "Job Position: ", OLD.job_position_name, " -> ", NEW.job_position_name, "<br/>");
    END IF;

    IF NEW.work_phone <> OLD.work_phone THEN
        SET audit_log = CONCAT(audit_log, "Work Phone: ", OLD.work_phone, " -> ", NEW.work_phone, "<br/>");
    END IF;

    IF NEW.work_telephone <> OLD.work_telephone THEN
        SET audit_log = CONCAT(audit_log, "Work Telephone: ", OLD.work_telephone, " -> ", NEW.work_telephone, "<br/>");
    END IF;

    IF NEW.work_email <> OLD.work_email THEN
        SET audit_log = CONCAT(audit_log, "Work Email: ", OLD.work_email, " -> ", NEW.work_email, "<br/>");
    END IF;

    IF NEW.manager_name <> OLD.manager_name THEN
        SET audit_log = CONCAT(audit_log, "Manager: ", OLD.manager_name, " -> ", NEW.manager_name, "<br/>");
    END IF;

    IF NEW.work_location_name <> OLD.work_location_name THEN
        SET audit_log = CONCAT(audit_log, "Work Location: ", OLD.work_location_name, " -> ", NEW.work_location_name, "<br/>");
    END IF;

    IF NEW.employment_type_name <> OLD.employment_type_name THEN
        SET audit_log = CONCAT(audit_log, "Employment Type: ", OLD.employment_type_name, " -> ", NEW.employment_type_name, "<br/>");
    END IF;

    IF NEW.pin_code <> OLD.pin_code THEN
        SET audit_log = CONCAT(audit_log, "Pin Code: ", OLD.pin_code, " -> ", NEW.pin_code, "<br/>");
    END IF;

    IF NEW.badge_id <> OLD.badge_id THEN
        SET audit_log = CONCAT(audit_log, "Badge ID: ", OLD.badge_id, " -> ", NEW.badge_id, "<br/>");
    END IF;

    IF NEW.on_board_date <> OLD.on_board_date THEN
        SET audit_log = CONCAT(audit_log, "On-Board Date: ", OLD.on_board_date, " -> ", NEW.on_board_date, "<br/>");
    END IF;

    IF NEW.off_board_date <> OLD.off_board_date THEN
        SET audit_log = CONCAT(audit_log, "Off-Board Date: ", OLD.off_board_date, " -> ", NEW.off_board_date, "<br/>");
    END IF;

    IF NEW.time_off_approver_name <> OLD.time_off_approver_name THEN
        SET audit_log = CONCAT(audit_log, "Time-Off Approver: ", OLD.time_off_approver_name, " -> ", NEW.time_off_approver_name, "<br/>");
    END IF;

    IF NEW.departure_reason_name <> OLD.departure_reason_name THEN
        SET audit_log = CONCAT(audit_log, "Departure Reason: ", OLD.departure_reason_name, " -> ", NEW.departure_reason_name, "<br/>");
    END IF;

    IF NEW.detailed_departure_reason <> OLD.detailed_departure_reason THEN
        SET audit_log = CONCAT(audit_log, "Detailed Departure Reason: ", OLD.detailed_departure_reason, " -> ", NEW.detailed_departure_reason, "<br/>");
    END IF;
    
    IF audit_log <> 'Employee changed.<br/><br/>' THEN
        INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
        VALUES ('employee', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
    END IF;
END //

DROP TRIGGER IF EXISTS employee_trigger_insert//
CREATE TRIGGER employee_trigger_insert
AFTER INSERT ON employee
FOR EACH ROW
BEGIN
    DECLARE audit_log TEXT DEFAULT 'Employee created.';

    INSERT INTO audit_log (table_name, reference_id, log, changed_by, changed_at) 
    VALUES ('employee', NEW.employee_id, audit_log, NEW.last_log_by, NOW());
END //