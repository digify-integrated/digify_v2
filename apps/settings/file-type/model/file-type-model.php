<?php

class FileTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkFileTypeExist($p_file_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkFileTypeExist(:p_file_type_id)');
        $stmt->bindValue(':p_file_type_id', $p_file_type_id, PDO::PARAM_INT);
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
    public function saveFileType($p_file_type_id, $p_file_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveFileType(:p_file_type_id, :p_file_type_name, :p_last_log_by, @p_new_file_type_id)');
        $stmt->bindValue(':p_file_type_id', $p_file_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_file_type_name', $p_file_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_file_type_id AS file_type_id');
        $fileTypeID = $result->fetch(PDO::FETCH_ASSOC)['file_type_id'];

        $stmt->closeCursor();
        
        return $fileTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteFileType($p_file_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteFileType(:p_file_type_id)');
        $stmt->bindValue(':p_file_type_id', $p_file_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getFileType($p_file_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getFileType(:p_file_type_id)');
        $stmt->bindValue(':p_file_type_id', $p_file_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>