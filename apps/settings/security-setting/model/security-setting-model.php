<?php

class SecuritySettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateSecuritySetting($p_security_setting_id, $p_value, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSecuritySetting(:p_security_setting_id, :p_value, :p_last_log_by)');
        $stmt->bindValue(':p_security_setting_id', $p_security_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_value', $p_value, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getSecuritySetting($p_security_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSecuritySetting(:p_security_setting_id)');
        $stmt->bindValue(':p_security_setting_id', $p_security_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>