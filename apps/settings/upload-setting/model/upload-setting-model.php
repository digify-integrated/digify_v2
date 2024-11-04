<?php
/**
* Class UploadSettingModel
*
* The UploadSettingModel class handles upload setting related operations and interactions.
*/
class UploadSettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateUploadSetting
    # Description: Updates the upload setting.
    #
    # Parameters:
    # - $p_upload_setting_id (int): The upload setting ID.
    # - $p_upload_setting_name (string): The upload setting name.
    # - $p_upload_setting_description (string): The upload setting description.
    # - $p_max_file_size (int): The max file size.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateUploadSetting($p_upload_setting_id, $p_upload_setting_name, $p_upload_setting_description, $p_max_file_size, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUploadSetting(:p_upload_setting_id, :p_upload_setting_name, :p_upload_setting_description, :p_max_file_size, :p_last_log_by)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_upload_setting_name', $p_upload_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_upload_setting_description', $p_upload_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_max_file_size', $p_max_file_size, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Insert methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: insertUploadSetting
    # Description: Inserts the upload setting.
    #
    # Parameters:
    # - $p_upload_setting_name (string): The upload setting name.
    # - $p_upload_setting_description (string): The upload setting description.
    # - $p_max_file_size (int): The max file size.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: String
    #
    # -------------------------------------------------------------
    public function insertUploadSetting($p_upload_setting_name, $p_upload_setting_description, $p_max_file_size, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL insertUploadSetting(:p_upload_setting_name, :p_upload_setting_description, :p_max_file_size, :p_last_log_by, @p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_name', $p_upload_setting_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_upload_setting_description', $p_upload_setting_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_max_file_size', $p_max_file_size, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $this->db->getConnection()->query('SELECT @p_upload_setting_id AS upload_setting_id');
        $uploadSettingID = $result->fetch(PDO::FETCH_ASSOC)['upload_setting_id'];
        
        return $uploadSettingID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: insertUploadSettingFileExtension
    # Description: Inserts the upload setting file extension.
    #
    # Parameters:
    # - $p_upload_setting_id (int): The upload setting name.
    # - $p_upload_setting_name (string): The upload setting description.
    # - $p_file_extension_id (int): The file extension ID.
    # - $p_file_extension_name (string): The file extension name.
    # - $p_file_extension (string): The file extension.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: String
    #
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
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: checkUploadSettingExist
    # Description: Checks if a upload setting exists.
    #
    # Parameters:
    # - $p_upload_setting_id (int): The upload setting ID.
    #
    # Returns: The result of the query as an associative array.
    #
    # -------------------------------------------------------------
    public function checkUploadSettingExist($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUploadSettingExist(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: checkUploadSettingFileExtensionExist
    # Description: Checks if a upload setting exists.
    #
    # Parameters:
    # - $p_upload_setting_file_extension_id (int): The upload setting file extension ID.
    #
    # Returns: The result of the query as an associative array.
    #
    # -------------------------------------------------------------
    public function checkUploadSettingFileExtensionExist($p_upload_setting_file_extension_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUploadSettingFileExtensionExist(:p_upload_setting_file_extension_id)');
        $stmt->bindValue(':p_upload_setting_file_extension_id', $p_upload_setting_file_extension_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: deleteUploadSetting
    # Description: Deletes the upload setting.
    #
    # Parameters:
    # - $p_upload_setting_id (int): The upload setting ID.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function deleteUploadSetting($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteUploadSetting(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: deleteUploadSettingFileExtension
    # Description: Deletes the upload setting file extension.
    #
    # Parameters:
    # - $p_upload_setting_file_extension_id (int): The upload setting file extension ID.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function deleteUploadSettingFileExtension($p_upload_setting_file_extension_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteUploadSettingFileExtension(:p_upload_setting_file_extension_id)');
        $stmt->bindValue(':p_upload_setting_file_extension_id', $p_upload_setting_file_extension_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getUploadSetting
    # Description: Retrieves the details of a upload setting.
    #
    # Parameters:
    # - $p_upload_setting_id (int): The upload setting ID.
    #
    # Returns:
    # - An array containing the upload setting details.
    #
    # -------------------------------------------------------------
    public function getUploadSetting($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getUploadSetting(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getUploadSettingFileExtension
    # Description: Retrieves the details of a upload setting file extension.
    #
    # Parameters:
    # - $p_upload_setting_id (int): The upload setting ID.
    #
    # Returns:
    # - An array containing the upload setting file extension details.
    #
    # -------------------------------------------------------------
    public function getUploadSettingFileExtension($p_upload_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getUploadSettingFileExtension(:p_upload_setting_id)');
        $stmt->bindValue(':p_upload_setting_id', $p_upload_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------
}
?>