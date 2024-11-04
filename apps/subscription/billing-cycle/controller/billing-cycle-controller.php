<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../../settings/authentication/model/authentication-model.php';
require_once '../../billing-cycle/model/billing-cycle-model.php';
require_once '../../../settings/security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new BillingCycleController(new BillingCycleModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class BillingCycleController {
    private $billingCycleModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(BillingCycleModel $billingCycleModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->billingCycleModel = $billingCycleModel;
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
                case 'add subscription tier':
                    $this->addBillingCycle();
                    break;
                case 'update subscription tier':
                    $this->updateBillingCycle();
                    break;
                case 'update app logo':
                    $this->updateAppLogo();
                    break;
                case 'get subscription tier details':
                    $this->getBillingCycleDetails();
                    break;
                case 'delete subscription tier':
                    $this->deleteBillingCycle();
                    break;
                case 'delete multiple subscription tier':
                    $this->deleteMultipleBillingCycle();
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
    public function addBillingCycle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $billingCycleName = filter_input(INPUT_POST, 'billing_cycle_name', FILTER_SANITIZE_STRING);
        $billingCycleDescription = filter_input(INPUT_POST, 'billing_cycle_description', FILTER_SANITIZE_STRING);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
        
        $billingCycleID = $this->billingCycleModel->saveBillingCycle(null, $billingCycleName, $billingCycleDescription, $orderSequence, $userID);
    
        $response = [
            'success' => true,
            'billingCycleID' => $this->securityModel->encryptData($billingCycleID),
            'title' => 'Save Billing Cycle',
            'message' => 'The subscription tier has been saved successfully.',
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
    public function updateBillingCycle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $billingCycleID = filter_input(INPUT_POST, 'billing_cycle_id', FILTER_VALIDATE_INT);
        $billingCycleName = filter_input(INPUT_POST, 'billing_cycle_name', FILTER_SANITIZE_STRING);
        $billingCycleDescription = filter_input(INPUT_POST, 'billing_cycle_description', FILTER_SANITIZE_STRING);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
    
        $checkBillingCycleExist = $this->billingCycleModel->checkBillingCycleExist($billingCycleID);
        $total = $checkBillingCycleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Billing Cycle',
                'message' => 'The subscription tier does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->billingCycleModel->saveBillingCycle($billingCycleID, $billingCycleName, $billingCycleDescription, $orderSequence, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Billing Cycle',
            'message' => 'The subscription tier has been saved successfully.',
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
    public function deleteBillingCycle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $billingCycleID = filter_input(INPUT_POST, 'billing_cycle_id', FILTER_VALIDATE_INT);
        
        $checkBillingCycleExist = $this->billingCycleModel->checkBillingCycleExist($billingCycleID);
        $total = $checkBillingCycleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Billing Cycle',
                'message' => 'The subscription tier does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->billingCycleModel->deleteBillingCycle($billingCycleID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Billing Cycle',
            'message' => 'The subscription tier has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleBillingCycle() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['billing_cycle_id']) && !empty($_POST['billing_cycle_id'])) {
            $billingCycleIDs = $_POST['billing_cycle_id'];
    
            foreach($billingCycleIDs as $billingCycleID){
                $checkBillingCycleExist = $this->billingCycleModel->checkBillingCycleExist($billingCycleID);
                $total = $checkBillingCycleExist['total'] ?? 0;

                if($total > 0){
                    $this->billingCycleModel->deleteBillingCycle($billingCycleID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Billing Cycle',
                'message' => 'The selected subscription tiers have been deleted successfully.',
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
                $filename = "billing_cycle_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $billingCycleDetails = $this->billingCycleModel->exportBillingCycle($columns, $ids);

                foreach ($billingCycleDetails as $billingCycleDetail) {
                    fputcsv($output, $billingCycleDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "billing_cycle_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $billingCycleDetails = $this->billingCycleModel->exportBillingCycle($columns, $ids);

                $rowNumber = 2;
                foreach ($billingCycleDetails as $billingCycleDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $billingCycleDetail[$column]);
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
    public function getBillingCycleDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $billingCycleID = filter_input(INPUT_POST, 'billing_cycle_id', FILTER_VALIDATE_INT);

        $checkBillingCycleExist = $this->billingCycleModel->checkBillingCycleExist($billingCycleID);
        $total = $checkBillingCycleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Billing Cycle Details',
                'message' => 'The subscription tier does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $billingCycleDetails = $this->billingCycleModel->getBillingCycle($billingCycleID);
        $response = [
            'success' => true,
            'billingCycleName' => $billingCycleDetails['billing_cycle_name'] ?? null,
            'billingCycleDescription' => $billingCycleDetails['billing_cycle_description'] ?? null,
            'orderSequence' => $billingCycleDetails['order_sequence'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>