<?php

class BankAccountTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkBankAccountTypeExist($p_bank_account_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkBankAccountTypeExist(:p_bank_account_type_id)');
        $stmt->bindValue(':p_bank_account_type_id', $p_bank_account_type_id, PDO::PARAM_INT);
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
    public function saveBankAccountType($p_bank_account_type_id, $p_bank_account_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveBankAccountType(:p_bank_account_type_id, :p_bank_account_type_name, :p_last_log_by, @p_new_bank_account_type_id)');
        $stmt->bindValue(':p_bank_account_type_id', $p_bank_account_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_bank_account_type_name', $p_bank_account_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_bank_account_type_id AS bank_account_type_id');
        $bankAccountTypeID = $result->fetch(PDO::FETCH_ASSOC)['bank_account_type_id'];

        $stmt->closeCursor();
        
        return $bankAccountTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteBankAccountType($p_bank_account_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteBankAccountType(:p_bank_account_type_id)');
        $stmt->bindValue(':p_bank_account_type_id', $p_bank_account_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getBankAccountType($p_bank_account_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getBankAccountType(:p_bank_account_type_id)');
        $stmt->bindValue(':p_bank_account_type_id', $p_bank_account_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>