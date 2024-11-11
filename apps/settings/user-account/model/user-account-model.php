<?php

class UserAccountModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkUserAccountExist($p_user_account_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUserAccountExist(:p_user_account_id)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkUserAccountUsernameExist($p_user_account_id, $p_username) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUserAccountUsernameExist(:p_user_account_id, :p_username)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_username', $p_username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkUserAccountEmailExist($p_user_account_id, $p_email) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUserAccountEmailExist(:p_user_account_id, :p_email)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email', $p_email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkUserAccountPhoneExist($p_user_account_id, $p_phone) {
        $stmt = $this->db->getConnection()->prepare('CALL checkUserAccountPhoneExist(:p_user_account_id, :p_phone)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_phone', $p_phone, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Add methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function addUserAccount($p_file_as, $p_email, $p_username, $p_password, $p_phone, $p_password_expiry_date, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL addUserAccount(:p_file_as, :p_email, :p_username, :p_password, :p_phone, :p_password_expiry_date, :p_last_log_by, @p_new_user_account_id)');
        $stmt->bindValue(':p_file_as', $p_file_as, PDO::PARAM_STR);
        $stmt->bindValue(':p_email', $p_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_username', $p_username, PDO::PARAM_STR);
        $stmt->bindValue(':p_password', $p_password, PDO::PARAM_STR);
        $stmt->bindValue(':p_phone', $p_phone, PDO::PARAM_STR);
        $stmt->bindValue(':p_password_expiry_date', $p_password_expiry_date, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();

        $result = $this->db->getConnection()->query('SELECT @p_new_user_account_id AS user_account_id');
        $userAccountID = $result->fetch(PDO::FETCH_ASSOC)['user_account_id'];

        $stmt->closeCursor();
        
        return $userAccountID;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountFullName($p_user_account_id, $p_file_as, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountFullName(:p_user_account_id, :p_file_as, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_file_as', $p_file_as, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountUsername($p_user_account_id, $p_username, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountUsername(:p_user_account_id, :p_username, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_username', $p_username, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountEmailAddress($p_user_account_id, $p_email, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountEmailAddress(:p_user_account_id, :p_email, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_email', $p_email, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountPhone($p_user_account_id, $p_phone, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountPhone(:p_user_account_id, :p_phone, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_phone', $p_phone, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountPassword($p_user_account_id, $p_password, $p_password_expiry_date, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountPassword(:p_user_account_id, :p_password, :p_password_expiry_date, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_password', $p_password, PDO::PARAM_STR);
        $stmt->bindValue(':p_password_expiry_date', $p_password_expiry_date, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateProfilePicture($p_user_account_id, $p_profile_picture, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateProfilePicture(:p_user_account_id, :p_profile_picture, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_profile_picture', $p_profile_picture, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateTwoFactorAuthenticationStatus($p_user_account_id, $p_two_factor_auth, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateTwoFactorAuthenticationStatus(:p_user_account_id, :p_two_factor_auth, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_two_factor_auth', $p_two_factor_auth, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateMultipleLoginSessionsStatus($p_user_account_id, $p_multiple_session, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateMultipleLoginSessionsStatus(:p_user_account_id, :p_multiple_session, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_multiple_session', $p_multiple_session, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountStatus($p_user_account_id, $p_active, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountStatus(:p_user_account_id, :p_active, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_active', $p_active, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserAccountLock($p_user_account_id, $p_locked, $p_account_lock_duration, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserAccountLock(:p_user_account_id, :p_locked, :p_account_lock_duration, :p_last_log_by)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_locked', $p_locked, PDO::PARAM_STR);
        $stmt->bindValue(':p_account_lock_duration', $p_account_lock_duration, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteUserAccount($p_user_account_id) {
        $stmt = $this->db->getConnection()->prepare('CALL deleteUserAccount(:p_user_account_id)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getUserAccount($p_user_account_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getUserAccount(:p_user_account_id)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
    
}
?>