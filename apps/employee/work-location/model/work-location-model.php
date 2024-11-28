<?php

class WorkLocationModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkWorkLocationExist($p_work_location_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkWorkLocationExist(:p_work_location_id)');
        $stmt->bindValue(':p_work_location_id', $p_work_location_id, PDO::PARAM_INT);
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
    public function saveWorkLocation($p_work_location_id, $p_work_location_name, $p_address, $p_city_id, $p_city_name, $p_state_id, $p_state_name, $p_country_id, $p_country_name, $p_phone, $p_telephone, $p_email, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveWorkLocation(:p_work_location_id, :p_work_location_name, :p_address, :p_city_id, :p_city_name, :p_state_id, :p_state_name, :p_country_id, :p_country_name, :p_phone, :p_telephone, :p_email, :p_last_log_by, @p_new_work_location_id)');
        $stmt->bindValue(':p_work_location_id', $p_work_location_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_work_location_name', $p_work_location_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_address', $p_address, PDO::PARAM_STR);
        $stmt->bindValue(':p_city_id', $p_city_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_city_name', $p_city_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_state_id', $p_state_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_state_name', $p_state_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_country_name', $p_country_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_phone', $p_phone, PDO::PARAM_STR);
        $stmt->bindValue(':p_telephone', $p_telephone, PDO::PARAM_STR);
        $stmt->bindValue(':p_email', $p_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_work_location_id AS work_location_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['work_location_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteWorkLocation($p_work_location_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteWorkLocation(:p_work_location_id)');
        $stmt->bindValue(':p_work_location_id', $p_work_location_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getWorkLocation($p_work_location_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getWorkLocation(:p_work_location_id)');
        $stmt->bindValue(':p_work_location_id', $p_work_location_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>