<?php

class AddressTypeModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkAddressTypeExist($p_address_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkAddressTypeExist(:p_address_type_id)');
        $stmt->bindValue(':p_address_type_id', $p_address_type_id, PDO::PARAM_INT);
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
    public function saveAddressType($p_address_type_id, $p_address_type_name, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveAddressType(:p_address_type_id, :p_address_type_name, :p_last_log_by, @p_new_address_type_id)');
        $stmt->bindValue(':p_address_type_id', $p_address_type_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_address_type_name', $p_address_type_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_address_type_id AS address_type_id');
        $addressTypeID = $result->fetch(PDO::FETCH_ASSOC)['address_type_id'];

        $stmt->closeCursor();
        
        return $addressTypeID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteAddressType($p_address_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteAddressType(:p_address_type_id)');
        $stmt->bindValue(':p_address_type_id', $p_address_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getAddressType($p_address_type_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getAddressType(:p_address_type_id)');
        $stmt->bindValue(':p_address_type_id', $p_address_type_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>