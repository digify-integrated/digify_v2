<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../user-account/model/user-account-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../upload-setting/model/upload-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new UserAccountController(new UserAccountModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new UploadSettingModel(new DatabaseModel), new SecuritySettingModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class UserAccountController {
    private $userAccountModel;
    private $authenticationModel;
    private $uploadSettingModel;
    private $securitySettingModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(UserAccountModel $userAccountModel, AuthenticationModel $authenticationModel, UploadSettingModel $uploadSettingModel, SecuritySettingModel $securitySettingModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->userAccountModel = $userAccountModel;
        $this->authenticationModel = $authenticationModel;
        $this->uploadSettingModel = $uploadSettingModel;
        $this->securitySettingModel = $securitySettingModel;
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
                case 'add user account':
                    $this->addUserAccount();
                    break;
                case 'update full name':
                    $this->updateFullName();
                    break;
                case 'update username':
                    $this->updateUsername();
                    break;
                case 'update email':
                    $this->updateEmail();
                    break;
                case 'update phone':
                    $this->updatePhone();
                    break;
                case 'update password':
                    $this->updatePassword();
                    break;
                case 'update profile picture':
                    $this->updateProfilePicture();
                    break;
                case 'enable two factor authentication':
                    $this->enableTwoFactorAuthentication();
                    break;
                case 'disable two factor authentication':
                    $this->disableTwoFactorAuthentication();
                    break;
                case 'enable multiple login sessions':
                    $this->enableMultipleLoginSessions();
                    break;
                case 'disable multiple login sessions':
                    $this->disableMultipleLoginSessions();
                    break;
                case 'get user account details':
                    $this->getUserAccountDetails();
                    break;
                case 'delete user account':
                    $this->deleteUserAccount();
                    break;
                case 'delete multiple user account':
                    $this->deleteMultipleUserAccount();
                    break;
                case 'export data':
                    $this->exportData();
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
    #   Add methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function addUserAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $fileAs = filter_input(INPUT_POST, 'file_as', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(4);
        $defaultPasswordDuration = $securitySettingDetails['value'] ?? DEFAULT_PASSWORD_DURATION;
    
        $passwordExpiryDate = $this->securityModel->encryptData(date('Y-m-d', strtotime('+'. $defaultPasswordDuration .' days')));
        
        $encryptedPassword = $this->securityModel->encryptData($password);
        
        $userAccountID = $this->userAccountModel->addUserAccount($fileAs, $email, $username, $encryptedPassword, $phone, $passwordExpiryDate, $userID);
    
        $response = [
            'success' => true,
            'userAccountID' => $this->securityModel->encryptData($userAccountID),
            'title' => 'Save User Account',
            'message' => 'The user account has been saved successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateFullName() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
        $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    
        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Full Name',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->userAccountModel->updateUserAccountFullName($userAccountID, $fullName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Full Name',
            'message' => 'The full name has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUsername() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    
        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Username',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    
        $checkUserAccountUsernameExist = $this->userAccountModel->checkUserAccountUsernameExist($userAccountID, $username);
        $total = $checkUserAccountUsernameExist['total'] ?? 0;

        if($total > 0){
            $response = [
                'success' => false,
                'title' => 'Update Username',
                'message' => 'The username already exists.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->userAccountModel->updateUserAccountUsername($userAccountID, $username, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Username',
            'message' => 'The username has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    
        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Email',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    
        $checkUserAccountEmailExist = $this->userAccountModel->checkUserAccountEmailExist($userAccountID, $email);
        $total = $checkUserAccountEmailExist['total'] ?? 0;

        if($total > 0){
            $response = [
                'success' => false,
                'title' => 'Update Email',
                'message' => 'The email address already exists.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->userAccountModel->updateUserAccountEmailAddress($userAccountID, $email, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Email',
            'message' => 'The email address has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updatePhone() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    
        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Phone',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
    
        $checkUserAccountPhoneExist = $this->userAccountModel->checkUserAccountPhoneExist($userAccountID, $phone);
        $total = $checkUserAccountPhoneExist['total'] ?? 0;

        if($total > 0){
            $response = [
                'success' => false,
                'title' => 'Update Phone',
                'message' => 'The phone already exists.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->userAccountModel->updateUserAccountPhone($userAccountID, $phone, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Phone',
            'message' => 'The phone has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
        $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    
        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Password',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $securitySettingDetails = $this->securitySettingModel->getSecuritySetting(4);
        $defaultPasswordDuration = $securitySettingDetails['value'] ?? DEFAULT_PASSWORD_DURATION;
    
        $passwordExpiryDate = $this->securityModel->encryptData(date('Y-m-d', strtotime('+'. $defaultPasswordDuration .' days')));
        
        $encryptedPassword = $this->securityModel->encryptData($newPassword);

        $this->userAccountModel->updateUserAccountPassword($userAccountID, $encryptedPassword, $passwordExpiryDate, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Password',
            'message' => 'The password has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateProfilePicture() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];

        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);

        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Profile Picture',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $profilePictureFileName = $_FILES['profile_picture']['name'];
        $profilePictureFileSize = $_FILES['profile_picture']['size'];
        $profilePictureFileError = $_FILES['profile_picture']['error'];
        $profilePictureTempName = $_FILES['profile_picture']['tmp_name'];
        $profilePictureFileExtension = explode('.', $profilePictureFileName);
        $profilePictureActualFileExtension = strtolower(end($profilePictureFileExtension));

        $uploadSetting = $this->uploadSettingModel->getUploadSetting(4);
        $maxFileSize = $uploadSetting['max_file_size'];

        $uploadSettingFileExtension = $this->uploadSettingModel->getUploadSettingFileExtension(4);
        $allowedFileExtensions = [];

        foreach ($uploadSettingFileExtension as $row) {
            $allowedFileExtensions[] = $row['file_extension'];
        }

        if (!in_array($profilePictureActualFileExtension, $allowedFileExtensions)) {
            $response = [
                'success' => false,
                'title' => 'Update Profile Picture',
                'message' => 'The file uploaded is not supported.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if(empty($profilePictureTempName)){
            $response = [
                'success' => false,
                'title' => 'Update Profile Picture',
                'message' => 'Please choose the profile picture.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($profilePictureFileError){
            $response = [
                'success' => false,
                'title' => 'Update Profile Picture',
                'message' => 'An error occurred while uploading the file.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($profilePictureFileSize > ($maxFileSize * 1024)){
            $response = [
                'success' => false,
                'title' => 'Update Profile Picture',
                'message' => 'The profile picture exceeds the maximum allowed size of ' . number_format($maxFileSize) . ' kb.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $fileName = $this->securityModel->generateFileName();
        $fileNew = $fileName . '.' . $profilePictureActualFileExtension;
            
        define('PROJECT_BASE_DIR', dirname(__DIR__));
        define('PROFILE_PICTURE_DIR', 'profile_picture/');

        $directory = PROJECT_BASE_DIR . '/'. PROFILE_PICTURE_DIR. $userAccountID. '/';
        $fileDestination = $directory. $fileNew;
        $filePath = '../settings/user-account/profile_picture/'. $userAccountID . '/' . $fileNew;

        $directoryChecker = $this->securityModel->directoryChecker(str_replace('./', '../', $directory));

        if(!$directoryChecker){
            $response = [
                'success' => false,
                'title' => 'Update Profile Picture Error',
                'message' => $directoryChecker,
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $userAccountDetails = $this->userAccountModel->getUserAccount($userAccountID);
        $profilePicturePath = !empty($userAccountDetails['profile_picture']) ? str_replace('../', '../../../../apps/', $userAccountDetails['profile_picture']) : null;

        if(file_exists($profilePicturePath)){
            if (!unlink($profilePicturePath)) {
                $response = [
                    'success' => false,
                    'title' => 'Update Profile Picture',
                    'message' => 'The profile picture cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        if(!move_uploaded_file($profilePictureTempName, $fileDestination)){
            $response = [
                'success' => false,
                'title' => 'Update Profile Picture',
                'message' => 'The profile picture cannot be uploaded due to an error.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->userAccountModel->updateProfilePicture($userAccountID, $filePath, $userID);

        $response = [
            'success' => true,
            'title' => 'Update Profile Picture',
            'message' => 'The profile picture has been updated successfully.',
            'messageType' => 'success'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteUserAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
        
        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete User Account',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $userAccountDetails = $this->userAccountModel->getUserAccount($userAccountID);
        $profilePicturePath = !empty($userAccountDetails['profile_picture']) ? str_replace('../', '../../../../apps/', $userAccountDetails['profile_picture']) : null;

        if(file_exists($profilePicturePath)){
            if (!unlink($profilePicturePath)) {
                $response = [
                    'success' => false,
                    'title' => 'Delete User Account',
                    'message' => 'The profile picture cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        $this->userAccountModel->deleteUserAccount($userAccountID);
                
        $response = [
            'success' => true,
            'title' => 'Delete User Account',
            'message' => 'The user account has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleUserAccount() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['user_account_id']) && !empty($_POST['user_account_id'])) {
            $userAccountIDs = $_POST['user_account_id'];
    
            foreach($userAccountIDs as $userAccountID){
                $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
                $total = $checkUserAccountExist['total'] ?? 0;

                if($total > 0){
                    $userAccountDetails = $this->userAccountModel->getUserAccount($userAccountID);
                    $profilePicturePath = !empty($userAccountDetails['profile_picture']) ? str_replace('../', '../../../../apps/', $userAccountDetails['profile_picture']) : null;

                    if(file_exists($profilePicturePath)){
                        if (!unlink($profilePicturePath)) {
                            $response = [
                                'success' => false,
                                'title' => 'Delete Multiple User Account',
                                'message' => 'The profile picture cannot be deleted due to an error.',
                                'messageType' => 'error'
                            ];
                            
                            echo json_encode($response);
                            exit;
                        }
                    }
                    
                    $this->userAccountModel->deleteUserAccount($userAccountID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple User Account',
                'message' => 'The selected user accounts have been deleted successfully.',
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
    #   Enable methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function enableTwoFactorAuthentication() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['user_account_id']) && !empty($_POST['user_account_id'])) {
            $userID = $_SESSION['user_account_id'];
            $userAccountID = htmlspecialchars($_POST['user_account_id'], ENT_QUOTES, 'UTF-8');
        
            $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
            $total = $checkUserAccountExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Enable Two-Factor Authentication',
                    'message' => 'The user account does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->userAccountModel->updateTwoFactorAuthenticationStatus($userAccountID, $this->securityModel->encryptData('Yes'), $userID);
                
            $response = [
                'success' => true,
                'title' => 'Enable Two-Factor Authentication',
                'message' => 'The two-factor authentication has been enabled successfully.',
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
    public function enableMultipleLoginSessions() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['user_account_id']) && !empty($_POST['user_account_id'])) {
            $userID = $_SESSION['user_account_id'];
            $userAccountID = htmlspecialchars($_POST['user_account_id'], ENT_QUOTES, 'UTF-8');
        
            $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
            $total = $checkUserAccountExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Enable Multiple Login Sessions',
                    'message' => 'The user account does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->userAccountModel->updateMultipleLoginSessionsStatus($userAccountID, $this->securityModel->encryptData('Yes'), $userID);
                
            $response = [
                'success' => true,
                'title' => 'Enable Multiple Login Sessions',
                'message' => 'The multiple login sessions has been enabled successfully.',
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
    #   Disable methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function disableTwoFactorAuthentication() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['user_account_id']) && !empty($_POST['user_account_id'])) {
            $userID = $_SESSION['user_account_id'];
            $userAccountID = htmlspecialchars($_POST['user_account_id'], ENT_QUOTES, 'UTF-8');
        
            $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
            $total = $checkUserAccountExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Disable Two-Factor Authentication',
                    'message' => 'The user account does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->userAccountModel->updateTwoFactorAuthenticationStatus($userAccountID, $this->securityModel->encryptData('No'), $userID);
                
            $response = [
                'success' => true,
                'title' => 'Disable Two-Factor Authentication',
                'message' => 'The two-factor authentication has been disabled successfully.',
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
    public function disableMultipleLoginSessions() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['user_account_id']) && !empty($_POST['user_account_id'])) {
            $userID = $_SESSION['user_account_id'];
            $userAccountID = htmlspecialchars($_POST['user_account_id'], ENT_QUOTES, 'UTF-8');
        
            $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
            $total = $checkUserAccountExist['total'] ?? 0;

            if($total === 0){
                $response = [
                    'success' => false,
                    'notExist' => true,
                    'title' => 'Disable Multiple Login Sessions',
                    'message' => 'The user account does not exist.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $this->userAccountModel->updateMultipleLoginSessionsStatus($userAccountID, $this->securityModel->encryptData('No'), $userID);
                
            $response = [
                'success' => true,
                'title' => 'Disable Multiple Login Sessions',
                'message' => 'The multiple login sessions has been disabled successfully.',
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
    #   Export methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function exportData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['export_to']) && !empty($_POST['export_to']) && isset($_POST['export_id']) && !empty($_POST['export_id']) && isset($_POST['table_column']) && !empty($_POST['table_column'])) {
            $exportTo = $_POST['export_to'];
            $exportIDs = $_POST['export_id']; 
            $tableColumns = $_POST['table_column'];
            
            if ($exportTo == 'csv') {
                $filename = "user_account_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $userAccountDetails = $this->userAccountModel->exportUserAccount($columns, $ids);

                foreach ($userAccountDetails as $userAccountDetail) {
                    fputcsv($output, $userAccountDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "user_account_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $userAccountDetails = $this->userAccountModel->exportUserAccount($columns, $ids);

                $rowNumber = 2;
                foreach ($userAccountDetails as $userAccountDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $userAccountDetail[$column]);
                        $colIndex++;
                    }
                    $rowNumber++;
                }

                ob_end_clean();

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Cache-Control: max-age=0');

                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
                exit;
            }
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
    public function getUserAccountDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);

        $checkUserAccountExist = $this->userAccountModel->checkUserAccountExist($userAccountID);
        $total = $checkUserAccountExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get User Account Details',
                'message' => 'The user account does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $userAccountDetails = $this->userAccountModel->getUserAccount($userAccountID);
        $profilePicture = $this->systemModel->checkImage(str_replace('../', './apps/', $userAccountDetails['profile_picture'])  ?? null, 'profile');
        $lastConnectionDate = (!empty($userAccountDetails['last_connection_date'])) ? date('d M Y h:i a', strtotime($userAccountDetails['last_connection_date'])) : 'Never Connected';
        $lastPasswordChange = (!empty($userAccountDetails['last_password_change'])) ? date('d M Y h:i a', strtotime($userAccountDetails['last_password_change'])) : 'Never Changed';
        $passwordExpiryDate = (!empty($userAccountDetails['password_expiry_date'])) ? date('d M Y', strtotime($this->securityModel->decryptData($userAccountDetails['password_expiry_date']))) : 'Never Connected';

        $response = [
            'success' => true,
            'fileAs' => $userAccountDetails['file_as'] ?? null,
            'email' => $userAccountDetails['email'] ?? null,
            'username' => $userAccountDetails['username'] ?? null,
            'phone' => $userAccountDetails['phone'] ?? null,
            'phoneSummary' => $userAccountDetails['phone'] ?? '-',
            'lastConnectionDate' => $lastConnectionDate,
            'lastPasswordChange' => $lastPasswordChange,
            'passwordExpiryDate' => $passwordExpiryDate,
            'profilePicture' => $profilePicture,
            'twoFactorAuthentication' => $this->securityModel->decryptData($userAccountDetails['two_factor_auth']),
            'multipleSession' => $this->securityModel->decryptData($userAccountDetails['multiple_session'])
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>