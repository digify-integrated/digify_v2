<?php

class DepartmentModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkDepartmentExist($p_department_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkDepartmentExist(:p_department_id)');
        $stmt->bindValue(':p_department_id', $p_department_id, PDO::PARAM_INT);
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
    public function saveDepartment($p_department_id, $p_department_name, $p_parent_department_id, $p_parent_department_name, $p_manager_id, $p_manager_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveDepartment(:p_department_id, :p_department_name, :p_parent_department_id, :p_parent_department_name, :p_manager_id, :p_manager_name, :p_last_log_by, @p_new_department_id)');
        $stmt->bindValue(':p_department_id', $p_department_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_department_name', $p_department_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_parent_department_id', $p_parent_department_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_parent_department_name', $p_parent_department_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_manager_id', $p_manager_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_manager_name', $p_manager_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_department_id AS department_id');
        $departmentID = $result->fetch(PDO::FETCH_ASSOC)['department_id'];

        $stmt->closeCursor();
        
        return $departmentID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteDepartment($p_department_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteDepartment(:p_department_id)');
        $stmt->bindValue(':p_department_id', $p_department_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getDepartment($p_department_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getDepartment(:p_department_id)');
        $stmt->bindValue(':p_department_id', $p_department_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>