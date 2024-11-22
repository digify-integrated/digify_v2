<?php

class NotificationSettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkNotificationSettingExist($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkNotificationSettingExist(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
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
    public function saveNotificationSetting($p_notification_setting_id, $p_notification_setting_name, $p_notification_setting_description, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveNotificationSetting(:p_notification_setting_id, :p_notification_setting_name, :p_notification_setting_description, :p_last_log_by, @p_new_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_notification_setting_name', $p_notification_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_notification_setting_description', $p_notification_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_notification_setting_id AS notification_setting_id');
        $fileTypeID = $result->fetch(PDO::FETCH_ASSOC)['notification_setting_id'];

        $stmt->closeCursor();
        
        return $fileTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteNotificationSetting($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteNotificationSetting(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getNotificationSetting($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getNotificationSetting(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------    
}
?>