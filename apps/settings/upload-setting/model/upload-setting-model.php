<?php

class UploadSettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkUploadSettingExist($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUploadSettingExist(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
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
    public function saveUploadSetting($p_upload_setting_id, $p_upload_setting_name, $p_upload_setting_description, $p_max_file_size, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveUploadSetting(:p_upload_setting_id, :p_upload_setting_name, :p_upload_setting_description, :p_max_file_size, :p_last_log_by, @p_new_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_upload_setting_name', $p_upload_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_upload_setting_description', $p_upload_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_max_file_size', $p_max_file_size, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_upload_setting_id AS upload_setting_id');
        $fileTypeID = $result->fetch(PDO::FETCH_ASSOC)['upload_setting_id'];

        $stmt->closeCursor();
        
        return $fileTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function insertUploadSettingFileExtension($p_upload_setting_id, $p_upload_setting_name, $p_file_extension_id, $p_file_extension_name, $p_file_extension, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertUploadSettingFileExtension(:p_upload_setting_id, :p_upload_setting_name, :p_file_extension_id, :p_file_extension_name, :p_file_extension, :p_last_log_by)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_upload_setting_name', $p_upload_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_file_extension_id', $p_file_extension_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_file_extension_name', $p_file_extension_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_file_extension', $p_file_extension, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteUploadSetting($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteUploadSetting(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteUploadSettingFileExtension($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteUploadSettingFileExtension(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getUploadSetting($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getUploadSetting(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getUploadSettingFileExtension($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getUploadSettingFileExtension(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>