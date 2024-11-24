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
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateNotificationChannel($p_notification_setting_id, $p_notification_channel, $p_notification_channel_value, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateNotificationChannel(:p_notification_setting_id, :p_notification_channel, :p_notification_channel_value, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_notification_channel', $p_notification_channel, PDO::PARAM_STR);
        $stmt->bindValue(':p_notification_channel_value', $p_notification_channel_value, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateSystemNotificationTemplate($p_notification_setting_id, $p_system_notification_title, $p_system_notification_message, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSystemNotificationTemplate(:p_notification_setting_id, :p_system_notification_title, :p_system_notification_message, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_notification_title', $p_system_notification_title, PDO::PARAM_STR);
        $stmt->bindValue(':p_system_notification_message', $p_system_notification_message, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmailNotificationTemplate($p_notification_setting_id, $p_email_notification_subject, $p_email_notification_body, $p_email_setting_id, $p_email_setting_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmailNotificationTemplate(:p_notification_setting_id, :p_email_notification_subject, :p_email_notification_body, :p_email_setting_id, :p_email_setting_name, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email_notification_subject', $p_email_notification_subject, PDO::PARAM_STR);
        $stmt->bindValue(':p_email_notification_body', $p_email_notification_body, PDO::PARAM_STR);
        $stmt->bindValue(':p_email_setting_id', $p_email_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email_setting_name', $p_email_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateSMSNotificationTemplate($p_notification_setting_id, $p_sms_notification_message, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSMSNotificationTemplate(:p_notification_setting_id, :p_sms_notification_message, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_sms_notification_message', $p_sms_notification_message, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
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

    # -------------------------------------------------------------
    public function getNotificationSettingEmailTemplate($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getNotificationSettingEmailTemplate(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------    

    # -------------------------------------------------------------
    public function getNotificationSettingSystemTemplate($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getNotificationSettingSystemTemplate(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------    
    
    # -------------------------------------------------------------
    public function getNotificationSettingSMSTemplate($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getNotificationSettingSMSTemplate(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------    
}
?>