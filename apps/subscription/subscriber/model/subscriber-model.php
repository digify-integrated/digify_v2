<?php

class SubscriberModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkSubscriberExist($p_subscriber_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSubscriberExist(:p_subscriber_id)');
        $stmt->bindValue(':p_subscriber_id', $p_subscriber_id, PDO::PARAM_INT);
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
    public function saveSubscriber($p_subscriber_id, $p_subscriber_name, $p_company_name, $p_phone, $p_email, $p_subscriber_status, $p_subscription_tier_id, $p_subscription_tier_name, $p_billing_cycle_id, $p_billing_cycle_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveSubscriber(:p_subscriber_id, :p_subscriber_name, :p_company_name, :p_phone, :p_email, :p_subscriber_status, :p_subscription_tier_id, :p_subscription_tier_name, :p_billing_cycle_id, :p_billing_cycle_name, :p_last_log_by, @p_new_subscriber_id)');
        $stmt->bindValue(':p_subscriber_id', $p_subscriber_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_subscriber_name', $p_subscriber_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_company_name', $p_company_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_phone', $p_phone, PDO::PARAM_STR);
        $stmt->bindValue(':p_email', $p_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_subscriber_status', $p_subscriber_status, PDO::PARAM_STR);
        $stmt->bindValue(':p_subscription_tier_id', $p_subscription_tier_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_subscription_tier_name', $p_subscription_tier_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_billing_cycle_id', $p_billing_cycle_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_billing_cycle_name', $p_billing_cycle_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_subscriber_id AS subscriber_id');
        $subscriberID = $result->fetch(PDO::FETCH_ASSOC)['subscriber_id'];

        $stmt->closeCursor();
        
        return $subscriberID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteSubscriber($p_subscriber_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteSubscriber(:p_subscriber_id)');
        $stmt->bindValue(':p_subscriber_id', $p_subscriber_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getSubscriber($p_subscriber_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSubscriber(:p_subscriber_id)');
        $stmt->bindValue(':p_subscriber_id', $p_subscriber_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>