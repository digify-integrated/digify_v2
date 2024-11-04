<?php
/**
* Class NotificationSettingModel
*
* The NotificationSettingModel class handles notification setting related operations and interactions.
*/
class NotificationSettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateNotificationSetting
    # Description: Updates the notification setting.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_notification_setting_name (string): The notification setting name.
    # - $p_notification_setting_description (string): The notification setting description.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateNotificationSetting($p_notification_setting_id, $p_notification_setting_name, $p_notification_setting_description, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateNotificationSetting(:p_notification_setting_id, :p_notification_setting_name, :p_notification_setting_description, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_notification_setting_name', $p_notification_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_notification_setting_description', $p_notification_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateSystemNotificationChannelStatus
    # Description: Updates the system notification setting channel.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_system_notification (int): The system notification setting.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateSystemNotificationChannelStatus($p_notification_setting_id, $p_system_notification, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSystemNotificationChannelStatus(:p_notification_setting_id, :p_system_notification, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_notification', $p_system_notification, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateEmailNotificationChannelStatus
    # Description: Updates the email notification setting channel.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_email_notification (int): The email notification setting.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateEmailNotificationChannelStatus($p_notification_setting_id, $p_email_notification, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmailNotificationChannelStatus(:p_notification_setting_id, :p_email_notification, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email_notification', $p_email_notification, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateSMSNotificationChannelStatus
    # Description: Updates the SMS notification setting channel.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_sms_notification (int): The SMS notification setting.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateSMSNotificationChannelStatus($p_notification_setting_id, $p_sms_notification, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSMSNotificationChannelStatus(:p_notification_setting_id, :p_sms_notification, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_sms_notification', $p_sms_notification, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateSystemNotificationTemplate
    # Description: Updates the system notification setting template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_system_notification_title (string): The system notification title.
    # - $p_system_notification_message (string): The system notification message.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateSystemNotificationTemplate($p_notification_setting_id, $p_system_notification_title, $p_system_notification_message, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSystemNotificationTemplate(:p_notification_setting_id, :p_system_notification_title, :p_system_notification_message, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_notification_title', $p_system_notification_title, PDO::PARAM_STR);
        $stmt->bindValue(':p_system_notification_message', $p_system_notification_message, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateEmailNotificationTemplate
    # Description: Updates the email notification setting template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_email_notification_subject (string): The email notification subject.
    # - $p_email_notification_body (string): The email notification body.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateEmailNotificationTemplate($p_notification_setting_id, $p_email_notification_subject, $p_email_notification_body, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmailNotificationTemplate(:p_notification_setting_id, :p_email_notification_subject, :p_email_notification_body, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email_notification_subject', $p_email_notification_subject, PDO::PARAM_STR);
        $stmt->bindValue(':p_email_notification_body', $p_email_notification_body, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateSMSNotificationTemplate
    # Description: Updates the SMS notification setting template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_sms_notification_message (string): The sms notification message.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateSMSNotificationTemplate($p_notification_setting_id, $p_sms_notification_message, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSMSNotificationTemplate(:p_notification_setting_id, :p_sms_notification_message, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_sms_notification_message', $p_sms_notification_message, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Insert methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: insertNotificationSetting
    # Description: Inserts the notification setting.
    #
    # Parameters:
    # - $p_notification_setting_name (string): The notification setting name.
    # - $p_notification_setting_description (string): The notification setting description.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: String
    #
    # -------------------------------------------------------------
    public function insertNotificationSetting($p_notification_setting_name, $p_notification_setting_description, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertNotificationSetting(:p_notification_setting_name, :p_notification_setting_description, :p_last_log_by, @p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_name', $p_notification_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_notification_setting_description', $p_notification_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $this->db->getConnection()->query('SELECT @p_notification_setting_id AS notification_setting_id');
        $menuItemID = $result->fetch(PDO::FETCH_ASSOC)['notification_setting_id'];
        
        return $menuItemID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: insertSystemNotificationTemplate
    # Description: Updates the system notification setting template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_system_notification_title (string): The system notification title.
    # - $p_system_notification_message (string): The system notification message.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function insertSystemNotificationTemplate($p_notification_setting_id, $p_system_notification_title, $p_system_notification_message, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertSystemNotificationTemplate(:p_notification_setting_id, :p_system_notification_title, :p_system_notification_message, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_notification_title', $p_system_notification_title, PDO::PARAM_STR);
        $stmt->bindValue(':p_system_notification_message', $p_system_notification_message, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: insertEmailNotificationTemplate
    # Description: Updates the system notification setting template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_email_notification_subject (string): The email notification subject.
    # - $p_email_notification_body (string): The email notification body.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function insertEmailNotificationTemplate($p_notification_setting_id, $p_email_notification_subject, $p_email_notification_body, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertEmailNotificationTemplate(:p_notification_setting_id, :p_email_notification_subject, :p_email_notification_body, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email_notification_subject', $p_email_notification_subject, PDO::PARAM_STR);
        $stmt->bindValue(':p_email_notification_body', $p_email_notification_body, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: insertSMSNotificationTemplate
    # Description: Updates the system notification setting template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    # - $p_sms_notification_message (string): The sms notification message.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function insertSMSNotificationTemplate($p_notification_setting_id, $p_sms_notification_message, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertSMSNotificationTemplate(:p_notification_setting_id, :p_sms_notification_message, :p_last_log_by)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_sms_notification_message', $p_sms_notification_message, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: checkNotificationSettingExist
    # Description: Checks if a notification setting exists.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns: The result of the query as an associative array.
    #
    # -------------------------------------------------------------
    public function checkNotificationSettingExist($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkNotificationSettingExist(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: checkEmailNotificationTemplateExist
    # Description: Checks if a email notification template exists.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns: The result of the query as an associative array.
    #
    # -------------------------------------------------------------
    public function checkEmailNotificationTemplateExist($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkEmailNotificationTemplateExist(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: checkSystemNotificationTemplateExist
    # Description: Checks if a system notification template exists.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns: The result of the query as an associative array.
    #
    # -------------------------------------------------------------
    public function checkSystemNotificationTemplateExist($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSystemNotificationTemplateExist(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: checkSMSNotificationTemplateExist
    # Description: Checks if a SMS notification template exists.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns: The result of the query as an associative array.
    #
    # -------------------------------------------------------------
    public function checkSMSNotificationTemplateExist($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSMSNotificationTemplateExist(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: deleteNotificationSetting
    # Description: Deletes the notification setting.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function deleteNotificationSetting($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteNotificationSetting(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getNotificationSetting
    # Description: Retrieves the details of a notification setting.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns:
    # - An array containing the notification setting details.
    #
    # -------------------------------------------------------------
    public function getNotificationSetting($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getNotificationSetting(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getSystemNotificationTemplate
    # Description: Retrieves the details of a system notification template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns:
    # - An array containing the system notification template details.
    #
    # -------------------------------------------------------------
    public function getSystemNotificationTemplate($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSystemNotificationTemplate(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getEmailNotificationTemplate
    # Description: Retrieves the details of a email notification template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns:
    # - An array containing the email notification template details.
    #
    # -------------------------------------------------------------
    public function getEmailNotificationTemplate($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getEmailNotificationTemplate(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getSMSNotificationTemplate
    # Description: Retrieves the details of a SMS notification template.
    #
    # Parameters:
    # - $p_notification_setting_id (int): The notification setting ID.
    #
    # Returns:
    # - An array containing the SMS notification template details.
    #
    # -------------------------------------------------------------
    public function getSMSNotificationTemplate($p_notification_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSMSNotificationTemplate(:p_notification_setting_id)');
        $stmt->bindValue(':p_notification_setting_id', $p_notification_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------
}
?>