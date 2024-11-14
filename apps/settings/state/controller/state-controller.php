<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../state/model/state-model.php';
require_once '../../country/model/country-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new StateController(new StateModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new CountryModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class StateController {
    private $stateModel;
    private $countryModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(StateModel $stateModel, AuthenticationModel $authenticationModel, CountryModel $countryModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->stateModel = $stateModel;
        $this->countryModel = $countryModel;
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
                case 'add state':
                    $this->addState();
                    break;
                case 'update state':
                    $this->updateState();
                    break;
                case 'get state details':
                    $this->getStateDetails();
                    break;
                case 'delete state':
                    $this->deleteState();
                    break;
                case 'delete multiple state':
                    $this->deleteMultipleState();
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
    public function addState() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $stateName = filter_input(INPUT_POST, 'state_name', FILTER_SANITIZE_STRING);
        $countryID = filter_input(INPUT_POST, 'country_id', FILTER_VALIDATE_INT);

        $countryDetails = $this->countryModel->getCountry($countryID);
        $countryName = $countryDetails['country_name'] ?? null;
        
        $stateID = $this->stateModel->saveState(null, $stateName, $countryID, $countryName, $userID);
    
        $response = [
            'success' => true,
            'stateID' => $this->securityModel->encryptData($stateID),
            'title' => 'Save State',
            'message' => 'The state has been saved successfully.',
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
    public function updateState() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $stateID = filter_input(INPUT_POST, 'state_id', FILTER_VALIDATE_INT);
        $stateName = filter_input(INPUT_POST, 'state_name', FILTER_SANITIZE_STRING);
        $countryID = filter_input(INPUT_POST, 'country_id', FILTER_VALIDATE_INT);
    
        $checkStateExist = $this->stateModel->checkStateExist($stateID);
        $total = $checkStateExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save State',
                'message' => 'The state does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $countryDetails = $this->countryModel->getCountry($countryID);
        $countryName = $countryDetails['country_name'] ?? null;
        
        $stateID = $this->stateModel->saveState($stateID, $stateName, $countryID, $countryName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save State',
            'message' => 'The state has been saved successfully.',
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
    public function deleteState() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $stateID = filter_input(INPUT_POST, 'state_id', FILTER_VALIDATE_INT);
        
        $checkStateExist = $this->stateModel->checkStateExist($stateID);
        $total = $checkStateExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete State',
                'message' => 'The state does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->stateModel->deleteState($stateID);
                
        $response = [
            'success' => true,
            'title' => 'Delete State',
            'message' => 'The state has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleState() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['state_id']) && !empty($_POST['state_id'])) {
            $stateIDs = $_POST['state_id'];
    
            foreach($stateIDs as $stateID){
                $checkStateExist = $this->stateModel->checkStateExist($stateID);
                $total = $checkStateExist['total'] ?? 0;

                if($total > 0){
                    $this->stateModel->deleteState($stateID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple States',
                'message' => 'The selected states have been deleted successfully.',
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
                $filename = "state_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $stateDetails = $this->stateModel->exportState($columns, $ids);

                foreach ($stateDetails as $stateDetail) {
                    fputcsv($output, $stateDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "state_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $stateDetails = $this->stateModel->exportState($columns, $ids);

                $rowNumber = 2;
                foreach ($stateDetails as $stateDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $stateDetail[$column]);
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
    public function getStateDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $stateID = filter_input(INPUT_POST, 'state_id', FILTER_VALIDATE_INT);

        $checkStateExist = $this->stateModel->checkStateExist($stateID);
        $total = $checkStateExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get State Details',
                'message' => 'The state does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $stateDetails = $this->stateModel->getState($stateID);

        $response = [
            'success' => true,
            'stateName' => $stateDetails['state_name'] ?? null,
            'countryID' => $stateDetails['country_id'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>