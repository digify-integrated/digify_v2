<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../bank-account-type/model/bank-account-type-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new BankAccountTypeController(new BankAccountTypeModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class BankAccountTypeController {
    private $bankAccountTypeModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(BankAccountTypeModel $bankAccountTypeModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->bankAccountTypeModel = $bankAccountTypeModel;
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
                case 'add bank account type':
                    $this->addBankAccountType();
                    break;
                case 'update bank account type':
                    $this->updateBankAccountType();
                    break;
                case 'get bank account type details':
                    $this->getBankAccountTypeDetails();
                    break;
                case 'delete bank account type':
                    $this->deleteBankAccountType();
                    break;
                case 'delete multiple bank account type':
                    $this->deleteMultipleBankAccountType();
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
    public function addBankAccountType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $bankAccountTypeName = filter_input(INPUT_POST, 'bank_account_type_name', FILTER_SANITIZE_STRING);
        
        $bankAccountTypeID = $this->bankAccountTypeModel->saveBankAccountType(null, $bankAccountTypeName, $userID);
    
        $response = [
            'success' => true,
            'bankAccountTypeID' => $this->securityModel->encryptData($bankAccountTypeID),
            'title' => 'Save Bank Account Type',
            'message' => 'The bank account type has been saved successfully.',
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
    public function updateBankAccountType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $bankAccountTypeID = filter_input(INPUT_POST, 'bank_account_type_id', FILTER_VALIDATE_INT);
        $bankAccountTypeName = filter_input(INPUT_POST, 'bank_account_type_name', FILTER_SANITIZE_STRING);
    
        $checkBankAccountTypeExist = $this->bankAccountTypeModel->checkBankAccountTypeExist($bankAccountTypeID);
        $total = $checkBankAccountTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Bank Account Type',
                'message' => 'The bank account type does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->bankAccountTypeModel->saveBankAccountType($bankAccountTypeID, $bankAccountTypeName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Bank Account Type',
            'message' => 'The bank account type has been saved successfully.',
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
    public function deleteBankAccountType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $bankAccountTypeID = filter_input(INPUT_POST, 'bank_account_type_id', FILTER_VALIDATE_INT);
        
        $checkBankAccountTypeExist = $this->bankAccountTypeModel->checkBankAccountTypeExist($bankAccountTypeID);
        $total = $checkBankAccountTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Bank Account Type',
                'message' => 'The bank account type does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->bankAccountTypeModel->deleteBankAccountType($bankAccountTypeID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Bank Account Type',
            'message' => 'The bank account type has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleBankAccountType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['bank_account_type_id']) && !empty($_POST['bank_account_type_id'])) {
            $bankAccountTypeIDs = $_POST['bank_account_type_id'];
    
            foreach($bankAccountTypeIDs as $bankAccountTypeID){
                $checkBankAccountTypeExist = $this->bankAccountTypeModel->checkBankAccountTypeExist($bankAccountTypeID);
                $total = $checkBankAccountTypeExist['total'] ?? 0;

                if($total > 0){
                    $this->bankAccountTypeModel->deleteBankAccountType($bankAccountTypeID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Bank Account Types',
                'message' => 'The selected bank account types have been deleted successfully.',
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
                $filename = "bank_account_type_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $bankAccountTypeDetails = $this->bankAccountTypeModel->exportBankAccountType($columns, $ids);

                foreach ($bankAccountTypeDetails as $bankAccountTypeDetail) {
                    fputcsv($output, $bankAccountTypeDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "bank_account_type_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $bankAccountTypeDetails = $this->bankAccountTypeModel->exportBankAccountType($columns, $ids);

                $rowNumber = 2;
                foreach ($bankAccountTypeDetails as $bankAccountTypeDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $bankAccountTypeDetail[$column]);
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
    public function getBankAccountTypeDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $bankAccountTypeID = filter_input(INPUT_POST, 'bank_account_type_id', FILTER_VALIDATE_INT);

        $checkBankAccountTypeExist = $this->bankAccountTypeModel->checkBankAccountTypeExist($bankAccountTypeID);
        $total = $checkBankAccountTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Bank Account Type Details',
                'message' => 'The bank account type does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $bankAccountTypeDetails = $this->bankAccountTypeModel->getBankAccountType($bankAccountTypeID);

        $response = [
            'success' => true,
            'bankAccountTypeName' => $bankAccountTypeDetails['bank_account_type_name'] ?? null,
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>