<?php

class CurrencyModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkCurrencyExist($p_currency_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkCurrencyExist(:p_currency_id)');
        $stmt->bindValue(':p_currency_id', $p_currency_id, PDO::PARAM_INT);
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
    public function saveCurrency($p_currency_id, $p_currency_name, $p_symbol, $p_shorthand, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveCurrency(:p_currency_id, :p_currency_name, :p_symbol, :p_shorthand, :p_last_log_by, @p_new_currency_id)');
        $stmt->bindValue(':p_currency_id', $p_currency_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_currency_name', $p_currency_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_symbol', $p_symbol, PDO::PARAM_STR);
        $stmt->bindValue(':p_shorthand', $p_shorthand, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_currency_id AS currency_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['currency_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteCurrency($p_currency_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteCurrency(:p_currency_id)');
        $stmt->bindValue(':p_currency_id', $p_currency_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getCurrency($p_currency_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getCurrency(:p_currency_id)');
        $stmt->bindValue(':p_currency_id', $p_currency_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>