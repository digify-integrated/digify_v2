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
    #   Save methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function saveEmployee($p_employee_id, $p_employee_name, $p_parent_employee_id, $p_parent_employee_name, $p_manager_id, $p_manager_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveEmployee(:p_employee_id, :p_employee_name, :p_parent_employee_id, :p_parent_employee_name, :p_manager_id, :p_manager_name, :p_last_log_by, @p_new_employee_id)');
        $stmt->bindValue(':p_employee_id', $p_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_employee_name', $p_employee_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_parent_employee_id', $p_parent_employee_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_parent_employee_name', $p_parent_employee_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_manager_id', $p_manager_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_manager_name', $p_manager_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_employee_id AS employee_id');
        $employeeID = $result->fetch(PDO::FETCH_ASSOC)['employee_id'];

        $stmt->closeCursor();
        
        return $employeeID;
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