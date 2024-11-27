<?php

class LanguageModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkLanguageExist($p_language_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkLanguageExist(:p_language_id)');
        $stmt->bindValue(':p_language_id', $p_language_id, PDO::PARAM_INT);
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
    public function saveLanguage($p_language_id, $p_language_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveLanguage(:p_language_id, :p_language_name, :p_last_log_by, @p_new_language_id)');
        $stmt->bindValue(':p_language_id', $p_language_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_language_name', $p_language_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_language_id AS language_id');
        $languageID = $result->fetch(PDO::FETCH_ASSOC)['language_id'];

        $stmt->closeCursor();
        
        return $languageID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteLanguage($p_language_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteLanguage(:p_language_id)');
        $stmt->bindValue(':p_language_id', $p_language_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getLanguage($p_language_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getLanguage(:p_language_id)');
        $stmt->bindValue(':p_language_id', $p_language_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>