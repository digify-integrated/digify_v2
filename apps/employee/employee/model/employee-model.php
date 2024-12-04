<?php

class EmployeeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkEmployeeExist($p_employee_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkEmployeeExist(:p_employee_id)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Insert methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function insertEmployee($p_full_name, $p_first_name, $p_middle_name, $p_last_name, $p_suffix, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertEmployee(:p_full_name, :p_first_name, :p_middle_name, :p_last_name, :p_suffix, :p_last_log_by, @p_new_employee_id)');
        $stmt->bindValue(':p_full_name', $p_full_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_first_name', $p_first_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_middle_name', $p_middle_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_name', $p_last_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_suffix', $p_suffix, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_employee_id AS employee_id');
        $employeeID = $result->fetch(PDO::FETCH_ASSOC)['employee_id'];

        $stmt->closeCursor();
        
        return $employeeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployee($p_employee_id, $p_full_name, $p_first_name, $p_middle_name, $p_last_name, $p_suffix, $p_nickname, $p_private_address, $p_private_address_city_id, $p_private_address_city_name, $p_private_address_state_id, $p_private_address_state_name, $p_private_address_country_id, $p_private_address_country_name, $p_civil_status_id, $p_civil_status_name, $p_dependents, $p_religion_id, $p_religion_name, $p_blood_type_id, $p_blood_type_name, $p_home_work_distance, $p_height, $p_weight, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployee(:p_employee_id, :p_full_name, :p_first_name, :p_middle_name, :p_last_name, :p_suffix, :p_nickname, :p_private_address, :p_private_address_city_id, :p_private_address_city_name, :p_private_address_state_id, :p_private_address_state_name, :p_private_address_country_id, :p_private_address_country_name, :p_civil_status_id, :p_civil_status_name, :p_dependents, :p_religion_id, :p_religion_name, :p_blood_type_id, :p_blood_type_name, :p_home_work_distance, :p_height, :p_weight, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_full_name', $p_full_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_first_name', $p_first_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_middle_name', $p_middle_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_name', $p_last_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_suffix', $p_suffix, PDO::PARAM_STR);
        $stmt->bindValue(':p_nickname', $p_nickname, PDO::PARAM_STR);
        $stmt->bindValue(':p_private_address', $p_private_address, PDO::PARAM_STR);
        $stmt->bindValue(':p_private_address_city_id', $p_private_address_city_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_private_address_city_name', $p_private_address_city_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_private_address_state_id', $p_private_address_state_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_private_address_state_name', $p_private_address_state_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_private_address_country_id', $p_private_address_country_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_private_address_country_name', $p_private_address_country_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_civil_status_id', $p_civil_status_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_civil_status_name', $p_civil_status_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_dependents', $p_dependents, PDO::PARAM_INT);
        $stmt->bindValue(':p_religion_id', $p_religion_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_religion_name', $p_religion_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_blood_type_id', $p_blood_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_blood_type_name', $p_blood_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_home_work_distance', $p_home_work_distance, PDO::PARAM_STR);
        $stmt->bindValue(':p_height', $p_height, PDO::PARAM_STR);
        $stmt->bindValue(':p_weight', $p_weight, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePINCode($p_employee_id, $p_pin_code, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeePINCode(:p_employee_id, :p_pin_code, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_pin_code', $p_pin_code, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeBadgeID($p_employee_id, $p_badge_id, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeeBadgeID(:p_employee_id, :p_badge_id, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_badge_id', $p_badge_id, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePrivateEmail($p_employee_id, $p_private_email, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeePrivateEmail(:p_employee_id, :p_private_email, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_private_email', $p_private_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePrivatePhone($p_employee_id, $p_private_phone, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeePrivatePhone(:p_employee_id, :p_private_phone, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_private_phone', $p_private_phone, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePrivateTelephone($p_employee_id, $p_private_telephone, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeePrivateTelephone(:p_employee_id, :p_private_telephone, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_private_telephone', $p_private_telephone, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeNationality($p_employee_id, $p_nationality_id, $p_nationality_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeeNationality(:p_employee_id, :p_nationality_id, :p_nationality_name, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_nationality_id', $p_nationality_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_nationality_name', $p_nationality_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeGender($p_employee_id, $p_gender_id, $p_gender_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeeGender(:p_employee_id, :p_gender_id, :p_gender_name, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_gender_id', $p_gender_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_gender_name', $p_gender_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeBirthday($p_employee_id, $p_birthday, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeeBirthday(:p_employee_id, :p_birthday, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_birthday', $p_birthday, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePlaceOfBirth($p_employee_id, $p_place_of_birth, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmployeePlaceOfBirth(:p_employee_id, :p_place_of_birth, :p_last_log_by)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_place_of_birth', $p_place_of_birth, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteEmployee($p_employee_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteEmployee(:p_employee_id)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployee($p_employee_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getEmployee(:p_employee_id)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>