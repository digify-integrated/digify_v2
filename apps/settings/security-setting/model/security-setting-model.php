<?php
/**
* Class SecuritySettingModel
*
* The SecuritySettingModel class handles security setting related operations and interactions.
*/
class SecuritySettingModel {
    public $db;

    public function __construct(DatabaseModel $db) {
        $this->db = $db;
    }

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateSecuritySetting
    # Description: Updates the security setting.
    #
    # Parameters:
    # - $p_max_failed_login (int): The max failed login attempts.
    # - $p_max_failed_otp_attempt (int): The max failed otp attempts.
    # - $p_password_expiry_duration (int): The password expiry duration.
    # - $p_otp_duration (int): The OTP duration.
    # - $p_reset_password_token_duration (int): The reset password token duration.
    # - $p_session_inactivity_limit (int): The session inactivity limit.
    # - $p_password_recovery_link (string): The password recovery link.
    # - $p_registration_verification_token_duration (int): The registration verification token duration.
    # - $p_last_log_by (int): The last logged user.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function updateSecuritySetting($p_max_failed_login, $p_max_failed_otp_attempt, $p_security_setting_description, $p_otp_duration, $p_reset_password_token_duration, $p_session_inactivity_limit, $p_password_recovery_link, $p_registration_verification_token_duration, $p_last_log_by) {
        $stmt = $this->db->getConnection()->prepare('CALL updateSecuritySetting(:p_max_failed_login, :p_max_failed_otp_attempt, :p_security_setting_description, :p_otp_duration, :p_reset_password_token_duration, :p_session_inactivity_limit, :p_password_recovery_link, :p_registration_verification_token_duration, :p_last_log_by)');
        $stmt->bindValue(':p_max_failed_login', $p_max_failed_login, PDO::PARAM_INT);
        $stmt->bindValue(':p_max_failed_otp_attempt', $p_max_failed_otp_attempt, PDO::PARAM_INT);
        $stmt->bindValue(':p_security_setting_description', $p_security_setting_description, PDO::PARAM_INT);
        $stmt->bindValue(':p_otp_duration', $p_otp_duration, PDO::PARAM_INT);
        $stmt->bindValue(':p_reset_password_token_duration', $p_reset_password_token_duration, PDO::PARAM_INT);
        $stmt->bindValue(':p_session_inactivity_limit', $p_session_inactivity_limit, PDO::PARAM_INT);
        $stmt->bindValue(':p_password_recovery_link', $p_password_recovery_link, PDO::PARAM_STR);
        $stmt->bindValue(':p_registration_verification_token_duration', $p_registration_verification_token_duration, PDO::PARAM_INT);
        $stmt->bindValue(':p_last_log_by', $p_last_log_by, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getSecuritySetting
    # Description: Retrieves the details of a security setting.
    #
    # Parameters:
    # - $p_security_setting_id (int): The security setting ID.
    #
    # Returns:
    # - An array containing the user details.
    #
    # -------------------------------------------------------------
    public function getSecuritySetting($p_security_setting_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getSecuritySetting(:p_security_setting_id)');
        $stmt->bindValue(':p_security_setting_id', $p_security_setting_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------
}
?>