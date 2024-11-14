<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../city/model/city-model.php';
require_once '../../state/model/state-model.php';
require_once '../../country/model/country-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new CityController(new CityModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new StateModel(new DatabaseModel), new CountryModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class CityController {
    private $cityModel;
    private $stateModel;
    private $countryModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(CityModel $cityModel, AuthenticationModel $authenticationModel, StateModel $stateModel, CountryModel $countryModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->cityModel = $cityModel;
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
                case 'add city':
                    $this->addCity();
                    break;
                case 'update city':
                    $this->updateCity();
                    break;
                case 'get city details':
                    $this->getCityDetails();
                    break;
                case 'delete city':
                    $this->deleteCity();
                    break;
                case 'delete multiple city':
                    $this->deleteMultipleCity();
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
    public function addCity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $cityName = filter_input(INPUT_POST, 'city_name', FILTER_SANITIZE_STRING);
        $stateID = filter_input(INPUT_POST, 'state_id', FILTER_VALIDATE_INT);

        $stateDetails = $this->stateModel->getState($stateID);
        $stateName = $stateDetails['state_name'] ?? null;
        $countryID = $stateDetails['country_id'] ?? null;
        $countryName = $stateDetails['country_name'] ?? null;
        
        $cityID = $this->cityModel->saveCity(null, $cityName, $stateID, $stateName, $countryID, $countryName, $userID);
    
        $response = [
            'success' => true,
            'cityID' => $this->securityModel->encryptData($cityID),
            'title' => 'Save City',
            'message' => 'The city has been saved successfully.',
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
    public function updateCity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        $cityName = filter_input(INPUT_POST, 'city_name', FILTER_SANITIZE_STRING);
        $stateID = filter_input(INPUT_POST, 'state_id', FILTER_VALIDATE_INT);
    
        $checkCityExist = $this->cityModel->checkCityExist($cityID);
        $total = $checkCityExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save City',
                'message' => 'The city does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $stateDetails = $this->stateModel->getState($stateID);
        $stateName = $stateDetails['state_name'] ?? null;
        $countryID = $stateDetails['country_id'] ?? null;
        $countryName = $stateDetails['country_name'] ?? null;
        
        $cityID = $this->cityModel->saveCity($cityID, $cityName, $stateID, $stateName, $countryID, $countryName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save City',
            'message' => 'The city has been saved successfully.',
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
    public function deleteCity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        
        $checkCityExist = $this->cityModel->checkCityExist($cityID);
        $total = $checkCityExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete City',
                'message' => 'The city does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->cityModel->deleteCity($cityID);
                
        $response = [
            'success' => true,
            'title' => 'Delete City',
            'message' => 'The city has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleCity() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['city_id']) && !empty($_POST['city_id'])) {
            $cityIDs = $_POST['city_id'];
    
            foreach($cityIDs as $cityID){
                $checkCityExist = $this->cityModel->checkCityExist($cityID);
                $total = $checkCityExist['total'] ?? 0;

                if($total > 0){
                    $this->cityModel->deleteCity($cityID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Cities',
                'message' => 'The selected cities have been deleted successfully.',
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
                $filename = "city_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $cityDetails = $this->cityModel->exportCity($columns, $ids);

                foreach ($cityDetails as $cityDetail) {
                    fputcsv($output, $cityDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "city_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $cityDetails = $this->cityModel->exportCity($columns, $ids);

                $rowNumber = 2;
                foreach ($cityDetails as $cityDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $cityDetail[$column]);
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
    public function getCityDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);

        $checkCityExist = $this->cityModel->checkCityExist($cityID);
        $total = $checkCityExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get City Details',
                'message' => 'The city does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $cityDetails = $this->cityModel->getCity($cityID);

        $response = [
            'success' => true,
            'cityName' => $cityDetails['city_name'] ?? null,
            'stateID' => $cityDetails['state_id'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>