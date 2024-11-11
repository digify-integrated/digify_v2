<?php

class RoleModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkRoleExist($p_role_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkRoleExist(:p_role_id)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkRolePermissionExist($p_role_permission_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkRolePermissionExist(:p_role_permission_id)');
        $stmt->bindValue(':p_role_permission_id', $p_role_permission_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkRoleSystemActionPermissionExist($p_role_system_action_permission_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkRoleSystemActionPermissionExist(:p_role_system_action_permission_id)');
        $stmt->bindValue(':p_role_system_action_permission_id', $p_role_system_action_permission_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkRoleUserAccountExist($p_role_user_account_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkRoleUserAccountExist(:p_role_user_account_id)');
        $stmt->bindValue(':p_role_user_account_id', $p_role_user_account_id, PDO::PARAM_INT);
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
    public function saveRole($p_role_id, $p_role_name, $p_role_description, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveRole(:p_role_id, :p_role_name, :p_role_description, :p_last_log_by, @p_new_role_id)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_role_name', $p_role_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_role_description', $p_role_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_role_id AS role_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['role_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateRolePermission($p_role_permission_id, $p_access_type, $p_access, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateRolePermission(:p_role_permission_id, :p_access_type, :p_access, :p_last_log_by)');
        $stmt->bindValue(':p_role_permission_id', $p_role_permission_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_access_type', $p_access_type, PDO::PARAM_STR);
        $stmt->bindValue(':p_access', $p_access, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateRoleSystemActionPermission($p_role_system_action_permission_id, $p_system_action_access, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateRoleSystemActionPermission(:p_role_system_action_permission_id, :p_system_action_access, :p_last_log_by)');
        $stmt->bindValue(':p_role_system_action_permission_id', $p_role_system_action_permission_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_action_access', $p_system_action_access, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Insert methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function insertRolePermission($p_role_id, $p_role_name, $p_menu_item_id, $p_menu_item_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertRolePermission(:p_role_id, :p_role_name, :p_menu_item_id, :p_menu_item_name, :p_last_log_by)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_role_name', $p_role_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_item_name', $p_menu_item_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function insertRoleSystemActionPermission($p_role_id, $p_role_name, $p_system_action_id, $p_system_action_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertRoleSystemActionPermission(:p_role_id, :p_role_name, :p_system_action_id, :p_system_action_name, :p_last_log_by)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_role_name', $p_role_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_system_action_id', $p_system_action_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_action_name', $p_system_action_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function insertRoleUserAccount($p_role_id, $p_role_name, $p_user_account_id, $p_file_as, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertRoleUserAccount(:p_role_id, :p_role_name, :p_user_account_id, :p_file_as, :p_last_log_by)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_role_name', $p_role_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_file_as', $p_file_as, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRole($p_role_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteRole(:p_role_id)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRolePermission($p_role_permission_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteRolePermission(:p_role_permission_id)');
        $stmt->bindValue(':p_role_permission_id', $p_role_permission_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRoleSystemActionPermission($p_role_system_action_permission_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteRoleSystemActionPermission(:p_role_system_action_permission_id)');
        $stmt->bindValue(':p_role_system_action_permission_id', $p_role_system_action_permission_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRoleUserAccount($p_role_user_account_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteRoleUserAccount(:p_role_user_account_id)');
        $stmt->bindValue(':p_role_user_account_id', $p_role_user_account_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getRole($p_role_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getRole(:p_role_id)');
        $stmt->bindValue(':p_role_id', $p_role_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>