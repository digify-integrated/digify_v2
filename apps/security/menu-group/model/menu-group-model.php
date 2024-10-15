<?php

class MenuGroupModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkMenuGroupExist($p_menu_group_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkMenuGroupExist(:p_menu_group_id)');
        $stmt->bindValue(':p_menu_group_id', $p_menu_group_id, PDO::PARAM_INT);
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
    public function saveMenuGroup($p_menu_group_id, $p_menu_group_name, $p_app_module_id, $p_app_module_name, $p_order_sequence, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveMenuGroup(:p_menu_group_id, :p_menu_group_name, :p_app_module_id, :p_app_module_name, :p_order_sequence, :p_last_log_by, @p_new_menu_group_id)');
        $stmt->bindValue(':p_menu_group_id', $p_menu_group_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_group_name', $p_menu_group_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_app_module_name', $p_app_module_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_order_sequence', $p_order_sequence, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_menu_group_id AS menu_group_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['menu_group_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMenuGroup($p_menu_group_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteMenuGroup(:p_menu_group_id)');
        $stmt->bindValue(':p_menu_group_id', $p_menu_group_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getMenuGroup($p_menu_group_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getMenuGroup(:p_menu_group_id)');
        $stmt->bindValue(':p_menu_group_id', $p_menu_group_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>