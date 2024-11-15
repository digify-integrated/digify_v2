<?php

class FileExtensionModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkFileExtensionExist($p_file_extension_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkFileExtensionExist(:p_file_extension_id)');
        $stmt->bindValue(':p_file_extension_id', $p_file_extension_id, PDO::PARAM_INT);
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
    public function saveFileExtension($p_file_extension_id, $p_file_extension_name, $p_file_extension, $p_file_type_id, $p_file_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveFileExtension(:p_file_extension_id, :p_file_extension_name, :p_file_extension, :p_file_type_id, :p_file_type_name, :p_last_log_by, @p_new_file_extension_id)');
        $stmt->bindValue(':p_file_extension_id', $p_file_extension_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_file_extension_name', $p_file_extension_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_file_extension', $p_file_extension, PDO::PARAM_STR);
        $stmt->bindValue(':p_file_type_id', $p_file_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_file_type_name', $p_file_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_file_extension_id AS file_extension_id');
        $fileExtensionID = $result->fetch(PDO::FETCH_ASSOC)['file_extension_id'];

        $stmt->closeCursor();
        
        return $fileExtensionID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteFileExtension($p_file_extension_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteFileExtension(:p_file_extension_id)');
        $stmt->bindValue(':p_file_extension_id', $p_file_extension_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getFileExtension($p_file_extension_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getFileExtension(:p_file_extension_id)');
        $stmt->bindValue(':p_file_extension_id', $p_file_extension_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>