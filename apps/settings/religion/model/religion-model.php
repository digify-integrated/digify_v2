<?php

class ReligionModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkReligionExist($p_religion_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkReligionExist(:p_religion_id)');
        $stmt->bindValue(':p_religion_id', $p_religion_id, PDO::PARAM_INT);
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
    public function saveReligion($p_religion_id, $p_religion_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveReligion(:p_religion_id, :p_religion_name, :p_last_log_by, @p_new_religion_id)');
        $stmt->bindValue(':p_religion_id', $p_religion_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_religion_name', $p_religion_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_religion_id AS religion_id');
        $religionID = $result->fetch(PDO::FETCH_ASSOC)['religion_id'];

        $stmt->closeCursor();
        
        return $religionID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteReligion($p_religion_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteReligion(:p_religion_id)');
        $stmt->bindValue(':p_religion_id', $p_religion_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getReligion($p_religion_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getReligion(:p_religion_id)');
        $stmt->bindValue(':p_religion_id', $p_religion_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>