<?php

class AppModuleModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkAppModuleExist($p_app_module_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkAppModuleExist(:p_app_module_id)');
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
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
    public function saveAppModule($p_app_module_id, $p_app_module_name, $p_app_module_description, $p_menu_item_id, $p_menu_item_name, $p_order_sequence, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveAppModule(:p_app_module_id, :p_app_module_name, :p_app_module_description, :p_menu_item_id, :p_menu_item_name, :p_order_sequence, :p_last_log_by, @p_new_app_module_id)');
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_app_module_name', $p_app_module_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_app_module_description', $p_app_module_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_item_name', $p_menu_item_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_order_sequence', $p_order_sequence, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_app_module_id AS app_module_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['app_module_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateAppLogo($p_app_module_id, $p_app_logo, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateAppLogo(:p_app_module_id, :p_app_logo, :p_last_log_by)');
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_app_logo', $p_app_logo, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteAppModule($p_app_module_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteAppModule(:p_app_module_id)');
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getAppModule($p_app_module_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getAppModule(:p_app_module_id)');
        $stmt->bindValue(':p_app_module_id', $p_app_module_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>