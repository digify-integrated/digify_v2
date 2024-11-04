<?php
session_start();

# -------------------------------------------------------------
#
# Function: EmailSettingController
# Description: 
# The EmailSettingController class handles email setting related operations and interactions.
#
# Parameters: None
#
# Returns: None
#
# -------------------------------------------------------------
class EmailSettingController {
    private $emailSettingModel;
    private $authenticationModel;
    private $securityModel;

    # -------------------------------------------------------------
    #
    # Function: __construct
    # Description: 
    # The constructor initializes the object with the provided emailSettingModel, AuthenticationModel and SecurityModel instances.
    # These instances are used for email setting related, user related operations and security related operations, respectively.
    #
    # Parameters:
    # - @param emailSettingModel $emailSettingModel     The emailSettingModel instance for email setting related operations.
    # - @param AuthenticationModel $authenticationModel     The AuthenticationModel instance for user related operations.
    # - @param SecurityModel $securityModel   The SecurityModel instance for security related operations.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function __construct(EmailSettingModel $emailSettingModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel) {
        $this->emailSettingModel = $emailSettingModel;
        $this->authenticationModel = $authenticationModel;
        $this->securityModel = $securityModel;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: handleRequest
    # Description: 
    # This method checks the request method and dispatches the corresponding transaction based on the provided transaction parameter.
    # The transaction determines which action should be performed.
    #
    # Parameters:
    # - $transaction (string): The type of transaction.
    #
    # Returns: Array
    #
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
            $sessionToken = $this->securityModel->decryptData($loginCredentialsDetails['session_token']);

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
            
            if ($sessionToken != $sessionToken && $multipleSession == 'No') {
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
                case 'add email setting':
                    $this->addEmailSetting();
                    break;
                case 'update email setting':
                    $this->updateEmailSetting();
                    break;
                case 'get email setting details':
                    $this->getEmailSettingDetails();
                    break;
                case 'delete email setting':
                    $this->deleteEmailSetting();
                    break;
                case 'delete multiple email setting':
                    $this->deleteMultipleEmailSetting();
                    break;
                default:
                    $response = [
                        'success' => false,
                        'title' => 'Error: Transaction Failed',
                        'message' => 'An error occurred while processing your transaction. Please try again or contact our support team for assistance.',
                        'messageType' => 'error'
                    ];
                    
                    echo json_encode($response);
                    break;
            }
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Add methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: addEmailSetting
    # Description: 
    # Inserts a email setting.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function addEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['email_setting_name']) && !empty($_POST['email_setting_name']) && isset($_POST['email_setting_description']) && !empty($_POST['email_setting_description']) && isset($_POST['mail_host']) && !empty($_POST['mail_host']) && isset($_POST['port']) && !empty($_POST['port']) && isset($_POST['mail_username']) && !empty($_POST['mail_username']) && isset($_POST['mail_password']) && !empty($_POST['mail_password']) && isset($_POST['mail_from_name']) && !empty($_POST['mail_from_name']) && isset($_POST['mail_from_email']) && !empty($_POST['mail_from_email']) && isset($_POST['mail_encryption']) && !empty($_POST['mail_encryption']) && isset($_POST['smtp_auth']) && isset($_POST['smtp_auto_tls'])) {
            $userID = $_SESSION['user_account_id'];
            $emailSettingName = htmlspecialchars($_POST['email_setting_name'], ENT_QUOTES, 'UTF-8');
            $emailSettingDescription = htmlspecialchars($_POST['email_setting_description'], ENT_QUOTES, 'UTF-8');
            $mailHost = htmlspecialchars($_POST['mail_host'], ENT_QUOTES, 'UTF-8');
            $port = htmlspecialchars($_POST['port'], ENT_QUOTES, 'UTF-8');
            $mailUsername = htmlspecialchars($_POST['mail_username'], ENT_QUOTES, 'UTF-8');
            $mailPassword = $this->securityModel->encryptData($_POST['mail_password']);
            $mailFromName = htmlspecialchars($_POST['mail_from_name'], ENT_QUOTES, 'UTF-8');
            $mailFromEmail = htmlspecialchars($_POST['mail_from_email'], ENT_QUOTES, 'UTF-8');
            $mailEncryption = htmlspecialchars($_POST['mail_encryption'], ENT_QUOTES, 'UTF-8');
            $smtpAuth = htmlspecialchars($_POST['smtp_auth'], ENT_QUOTES, 'UTF-8');
            $smtpAutoTLS = htmlspecialchars($_POST['smtp_auto_tls'], ENT_QUOTES, 'UTF-8');
        
