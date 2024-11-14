<?php

class CountryModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkCountryExist($p_country_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkCountryExist(:p_country_id)');
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
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
    public function saveCountry($p_country_id, $p_country_name, $p_country_code, $p_phone_code, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveCountry(:p_country_id, :p_country_name, :p_country_code, :p_phone_code, :p_last_log_by, @p_new_country_id)');
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_country_name', $p_country_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_country_code', $p_country_code, PDO::PARAM_STR);
        $stmt->bindValue(':p_phone_code', $p_phone_code, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_country_id AS country_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['country_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteCountry($p_country_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteCountry(:p_country_id)');
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getCountry($p_country_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getCountry(:p_country_id)');
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>