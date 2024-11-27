<?php

class CredentialTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkCredentialTypeExist($p_credential_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkCredentialTypeExist(:p_credential_type_id)');
        $stmt->bindValue(':p_credential_type_id', $p_credential_type_id, PDO::PARAM_INT);
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
    public function saveCredentialType($p_credential_type_id, $p_credential_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveCredentialType(:p_credential_type_id, :p_credential_type_name, :p_last_log_by, @p_new_credential_type_id)');
        $stmt->bindValue(':p_credential_type_id', $p_credential_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_credential_type_name', $p_credential_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_credential_type_id AS credential_type_id');
        $credentialTypeID = $result->fetch(PDO::FETCH_ASSOC)['credential_type_id'];

        $stmt->closeCursor();
        
        return $credentialTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteCredentialType($p_credential_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteCredentialType(:p_credential_type_id)');
        $stmt->bindValue(':p_credential_type_id', $p_credential_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getCredentialType($p_credential_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getCredentialType(:p_credential_type_id)');
        $stmt->bindValue(':p_credential_type_id', $p_credential_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>