<?php

class BankModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkBankExist($p_bank_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkBankExist(:p_bank_id)');
        $stmt->bindValue(':p_bank_id', $p_bank_id, PDO::PARAM_INT);
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
    public function saveBank($p_bank_id, $p_bank_name, $p_state_id, $p_state_name, $p_country_id, $p_country_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveBank(:p_bank_id, :p_bank_name, :p_state_id, :p_state_name, :p_country_id, :p_country_name, :p_last_log_by, @p_new_bank_id)');
        $stmt->bindValue(':p_bank_id', $p_bank_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_bank_name', $p_bank_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_state_id', $p_state_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_state_name', $p_state_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_country_name', $p_country_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_bank_id AS bank_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['bank_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteBank($p_bank_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteBank(:p_bank_id)');
        $stmt->bindValue(':p_bank_id', $p_bank_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getBank($p_bank_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getBank(:p_bank_id)');
        $stmt->bindValue(':p_bank_id', $p_bank_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>