<?php

class BillingCycleModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkBillingCycleExist($p_billing_cycle_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkBillingCycleExist(:p_billing_cycle_id)');
        $stmt->bindValue(':p_billing_cycle_id', $p_billing_cycle_id, PDO::PARAM_INT);
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
    public function saveBillingCycle($p_billing_cycle_id, $p_billing_cycle_name, $p_billing_cycle_description, $p_order_sequence, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveBillingCycle(:p_billing_cycle_id, :p_billing_cycle_name, :p_billing_cycle_description, :p_order_sequence, :p_last_log_by, @p_new_billing_cycle_id)');
        $stmt->bindValue(':p_billing_cycle_id', $p_billing_cycle_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_billing_cycle_name', $p_billing_cycle_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_billing_cycle_description', $p_billing_cycle_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_order_sequence', $p_order_sequence, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_billing_cycle_id AS billing_cycle_id');
        $billingCycleID = $result->fetch(PDO::FETCH_ASSOC)['billing_cycle_id'];

        $stmt->closeCursor();
        
        return $billingCycleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteBillingCycle($p_billing_cycle_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteBillingCycle(:p_billing_cycle_id)');
        $stmt->bindValue(':p_billing_cycle_id', $p_billing_cycle_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getBillingCycle($p_billing_cycle_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getBillingCycle(:p_billing_cycle_id)');
        $stmt->bindValue(':p_billing_cycle_id', $p_billing_cycle_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>