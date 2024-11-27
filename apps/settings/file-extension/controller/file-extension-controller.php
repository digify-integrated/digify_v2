<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../file-extension/model/file-extension-model.php';
require_once '../../file-type/model/file-type-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new FileExtensionController(new FileExtensionModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new FileTypeModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class FileExtensionController {
    private $fileExtensionModel;
    private $authenticationModel;
    private $fileTypeModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(FileExtensionModel $fileExtensionModel, AuthenticationModel $authenticationModel, FileTypeModel $fileTypeModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->fileExtensionModel = $fileExtensionModel;
        $this->authenticationModel = $authenticationModel;
        $this->fileTypeModel = $fileTypeModel;
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
                case 'add file extension':
                    $this->addFileExtension();
                    break;
                case 'update file extension':
                    $this->updateFileExtension();
                    break;
                case 'get file extension details':
                    $this->getFileExtensionDetails();
                    break;
                case 'delete file extension':
                    $this->deleteFileExtension();
                    break;
                case 'delete multiple file extension':
                    $this->deleteMultipleFileExtension();
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
    public function addFileExtension() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $fileExtensionName = filter_input(INPUT_POST, 'file_extension_name', FILTER_SANITIZE_STRING);
        $fileExtension = filter_input(INPUT_POST, 'file_extension', FILTER_SANITIZE_STRING);
        $fileTypeID = filter_input(INPUT_POST, 'file_type_id', FILTER_VALIDATE_INT);

        $fileTypeDetails = $this->fileTypeModel->getFileType($fileTypeID);
        $fileTypeName = $fileTypeDetails['file_type_name'] ?? null;
        
        $fileExtensionID = $this->fileExtensionModel->saveFileExtension(null, $fileExtensionName, $fileExtension, $fileTypeID, $fileTypeName, $userID);
    
        $response = [
            'success' => true,
            'fileExtensionID' => $this->securityModel->encryptData($fileExtensionID),
            'title' => 'Save File Extension',
            'message' => 'The file extension has been saved successfully.',
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
    public function updateFileExtension() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $fileExtensionID = filter_input(INPUT_POST, 'file_extension_id', FILTER_VALIDATE_INT);
        $fileExtensionName = filter_input(INPUT_POST, 'file_extension_name', FILTER_SANITIZE_STRING);
        $fileExtension = filter_input(INPUT_POST, 'file_extension', FILTER_SANITIZE_STRING);
        $fileTypeID = filter_input(INPUT_POST, 'file_type_id', FILTER_VALIDATE_INT);
    
        $checkFileExtensionExist = $this->fileExtensionModel->checkFileExtensionExist($fileExtensionID);
        $total = $checkFileExtensionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save File Extension',
                'message' => 'The file extension does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $fileTypeDetails = $this->fileTypeModel->getFileType($fileTypeID);
        $fileTypeName = $fileTypeDetails['file_type_name'] ?? null;

        $this->fileExtensionModel->saveFileExtension($fileExtensionID, $fileExtensionName, $fileExtension, $fileTypeID, $fileTypeName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save File Extension',
            'message' => 'The file extension has been saved successfully.',
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
    public function deleteFileExtension() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $fileExtensionID = filter_input(INPUT_POST, 'file_extension_id', FILTER_VALIDATE_INT);
        
        $checkFileExtensionExist = $this->fileExtensionModel->checkFileExtensionExist($fileExtensionID);
        $total = $checkFileExtensionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete File Extension',
                'message' => 'The file extension does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->fileExtensionModel->deleteFileExtension($fileExtensionID);
                
        $response = [
            'success' => true,
            'title' => 'Delete File Extension',
            'message' => 'The file extension has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleFileExtension() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['file_extension_id']) && !empty($_POST['file_extension_id'])) {
            $fileExtensionIDs = $_POST['file_extension_id'];
    
            foreach($fileExtensionIDs as $fileExtensionID){
                $checkFileExtensionExist = $this->fileExtensionModel->checkFileExtensionExist($fileExtensionID);
                $total = $checkFileExtensionExist['total'] ?? 0;

                if($total > 0){                    
                    $this->fileExtensionModel->deleteFileExtension($fileExtensionID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple File Extensions',
                'message' => 'The selected file extensions have been deleted successfully.',
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
                $filename = "file_extension_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $fileExtensionDetails = $this->fileExtensionModel->exportFileExtension($columns, $ids);

                foreach ($fileExtensionDetails as $fileExtensionDetail) {
                    fputcsv($output, $fileExtensionDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "file_extension_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $fileExtensionDetails = $this->fileExtensionModel->exportFileExtension($columns, $ids);

                $rowNumber = 2;
                foreach ($fileExtensionDetails as $fileExtensionDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $fileExtensionDetail[$column]);
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
    public function getFileExtensionDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $fileExtensionID = filter_input(INPUT_POST, 'file_extension_id', FILTER_VALIDATE_INT);

        $checkFileExtensionExist = $this->fileExtensionModel->checkFileExtensionExist($fileExtensionID);
        $total = $checkFileExtensionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get File Extension Details',
                'message' => 'The file extension does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $fileExtensionDetails = $this->fileExtensionModel->getFileExtension($fileExtensionID);

        $response = [
            'success' => true,
            'fileExtensionName' => $fileExtensionDetails['file_extension_name'] ?? null,
            'fileExtension' => $fileExtensionDetails['file_extension'] ?? null,
            'fileTypeID' => $fileExtensionDetails['file_type_id'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>