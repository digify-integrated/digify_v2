<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../../settings/authentication/model/authentication-model.php';
require_once '../../work-location/model/work-location-model.php';
require_once '../../../settings/country/model/country-model.php';
require_once '../../../settings/state/model/state-model.php';
require_once '../../../settings/city/model/city-model.php';
require_once '../../../settings/security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new WorkLocationController(new WorkLocationModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new CountryModel(new DatabaseModel), new StateModel(new DatabaseModel), new CityModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class WorkLocationController {
    private $workLocationModel;
    private $countryModel;
    private $stateModel;
    private $cityModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(WorkLocationModel $workLocationModel, AuthenticationModel $authenticationModel, CountryModel $countryModel, StateModel $stateModel, CityModel $cityModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->workLocationModel = $workLocationModel;
        $this->countryModel = $countryModel;
        $this->stateModel = $stateModel;
        $this->cityModel = $cityModel;
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
                case 'add work location':
                    $this->addWorkLocation();
                    break;
                case 'update work location':
                    $this->updateWorkLocation();
                    break;
                case 'get work location details':
                    $this->getWorkLocationDetails();
                    break;
                case 'delete work location':
                    $this->deleteWorkLocation();
                    break;
                case 'delete multiple work location':
                    $this->deleteMultipleWorkLocation();
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
    public function addWorkLocation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $workLocationName = filter_input(INPUT_POST, 'work_location_name', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);

        $cityDetails = $this->cityModel->getCity($cityID);
        $cityName = $cityDetails['city_name'] ?? null;
        $stateID = $cityDetails['state_id'] ?? null;
        $stateName = $cityDetails['state_name'] ?? null;
        $countryID = $cityDetails['country_id'] ?? null;
        $countryName = $cityDetails['country_name'] ?? null;
        
        $workLocationID = $this->workLocationModel->saveWorkLocation(null, $workLocationName, $address, $cityID, $cityName, $stateID, $stateName, $countryID, $countryName, $phone, $telephone, $email, $userID);
    
        $response = [
            'success' => true,
            'workLocationID' => $this->securityModel->encryptData($workLocationID),
            'title' => 'Save Work Location',
            'message' => 'The work location has been saved successfully.',
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
    public function updateWorkLocation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $workLocationID = filter_input(INPUT_POST, 'work_location_id', FILTER_VALIDATE_INT);
        $workLocationName = filter_input(INPUT_POST, 'work_location_name', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    
        $checkWorkLocationExist = $this->workLocationModel->checkWorkLocationExist($workLocationID);
        $total = $checkWorkLocationExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Work Location',
                'message' => 'The work location does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $cityDetails = $this->cityModel->getCity($cityID);
        $cityName = $cityDetails['city_name'] ?? null;
        $stateID = $cityDetails['state_id'] ?? null;
        $stateName = $cityDetails['state_name'] ?? null;
        $countryID = $cityDetails['country_id'] ?? null;
        $countryName = $cityDetails['country_name'] ?? null;
        
        $workLocationID = $this->workLocationModel->saveWorkLocation($workLocationID, $workLocationName, $address, $cityID, $cityName, $stateID, $stateName, $countryID, $countryName, $phone, $telephone, $email, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Work Location',
            'message' => 'The work location has been saved successfully.',
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
    public function deleteWorkLocation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $workLocationID = filter_input(INPUT_POST, 'work_location_id', FILTER_VALIDATE_INT);
        
        $checkWorkLocationExist = $this->workLocationModel->checkWorkLocationExist($workLocationID);
        $total = $checkWorkLocationExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Work Location',
                'message' => 'The work location does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->workLocationModel->deleteWorkLocation($workLocationID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Work Location',
            'message' => 'The work location has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleWorkLocation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['work_location_id']) && !empty($_POST['work_location_id'])) {
            $workLocationIDs = $_POST['work_location_id'];
    
            foreach($workLocationIDs as $workLocationID){
                $checkWorkLocationExist = $this->workLocationModel->checkWorkLocationExist($workLocationID);
                $total = $checkWorkLocationExist['total'] ?? 0;

                if($total > 0){
                    $this->workLocationModel->deleteWorkLocation($workLocationID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Work Locations',
                'message' => 'The selected work locations have been deleted successfully.',
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
                $filename = "work_location_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $workLocationDetails = $this->workLocationModel->exportWorkLocation($columns, $ids);

                foreach ($workLocationDetails as $workLocationDetail) {
                    fputcsv($output, $workLocationDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "work_location_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $workLocationDetails = $this->workLocationModel->exportWorkLocation($columns, $ids);

                $rowNumber = 2;
                foreach ($workLocationDetails as $workLocationDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $workLocationDetail[$column]);
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
    public function getWorkLocationDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $workLocationID = filter_input(INPUT_POST, 'work_location_id', FILTER_VALIDATE_INT);

        $checkWorkLocationExist = $this->workLocationModel->checkWorkLocationExist($workLocationID);
        $total = $checkWorkLocationExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Work Location Details',
                'message' => 'The work location does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $workLocationDetails = $this->workLocationModel->getWorkLocation($workLocationID);

        $response = [
            'success' => true,
            'workLocationName' => $workLocationDetails['work_location_name'] ?? null,
            'address' => $workLocationDetails['address'] ?? null,
            'cityID' => $workLocationDetails['city_id'] ?? null,
            'phone' => $workLocationDetails['phone'] ?? null,
            'telephone' => $workLocationDetails['telephone'] ?? null,
            'email' => $workLocationDetails['email'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>