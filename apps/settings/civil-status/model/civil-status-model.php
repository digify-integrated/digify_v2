<?php

class CivilStatusModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkCivilStatusExist($p_civil_status_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkCivilStatusExist(:p_civil_status_id)');
        $stmt->bindValue(':p_civil_status_id', $p_civil_status_id, PDO::PARAM_INT);
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
    public function saveCivilStatus($p_civil_status_id, $p_civil_status_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveCivilStatus(:p_civil_status_id, :p_civil_status_name, :p_last_log_by, @p_new_civil_status_id)');
        $stmt->bindValue(':p_civil_status_id', $p_civil_status_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_civil_status_name', $p_civil_status_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_civil_status_id AS civil_status_id');
        $civilStatusID = $result->fetch(PDO::FETCH_ASSOC)['civil_status_id'];

        $stmt->closeCursor();
        
        return $civilStatusID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteCivilStatus($p_civil_status_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteCivilStatus(:p_civil_status_id)');
        $stmt->bindValue(':p_civil_status_id', $p_civil_status_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getCivilStatus($p_civil_status_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getCivilStatus(:p_civil_status_id)');
        $stmt->bindValue(':p_civil_status_id', $p_civil_status_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>