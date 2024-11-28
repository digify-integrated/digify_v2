<?php

class DepartureReasonModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkDepartureReasonExist($p_departure_reason_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkDepartureReasonExist(:p_departure_reason_id)');
        $stmt->bindValue(':p_departure_reason_id', $p_departure_reason_id, PDO::PARAM_INT);
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
    public function saveDepartureReason($p_departure_reason_id, $p_departure_reason_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveDepartureReason(:p_departure_reason_id, :p_departure_reason_name, :p_last_log_by, @p_new_departure_reason_id)');
        $stmt->bindValue(':p_departure_reason_id', $p_departure_reason_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_departure_reason_name', $p_departure_reason_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_departure_reason_id AS departure_reason_id');
        $departureReasonID = $result->fetch(PDO::FETCH_ASSOC)['departure_reason_id'];

        $stmt->closeCursor();
        
        return $departureReasonID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteDepartureReason($p_departure_reason_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteDepartureReason(:p_departure_reason_id)');
        $stmt->bindValue(':p_departure_reason_id', $p_departure_reason_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getDepartureReason($p_departure_reason_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getDepartureReason(:p_departure_reason_id)');
        $stmt->bindValue(':p_departure_reason_id', $p_departure_reason_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>