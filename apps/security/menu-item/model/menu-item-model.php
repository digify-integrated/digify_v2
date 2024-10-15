<?php

class MenuItemModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkMenuItemExist($p_menu_item_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkMenuItemExist(:p_menu_item_id)');
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
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
    public function saveMenuItem($p_menu_item_id, $p_menu_item_name, $p_menu_item_url, $p_menu_item_icon, $p_menu_group_id, $p_menu_group_name, $p_app_module_id, $p_app_module_name, $p_parent_id, $p_parent_name, $p_order_sequence, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveMenuItem(:p_menu_item_id, :p_menu_item_name, :p_menu_item_url, :p_menu_item_icon, :p_menu_group_id, :p_menu_group_name, :p_app_module_id, :p_app_module_name, :p_parent_id, :p_parent_name, :p_order_sequence, :p_last_log_by, @p_new_menu_item_id)');
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_item_name', $p_menu_item_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_menu_item_url', $p_menu_item_url, PDO::PARAM_STR);
        $stmt->bindValue(':p_menu_item_icon', $p_menu_item_icon, PDO::PARAM_STR);
        $stmt->bindValue(':p_menu_group_id', $p_menu_group_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_group_name', $p_menu_group_name, PDO::PARAM_INT);
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_app_module_name', $p_app_module_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_parent_id', $p_parent_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_parent_name', $p_parent_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_order_sequence', $p_order_sequence, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_menu_item_id AS menu_item_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['menu_item_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMenuItem($p_menu_item_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteMenuItem(:p_menu_item_id)');
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getMenuItem($p_menu_item_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getMenuItem(:p_menu_item_id)');
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>