<?php

class LanguageProficiencyModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkLanguageProficiencyExist($p_language_proficiency_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkLanguageProficiencyExist(:p_language_proficiency_id)');
        $stmt->bindValue(':p_language_proficiency_id', $p_language_proficiency_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function saveLanguageProficiency($p_language_proficiency_id, $p_language_proficiency_name, $p_language_proficiency_description, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveLanguageProficiency(:p_language_proficiency_id, :p_language_proficiency_name, :p_language_proficiency_description, :p_last_log_by, @p_new_language_proficiency_id)');
        $stmt->bindValue(':p_language_proficiency_id', $p_language_proficiency_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_language_proficiency_name', $p_language_proficiency_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_language_proficiency_description', $p_language_proficiency_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_language_proficiency_id AS language_proficiency_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['language_proficiency_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteLanguageProficiency($p_language_proficiency_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteLanguageProficiency(:p_language_proficiency_id)');
        $stmt->bindValue(':p_language_proficiency_id', $p_language_proficiency_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getLanguageProficiency($p_language_proficiency_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getLanguageProficiency(:p_language_proficiency_id)');
        $stmt->bindValue(':p_language_proficiency_id', $p_language_proficiency_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>