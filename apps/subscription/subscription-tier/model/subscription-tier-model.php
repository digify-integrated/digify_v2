<?php

class SubscriptionTierModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkSubscriptionTierExist($p_subscription_tier_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSubscriptionTierExist(:p_subscription_tier_id)');
        $stmt->bindValue(':p_subscription_tier_id', $p_subscription_tier_id, PDO::PARAM_INT);
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
    public function saveSubscriptionTier($p_subscription_tier_id, $p_subscription_tier_name, $p_subscription_tier_description, $p_order_sequence, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveSubscriptionTier(:p_subscription_tier_id, :p_subscription_tier_name, :p_subscription_tier_description, :p_order_sequence, :p_last_log_by, @p_new_subscription_tier_id)');
        $stmt->bindValue(':p_subscription_tier_id', $p_subscription_tier_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_subscription_tier_name', $p_subscription_tier_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_subscription_tier_description', $p_subscription_tier_description, PDO::PARAM_STR);
        $stmt->bindValue(':p_order_sequence', $p_order_sequence, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_subscription_tier_id AS subscription_tier_id');
        $subscriptionTierID = $result->fetch(PDO::FETCH_ASSOC)['subscription_tier_id'];

        $stmt->closeCursor();
        
        return $subscriptionTierID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteSubscriptionTier($p_subscription_tier_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteSubscriptionTier(:p_subscription_tier_id)');
        $stmt->bindValue(':p_subscription_tier_id', $p_subscription_tier_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getSubscriptionTier($p_subscription_tier_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSubscriptionTier(:p_subscription_tier_id)');
        $stmt->bindValue(':p_subscription_tier_id', $p_subscription_tier_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>