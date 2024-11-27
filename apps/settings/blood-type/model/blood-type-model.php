<?php

class BloodTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkBloodTypeExist($p_blood_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkBloodTypeExist(:p_blood_type_id)');
        $stmt->bindValue(':p_blood_type_id', $p_blood_type_id, PDO::PARAM_INT);
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
    public function saveBloodType($p_blood_type_id, $p_blood_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveBloodType(:p_blood_type_id, :p_blood_type_name, :p_last_log_by, @p_new_blood_type_id)');
        $stmt->bindValue(':p_blood_type_id', $p_blood_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_blood_type_name', $p_blood_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_blood_type_id AS blood_type_id');
        $bloodTypeID = $result->fetch(PDO::FETCH_ASSOC)['blood_type_id'];

        $stmt->closeCursor();
        
        return $bloodTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteBloodType($p_blood_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteBloodType(:p_blood_type_id)');
        $stmt->bindValue(':p_blood_type_id', $p_blood_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getBloodType($p_blood_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getBloodType(:p_blood_type_id)');
        $stmt->bindValue(':p_blood_type_id', $p_blood_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>