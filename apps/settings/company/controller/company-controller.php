<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../company/model/company-model.php';
require_once '../../country/model/country-model.php';
require_once '../../state/model/state-model.php';
require_once '../../city/model/city-model.php';
require_once '../../currency/model/currency-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../upload-setting/model/upload-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new CompanyController(new CompanyModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new CountryModel(new DatabaseModel), new StateModel(new DatabaseModel), new CityModel(new DatabaseModel), new CurrencyModel(new DatabaseModel), new UploadSettingModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class CompanyController {
    private $companyModel;
    private $countryModel;
    private $stateModel;
    private $cityModel;
    private $currencyModel;
    private $authenticationModel;
    private $uploadSettingModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(CompanyModel $companyModel, AuthenticationModel $authenticationModel, CountryModel $countryModel, StateModel $stateModel, CityModel $cityModel, CurrencyModel $currencyModel, UploadSettingModel $uploadSettingModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->companyModel = $companyModel;
        $this->countryModel = $countryModel;
        $this->stateModel = $stateModel;
        $this->cityModel = $cityModel;
        $this->currencyModel = $currencyModel;
        $this->authenticationModel = $authenticationModel;
        $this->uploadSettingModel = $uploadSettingModel;
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
                case 'add company':
                    $this->addCompany();
                    break;
                case 'update company':
                    $this->updateCompany();
                    break;
                case 'update company logo':
                    $this->updateCompanyLogo();
                    break;
                case 'get company details':
                    $this->getCompanyDetails();
                    break;
                case 'delete company':
                    $this->deleteCompany();
                    break;
                case 'delete multiple company':
                    $this->deleteMultipleCompany();
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
    public function addCompany() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $companyName = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        $taxID = filter_input(INPUT_POST, 'tax_id', FILTER_SANITIZE_STRING);
        $currencyID = filter_input(INPUT_POST, 'currency_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING);

        $cityDetails = $this->cityModel->getCity($cityID);
        $cityName = $cityDetails['city_name'] ?? null;
        $stateID = $cityDetails['state_id'] ?? null;
        $stateName = $cityDetails['state_name'] ?? null;
        $countryID = $cityDetails['country_id'] ?? null;
        $countryName = $cityDetails['country_name'] ?? null;

        $currencyModel = $this->currencyModel->getCurrency($currencyID);
        $currencyName = $currencyModel['currency_name'] ?? null;
        
        $companyID = $this->companyModel->saveCompany(null, $companyName, $address, $cityID, $cityName, $stateID, $stateName, $countryID, $countryName, $taxID, $currencyID, $currencyName, $phone, $telephone, $email, $website, $userID);
    
        $response = [
            'success' => true,
            'companyID' => $this->securityModel->encryptData($companyID),
            'title' => 'Save Company',
            'message' => 'The company has been saved successfully.',
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
    public function updateCompany() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $companyID = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);
        $companyName = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
        $cityID = filter_input(INPUT_POST, 'city_id', FILTER_VALIDATE_INT);
        $taxID = filter_input(INPUT_POST, 'tax_id', FILTER_SANITIZE_STRING);
        $currencyID = filter_input(INPUT_POST, 'currency_id', FILTER_VALIDATE_INT);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $telephone = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING);
    
        $checkCompanyExist = $this->companyModel->checkCompanyExist($companyID);
        $total = $checkCompanyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Company',
                'message' => 'The company does not exist.',
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

        $currencyModel = $this->currencyModel->getCurrency($currencyID);
        $currencyName = $currencyModel['currency_name'] ?? null;
        
        $companyID = $this->companyModel->saveCompany($companyID, $companyName, $address, $cityID, $cityName, $stateID, $stateName, $countryID, $countryName, $taxID, $currencyID, $currencyName, $phone, $telephone, $email, $website, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Company',
            'message' => 'The company has been saved successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateCompanyLogo() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];

        $companyID = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);

        $checkCompanyExist = $this->companyModel->checkCompanyExist($companyID);
        $total = $checkCompanyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Company Logo',
                'message' => 'The company logo does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $companyLogoFileName = $_FILES['company_logo']['name'];
        $companyLogoFileSize = $_FILES['company_logo']['size'];
        $companyLogoFileError = $_FILES['company_logo']['error'];
        $companyLogoTempName = $_FILES['company_logo']['tmp_name'];
        $companyLogoFileExtension = explode('.', $companyLogoFileName);
        $companyLogoActualFileExtension = strtolower(end($companyLogoFileExtension));

        $uploadSetting = $this->uploadSettingModel->getUploadSetting(5);
        $maxFileSize = $uploadSetting['max_file_size'];

        $uploadSettingFileExtension = $this->uploadSettingModel->getUploadSettingFileExtension(5);
        $allowedFileExtensions = [];

        foreach ($uploadSettingFileExtension as $row) {
            $allowedFileExtensions[] = $row['file_extension'];
        }

        if (!in_array($companyLogoActualFileExtension, $allowedFileExtensions)) {
            $response = [
                'success' => false,
                'title' => 'Update Company Logo',
                'message' => 'The file uploaded is not supported.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if(empty($companyLogoTempName)){
            $response = [
                'success' => false,
                'title' => 'Update Company Logo',
                'message' => 'Please choose the company logo.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($companyLogoFileError){
            $response = [
                'success' => false,
                'title' => 'Update Company Logo',
                'message' => 'An error occurred while uploading the file.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($companyLogoFileSize > ($maxFileSize * 1024)){
            $response = [
                'success' => false,
                'title' => 'Update Company Logo',
                'message' => 'The company logo exceeds the maximum allowed size of ' . number_format($maxFileSize) . ' kb.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $fileName = $this->securityModel->generateFileName();
        $fileNew = $fileName . '.' . $companyLogoActualFileExtension;
            
        define('PROJECT_BASE_DIR', dirname(__DIR__));
        define('COMPANY_LOGO_DIR', 'image/logo/');

        $directory = PROJECT_BASE_DIR . '/'. COMPANY_LOGO_DIR. $companyID. '/';
        $fileDestination = $directory. $fileNew;
        $filePath = '../settings/company/image/logo/'. $companyID . '/' . $fileNew;

        $directoryChecker = $this->securityModel->directoryChecker(str_replace('./', '../', $directory));

        if(!$directoryChecker){
            $response = [
                'success' => false,
                'title' => 'Update Company Logo Error',
                'message' => $directoryChecker,
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $companyDetails = $this->companyModel->getCompany($companyID);
        $companyLogoPath = !empty($companyDetails['company_logo']) ? str_replace('../', '../../../../apps/', $companyDetails['company_logo']) : null;

        if(file_exists($companyLogoPath)){
            if (!unlink($companyLogoPath)) {
                $response = [
                    'success' => false,
                    'title' => 'Update Company Logo',
                    'message' => 'The company logo cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        if(!move_uploaded_file($companyLogoTempName, $fileDestination)){
            $response = [
                'success' => false,
                'title' => 'Update Company Logo',
                'message' => 'The company logo cannot be uploaded due to an error.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->companyModel->updateCompanyLogo($companyID, $filePath, $userID);

        $response = [
            'success' => true,
            'title' => 'Update Company Logo',
            'message' => 'The company logo has been updated successfully.',
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
    public function deleteCompany() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $companyID = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);
        
        $checkCompanyExist = $this->companyModel->checkCompanyExist($companyID);
        $total = $checkCompanyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Company',
                'message' => 'The company does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $companyDetails = $this->companyModel->getCompany($companyID);
        $companyLogoPath = !empty($companyDetails['company_logo']) ? str_replace('../', '../../../../apps/', $companyDetails['company_logo']) : null;

        if(file_exists($companyLogoPath)){
            if (!unlink($companyLogoPath)) {
                $response = [
                    'success' => false,
                    'title' => 'Delete Company',
                    'message' => 'The company logo cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        $this->companyModel->deleteCompany($companyID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Company',
            'message' => 'The company has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleCompany() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['company_id']) && !empty($_POST['company_id'])) {
            $companyIDs = $_POST['company_id'];
    
            foreach($companyIDs as $companyID){
                $checkCompanyExist = $this->companyModel->checkCompanyExist($companyID);
                $total = $checkCompanyExist['total'] ?? 0;

                if($total > 0){
                    $companyDetails = $this->companyModel->getCompany($companyID);
                    $companyLogoPath = !empty($companyDetails['company_logo']) ? str_replace('../', '../../../../apps/', $companyDetails['company_logo']) : null;

                    if(file_exists($companyLogoPath)){
                        if (!unlink($companyLogoPath)) {
                            $response = [
                                'success' => false,
                                'title' => 'Delete Multiple Companys',
                                'message' => 'The company logo cannot be deleted due to an error.',
                                'messageType' => 'error'
                            ];
                            
                            echo json_encode($response);
                            exit;
                        }
                    }
                    
                    $this->companyModel->deleteCompany($companyID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Companys',
                'message' => 'The selected companys have been deleted successfully.',
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
                $filename = "company_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $companyDetails = $this->companyModel->exportCompany($columns, $ids);

                foreach ($companyDetails as $companyDetail) {
                    fputcsv($output, $companyDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "company_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $companyDetails = $this->companyModel->exportCompany($columns, $ids);

                $rowNumber = 2;
                foreach ($companyDetails as $companyDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $companyDetail[$column]);
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
    public function getCompanyDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $companyID = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);

        $checkCompanyExist = $this->companyModel->checkCompanyExist($companyID);
        $total = $checkCompanyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Company Details',
                'message' => 'The company does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $companyDetails = $this->companyModel->getCompany($companyID);
        $companyLogo = $this->systemModel->checkImage(str_replace('../', './apps/', $companyDetails['company_logo'])  ?? null, 'company logo');

        $response = [
            'success' => true,
            'companyName' => $companyDetails['company_name'] ?? null,
            'address' => $companyDetails['address'] ?? null,
            'cityID' => $companyDetails['city_id'] ?? null,
            'taxID' => $companyDetails['tax_id'] ?? null,
            'currencyID' => $companyDetails['currency_id'] ?? null,
            'phone' => $companyDetails['phone'] ?? null,
            'telephone' => $companyDetails['telephone'] ?? null,
            'email' => $companyDetails['email'] ?? null,
            'website' => $companyDetails['website'] ?? null,
            'companyLogo' => $companyLogo
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>