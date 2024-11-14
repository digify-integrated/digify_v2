<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../currency/model/currency-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new CurrencyController(new CurrencyModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class CurrencyController {
    private $currencyModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(CurrencyModel $currencyModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->currencyModel = $currencyModel;
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
                case 'add currency':
                    $this->addCurrency();
                    break;
                case 'update currency':
                    $this->updateCurrency();
                    break;
                case 'get currency details':
                    $this->getCurrencyDetails();
                    break;
                case 'delete currency':
                    $this->deleteCurrency();
                    break;
                case 'delete multiple currency':
                    $this->deleteMultipleCurrency();
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
    public function addCurrency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $currencyName = filter_input(INPUT_POST, 'currency_name', FILTER_SANITIZE_STRING);
        $symbol = filter_input(INPUT_POST, 'symbol', FILTER_SANITIZE_STRING);
        $shorthand = filter_input(INPUT_POST, 'shorthand', FILTER_SANITIZE_STRING);
        
        $currencyID = $this->currencyModel->saveCurrency(null, $currencyName, $symbol, $shorthand, $userID);
    
        $response = [
            'success' => true,
            'currencyID' => $this->securityModel->encryptData($currencyID),
            'title' => 'Save Currency',
            'message' => 'The currency has been saved successfully.',
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
    public function updateCurrency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $currencyID = filter_input(INPUT_POST, 'currency_id', FILTER_VALIDATE_INT);
        $currencyName = filter_input(INPUT_POST, 'currency_name', FILTER_SANITIZE_STRING);
        $symbol = filter_input(INPUT_POST, 'symbol', FILTER_SANITIZE_STRING);
        $shorthand = filter_input(INPUT_POST, 'shorthand', FILTER_SANITIZE_STRING);
            
        $checkCurrencyExist = $this->currencyModel->checkCurrencyExist($currencyID);
        $total = $checkCurrencyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Currency',
                'message' => 'The currency does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $currencyID = $this->currencyModel->saveCurrency($currencyID, $currencyName, $symbol, $shorthand, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Currency',
            'message' => 'The currency has been saved successfully.',
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
    public function deleteCurrency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $currencyID = filter_input(INPUT_POST, 'currency_id', FILTER_VALIDATE_INT);
        
        $checkCurrencyExist = $this->currencyModel->checkCurrencyExist($currencyID);
        $total = $checkCurrencyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Currency',
                'message' => 'The currency does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->currencyModel->deleteCurrency($currencyID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Currency',
            'message' => 'The currency has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleCurrency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['currency_id']) && !empty($_POST['currency_id'])) {
            $currencyIDs = $_POST['currency_id'];
    
            foreach($currencyIDs as $currencyID){
                $checkCurrencyExist = $this->currencyModel->checkCurrencyExist($currencyID);
                $total = $checkCurrencyExist['total'] ?? 0;

                if($total > 0){
                    $this->currencyModel->deleteCurrency($currencyID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Currencies',
                'message' => 'The selected currencies have been deleted successfully.',
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
                $filename = "currency_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $currencyDetails = $this->currencyModel->exportCurrency($columns, $ids);

                foreach ($currencyDetails as $currencyDetail) {
                    fputcsv($output, $currencyDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "currency_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $currencyDetails = $this->currencyModel->exportCurrency($columns, $ids);

                $rowNumber = 2;
                foreach ($currencyDetails as $currencyDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $currencyDetail[$column]);
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
    public function getCurrencyDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $currencyID = filter_input(INPUT_POST, 'currency_id', FILTER_VALIDATE_INT);

        $checkCurrencyExist = $this->currencyModel->checkCurrencyExist($currencyID);
        $total = $checkCurrencyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Currency Details',
                'message' => 'The currency does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $currencyDetails = $this->currencyModel->getCurrency($currencyID);

        $response = [
            'success' => true,
            'currencyName' => $currencyDetails['currency_name'] ?? null,
            'symbol' => $currencyDetails['symbol'] ?? null,
            'shorthand' => $currencyDetails['shorthand'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>