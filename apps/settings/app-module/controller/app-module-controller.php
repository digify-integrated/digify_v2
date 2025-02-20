<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../app-module/model/app-module-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../menu-item/model/menu-item-model.php';
require_once '../../upload-setting/model/upload-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new AppModuleController(new AppModuleModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new MenuItemModel(new DatabaseModel), new UploadSettingModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class AppModuleController {
    private $appModuleModel;
    private $authenticationModel;
    private $menuItemModel;
    private $uploadSettingModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(AppModuleModel $appModuleModel, AuthenticationModel $authenticationModel, MenuItemModel $menuItemModel, UploadSettingModel $uploadSettingModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->appModuleModel = $appModuleModel;
        $this->authenticationModel = $authenticationModel;
        $this->menuItemModel = $menuItemModel;
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
                case 'add app module':
                    $this->addAppModule();
                    break;
                case 'update app module':
                    $this->updateAppModule();
                    break;
                case 'update app logo':
                    $this->updateAppLogo();
                    break;
                case 'get app module details':
                    $this->getAppModuleDetails();
                    break;
                case 'delete app module':
                    $this->deleteAppModule();
                    break;
                case 'delete multiple app module':
                    $this->deleteMultipleAppModule();
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
    public function addAppModule() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $appModuleName = filter_input(INPUT_POST, 'app_module_name', FILTER_SANITIZE_STRING);
        $appModuleDescription = filter_input(INPUT_POST, 'app_module_description', FILTER_SANITIZE_STRING);
        $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);

        $menuItemDetails = $this->menuItemModel->getMenuItem($menuItemID);
        $menuItemName = $menuItemDetails['menu_item_name'];
        
        $appModuleID = $this->appModuleModel->saveAppModule(null, $appModuleName, $appModuleDescription, $menuItemID, $menuItemName, $orderSequence, $userID);
    
        $response = [
            'success' => true,
            'appModuleID' => $this->securityModel->encryptData($appModuleID),
            'title' => 'Save App Module',
            'message' => 'The app module has been saved successfully.',
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
    public function updateAppModule() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $appModuleID = filter_input(INPUT_POST, 'app_module_id', FILTER_VALIDATE_INT);
        $appModuleName = filter_input(INPUT_POST, 'app_module_name', FILTER_SANITIZE_STRING);
        $appModuleDescription = filter_input(INPUT_POST, 'app_module_description', FILTER_SANITIZE_STRING);
        $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
    
        $checkAppModuleExist = $this->appModuleModel->checkAppModuleExist($appModuleID);
        $total = $checkAppModuleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save App Module',
                'message' => 'The app module does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $menuItemDetails = $this->menuItemModel->getMenuItem($menuItemID);
        $menuItemName = $menuItemDetails['menu_item_name'];

        $this->appModuleModel->saveAppModule($appModuleID, $appModuleName, $appModuleDescription, $menuItemID, $menuItemName, $orderSequence, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save App Module',
            'message' => 'The app module has been saved successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateAppLogo() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];

        $appModuleID = filter_input(INPUT_POST, 'app_module_id', FILTER_VALIDATE_INT);

        $checkAppModuleExist = $this->appModuleModel->checkAppModuleExist($appModuleID);
        $total = $checkAppModuleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update App Logo',
                'message' => 'The app logo does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $appLogoFileName = $_FILES['app_logo']['name'];
        $appLogoFileSize = $_FILES['app_logo']['size'];
        $appLogoFileError = $_FILES['app_logo']['error'];
        $appLogoTempName = $_FILES['app_logo']['tmp_name'];
        $appLogoFileExtension = explode('.', $appLogoFileName);
        $appLogoActualFileExtension = strtolower(end($appLogoFileExtension));

        $uploadSetting = $this->uploadSettingModel->getUploadSetting(1);
        $maxFileSize = $uploadSetting['max_file_size'];

        $uploadSettingFileExtension = $this->uploadSettingModel->getUploadSettingFileExtension(1);
        $allowedFileExtensions = [];

        foreach ($uploadSettingFileExtension as $row) {
            $allowedFileExtensions[] = $row['file_extension'];
        }

        if (!in_array($appLogoActualFileExtension, $allowedFileExtensions)) {
            $response = [
                'success' => false,
                'title' => 'Update App Logo',
                'message' => 'The file uploaded is not supported.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if(empty($appLogoTempName)){
            $response = [
                'success' => false,
                'title' => 'Update App Logo',
                'message' => 'Please choose the app logo.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($appLogoFileError){
            $response = [
                'success' => false,
                'title' => 'Update App Logo',
                'message' => 'An error occurred while uploading the file.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($appLogoFileSize > ($maxFileSize * 1024)){
            $response = [
                'success' => false,
                'title' => 'Update App Logo',
                'message' => 'The app logo exceeds the maximum allowed size of ' . number_format($maxFileSize) . ' kb.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $fileName = $this->securityModel->generateFileName();
        $fileNew = $fileName . '.' . $appLogoActualFileExtension;
            
        define('PROJECT_BASE_DIR', dirname(__DIR__));
        define('APP_LOGO_DIR', 'image/logo/');

        $directory = PROJECT_BASE_DIR . '/'. APP_LOGO_DIR. $appModuleID. '/';
        $fileDestination = $directory. $fileNew;
        $filePath = '../settings/app-module/image/logo/'. $appModuleID . '/' . $fileNew;

        $directoryChecker = $this->securityModel->directoryChecker(str_replace('./', '../', $directory));

        if(!$directoryChecker){
            $response = [
                'success' => false,
                'title' => 'Update App Logo Error',
                'message' => $directoryChecker,
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $appModuleDetails = $this->appModuleModel->getAppModule($appModuleID);
        $appLogoPath = !empty($appModuleDetails['app_logo']) ? str_replace('../', '../../../../apps/', $appModuleDetails['app_logo']) : null;

        if(file_exists($appLogoPath)){
            if (!unlink($appLogoPath)) {
                $response = [
                    'success' => false,
                    'title' => 'Update App Logo',
                    'message' => 'The app logo cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        if(!move_uploaded_file($appLogoTempName, $fileDestination)){
            $response = [
                'success' => false,
                'title' => 'Update App Logo',
                'message' => 'The app logo cannot be uploaded due to an error.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->appModuleModel->updateAppLogo($appModuleID, $filePath, $userID);

        $response = [
            'success' => true,
            'title' => 'Update App Logo',
            'message' => 'The app logo has been updated successfully.',
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
    public function deleteAppModule() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $appModuleID = filter_input(INPUT_POST, 'app_module_id', FILTER_VALIDATE_INT);
        
        $checkAppModuleExist = $this->appModuleModel->checkAppModuleExist($appModuleID);
        $total = $checkAppModuleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete App Module',
                'message' => 'The app module does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $appModuleDetails = $this->appModuleModel->getAppModule($appModuleID);
        $appLogoPath = !empty($appModuleDetails['app_logo']) ? str_replace('../', '../../../../apps/', $appModuleDetails['app_logo']) : null;

        if(file_exists($appLogoPath)){
            if (!unlink($appLogoPath)) {
                $response = [
                    'success' => false,
                    'title' => 'Delete App Module',
                    'message' => 'The app logo cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        $this->appModuleModel->deleteAppModule($appModuleID);
                
        $response = [
            'success' => true,
            'title' => 'Delete App Module',
            'message' => 'The app module has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleAppModule() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['app_module_id']) && !empty($_POST['app_module_id'])) {
            $appModuleIDs = $_POST['app_module_id'];
    
            foreach($appModuleIDs as $appModuleID){
                $checkAppModuleExist = $this->appModuleModel->checkAppModuleExist($appModuleID);
                $total = $checkAppModuleExist['total'] ?? 0;

                if($total > 0){
                    $appModuleDetails = $this->appModuleModel->getAppModule($appModuleID);
                    $appLogoPath = !empty($appModuleDetails['app_logo']) ? str_replace('../', '../../../../apps/', $appModuleDetails['app_logo']) : null;

                    if(file_exists($appLogoPath)){
                        if (!unlink($appLogoPath)) {
                            $response = [
                                'success' => false,
                                'title' => 'Delete Multiple App Modules',
                                'message' => 'The app logo cannot be deleted due to an error.',
                                'messageType' => 'error'
                            ];
                            
                            echo json_encode($response);
                            exit;
                        }
                    }
                    
                    $this->appModuleModel->deleteAppModule($appModuleID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple App Modules',
                'message' => 'The selected app modules have been deleted successfully.',
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
                $filename = "app_module_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $appModuleDetails = $this->appModuleModel->exportAppModule($columns, $ids);

                foreach ($appModuleDetails as $appModuleDetail) {
                    fputcsv($output, $appModuleDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "app_module_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $appModuleDetails = $this->appModuleModel->exportAppModule($columns, $ids);

                $rowNumber = 2;
                foreach ($appModuleDetails as $appModuleDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $appModuleDetail[$column]);
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
    public function getAppModuleDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $appModuleID = filter_input(INPUT_POST, 'app_module_id', FILTER_VALIDATE_INT);

        $checkAppModuleExist = $this->appModuleModel->checkAppModuleExist($appModuleID);
        $total = $checkAppModuleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get App Module Details',
                'message' => 'The app module does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $appModuleDetails = $this->appModuleModel->getAppModule($appModuleID);
        $appLogo = $this->systemModel->checkImage(str_replace('../', './apps/', $appModuleDetails['app_logo'])  ?? null, 'app module logo');

        $response = [
            'success' => true,
            'appModuleName' => $appModuleDetails['app_module_name'] ?? null,
            'appModuleDescription' => $appModuleDetails['app_module_description'] ?? null,
            'menuItemID' => $appModuleDetails['menu_item_id'] ?? null,
            'menuItemName' => $appModuleDetails['menu_item_name'] ?? null,
            'orderSequence' => $appModuleDetails['order_sequence'] ?? null,
            'appLogo' => $appLogo
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>