            $emailSettingID = $this->emailSettingModel->insertEmailSetting($emailSettingName, $emailSettingDescription, $mailHost, $port, $smtpAuth, $smtpAutoTLS, $mailUsername, $mailPassword, $mailEncryption, $mailFromName, $mailFromEmail, $userID);
    
            $response = [
                'success' => true,
                'emailSettingID' => $this->securityModel->encryptData($emailSettingID),
                'title' => 'Insert Email Setting Success',
                'message' => 'The email setting has been inserted successfully.',
                'messageType' => 'success'
            ];
            
            echo json_encode($response);
            exit;
        }
        else{
            $response = [
                'success' => false,
                'title' => 'Error: Transaction Failed',
                'message' => 'An error occurred while processing your transaction. Please try again or contact our support team for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: updateEmailSetting
    # Description: 
    # Updates the email setting if it exists; otherwise, return an error message.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function updateEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        if (isset($_POST['email_setting_id']) && !empty($_POST['email_setting_id']) && isset($_POST['email_setting_name']) && !empty($_POST['email_setting_name']) && isset($_POST['email_setting_description']) && !empty($_POST['email_setting_description']) && isset($_POST['mail_host']) && !empty($_POST['mail_host']) && isset($_POST['port']) && !empty($_POST['port']) && isset($_POST['mail_username']) && !empty($_POST['mail_username']) && isset($_POST['mail_password']) && !empty($_POST['mail_password']) && isset($_POST['mail_from_name']) && !empty($_POST['mail_from_name']) && isset($_POST['mail_from_email']) && !empty($_POST['mail_from_email']) && isset($_POST['mail_encryption']) && !empty($_POST['mail_encryption']) && isset($_POST['smtp_auth']) && isset($_POST['smtp_auto_tls']) ) {
            $userID = $_SESSION['user_account_id'];
            $emailSettingID = htmlspecialchars($_POST['email_setting_id'], ENT_QUOTES, 'UTF-8');
            $emailSettingName = htmlspecialchars($_POST['email_setting_name'], ENT_QUOTES, 'UTF-8');
            $emailSettingDescription = htmlspecialchars($_POST['email_setting_description'], ENT_QUOTES, 'UTF-8');
            $mailHost = htmlspecialchars($_POST['mail_host'], ENT_QUOTES, 'UTF-8');
            $port = htmlspecialchars($_POST['port'], ENT_QUOTES, 'UTF-8');
            $mailUsername = htmlspecialchars($_POST['mail_username'], ENT_QUOTES, 'UTF-8');
            $mailPassword = $this->securityModel->encryptData($_POST['mail_password']);
            $mailFromName = htmlspecialchars($_POST['mail_from_name'], ENT_QUOTES, 'UTF-8');
            $mailFromEmail = htmlspecialchars($_POST['mail_from_email'], ENT_QUOTES, 'UTF-8');
            $mailEncryption = htmlspecialchars($_POST['mail_encryption'], ENT_QUOTES, 'UTF-8');
            $smtpAuth = htmlspecialchars($_POST['smtp_auth'], ENT_QUOTES, 'UTF-8');
            $smtpAutoTLS = htmlspecialchars($_POST['smtp_auto_tls'], ENT_QUOTES, 'UTF-8');
        
            $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
            $total = $checkEmailSettingExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Update Email Setting Error',
                    'message' => 'The email setting does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->emailSettingModel->updateEmailSetting($emailSettingID, $emailSettingName, $emailSettingDescription, $mailHost, $port, $smtpAuth, $smtpAutoTLS, $mailUsername, $mailPassword, $mailEncryption, $mailFromName, $mailFromEmail, $userID);
                
            $response = [
                'success' => true,
                'title' => 'Update Email Setting Success',
                'message' => 'The email setting has been updated successfully.',
                'messageType' => 'success'
            ];
            
            echo json_encode($response);
            exit;
        }
        else{
            $response = [
                'success' => false,
                'title' => 'Error: Transaction Failed',
                'message' => 'An error occurred while processing your transaction. Please try again or contact our support team for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: deleteEmailSetting
    # Description: 
    # Delete the email setting if it exists; otherwise, return an error message.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function deleteEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['email_setting_id']) && !empty($_POST['email_setting_id'])) {
            $emailSettingID = htmlspecialchars($_POST['email_setting_id'], ENT_QUOTES, 'UTF-8');
        
            $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
            $total = $checkEmailSettingExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Delete Email Setting Error',
                    'message' => 'The email setting does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->emailSettingModel->deleteEmailSetting($emailSettingID);
                
            $response = [
                'success' => true,
                'title' => 'Delete Email Setting Success',
                'message' => 'The email setting has been deleted successfully.',
                'messageType' => 'success'
            ];
            
            echo json_encode($response);
            exit;
        }
        else{
            $response = [
                'success' => false,
                'title' => 'Error: Transaction Failed',
                'message' => 'An error occurred while processing your transaction. Please try again or contact our support team for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: deleteMultipleEmailSetting
    # Description: 
    # Delete the selected email settings if it exists; otherwise, skip it.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function deleteMultipleEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['email_setting_id']) && !empty($_POST['email_setting_id'])) {
            $emailSettingIDs = $_POST['email_setting_id'];
    
            foreach($emailSettingIDs as $emailSettingID){
                $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
                $total = $checkEmailSettingExist['total'] ?? 0;

                if($total > 0){
                    $this->emailSettingModel->deleteEmailSetting($emailSettingID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Email Setting Success',
                'message' => 'The selected email settings have been deleted successfully.',
                'messageType' => 'success'
            ];
            
            echo json_encode($response);
            exit;
        }
        else{
            $response = [
                'success' => false,
                'title' => 'Error: Transaction Failed',
                'message' => 'An error occurred while processing your transaction. Please try again or contact our support team for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Get details methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: getEmailSettingDetails
    # Description: 
    # Handles the retrieval of email setting details.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function getEmailSettingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        if (isset($_POST['email_setting_id']) && !empty($_POST['email_setting_id'])) {
            $userID = $_SESSION['user_account_id'];
            $emailSettingID = htmlspecialchars($_POST['email_setting_id'], ENT_QUOTES, 'UTF-8');

            $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
            $total = $checkEmailSettingExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Get Email Setting Details Error',
                    'message' => 'The email setting does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }
    
            $emailSettingDetails = $this->emailSettingModel->getEmailSetting($emailSettingID);
            $smtpAutoTLS = $emailSettingDetails['smtp_auto_tls'] ?? null;
            $smtpAuth = $emailSettingDetails['smtp_auth'] ?? null;
            
            $smtpAutoTLSSummary = ($smtpAutoTLS == 1) ? 'Yes' : 'No';
            $smtpAuthSummary = ($smtpAuth == 1) ? 'Yes' : 'No';

            $response = [
                'success' => true,
                'emailSettingName' => $emailSettingDetails['email_setting_name'] ?? null,
                'emailSettingDescription' => $emailSettingDetails['email_setting_description'] ?? null,
                'mailHost' => $emailSettingDetails['mail_host'] ?? null,
                'port' => $emailSettingDetails['port'] ?? null,
                'smtpAuth' => $smtpAuth ?? null,
                'smtpAutoTLS' => $smtpAutoTLS ?? null,
                'smtpAuthSummary' => $smtpAuthSummary,
                'smtpAutoTLSSummary' => $smtpAutoTLSSummary,
                'mailUsername' => $emailSettingDetails['mail_username'] ?? null,
                'mailPassword' => $this->securityModel->decryptData($emailSettingDetails['mail_password'] ?? null),
                'mailEncryption' => $emailSettingDetails['mail_encryption'] ?? null,
                'mailFromName' => $emailSettingDetails['mail_from_name'] ?? null,
                'mailFromEmail' => $emailSettingDetails['mail_from_email'] ?? null
            ];

            echo json_encode($response);
            exit;
        }
        else{
            $response = [
                'success' => false,
                'title' => 'Error: Transaction Failed',
                'message' => 'An error occurred while processing your transaction. Please try again or contact our support team for assistance.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

require_once '../../global/config/config.php';
require_once '../../global/model/database-model.php';
require_once '../../global/model/security-model.php';
require_once '../../global/model/system-model.php';
require_once '../../email-setting/model/email-setting-model.php';
require_once '../../authentication/model/authentication-model.php';

$controller = new EmailSettingController(new emailSettingModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel), new SecurityModel());
$controller->handleRequest();

?>