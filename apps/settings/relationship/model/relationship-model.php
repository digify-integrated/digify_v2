<?php

class RelationshipModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkRelationshipExist($p_relationship_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkRelationshipExist(:p_relationship_id)');
        $stmt->bindValue(':p_relationship_id', $p_relationship_id, PDO::PARAM_INT);
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
    public function saveRelationship($p_relationship_id, $p_relationship_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveRelationship(:p_relationship_id, :p_relationship_name, :p_last_log_by, @p_new_relationship_id)');
        $stmt->bindValue(':p_relationship_id', $p_relationship_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_relationship_name', $p_relationship_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_relationship_id AS relationship_id');
        $relationshipID = $result->fetch(PDO::FETCH_ASSOC)['relationship_id'];

        $stmt->closeCursor();
        
        return $relationshipID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRelationship($p_relationship_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteRelationship(:p_relationship_id)');
        $stmt->bindValue(':p_relationship_id', $p_relationship_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getRelationship($p_relationship_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getRelationship(:p_relationship_id)');
        $stmt->bindValue(':p_relationship_id', $p_relationship_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>