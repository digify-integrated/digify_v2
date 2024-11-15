<?php

class CompanyModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkCompanyExist($p_company_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkCompanyExist(:p_company_id)');
        $stmt->bindValue(':p_company_id', $p_company_id, PDO::PARAM_INT);
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
    public function saveCompany($p_company_id, $p_company_name, $p_address, $p_city_id, $p_city_name, $p_state_id, $p_state_name, $p_country_id, $p_country_name, $p_tax_id, $p_currency_id, $p_currency_name, $p_phone, $p_telephone, $p_email, $p_website, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL saveCompany(:p_company_id, :p_company_name, :p_address, :p_city_id, :p_city_name, :p_state_id, :p_state_name, :p_country_id, :p_country_name, :p_tax_id, :p_currency_id, :p_currency_name, :p_phone, :p_telephone, :p_email, :p_website, :p_last_log_by, @p_new_company_id)');
        $stmt->bindValue(':p_company_id', $p_company_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_company_name', $p_company_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_address', $p_address, PDO::PARAM_STR);
        $stmt->bindValue(':p_city_id', $p_city_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_city_name', $p_city_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_state_id', $p_state_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_state_name', $p_state_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_country_id', $p_country_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_country_name', $p_country_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_tax_id', $p_tax_id, PDO::PARAM_STR);
        $stmt->bindValue(':p_currency_id', $p_currency_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_currency_name', $p_currency_name, PDO::PARAM_STR);
        $stmt->bindValue(':p_phone', $p_phone, PDO::PARAM_STR);
        $stmt->bindValue(':p_telephone', $p_telephone, PDO::PARAM_STR);
        $stmt->bindValue(':p_email', $p_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_website', $p_website, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_company_id AS company_id');
        $appModuleID = $result->fetch(PDO::FETCH_ASSOC)['company_id'];

        $stmt->closeCursor();
        
        return $appModuleID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateCompanyLogo($p_company_id, $p_company_logo, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateCompanyLogo(:p_company_id, :p_company_logo, :p_last_log_by)');
        $stmt->bindValue(':p_company_id', $p_company_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_company_logo', $p_company_logo, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteCompany($p_company_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteCompany(:p_company_id)');
        $stmt->bindValue(':p_company_id', $p_company_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getCompany($p_company_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getCompany(:p_company_id)');
        $stmt->bindValue(':p_company_id', $p_company_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>