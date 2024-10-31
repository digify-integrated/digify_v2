<?php
session_start();

# -------------------------------------------------------------
#
# Function: UploadSettingController
# Description: 
# The UploadSettingController class handles upload setting related operations and interactions.
#
# Parameters: None
#
# Returns: None
#
# -------------------------------------------------------------
class UploadSettingController {
    private $uploadSettingModel;
    private $fileExtensionModel;
    private $authenticationModel;
    private $securityModel;

    # -------------------------------------------------------------
    #
    # Function: __construct
    # Description: 
    # The constructor initializes the object with the provided uploadSettingModel, AuthenticationModel and SecurityModel instances.
    # These instances are used for upload setting related, user related operations and security related operations, respectively.
    #
    # Parameters:
    # - @param uploadSettingModel $uploadSettingModel     The uploadSettingModel instance for upload setting related operations.
    # - @param FileExtensionModel $fileExtensionModel     The fileExtensionModel instance for file extension related operations.
    # - @param AuthenticationModel $authenticationModel     The AuthenticationModel instance for user related operations.
    # - @param SecurityModel $securityModel   The SecurityModel instance for security related operations.
    #
    # Returns: None
    #
    # -------------------------------------------------------------
    public function __construct(UploadSettingModel $uploadSettingModel, FileExtensionModel $fileExtensionModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel) {
        $this->uploadSettingModel = $uploadSettingModel;
        $this->fileExtensionModel = $fileExtensionModel;
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
                case 'add upload setting':
                    $this->addUploadSetting();
                    break;
                case 'update upload setting':
                    $this->updateUploadSetting();
                    break;
                case 'assign upload setting file extension':
                    $this->assignUploadSettingFileExtension();
                    break;
                case 'get upload setting details':
                    $this->getUploadSettingDetails();
                    break;
                case 'delete upload setting':
                    $this->deleteUploadSetting();
                    break;
                case 'delete multiple upload setting':
                    $this->deleteMultipleUploadSetting();
                    break;
                case 'delete file extension':
                    $this->deleteUploadSettingFileExtension();
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
    # Function: addUploadSetting
    # Description: 
    # Inserts a upload setting.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function addUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['upload_setting_name']) && !empty($_POST['upload_setting_name']) && isset($_POST['upload_setting_description']) && !empty($_POST['upload_setting_description']) && isset($_POST['max_file_size']) && !empty($_POST['max_file_size'])) {
            $userID = $_SESSION['user_account_id'];
            $uploadSettingName = htmlspecialchars($_POST['upload_setting_name'], ENT_QUOTES, 'UTF-8');
            $uploadSettingDescription = htmlspecialchars($_POST['upload_setting_description'], ENT_QUOTES, 'UTF-8');
            $maxFileSize = htmlspecialchars($_POST['max_file_size'], ENT_QUOTES, 'UTF-8');
        
            $uploadSettingID = $this->uploadSettingModel->insertUploadSetting($uploadSettingName, $uploadSettingDescription, $maxFileSize, $userID);
    
            $response = [
                'success' => true,
                'uploadSettingID' => $this->securityModel->encryptData($uploadSettingID),
                'title' => 'Insert Upload Setting Success',
                'message' => 'The upload setting has been inserted successfully.',
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
    # Function: updateUploadSetting
    # Description: 
    # Updates the upload setting if it exists; otherwise, return an error message.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function updateUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        if (isset($_POST['upload_setting_id']) && !empty($_POST['upload_setting_id']) && isset($_POST['upload_setting_name']) && !empty($_POST['upload_setting_name']) && isset($_POST['upload_setting_description']) && !empty($_POST['upload_setting_description']) && isset($_POST['max_file_size']) && !empty($_POST['max_file_size'])) {
            $userID = $_SESSION['user_account_id'];
            $uploadSettingID = htmlspecialchars($_POST['upload_setting_id'], ENT_QUOTES, 'UTF-8');
            $uploadSettingName = htmlspecialchars($_POST['upload_setting_name'], ENT_QUOTES, 'UTF-8');
            $uploadSettingDescription = htmlspecialchars($_POST['upload_setting_description'], ENT_QUOTES, 'UTF-8');
            $maxFileSize = htmlspecialchars($_POST['max_file_size'], ENT_QUOTES, 'UTF-8');
        
            $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
            $total = $checkUploadSettingExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Update Upload Setting Error',
                    'message' => 'The upload setting does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->uploadSettingModel->updateUploadSetting($uploadSettingID, $uploadSettingName, $uploadSettingDescription, $maxFileSize, $userID);
                
            $response = [
                'success' => true,
                'title' => 'Update Upload Setting Success',
                'message' => 'The upload setting has been updated successfully.',
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
    #   Assign methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #
    # Function: assignUploadSettingFileExtension
    # Description: 
    # Assigns a file extension on upload setting.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function assignUploadSettingFileExtension() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['upload_setting_id']) && !empty($_POST['upload_setting_id'])) {
            if(!isset($_POST['file_extension_id']) || empty($_POST['file_extension_id'])){
                $response = [
                    'success' => false,
                    'title' => 'File Extension Selection Required',
                    'message' => 'Please select the file extension(s) you wish to assign to the upload setting.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $userID = $_SESSION['user_account_id'];
            $uploadSettingID = htmlspecialchars($_POST['upload_setting_id'], ENT_QUOTES, 'UTF-8');
            $fileExtensionIDs = $_POST['file_extension_id'];
            
            $uploadSettingDetails = $this->uploadSettingModel->getUploadSetting($uploadSettingID);
            $uploadSettingName = $uploadSettingDetails['upload_setting_name'] ?? null;

            foreach ($fileExtensionIDs as $fileExtensionID) {
                $fileExtensionDetails = $this->fileExtensionModel->getFileExtension($fileExtensionID);
                $fileExtensionName = $fileExtensionDetails['file_extension_name'] ?? null;
                $fileExtension = $fileExtensionDetails['file_extension'] ?? null;

                $this->uploadSettingModel->insertUploadSettingFileExtension($uploadSettingID, $uploadSettingName, $fileExtensionID, $fileExtensionName, $fileExtension, $userID);
            }
    
            $response = [
                'success' => true,
                'title' => 'Assign File Extension Success',
                'message' => 'The file extension has been assigned successfully.',
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
    # Function: deleteUploadSetting
    # Description: 
    # Delete the upload setting if it exists; otherwise, return an error message.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function deleteUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['upload_setting_id']) && !empty($_POST['upload_setting_id'])) {
            $uploadSettingID = htmlspecialchars($_POST['upload_setting_id'], ENT_QUOTES, 'UTF-8');
        
            $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
            $total = $checkUploadSettingExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Delete Upload Setting Error',
                    'message' => 'The upload setting does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->uploadSettingModel->deleteUploadSetting($uploadSettingID);
                
            $response = [
                'success' => true,
                'title' => 'Delete Upload Setting Success',
                'message' => 'The upload setting has been deleted successfully.',
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
    # Function: deleteUploadSettingFileExtension
    # Description: 
    # Delete the upload setting if it exists; otherwise, return an error message.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function deleteUploadSettingFileExtension() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['upload_setting_file_extension_id']) && !empty($_POST['upload_setting_file_extension_id'])) {
            $uploadSettingFileExtensionID = htmlspecialchars($_POST['upload_setting_file_extension_id'], ENT_QUOTES, 'UTF-8');
        
            $checkUploadSettingFileExtensionExist = $this->uploadSettingModel->checkUploadSettingFileExtensionExist($uploadSettingFileExtensionID);
            $total = $checkUploadSettingFileExtensionExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Delete File Extension Error',
                    'message' => 'The file extension does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->uploadSettingModel->deleteUploadSettingFileExtension($uploadSettingFileExtensionID);
                
            $response = [
                'success' => true,
                'title' => 'Delete File Extension Success',
                'message' => 'The file extension has been deleted successfully.',
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
    # Function: deleteMultipleUploadSetting
    # Description: 
    # Delete the selected upload settings if it exists; otherwise, skip it.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function deleteMultipleUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['upload_setting_id']) && !empty($_POST['upload_setting_id'])) {
            $uploadSettingIDs = $_POST['upload_setting_id'];
    
            foreach($uploadSettingIDs as $uploadSettingID){
                $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
                $total = $checkUploadSettingExist['total'] ?? 0;

                if($total > 0){
                    $this->uploadSettingModel->deleteUploadSetting($uploadSettingID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Upload Setting Success',
                'message' => 'The selected upload settings have been deleted successfully.',
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
    # Function: getUploadSettingDetails
    # Description: 
    # Handles the retrieval of upload setting details.
    #
    # Parameters: None
    #
    # Returns: Array
    #
    # -------------------------------------------------------------
    public function getUploadSettingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        if (isset($_POST['upload_setting_id']) && !empty($_POST['upload_setting_id'])) {
            $userID = $_SESSION['user_account_id'];
            $uploadSettingID = htmlspecialchars($_POST['upload_setting_id'], ENT_QUOTES, 'UTF-8');

            $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
            $total = $checkUploadSettingExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Get Upload Setting Details Error',
                    'message' => 'The upload setting does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }
    
            $uploadSettingDetails = $this->uploadSettingModel->getUploadSetting($uploadSettingID);

            $response = [
                'success' => true,
                'uploadSettingName' => $uploadSettingDetails['upload_setting_name'] ?? null,
                'uploadSettingDescription' => $uploadSettingDetails['upload_setting_description'] ?? null,
                'maxFileSize' => $uploadSettingDetails['max_file_size'] ?? null
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
require_once '../../upload-setting/model/upload-setting-model.php';
require_once '../../file-extension/model/file-extension-model.php';
require_once '../../authentication/model/authentication-model.php';

$controller = new UploadSettingController(new uploadSettingModel(new DatabaseModel), new FileExtensionModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel), new SecurityModel());
$controller->handleRequest();

?>