<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../upload-setting/model/upload-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new UserAccountController(new SecuritySettingModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new UploadSettingModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class UserAccountController {
    private $securitySettingModel;
    private $authenticationModel;
    private $uploadSettingModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(SecuritySettingModel $securitySettingModel, AuthenticationModel $authenticationModel, UploadSettingModel $uploadSettingModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->securitySettingModel = $securitySettingModel;
        $this->authenticationModel = $authenticationModel;
        $this->uploadSettingModel = $uploadSettingModel;
        $this->securityModel = $securityModel;
        $this->systemModel = $systemModel;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function handleRequest(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userID = $_SESSION['user_account_id'];
            $sessionToken = $_SESSION['session_token'];

            $checkLoginCredentialsExist = $this->authenticationModel->checkLoginCredentialsExist($userID, null);
            $total = $checkLoginCredentialsExist['total'] ?? 0;

            if ($total === 0) {
                $response = [
                    'success' => false,
                    'userNotExist' => true,
                    'title' => 'User Account Not Exist',
                    'message' => 'The user account specified does not exist. Please contact the administrator for assistance.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $loginCredentialsDetails = $this->authenticationModel->getLoginCredentials($userID, null);
            $active = $this->securityModel->decryptData($loginCredentialsDetails['active']);
            $locked = $this->securityModel->decryptData($loginCredentialsDetails['locked']);
            $multipleSession = $this->securityModel->decryptData($loginCredentialsDetails['multiple_session']);
            $currentSessionToken = $this->securityModel->decryptData($loginCredentialsDetails['session_token']);

            if ($active === 'No') {
                $response = [
                    'success' => false,
                    'userInactive' => true,
                    'title' => 'User Account Inactive',
                    'message' => 'Your account is currently inactive. Kindly reach out to the administrator for further assistance.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }
        
            if ($locked === 'Yes') {
                $response = [
                    'success' => false,
                    'userLocked' => true,
                    'title' => 'User Account Locked',
                    'message' => 'Your account is currently locked. Kindly reach out to the administrator for assistance in unlocking it.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }
            
            if ($sessionToken != $currentSessionToken && $multipleSession == 'No') {
                $response = [
                    'success' => false,
                    'sessionExpired' => true,
                    'title' => 'Session Expired',
                    'message' => 'Your session has expired. Please log in again to continue',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $transaction = isset($_POST['transaction']) ? $_POST['transaction'] : null;

            switch ($transaction) {
                case 'update max failed login attempt':
                    $this->updateMaxFailedLoginAttempt();
                    break;
                case 'update max failed OTP attempt':
                    $this->updateMaxFailedOTPAttempt();
                    break;
                case 'update default forgot password link':
                    $this->updateDefaultForgotPasswordLink();
                    break;
                case 'update password expiry duration':
                    $this->updatePasswordExpiryDuration();
                    break;
                case 'update session timeout duration':
                    $this->updateSessionTimeoutDuration();
                    break;
                case 'update OTP duration':
                    $this->updateOTPDuration();
                    break;
                case 'update reset password token duration':
                    $this->updateResetPasswordTokenDuration();
                    break;
                case 'get security setting details':
                    $this->getSecuritySettingDetails();
                    break;
                default:
                    $response = [
                        'success' => false,
                        'title' => 'Error: Transaction Failed',
                        'message' => 'We encountered an issue while processing your request. Please try again, and if the problem persists, reach out to our support team for assistance.',
                        'messageType' => 'error'
                    ];
                    
                    echo json_encode($response);
                    break;
            }
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateMaxFailedLoginAttempt() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $maxFailedLogin = filter_input(INPUT_POST, 'max_failed_login', FILTER_VALIDATE_INT);

        $this->securitySettingModel->updateSecuritySetting(1, $maxFailedLogin, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Max Failed Login Attempt',
            'message' => 'The max failed login attempt has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateMaxFailedOTPAttempt() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $maxFailedOTPAttempt = filter_input(INPUT_POST, 'max_failed_otp_attempt', FILTER_VALIDATE_INT);

        $this->securitySettingModel->updateSecuritySetting(2, $maxFailedOTPAttempt, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Max Failed OTP Attempt',
            'message' => 'The max failed OTP attempt has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateDefaultForgotPasswordLink() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $defaultForgotPasswordLink = filter_input(INPUT_POST, 'default_forgot_password_link', FILTER_SANITIZE_STRING);

        $this->securitySettingModel->updateSecuritySetting(3, $defaultForgotPasswordLink, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Default Forgot Password Link',
            'message' => 'The default forgot password link has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updatePasswordExpiryDuration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $passwordExpiryDuration = filter_input(INPUT_POST, 'password_expiry_duration', FILTER_VALIDATE_INT);

        $this->securitySettingModel->updateSecuritySetting(4, $passwordExpiryDuration, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Password Expiry Duration',
            'message' => 'The password expiry duration has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateSessionTimeoutDuration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $sessionTimeoutDuration = filter_input(INPUT_POST, 'session_timeout_duration', FILTER_VALIDATE_INT);

        $this->securitySettingModel->updateSecuritySetting(5, $sessionTimeoutDuration, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Session Timeout Duration',
            'message' => 'The session timeout duration has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateOTPDuration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $otpDuration = filter_input(INPUT_POST, 'otp_duration', FILTER_VALIDATE_INT);

        $this->securitySettingModel->updateSecuritySetting(6, $otpDuration, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update OTP Duration',
            'message' => 'The OTP duration has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateResetPasswordTokenDuration() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $resetPasswordTokenDuration = filter_input(INPUT_POST, 'reset_password_token_duration', FILTER_VALIDATE_INT);

        $this->securitySettingModel->updateSecuritySetting(7, $resetPasswordTokenDuration, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Reset Password Token Duration',
            'message' => 'The reset password token duration has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get details methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getSecuritySettingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $maxFailedLoginAttempt = $this->securitySettingModel->getSecuritySetting(1)['value'] ?? MAX_FAILED_LOGIN_ATTEMPTS;
        $maxFailedOTPAttempt = $this->securitySettingModel->getSecuritySetting(2)['value'] ?? MAX_FAILED_OTP_ATTEMPTS;
        $defaultPasswordLink = $this->securitySettingModel->getSecuritySetting(3)['value'] ?? DEFAULT_PASSWORD_RECOVERY_LINK;
        $passwordExpiryDuration = $this->securitySettingModel->getSecuritySetting(4)['value'] ?? DEFAULT_PASSWORD_DURATION;
        $sessionTimeoutDuration = $this->securitySettingModel->getSecuritySetting(5)['value'] ?? DEFAULT_SESSION_INACTIVITY;
        $otpDuration = $this->securitySettingModel->getSecuritySetting(6)['value'] ?? DEFAULT_OTP_DURATION;
        $resetPasswordTokenDuration = $this->securitySettingModel->getSecuritySetting(7)['value'] ?? RESET_PASSWORD_TOKEN_DURATION;
        
        $response = [
            'success' => true,
            'maxFailedLoginAttempt' => $maxFailedLoginAttempt,
            'maxFailedOTPAttempt' => $maxFailedOTPAttempt,
            'defaultPasswordLink' => $defaultPasswordLink,
            'passwordExpiryDuration' => $passwordExpiryDuration . ' days',
            'sessionTimeoutDuration' => $sessionTimeoutDuration . ' minutes',
            'otpDuration' => $otpDuration . ' minutes',
            'resetPasswordTokenDuration' => $resetPasswordTokenDuration . ' minutes'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>