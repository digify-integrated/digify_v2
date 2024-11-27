<?php

class EducationalStageModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkEducationalStageExist($p_educational_stage_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkEducationalStageExist(:p_educational_stage_id)');
        $stmt->bindValue(':p_educational_stage_id', $p_educational_stage_id, PDO::PARAM_INT);
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
    public function saveEducationalStage($p_educational_stage_id, $p_educational_stage_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveEducationalStage(:p_educational_stage_id, :p_educational_stage_name, :p_last_log_by, @p_new_educational_stage_id)');
        $stmt->bindValue(':p_educational_stage_id', $p_educational_stage_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_educational_stage_name', $p_educational_stage_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_educational_stage_id AS educational_stage_id');
        $educationalStageID = $result->fetch(PDO::FETCH_ASSOC)['educational_stage_id'];

        $stmt->closeCursor();
        
        return $educationalStageID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteEducationalStage($p_educational_stage_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteEducationalStage(:p_educational_stage_id)');
        $stmt->bindValue(':p_educational_stage_id', $p_educational_stage_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEducationalStage($p_educational_stage_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getEducationalStage(:p_educational_stage_id)');
        $stmt->bindValue(':p_educational_stage_id', $p_educational_stage_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>