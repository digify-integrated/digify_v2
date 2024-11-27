<?php

class GenderModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkGenderExist($p_gender_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkGenderExist(:p_gender_id)');
        $stmt->bindValue(':p_gender_id', $p_gender_id, PDO::PARAM_INT);
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
    public function saveGender($p_gender_id, $p_gender_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveGender(:p_gender_id, :p_gender_name, :p_last_log_by, @p_new_gender_id)');
        $stmt->bindValue(':p_gender_id', $p_gender_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_gender_name', $p_gender_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_gender_id AS gender_id');
        $genderID = $result->fetch(PDO::FETCH_ASSOC)['gender_id'];

        $stmt->closeCursor();
        
        return $genderID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteGender($p_gender_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteGender(:p_gender_id)');
        $stmt->bindValue(':p_gender_id', $p_gender_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getGender($p_gender_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getGender(:p_gender_id)');
        $stmt->bindValue(':p_gender_id', $p_gender_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>