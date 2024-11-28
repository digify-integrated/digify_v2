<?php

class EmploymentTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkEmploymentTypeExist($p_employment_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkEmploymentTypeExist(:p_employment_type_id)');
        $stmt->bindValue(':p_employment_type_id', $p_employment_type_id, PDO::PARAM_INT);
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
    public function saveEmploymentType($p_employment_type_id, $p_employment_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveEmploymentType(:p_employment_type_id, :p_employment_type_name, :p_last_log_by, @p_new_employment_type_id)');
        $stmt->bindValue(':p_employment_type_id', $p_employment_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_employment_type_name', $p_employment_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_employment_type_id AS employment_type_id');
        $employmentTypeID = $result->fetch(PDO::FETCH_ASSOC)['employment_type_id'];

        $stmt->closeCursor();
        
        return $employmentTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteEmploymentType($p_employment_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteEmploymentType(:p_employment_type_id)');
        $stmt->bindValue(':p_employment_type_id', $p_employment_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmploymentType($p_employment_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getEmploymentType(:p_employment_type_id)');
        $stmt->bindValue(':p_employment_type_id', $p_employment_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>