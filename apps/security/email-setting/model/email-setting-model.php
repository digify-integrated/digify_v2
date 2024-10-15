<?php

class EmailSettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }
    
    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmailSetting($p_email_setting_id, $p_email_setting_name, $p_email_setting_description, $p_mail_host, $p_port, $p_smtp_auth, $p_smtp_auto_tls, $p_mail_username, $p_mail_password, $p_mail_encryption, $p_mail_from_name, $p_mail_from_email, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateEmailSetting(:p_email_setting_id, :p_email_setting_name, :p_email_setting_description, :p_mail_host, :p_port, :p_smtp_auth, :p_smtp_auto_tls, :p_mail_username, :p_mail_password, :p_mail_encryption, :p_mail_from_name, :p_mail_from_email, :p_last_log_by)');
        $stmt->bindValue(':p_email_setting_id', $p_email_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email_setting_name', $p_email_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_email_setting_description', $p_email_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_host', $p_mail_host, PDO::PARAM_STR);
        $stmt->bindValue(':p_port', $p_port, PDO::PARAM_STR);
        $stmt->bindValue(':p_smtp_auth', $p_smtp_auth, PDO::PARAM_INT);
        $stmt->bindValue(':p_smtp_auto_tls', $p_smtp_auto_tls, PDO::PARAM_INT);
        $stmt->bindValue(':p_mail_username', $p_mail_username, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_password', $p_mail_password, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_encryption', $p_mail_encryption, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_from_name', $p_mail_from_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_from_email', $p_mail_from_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Insert methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function insertEmailSetting($p_email_setting_name, $p_email_setting_description, $p_mail_host, $p_port, $p_smtp_auth, $p_smtp_auto_tls, $p_mail_username, $p_mail_password, $p_mail_encryption, $p_mail_from_name, $p_mail_from_email, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertEmailSetting(:p_email_setting_name, :p_email_setting_description, :p_mail_host, :p_port, :p_smtp_auth, :p_smtp_auto_tls, :p_mail_username, :p_mail_password, :p_mail_encryption, :p_mail_from_name, :p_mail_from_email, :p_last_log_by, @p_email_setting_id)');
        $stmt->bindValue(':p_email_setting_name', $p_email_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_email_setting_description', $p_email_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_host', $p_mail_host, PDO::PARAM_STR);
        $stmt->bindValue(':p_port', $p_port, PDO::PARAM_STR);
        $stmt->bindValue(':p_smtp_auth', $p_smtp_auth, PDO::PARAM_INT);
        $stmt->bindValue(':p_smtp_auto_tls', $p_smtp_auto_tls, PDO::PARAM_INT);
        $stmt->bindValue(':p_mail_username', $p_mail_username, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_password', $p_mail_password, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_encryption', $p_mail_encryption, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_from_name', $p_mail_from_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_mail_from_email', $p_mail_from_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $this->db->getConnection()->query('SELECT @p_email_setting_id AS email_setting_id');
        $menuItemID = $result->fetch(PDO::FETCH_ASSOC)['email_setting_id'];

        $stmt->closeCursor();
        
        return $menuItemID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkEmailSettingExist($p_email_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkEmailSettingExist(:p_email_setting_id)');
        $stmt->bindValue(':p_email_setting_id', $p_email_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteEmailSetting($p_email_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteEmailSetting(:p_email_setting_id)');
        $stmt->bindValue(':p_email_setting_id', $p_email_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmailSetting($p_email_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getEmailSetting(:p_email_setting_id)');
        $stmt->bindValue(':p_email_setting_id', $p_email_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
}
?>