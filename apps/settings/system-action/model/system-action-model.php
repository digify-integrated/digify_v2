<?php

class SystemActionModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkSystemActionExist($p_system_action_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSystemActionExist(:p_system_action_id)');
        $stmt->bindValue(':p_system_action_id', $p_system_action_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function saveSystemAction($p_system_action_id, $p_system_action_name, $p_system_action_description, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveSystemAction(:p_system_action_id, :p_system_action_name, :p_system_action_description, :p_last_log_by, @p_new_system_action_id)');
        $stmt->bindValue(':p_system_action_id', $p_system_action_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_action_name', $p_system_action_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_system_action_description', $p_system_action_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_system_action_id AS system_action_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['system_action_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteSystemAction($p_system_action_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteSystemAction(:p_system_action_id)');
        $stmt->bindValue(':p_system_action_id', $p_system_action_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getSystemAction($p_system_action_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSystemAction(:p_system_action_id)');
        $stmt->bindValue(':p_system_action_id', $p_system_action_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>