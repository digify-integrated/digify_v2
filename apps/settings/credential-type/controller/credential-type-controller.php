<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../credential-type/model/credential-type-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new CredentialTypeController(new CredentialTypeModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class CredentialTypeController {
    private $credentialTypeModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(CredentialTypeModel $credentialTypeModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->credentialTypeModel = $credentialTypeModel;
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
                case 'add credential type':
                    $this->addCredentialType();
                    break;
                case 'update credential type':
                    $this->updateCredentialType();
                    break;
                case 'get credential type details':
                    $this->getCredentialTypeDetails();
                    break;
                case 'delete credential type':
                    $this->deleteCredentialType();
                    break;
                case 'delete multiple credential type':
                    $this->deleteMultipleCredentialType();
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
    public function addCredentialType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $credentialTypeName = filter_input(INPUT_POST, 'credential_type_name', FILTER_SANITIZE_STRING);
        
        $credentialTypeID = $this->credentialTypeModel->saveCredentialType(null, $credentialTypeName, $userID);
    
        $response = [
            'success' => true,
            'credentialTypeID' => $this->securityModel->encryptData($credentialTypeID),
            'title' => 'Save Credential Type',
            'message' => 'The credential type has been saved successfully.',
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
    public function updateCredentialType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $credentialTypeID = filter_input(INPUT_POST, 'credential_type_id', FILTER_VALIDATE_INT);
        $credentialTypeName = filter_input(INPUT_POST, 'credential_type_name', FILTER_SANITIZE_STRING);
    
        $checkCredentialTypeExist = $this->credentialTypeModel->checkCredentialTypeExist($credentialTypeID);
        $total = $checkCredentialTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Credential Type',
                'message' => 'The credential type does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->credentialTypeModel->saveCredentialType($credentialTypeID, $credentialTypeName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Credential Type',
            'message' => 'The credential type has been saved successfully.',
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
    public function deleteCredentialType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $credentialTypeID = filter_input(INPUT_POST, 'credential_type_id', FILTER_VALIDATE_INT);
        
        $checkCredentialTypeExist = $this->credentialTypeModel->checkCredentialTypeExist($credentialTypeID);
        $total = $checkCredentialTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Credential Type',
                'message' => 'The credential type does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $credentialTypeDetails = $this->credentialTypeModel->getCredentialType($credentialTypeID);
        $appLogoPath = !empty($credentialTypeDetails['app_logo']) ? str_replace('../', '../../../../apps/', $credentialTypeDetails['app_logo']) : null;

        if(file_exists($appLogoPath)){
            if (!unlink($appLogoPath)) {
                $response = [
                    'success' => false,
                    'title' => 'Delete Credential Type',
                    'message' => 'The app logo cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        $this->credentialTypeModel->deleteCredentialType($credentialTypeID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Credential Type',
            'message' => 'The credential type has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleCredentialType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['credential_type_id']) && !empty($_POST['credential_type_id'])) {
            $credentialTypeIDs = $_POST['credential_type_id'];
    
            foreach($credentialTypeIDs as $credentialTypeID){
                $checkCredentialTypeExist = $this->credentialTypeModel->checkCredentialTypeExist($credentialTypeID);
                $total = $checkCredentialTypeExist['total'] ?? 0;

                if($total > 0){
                    $credentialTypeDetails = $this->credentialTypeModel->getCredentialType($credentialTypeID);
                    $appLogoPath = !empty($credentialTypeDetails['app_logo']) ? str_replace('../', '../../../../apps/', $credentialTypeDetails['app_logo']) : null;

                    if(file_exists($appLogoPath)){
                        if (!unlink($appLogoPath)) {
                            $response = [
                                'success' => false,
                                'title' => 'Delete Multiple Credential Types',
                                'message' => 'The app logo cannot be deleted due to an error.',
                                'messageType' => 'error'
                            ];
                            
                            echo json_encode($response);
                            exit;
                        }
                    }
                    
                    $this->credentialTypeModel->deleteCredentialType($credentialTypeID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Credential Types',
                'message' => 'The selected credential types have been deleted successfully.',
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
                $filename = "credential_type_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $credentialTypeDetails = $this->credentialTypeModel->exportCredentialType($columns, $ids);

                foreach ($credentialTypeDetails as $credentialTypeDetail) {
                    fputcsv($output, $credentialTypeDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "credential_type_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $credentialTypeDetails = $this->credentialTypeModel->exportCredentialType($columns, $ids);

                $rowNumber = 2;
                foreach ($credentialTypeDetails as $credentialTypeDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $credentialTypeDetail[$column]);
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
    public function getCredentialTypeDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $credentialTypeID = filter_input(INPUT_POST, 'credential_type_id', FILTER_VALIDATE_INT);

        $checkCredentialTypeExist = $this->credentialTypeModel->checkCredentialTypeExist($credentialTypeID);
        $total = $checkCredentialTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Credential Type Details',
                'message' => 'The credential type does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $credentialTypeDetails = $this->credentialTypeModel->getCredentialType($credentialTypeID);

        $response = [
            'success' => true,
            'credentialTypeName' => $credentialTypeDetails['credential_type_name'] ?? null,
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>