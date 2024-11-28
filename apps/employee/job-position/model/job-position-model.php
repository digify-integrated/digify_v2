<?php

class JobPositionModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkJobPositionExist($p_job_position_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkJobPositionExist(:p_job_position_id)');
        $stmt->bindValue(':p_job_position_id', $p_job_position_id, PDO::PARAM_INT);
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
    public function saveJobPosition($p_job_position_id, $p_job_position_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveJobPosition(:p_job_position_id, :p_job_position_name, :p_last_log_by, @p_new_job_position_id)');
        $stmt->bindValue(':p_job_position_id', $p_job_position_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_job_position_name', $p_job_position_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_job_position_id AS job_position_id');
        $jobPositionID = $result->fetch(PDO::FETCH_ASSOC)['job_position_id'];

        $stmt->closeCursor();
        
        return $jobPositionID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteJobPosition($p_job_position_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteJobPosition(:p_job_position_id)');
        $stmt->bindValue(':p_job_position_id', $p_job_position_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getJobPosition($p_job_position_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getJobPosition(:p_job_position_id)');
        $stmt->bindValue(':p_job_position_id', $p_job_position_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>