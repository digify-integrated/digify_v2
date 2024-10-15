<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../system-action/model/system-action-model.php';

require_once '../../../../assets/libs/PhpSpreadsheet/autoload.php';

$controller = new SystemActionController(new SystemActionModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class SystemActionController {
    private $systemActionModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(SystemActionModel $systemActionModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->systemActionModel = $systemActionModel;
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
            $active = $loginCredentialsDetails['active'];
            $locked = $loginCredentialsDetails['locked'];
            $multipleSession = $loginCredentialsDetails['multiple_session'];
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
                case 'add system action':
                    $this->addSystemAction();
                    break;
                case 'update system action':
                    $this->updateSystemAction();
                    break;
                case 'get system action details':
                    $this->getSystemActionDetails();
                    break;
                case 'delete system action':
                    $this->deleteSystemAction();
                    break;
                case 'delete multiple system action':
                    $this->deleteMultipleSystemAction();
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
    public function addSystemAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $systemActionName = filter_input(INPUT_POST, 'system_action_name', FILTER_SANITIZE_STRING);
        $systemActionDescription = filter_input(INPUT_POST, 'system_action_description', FILTER_SANITIZE_STRING);
        
        $systemActionID = $this->systemActionModel->saveSystemAction(null, $systemActionName, $systemActionDescription, $userID);
    
        $response = [
            'success' => true,
            'systemActionID' => $this->securityModel->encryptData($systemActionID),
            'title' => 'Save System Action',
            'message' => 'The system action has been saved successfully.',
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
    public function updateSystemAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $systemActionID = filter_input(INPUT_POST, 'system_action_id', FILTER_VALIDATE_INT);
        $systemActionName = filter_input(INPUT_POST, 'system_action_name', FILTER_SANITIZE_STRING);
        $systemActionDescription = filter_input(INPUT_POST, 'system_action_description', FILTER_SANITIZE_STRING);
    
        $checkSystemActionExist = $this->systemActionModel->checkSystemActionExist($systemActionID);
        $total = $checkSystemActionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save System Action',
                'message' => 'The system action does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $systemActionID = $this->systemActionModel->saveSystemAction($systemActionID, $systemActionName, $systemActionDescription, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save System Action',
            'message' => 'The system action has been saved successfully.',
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
    public function deleteSystemAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $systemActionID = filter_input(INPUT_POST, 'system_action_id', FILTER_VALIDATE_INT);
        
        $checkSystemActionExist = $this->systemActionModel->checkSystemActionExist($systemActionID);
        $total = $checkSystemActionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete System Action',
                'message' => 'The system action does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->systemActionModel->deleteSystemAction($systemActionID);
                
        $response = [
            'success' => true,
            'title' => 'Delete System Action',
            'message' => 'The system action has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleSystemAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['system_action_id']) && !empty($_POST['system_action_id'])) {
            $systemActionIDs = $_POST['system_action_id'];
    
            foreach($systemActionIDs as $systemActionID){
                $checkSystemActionExist = $this->systemActionModel->checkSystemActionExist($systemActionID);
                $total = $checkSystemActionExist['total'] ?? 0;

                if($total > 0){
                    $this->systemActionModel->deleteSystemAction($systemActionID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple System Action',
                'message' => 'The selected system actions have been deleted successfully.',
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
                $filename = "system_action_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $systemActionDetails = $this->systemActionModel->exportSystemAction($columns, $ids);

                foreach ($systemActionDetails as $systemActionDetail) {
                    fputcsv($output, $systemActionDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "system_action_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $systemActionDetails = $this->systemActionModel->exportSystemAction($columns, $ids);

                $rowNumber = 2;
                foreach ($systemActionDetails as $systemActionDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $systemActionDetail[$column]);
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
    public function getSystemActionDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $systemActionID = filter_input(INPUT_POST, 'system_action_id', FILTER_VALIDATE_INT);

        $checkSystemActionExist = $this->systemActionModel->checkSystemActionExist($systemActionID);
        $total = $checkSystemActionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get System Action Details',
                'message' => 'The system action does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $systemActionDetails = $this->systemActionModel->getSystemAction($systemActionID);

        $response = [
            'success' => true,
            'systemActionName' => $systemActionDetails['system_action_name'] ?? null,
            'systemActionDescription' => $systemActionDetails['system_action_description'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>