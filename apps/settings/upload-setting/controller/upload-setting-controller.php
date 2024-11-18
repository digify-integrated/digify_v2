<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../upload-setting/model/upload-setting-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new UploadSettingController(new UploadSettingModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class UploadSettingController {
    private $uploadSettingModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(UploadSettingModel $uploadSettingModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->uploadSettingModel = $uploadSettingModel;
        $this->authenticationModel = $authenticationModel;
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
                case 'add upload setting':
                    $this->addUploadSetting();
                    break;
                case 'update upload setting':
                    $this->updateUploadSetting();
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
    public function addUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $uploadSettingName = filter_input(INPUT_POST, 'upload_setting_name', FILTER_SANITIZE_STRING);
        $uploadSettingDescription = filter_input(INPUT_POST, 'upload_setting_description', FILTER_SANITIZE_STRING);
        $maxFileSize = filter_input(INPUT_POST, 'max_file_size', FILTER_SANITIZE_STRING);
        
        $uploadSettingID = $this->uploadSettingModel->saveUploadSetting(null, $uploadSettingName, $uploadSettingDescription, $maxFileSize, $userID);
    
        $response = [
            'success' => true,
            'uploadSettingID' => $this->securityModel->encryptData($uploadSettingID),
            'title' => 'Save Upload Setting',
            'message' => 'The upload setting has been saved successfully.',
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
    public function updateUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $uploadSettingID = filter_input(INPUT_POST, 'upload_setting_id', FILTER_VALIDATE_INT);
        $uploadSettingName = filter_input(INPUT_POST, 'upload_setting_name', FILTER_SANITIZE_STRING);
        $uploadSettingDescription = filter_input(INPUT_POST, 'upload_setting_description', FILTER_SANITIZE_STRING);
        $maxFileSize = filter_input(INPUT_POST, 'max_file_size', FILTER_SANITIZE_STRING);
    
        $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
        $total = $checkUploadSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Upload Setting',
                'message' => 'The upload setting does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->uploadSettingModel->saveUploadSetting($uploadSettingID, $uploadSettingName, $uploadSettingDescription, $maxFileSize, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Upload Setting',
            'message' => 'The upload setting has been saved successfully.',
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
    public function deleteUploadSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $uploadSettingID = filter_input(INPUT_POST, 'upload_setting_id', FILTER_VALIDATE_INT);
        
        $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
        $total = $checkUploadSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Upload Setting',
                'message' => 'The upload setting does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->uploadSettingModel->deleteUploadSetting($uploadSettingID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Upload Setting',
            'message' => 'The upload setting has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

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
                'title' => 'Delete Multiple Upload Settings',
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
                $filename = "upload_setting_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $uploadSettingDetails = $this->uploadSettingModel->exportUploadSetting($columns, $ids);

                foreach ($uploadSettingDetails as $uploadSettingDetail) {
                    fputcsv($output, $uploadSettingDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "upload_setting_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $uploadSettingDetails = $this->uploadSettingModel->exportUploadSetting($columns, $ids);

                $rowNumber = 2;
                foreach ($uploadSettingDetails as $uploadSettingDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $uploadSettingDetail[$column]);
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
    public function getUploadSettingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $uploadSettingID = filter_input(INPUT_POST, 'upload_setting_id', FILTER_VALIDATE_INT);

        $checkUploadSettingExist = $this->uploadSettingModel->checkUploadSettingExist($uploadSettingID);
        $total = $checkUploadSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Upload Setting Details',
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
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>