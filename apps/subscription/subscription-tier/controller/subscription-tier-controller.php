<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../../settings/authentication/model/authentication-model.php';
require_once '../../subscription-tier/model/subscription-tier-model.php';
require_once '../../../settings/security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new SubscriptionTierController(new SubscriptionTierModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class SubscriptionTierController {
    private $subscriptionTierModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(SubscriptionTierModel $subscriptionTierModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->subscriptionTierModel = $subscriptionTierModel;
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
                    $this->addSubscriptionTier();
                    break;
                case 'update subscription tier':
                    $this->updateSubscriptionTier();
                    break;
                case 'update app logo':
                    $this->updateAppLogo();
                    break;
                case 'get subscription tier details':
                    $this->getSubscriptionTierDetails();
                    break;
                case 'delete subscription tier':
                    $this->deleteSubscriptionTier();
                    break;
                case 'delete multiple subscription tier':
                    $this->deleteMultipleSubscriptionTier();
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
    public function addSubscriptionTier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $subscriptionTierName = filter_input(INPUT_POST, 'subscription_tier_name', FILTER_SANITIZE_STRING);
        $subscriptionTierDescription = filter_input(INPUT_POST, 'subscription_tier_description', FILTER_SANITIZE_STRING);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
        
        $subscriptionTierID = $this->subscriptionTierModel->saveSubscriptionTier(null, $subscriptionTierName, $subscriptionTierDescription, $orderSequence, $userID);
    
        $response = [
            'success' => true,
            'subscriptionTierID' => $this->securityModel->encryptData($subscriptionTierID),
            'title' => 'Save Subscription Tier',
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
    public function updateSubscriptionTier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $subscriptionTierID = filter_input(INPUT_POST, 'subscription_tier_id', FILTER_VALIDATE_INT);
        $subscriptionTierName = filter_input(INPUT_POST, 'subscription_tier_name', FILTER_SANITIZE_STRING);
        $subscriptionTierDescription = filter_input(INPUT_POST, 'subscription_tier_description', FILTER_SANITIZE_STRING);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
    
        $checkSubscriptionTierExist = $this->subscriptionTierModel->checkSubscriptionTierExist($subscriptionTierID);
        $total = $checkSubscriptionTierExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Subscription Tier',
                'message' => 'The subscription tier does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->subscriptionTierModel->saveSubscriptionTier($subscriptionTierID, $subscriptionTierName, $subscriptionTierDescription, $orderSequence, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Subscription Tier',
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
    public function deleteSubscriptionTier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $subscriptionTierID = filter_input(INPUT_POST, 'subscription_tier_id', FILTER_VALIDATE_INT);
        
        $checkSubscriptionTierExist = $this->subscriptionTierModel->checkSubscriptionTierExist($subscriptionTierID);
        $total = $checkSubscriptionTierExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Subscription Tier',
                'message' => 'The subscription tier does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->subscriptionTierModel->deleteSubscriptionTier($subscriptionTierID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Subscription Tier',
            'message' => 'The subscription tier has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleSubscriptionTier() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['subscription_tier_id']) && !empty($_POST['subscription_tier_id'])) {
            $subscriptionTierIDs = $_POST['subscription_tier_id'];
    
            foreach($subscriptionTierIDs as $subscriptionTierID){
                $checkSubscriptionTierExist = $this->subscriptionTierModel->checkSubscriptionTierExist($subscriptionTierID);
                $total = $checkSubscriptionTierExist['total'] ?? 0;

                if($total > 0){
                    $this->subscriptionTierModel->deleteSubscriptionTier($subscriptionTierID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Subscription Tier',
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
                $filename = "subscription_tier_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $subscriptionTierDetails = $this->subscriptionTierModel->exportSubscriptionTier($columns, $ids);

                foreach ($subscriptionTierDetails as $subscriptionTierDetail) {
                    fputcsv($output, $subscriptionTierDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "subscription_tier_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $subscriptionTierDetails = $this->subscriptionTierModel->exportSubscriptionTier($columns, $ids);

                $rowNumber = 2;
                foreach ($subscriptionTierDetails as $subscriptionTierDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $subscriptionTierDetail[$column]);
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
    public function getSubscriptionTierDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $subscriptionTierID = filter_input(INPUT_POST, 'subscription_tier_id', FILTER_VALIDATE_INT);

        $checkSubscriptionTierExist = $this->subscriptionTierModel->checkSubscriptionTierExist($subscriptionTierID);
        $total = $checkSubscriptionTierExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Subscription Tier Details',
                'message' => 'The subscription tier does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $subscriptionTierDetails = $this->subscriptionTierModel->getSubscriptionTier($subscriptionTierID);
        $response = [
            'success' => true,
            'subscriptionTierName' => $subscriptionTierDetails['subscription_tier_name'] ?? null,
            'subscriptionTierDescription' => $subscriptionTierDetails['subscription_tier_description'] ?? null,
            'orderSequence' => $subscriptionTierDetails['order_sequence'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>