<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../email-setting/model/email-setting-model.php';
require_once '../../notification-setting/model/notification-setting-model.php';

require_once '../../../../assets/plugins/phpmailer/src/PHPMailer.php';
require_once '../../../../assets/plugins/phpmailer/src/Exception.php';
require_once '../../../../assets/plugins/phpmailer/src/SMTP.php';

$controller = new AuthenticationController(new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecuritySettingModel(new DatabaseModel), new EmailSettingModel(new DatabaseModel), new NotificationSettingModel(new DatabaseModel), new SystemModel(), new SecurityModel());
$controller->handleRequest();

# -------------------------------------------------------------
class AuthenticationController {
    private $authenticationModel;
    private $securitySettingModel;
    private $emailSettingModel;
    private $notificationSettingModel;
    private $systemModel;
    private $securityModel;

    # -------------------------------------------------------------
    public function __construct(AuthenticationModel $authenticationModel, SecuritySettingModel $securitySettingModel, EmailSettingModel $emailSettingModel, NotificationSettingModel $notificationSettingModel, SystemModel $systemModel, SecurityModel $securityModel) {
        $this->authenticationModel = $authenticationModel;
        $this->securitySettingModel = $securitySettingModel;
        $this->emailSettingModel = $emailSettingModel;
        $this->notificationSettingModel = $notificationSettingModel;
        $this->systemModel = $systemModel;
        $this->securityModel = $securityModel;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function handleRequest(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $transaction = filter_input(INPUT_POST, 'transaction', FILTER_SANITIZE_STRING);

            switch ($transaction) {
                case 'authenticate':
                    $this->authenticate();
                    break;
                case 'otp verification':
                    $this->verifyOTP();
                    break;
                case 'resend otp':
                    $this->resendOTP();
                    break; 
                case 'forgot password':
                    $this->forgotPassword();
                    break;
                case 'password reset':
                    $this->passwordReset();
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
    #   Authenticate methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        
        $checkLoginCredentialsExist = $this->authenticationModel->checkLoginCredentialsExist(null, $username);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $response = [
                'success' => false,
                'title' => 'Authentication Failed',
                'message' => 'Invalid credentials. Please check and try again.',
                'messageType' => 'error'
            ];
        
            echo json_encode($response);
            exit; 
        }

        $loginCredentialsDetails = $this->authenticationModel->getLoginCredentials(null, $username);
        $userAccountID = $loginCredentialsDetails['user_account_id'];
        $email = $loginCredentialsDetails['email'];
        $active = $this->securityModel->decryptData($loginCredentialsDetails['active']);
        $userPassword = $this->securityModel->decryptData($loginCredentialsDetails['password']);
        $locked = $this->securityModel->decryptData($loginCredentialsDetails['locked']);
        $failedLoginAttempts = $this->securityModel->decryptData($loginCredentialsDetails['failed_login_attempts']);
        $passwordExpiryDate = $this->securityModel->decryptData($loginCredentialsDetails['password_expiry_date']);
        $accountLockDuration = $this->securityModel->decryptData($loginCredentialsDetails['account_lock_duration']);
        $lastFailedLoginAttempt = $loginCredentialsDetails['last_failed_login_attempt'];
        $twoFactorAuth = $this->securityModel->decryptData($loginCredentialsDetails['two_factor_auth']);
        $encryptedUserID = $this->securityModel->encryptData($userAccountID);
    
        if ($password !== $userPassword) {
            $this->handleInvalidCredentials($userAccountID, $failedLoginAttempts);
            return;
        }
     
        if ($active === 'No') {
            $response = [
                'success' => false,
                'title' => 'Account Inactive',
                'message' => 'Your account is inactive. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
        }
    
       if ($this->checkPasswordHasExpired($passwordExpiryDate)) {
            $this->handlePasswordExpiration($userAccountID, $encryptedUserID);
            exit;
        }
    
        if ($locked === 'Yes') {
            $this->handleAccountLock($userAccountID, $accountLockDuration, $lastFailedLoginAttempt);
            exit;
        }
    
        $this->authenticationModel->updateLoginAttempt($userAccountID, $this->securityModel->encryptData(0), '');
    
        if ($twoFactorAuth === 'Yes') {
            $this->handleTwoFactorAuth($userAccountID, $email, $encryptedUserID);
            exit;
        }

        $sessionToken = $this->generateToken(6, 6);
        $encryptedSessionToken = $this->securityModel->encryptData($sessionToken);

        $this->authenticationModel->updateLastConnection($userAccountID, $encryptedSessionToken);
        
        $_SESSION['user_account_id'] = $userAccountID;
        $_SESSION['session_token'] = $sessionToken;

        $response = [
            'success' => true,
            'redirectLink' => 'apps.php'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Forgot methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        $checkLoginCredentialsExist = $this->authenticationModel->checkLoginCredentialsExist(null, $email);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Invalid Credentials',
                'message' => 'Invalid credentials. Please check and try again.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $loginCredentialsDetails = $this->authenticationModel->getLoginCredentials(null, $email);
        $userAccountID = $loginCredentialsDetails['user_account_id'];
        $active = $this->securityModel->decryptData($loginCredentialsDetails['active']);
        $locked = $this->securityModel->decryptData($loginCredentialsDetails['locked']);
        $encryptedUserID = $this->securityModel->encryptData($userAccountID);
    
        if ($active === 'No') {
            $response = [
                'success' => false,
                'notActive' => true,
                'title' => 'Account Inactive',
                'message' => 'Your account is inactive. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    
        if ($locked === 'Yes') {
            $response = [
                'success' => false,
                'locked' => true,
                'title' => 'Account Locked',
                'message' => 'Your account is locked. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(6);
        $resetPasswordTokenDuration = $securitySettingDetails['value'] ?? RESET_PASSWORD_TOKEN_DURATION;

        $resetToken = $this->generateToken();
        $encryptedResetToken = $this->securityModel->encryptData($resetToken);
        $resetTokenExpiryDate = $this->securityModel->encryptData(date('Y-m-d H:i:s', strtotime('+'. $resetPasswordTokenDuration .' minutes')));
    
        $this->authenticationModel->updateResetToken($userAccountID, $encryptedResetToken, $resetTokenExpiryDate);
        $this->sendPasswordReset($email, $encryptedUserID, $encryptedResetToken, $resetPasswordTokenDuration, 2);

        $response = [
            'success' => true,
            'title' => 'Password Reset',
            'message' => "We've sent a password reset link to your registered email address. Please check your inbox and follow the provided instructions to securely reset your password. If you don't receive the email within a few minutes, please also check your spam folder.",
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Verify methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function verifyOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_SANITIZE_NUMBER_INT);
        $otpCode1 = filter_input(INPUT_POST, 'otp_code_1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode2 = filter_input(INPUT_POST, 'otp_code_2', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode3 = filter_input(INPUT_POST, 'otp_code_3', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode4 = filter_input(INPUT_POST, 'otp_code_4', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode5 = filter_input(INPUT_POST, 'otp_code_5', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpCode6 = filter_input(INPUT_POST, 'otp_code_6', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 9]]);
        $otpVerificationCode = $otpCode1 . $otpCode2 . $otpCode3 . $otpCode4 . $otpCode5 . $otpCode6;

        $checkLoginCredentialsExist = $this->authenticationModel->checkLoginCredentialsExist($userAccountID, null);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Invalid Credentials',
                'message' => 'Invalid credentials. Please check and try again.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $loginCredentialsDetails = $this->authenticationModel->getLoginCredentials($userAccountID, null);
        $otp = $this->securityModel->decryptData($loginCredentialsDetails['otp']);
        $failedOTPAttempts = $this->securityModel->decryptData($loginCredentialsDetails['failed_otp_attempts']);
        $otpExpiryDate = $this->securityModel->decryptData($loginCredentialsDetails['otp_expiry_date']);
        $active = $this->securityModel->decryptData($loginCredentialsDetails['active']);
        $locked = $this->securityModel->decryptData($loginCredentialsDetails['locked']);
    
        if ($active === 'No') {
            $response = [
                'success' => false,
                'notActive' => true,
                'title' => 'Account Inactive',
                'message' => 'Your account is inactive. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    
        if ($locked === 'Yes') {
            $response = [
                'success' => false,
                'locked' => true,
                'title' => 'Account Locked',
                'message' => 'Your account is locked. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        if ($otpVerificationCode !== $otp) {
            $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(2);
            $maxFailedOTPAttempts = $securitySettingDetails['value'] ?? MAX_FAILED_OTP_ATTEMPTS;

            if ($failedOTPAttempts >= $maxFailedOTPAttempts) {
                $otpExpiryDate = $this->securityModel->encryptData(date('Y-m-d H:i:s', strtotime('-1 year')));
                $this->authenticationModel->updateOTPAsExpired($userAccountID, $otpExpiryDate);

                $response = [
                    'success' => false,
                    'otpMaxFailedAttempt' => true,
                    'title' => 'Invalid OTP Code',
                    'message' => 'The OTP code you entered is invalid. Please request a new one.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }
    
            $this->authenticationModel->updateFailedOTPAttempts($userAccountID, $this->securityModel->encryptData($failedOTPAttempts + 1));

            $response = [
                'success' => false,
                'incorrectOTPCode' => true,
                'title' => 'Invalid OTP Code',
                'message' => 'The OTP code you entered is incorrect.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        if (strtotime(date('Y-m-d H:i:s')) > strtotime($otpExpiryDate)) {
            $response = [
                'success' => false,
                'expiredOTP' => true,
                'title' => 'Expired OTP Code',
                'message' => 'The OTP code you entered is expired. Please request a new one.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $sessionToken = $this->generateToken(6, 6);
        $encryptedSessionToken = $this->securityModel->encryptData($sessionToken);

        $this->authenticationModel->updateLastConnection($userAccountID, $encryptedSessionToken);
        
        $_SESSION['user_account_id'] = $userAccountID;
        $_SESSION['session_token'] = $sessionToken;

        $response = [
            'success' => true,
            'redirectLink' => 'apps.php'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Pasword reset methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function passwordReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_SANITIZE_NUMBER_INT);
        $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);

        $checkLoginCredentialsExist = $this->authenticationModel->checkLoginCredentialsExist($userAccountID, null);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
    
        if ($total === 0) {
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Invalid Credentials',
                'message' => 'Invalid credentials. Please check and try again.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $loginCredentialsDetails = $this->authenticationModel->getLoginCredentials($userAccountID, null);
        $email = $loginCredentialsDetails['email'];
        $active = $this->securityModel->decryptData($loginCredentialsDetails['active']);
        $locked = $this->securityModel->decryptData($loginCredentialsDetails['locked']);
        $resetTokenExpiryDate = $this->securityModel->decryptData($loginCredentialsDetails['reset_token_expiry_date']);
    
        if ($active === 'No') {
            $response = [
                'success' => false,
                'notActive' => true,
                'title' => 'Account Inactive',
                'message' => 'Your account is inactive. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    
        if ($locked === 'Yes') {
            $response = [
                'success' => false,
                'locked' => true,
                'title' => 'Account Locked',
                'message' => 'Your account is locked. Please contact the administrator for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        if(strtotime(date('Y-m-d H:i:s')) > strtotime($resetTokenExpiryDate)){
            $response = [
                'success' => false,
                'title' => 'Password Reset Link Expired',
                'message' => 'The password reset link has expired. Please request a new link to reset your password.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $checkPasswordHistory = $this->checkPasswordHistory($userAccountID, $newPassword);
    
        if ($checkPasswordHistory > 0) {
            $response = [
                'success' => false,
                'passwordExist' => true,
                'title' => 'Password Already Used',
                'message' => 'Your new password cannot be identical to your previous one for security reasons. Please choose a different password to proceed.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(4);
        $defaultPasswordDuration = $securitySettingDetails['value'] ?? DEFAULT_PASSWORD_DURATION;
    
        $passwordExpiryDate = $this->securityModel->encryptData(date('Y-m-d', strtotime('+'. $defaultPasswordDuration .' days')));
        
        $encryptedPassword = $this->securityModel->encryptData($newPassword);

        $this->authenticationModel->updateUserPassword($userAccountID, $encryptedPassword, $passwordExpiryDate, $this->securityModel->encryptData('No'), $this->securityModel->encryptData(0), $this->securityModel->encryptData(0));

        $resetTokenExpiryDate = $this->securityModel->encryptData(date('Y-m-d H:i:s', strtotime('-1 year')));
        $this->authenticationModel->updateResetTokenAsExpired($userAccountID, $resetTokenExpiryDate);

        $response = [
            'success' => true,
            'title' => 'Password Reset Success',
            'message' => 'Your password has been successfully updated. For security reasons, please use your new password to log in.',
            'messageType' => 'success'            
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Resend methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function resendOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userAccountID = htmlspecialchars($_POST['user_account_id'], ENT_QUOTES, 'UTF-8');

        $loginCredentialsDetails = $this->authenticationModel->getLoginCredentials($userAccountID, null);
        $email = $loginCredentialsDetails['email'];
        
        $this->resendOTPCode($userAccountID, $email);
        
        $response = [
            'success' => true
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function resendOTPCode($userAccountID, $email) {
        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(6);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $otp = $this->generateOTPToken(6, 6);
        $encryptedOTP = $this->securityModel->encryptData($otp);
        $otpExpiryDate = $this->securityModel->encryptData(date('Y-m-d H:i:s', strtotime('+'. $otpDuration .' minutes')));
        $failedLoginAttempts = $this->securityModel->encryptData(0);
    
        $this->authenticationModel->updateOTP($userAccountID, $encryptedOTP, $otpExpiryDate, $failedLoginAttempts);
        $this->sendOTP($email, $otp, 1);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Handle methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function handleInvalidCredentials($userAccountID, $failedAttempts) {
        $failedAttempts = $failedAttempts + 1;
        $lastFailedLogin = date('Y-m-d H:i:s');
    
        $this->authenticationModel->updateLoginAttempt($userAccountID, $this->securityModel->encryptData($failedAttempts), $lastFailedLogin);

        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(1);
        $maxFailedLoginAttempts = $securitySettingDetails['value'] ?? MAX_FAILED_LOGIN_ATTEMPTS;

        $userAccountLockDurationSettingDetails = $this->securitySettingModel->getSecuritySetting(8);
        $baseLockDuration = $userAccountLockDurationSettingDetails['value'] ?? BASE_USER_ACCOUNT_DURATION;

        if ($failedAttempts > $maxFailedLoginAttempts) {
            $lockDuration = pow(2, ($failedAttempts - $maxFailedLoginAttempts)) * $baseLockDuration;
            $this->authenticationModel->updateAccountLock($userAccountID, $this->securityModel->encryptData('Yes'), $this->securityModel->encryptData($lockDuration));

            $durationParts = $this->formatDuration($lockDuration);
            $lockMessage = count($durationParts) > 0 ? ' for ' . implode(', ', $durationParts) : '';
            
            $response = [
                'success' => false,
                'title' => 'Account Locked',
                'message' => 'Too many failed login attempts. Your account has been locked' . $lockMessage . '.',
                'messageType' => 'error'
            ];
        }
        else {
            $response = [
                'success' => false,
                'title' => 'Authentication Failed',
                'message' => 'Invalid credentials. Please check and try again.', 
                'messageType' => 'error'
            ];
        }
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function handlePasswordExpiration($userAccountID, $encryptedUserID) {
        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(3);
        $defaultForgotPasswordLink = $securitySettingDetails['value'] ?? DEFAULT_PASSWORD_RECOVERY_LINK;

        $resetPasswordTokenDurationDetails = $this->securitySettingModel->getSecuritySetting(6);
        $resetPasswordTokenDuration = $resetPasswordTokenDurationDetails['value'] ?? RESET_PASSWORD_TOKEN_DURATION;

        $resetToken = $this->generateToken();
        $encryptedResetToken = $this->securityModel->encryptData($resetToken);
        $encryptedUserAccountID = $this->securityModel->encryptData($userAccountID);
        $resetTokenExpiryDate = $this->securityModel->encryptData(date('Y-m-d H:i:s', strtotime('+'. $resetPasswordTokenDuration .' minutes')));
    
        $this->authenticationModel->updateResetToken($userAccountID, $encryptedResetToken, $resetTokenExpiryDate);
    
        $response = [
            'success' => false,
            'passwordExpired' => true,
            'title' => 'Account Password Expired',
            'message' => 'Your password has expired. Please reset it to proceed.',
            'redirectLink' => $defaultForgotPasswordLink . $encryptedUserAccountID .'&token=' . $encryptedResetToken,
            'messageType' => 'error'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function handleAccountLock($userAccountID, $accountLockDuration, $lastFailedLoginAttempt) {
        $unlockTime = strtotime("+{$accountLockDuration} minutes", strtotime($lastFailedLoginAttempt));
    
        if (time() < $unlockTime) {
            $remainingTime = ($unlockTime - time()) / 60;
            $durationParts = $this->formatDuration(round($remainingTime));
    
            $message = 'Your account is locked. Try again in ' . 
                       (!empty($durationParts) ? implode(', ', $durationParts) : 'a moment') . '.';
    
            echo json_encode([
                'success' => false,
                'title' => 'Account Locked',
                'message' => $message,
                'messageType' => 'error'
            ]);
            exit;
        }

        $this->authenticationModel->updateAccountLock($userAccountID, $this->securityModel->encryptData('No'), $this->securityModel->encryptData(0));
    }    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function handleTwoFactorAuth($userAccountID, $email, $encryptedUserID) {
        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(6);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $otp = $this->generateOTPToken(6, 6);
        $encryptedOTP = $this->securityModel->encryptData($otp);
        $otpExpiryDate = $this->securityModel->encryptData(date('Y-m-d H:i:s', strtotime('+'. $otpDuration .' minutes')));
        $failedLoginAttempts = $this->securityModel->encryptData(0);
    
        $this->authenticationModel->updateOTP($userAccountID, $encryptedOTP, $otpExpiryDate, $failedLoginAttempts);
        $this->sendOTP($email, $otp, 1);
    
        $response = [
            'success' => true,
            'redirectLink' => 'otp-verification.php?id=' . $encryptedUserID
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Send methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function sendOTP($email, $otp, $notificationSettingID) {
        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(6);
        $otpDuration = $securitySettingDetails['value'] ?? DEFAULT_OTP_DURATION;

        $notificationSettingDetails = $this->notificationSettingModel->getEmailNotificationTemplate($notificationSettingID);
        $emailSettingID = $notificationSettingDetails['email_setting_id'] ?? null;

        $emailSetting = $this->emailSettingModel->getEmailSetting($emailSettingID);
        $mailFromName = $emailSetting['mail_from_name'] ?? null;
        $mailFromEmail = $emailSetting['mail_from_email'] ?? null;

        $emailSubject = $notificationSettingDetails['email_notification_subject'] ?? null;
        $emailBody = $notificationSettingDetails['email_notification_body'] ?? null;
        $emailBody = str_replace('#{OTP_CODE}', $otp, $emailBody);
        $emailBody = str_replace('#{OTP_CODE_VALIDITY}', $otpDuration . ' minutes', $emailBody);

        $message = file_get_contents('../../notification-setting/template/default-email.html');
        $message = str_replace('{EMAIL_SUBJECT}', $emailSubject, $message);
        $message = str_replace('{EMAIL_CONTENT}', $emailBody, $message);
    
        $mailer = new PHPMailer\PHPMailer\PHPMailer();
        $this->configureSMTP(1, $mailer);
        
        $mailer->setFrom($mailFromEmail, $mailFromName);
        $mailer->addAddress($email);
        $mailer->Subject = $emailSubject;
        $mailer->Body = $message;
    
        if ($mailer->send()) {
            return true;
        }
        else {
            return 'Failed to send OTP. Error: ' . $mailer->ErrorInfo;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function sendPasswordReset($email, $userAccountID, $resetToken, $resetPasswordTokenDuration, $notificationSettingID) {
        $emailSetting = $this->emailSettingModel->getEmailSetting(1);
        $mailFromName = $emailSetting['mail_from_name'];
        $mailFromEmail = $emailSetting['mail_from_email'];

        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(3);
        $defaultForgotPasswordLink = $securitySettingDetails['value'] ?? null;

        $notificationSettingDetails = $this->notificationSettingModel->getEmailNotificationTemplate($notificationSettingID);
        $emailSettingID = $notificationSettingDetails['email_setting_id'] ?? null;

        $emailSetting = $this->emailSettingModel->getEmailSetting($emailSettingID);
        $mailFromName = $emailSetting['mail_from_name'] ?? null;
        $mailFromEmail = $emailSetting['mail_from_email'] ?? null;

        $emailSubject = $notificationSettingDetails['email_notification_subject'] ?? null;
        $emailBody = $notificationSettingDetails['email_notification_body'] ?? null;
        $emailBody = str_replace('#{RESET_LINK}', $defaultForgotPasswordLink . $userAccountID .'&token=' . $resetToken, $emailBody);
        $emailBody = str_replace('#{RESET_LINK_VALIDITY}', $resetPasswordTokenDuration . ' minute', $emailBody);

        $message = file_get_contents('../../notification-setting/template/default-email.html');
        $message = str_replace('{EMAIL_SUBJECT}', $emailSubject, $message);
        $message = str_replace('{EMAIL_CONTENT}', $emailBody, $message);
    
        $mailer = new PHPMailer\PHPMailer\PHPMailer();
        $this->configureSMTP(1, $mailer);
        
        $mailer->setFrom($mailFromEmail, $mailFromName);
        $mailer->addAddress($email);
        $mailer->Subject = $emailSubject;
        $mailer->Body = $message;
    
        if ($mailer->send()) {
            return true;
        } 
        else {
            return 'Failed to send password reset email. Error: ' . $mailer->ErrorInfo;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function checkPasswordHasExpired($passwordExpiryDate) {
        return (new DateTime() > new DateTime($passwordExpiryDate));
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function checkPasswordHistory($userAccountID, $currentPassword) {
        $total = 0;
        $passwordHistory = $this->authenticationModel->getPasswordHistory($userAccountID);
    
        foreach ($passwordHistory as $history) {
            $password = $this->securityModel->decryptData($history['password']);
    
            if ($password === $currentPassword) {
                $total++;
            }
        }
    
        return $total;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Format methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function formatDuration($lockDuration) {
        $durationParts = [];

        $timeUnits = [
            ['year', 60 * 60 * 24 * 30 * 12],
            ['month', 60 * 60 * 24 * 30],
            ['day', 60 * 60 * 24],
            ['hour', 60 * 60],
            ['minute', 60]
        ];

        foreach ($timeUnits as list($unit, $seconds)) {
            $value = floor($lockDuration / $seconds);
            $lockDuration %= $seconds;

            if ($value > 0) {
                $durationParts[] = number_format($value) . ' ' . $unit . ($value > 1 ? 's' : '');
            }
        }

        return $durationParts;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Configure methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function configureSMTP($emailSettingID, $mailer, $isHTML = true) {
        $emailSetting = $this->emailSettingModel->getEmailSetting($emailSettingID);
    
        $mailer->isSMTP();
        $mailer->isHTML($isHTML);
        $mailer->Host = $emailSetting['mail_host'] ?? MAIL_HOST;
        $mailer->SMTPAuth = !empty($emailSetting['smtp_auth']);
        $mailer->Username = $emailSetting['mail_username'] ?? MAIL_USERNAME;
        $mailer->Password = !empty($emailSetting['mail_password']) ? 
                            $this->securityModel->decryptData($emailSetting['mail_password']) : 
                            MAIL_PASSWORD;
        $mailer->SMTPSecure = $emailSetting['mail_encryption'] ?? MAIL_SMTP_SECURE;
        $mailer->Port = $emailSetting['port'] ?? MAIL_PORT;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Generate methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function generateOTPToken($length = 6) {
        $minValue = 10 ** ($length - 1);
        $maxValue = (10 ** $length) - 1;

        return (string) random_int($minValue, $maxValue);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function generateToken($minLength = 10, $maxLength = 12) {
        $length = random_int($minLength, $maxLength);
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        $resetToken = '';
        for ($i = 0; $i < $length; $i++) {
            $resetToken .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $resetToken;
    }
    # -------------------------------------------------------------

}
?>