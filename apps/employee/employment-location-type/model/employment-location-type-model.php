<?php

class EmploymentLocationTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkEmploymentLocationTypeExist($p_employment_location_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkEmploymentLocationTypeExist(:p_employment_location_type_id)');
        $stmt->bindValue(':p_employment_location_type_id', $p_employment_location_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function saveEmploymentLocationType($p_employment_location_type_id, $p_employment_location_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveEmploymentLocationType(:p_employment_location_type_id, :p_employment_location_type_name, :p_last_log_by, @p_new_employment_location_type_id)');
        $stmt->bindValue(':p_employment_location_type_id', $p_employment_location_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_employment_location_type_name', $p_employment_location_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_employment_location_type_id AS employment_location_type_id');
        $employmentLocationTypeID = $result->fetch(PDO::FETCH_ASSOC)['employment_location_type_id'];

        $stmt->closeCursor();
        
        return $employmentLocationTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteEmploymentLocationType($p_employment_location_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteEmploymentLocationType(:p_employment_location_type_id)');
        $stmt->bindValue(':p_employment_location_type_id', $p_employment_location_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmploymentLocationType($p_employment_location_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getEmploymentLocationType(:p_employment_location_type_id)');
        $stmt->bindValue(':p_employment_location_type_id', $p_employment_location_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>