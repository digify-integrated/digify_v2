<?php

class ContactInformationTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkContactInformationTypeExist($p_contact_information_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkContactInformationTypeExist(:p_contact_information_type_id)');
        $stmt->bindValue(':p_contact_information_type_id', $p_contact_information_type_id, PDO::PARAM_INT);
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
    public function saveContactInformationType($p_contact_information_type_id, $p_contact_information_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveContactInformationType(:p_contact_information_type_id, :p_contact_information_type_name, :p_last_log_by, @p_new_contact_information_type_id)');
        $stmt->bindValue(':p_contact_information_type_id', $p_contact_information_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_contact_information_type_name', $p_contact_information_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_contact_information_type_id AS contact_information_type_id');
        $contactInformationTypeID = $result->fetch(PDO::FETCH_ASSOC)['contact_information_type_id'];

        $stmt->closeCursor();
        
        return $contactInformationTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteContactInformationType($p_contact_information_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteContactInformationType(:p_contact_information_type_id)');
        $stmt->bindValue(':p_contact_information_type_id', $p_contact_information_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getContactInformationType($p_contact_information_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getContactInformationType(:p_contact_information_type_id)');
        $stmt->bindValue(':p_contact_information_type_id', $p_contact_information_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>