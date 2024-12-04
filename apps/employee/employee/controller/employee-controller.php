<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../../settings/authentication/model/authentication-model.php';
require_once '../../employee/model/employee-model.php';
require_once '../../../settings/security-setting/model/security-setting-model.php';
require_once '../../../settings/city/model/city-model.php';
require_once '../../../settings/country/model/country-model.php';
require_once '../../../settings/civil-status/model/civil-status-model.php';
require_once '../../../settings/religion/model/religion-model.php';
require_once '../../../settings/blood-type/model/blood-type-model.php';
require_once '../../../settings/gender/model/gender-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new EmployeeController(new EmployeeModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new CityModel(new DatabaseModel), new CountryModel(new DatabaseModel), new CivilStatusModel(new DatabaseModel), new ReligionModel(new DatabaseModel), new BloodTypeModel(new DatabaseModel), new GenderModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class EmployeeController {
    private $employeeModel;
    private $authenticationModel;
    private $cityModel;
    private $countryModel;
    private $civilStatusModel;
    private $religionModel;
    private $bloodTypeModel;
    private $genderModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(EmployeeModel $employeeModel, AuthenticationModel $authenticationModel, CityModel $cityModel, CountryModel $countryModel, CivilStatusModel $civilStatusModel, ReligionModel $religionModel, BloodTypeModel $bloodTypeModel, GenderModel $genderModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->employeeModel = $employeeModel;
        $this->authenticationModel = $authenticationModel;
        $this->cityModel = $cityModel;
        $this->countryModel = $countryModel;
        $this->civilStatusModel = $civilStatusModel;
        $this->religionModel = $religionModel;
        $this->bloodTypeModel = $bloodTypeModel;
        $this->genderModel = $genderModel;
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
                case 'add employee':
                    $this->addEmployee();
                    break;
                case 'update personal information':
                    $this->updatePersonalInformation();
                    break;
                case 'update employee PIN code':
                    $this->updateEmployeePINCode();
                    break;
                case 'update employee badge id':
                    $this->updateEmployeeBadgeID();
                    break;
                case 'update employee private email':
                    $this->updateEmployeePrivateEmail();
                    break;
                case 'update employee private phone':
                    $this->updateEmployeePrivatePhone();
                    break;
                case 'update employee private telephone':
                    $this->updateEmployeePrivateTelephone();
                    break;
                case 'update employee nationality':
                    $this->updateEmployeeNationality();
                    break;
                case 'update employee gender':
                    $this->updateEmployeeGender();
                    break;
                case 'update employee birthday':
                    $this->updateEmployeeBirthday();
                    break;
                case 'update employee place of birth':
                    $this->updateEmployeePlaceOfBirth();
                    break;
                case 'get employee personal details':
                    $this->getEmployeePersonalDetails();
                    break;
                case 'get employee pin code details':
                    $this->getEmployeePINCodeDetails();
                    break;
                case 'get employee badge id details':
                    $this->getEmployeeBadgeIDDetails();
                    break;
                case 'get employee private email details':
                    $this->getEmployeePrivateEmailDetails();
                    break;
                case 'get employee private phone details':
                    $this->getEmployeePrivatePhoneDetails();
                    break;
                case 'get employee private telephone details':
                    $this->getEmployeePrivateTelephoneDetails();
                    break;
                case 'get employee nationality details':
                    $this->getEmployeeNationalityDetails();
                    break;
                case 'get employee gender details':
                    $this->getEmployeeGenderDetails();
                    break;
                case 'get employee birthday details':
                    $this->getEmployeeBirthdayDetails();
                    break;
                case 'get employee place of birth details':
                    $this->getEmployeePlaceOfBirthDetails();
                    break;
                case 'delete employee':
                    $this->deleteEmployee();
                    break;
                case 'delete multiple employee':
                    $this->deleteMultipleEmployee();
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
    public function addEmployee() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $middleName = filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $suffix = filter_input(INPUT_POST, 'suffix', FILTER_SANITIZE_STRING);

        $fullName = $firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix;
        
        $employeeID = $this->employeeModel->insertEmployee($fullName, $firstName, $middleName, $lastName, $suffix, $userID);
    
        $response = [
            'success' => true,
            'employeeID' => $this->securityModel->encryptData($employeeID),
            'title' => 'Save Employee',
            'message' => 'The employee has been saved successfully.',
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
    public function updatePersonalInformation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $middleName = filter_input(INPUT_POST, 'middle_name', FILTER_SANITIZE_STRING);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $suffix = filter_input(INPUT_POST, 'suffix', FILTER_SANITIZE_STRING);
        $privateAddress = filter_input(INPUT_POST, 'private_address', FILTER_SANITIZE_STRING);
        $privateAddressCityID = filter_input(INPUT_POST, 'private_address_city_id', FILTER_VALIDATE_INT);
        $nickname = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING);
        $civilStatusID = filter_input(INPUT_POST, 'civil_status_id', FILTER_VALIDATE_INT);
        $dependents = filter_input(INPUT_POST, 'dependents', FILTER_VALIDATE_INT);
        $religionID = filter_input(INPUT_POST, 'religion_id', FILTER_VALIDATE_INT);
        $bloodTypeID = filter_input(INPUT_POST, 'blood_type_id', FILTER_VALIDATE_INT);
        $height = filter_input(INPUT_POST, 'height', FILTER_SANITIZE_STRING);
        $weight = filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_STRING);
        $homeWorkDistance = filter_input(INPUT_POST, 'home_work_distance', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Personal Information',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $fullName = $firstName . ' ' . $middleName . ' ' . $lastName . ' ' . $suffix;

        $cityDetails = $this->cityModel->getCity($privateAddressCityID);
        $privateAddressCityName = $cityDetails['city_name'] ?? '';
        $privateAddressStateID = $cityDetails['state_id'] ?? '';
        $privateAddressStateName = $cityDetails['state_name'] ?? '';        
        $privateAddressCountryID = $cityDetails['country_id'] ?? '';        
        $privateAddressCountryName = $cityDetails['country_name'] ?? '';        

        $civilStatusDetails = $this->civilStatusModel->getCivilStatus($civilStatusID);
        $civilStatusName = $civilStatusDetails['civil_status_name'] ?? '';

        $religionDetails = $this->religionModel->getReligion($religionID);
        $religionName = $religionDetails['religion_name'] ?? '';

        $bloodTypeDetails = $this->bloodTypeModel->getBloodType($bloodTypeID);
        $bloodTypeName = $bloodTypeDetails['blood_type_name'] ?? '';
        
        $this->employeeModel->updateEmployee($employeeID, $fullName, $firstName, $middleName, $lastName, $suffix, $nickname, $privateAddress, $privateAddressCityID, $privateAddressCityName, $privateAddressStateID, $privateAddressStateName, $privateAddressCountryID, $privateAddressCountryName, $civilStatusID, $civilStatusName, $dependents, $religionID, $religionName, $bloodTypeID, $bloodTypeName, $homeWorkDistance, $height, $weight, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Personal Information',
            'message' => 'The personal information has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePINCode() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $pinCode = filter_input(INPUT_POST, 'pin_code', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee PIN Code',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeePINCode($employeeID, $pinCode, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee PIN Code',
            'message' => 'The PIN code has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeBadgeID() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $badgeID = filter_input(INPUT_POST, 'badge_id', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Badge ID',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeeBadgeID($employeeID, $badgeID, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Badge ID',
            'message' => 'The badge ID has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePrivateEmail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $privateEmail = filter_input(INPUT_POST, 'private_email', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Private Email',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeePrivateEmail($employeeID, $privateEmail, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Private Email',
            'message' => 'The private email has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePrivatePhone() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $privatePhone = filter_input(INPUT_POST, 'private_phone', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Private Phone',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeePrivatePhone($employeeID, $privatePhone, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Private Phone',
            'message' => 'The private phone has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePrivateTelephone() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $privateTelephone = filter_input(INPUT_POST, 'private_telephone', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Private Telephone',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeePrivateTelephone($employeeID, $privateTelephone, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Private Telephone',
            'message' => 'The private telephone has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeNationality() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $nationalityID = filter_input(INPUT_POST, 'nationality_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Nationality',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $nationalityDetails = $this->countryModel->getCountry($nationalityID);
        $nationalityName = $nationalityDetails['country_name'] ?? null;
        
        $this->employeeModel->updateEmployeeNationality($employeeID, $nationalityID, $nationalityName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Nationality',
            'message' => 'The nationality has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeGender() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $genderID = filter_input(INPUT_POST, 'gender_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Gender',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $genderDetails = $this->genderModel->getGender($genderID);
        $genderName = $genderDetails['gender_name'] ?? null;
        
        $this->employeeModel->updateEmployeeGender($employeeID, $genderID, $genderName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Gender',
            'message' => 'The gender has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeBirthday() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $birthday = $this->systemModel->checkDate('empty', $_POST['birthday'], '', 'Y-m-d', '');
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Birthday',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeeBirthday($employeeID, $birthday, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Birthday',
            'message' => 'The birthday has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeePlaceOfBirth() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $placeOfBirth = filter_input(INPUT_POST, 'place_of_birth', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Place of Birth',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeePlaceOfBirth($employeeID, $placeOfBirth, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Place of Birth',
            'message' => 'The place of birth has been updated successfully.',
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
    public function deleteEmployee() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Employee',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->employeeModel->deleteEmployee($employeeID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Employee',
            'message' => 'The employee has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleEmployee() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['employee_id']) && !empty($_POST['employee_id'])) {
            $employeeIDs = $_POST['employee_id'];
    
            foreach($employeeIDs as $employeeID){
                $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
                $total = $checkEmployeeExist['total'] ?? 0;

                if($total > 0){                    
                    $this->employeeModel->deleteEmployee($employeeID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Employees',
                'message' => 'The selected employees have been deleted successfully.',
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
                $filename = "employee_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $employeeDetails = $this->employeeModel->exportEmployee($columns, $ids);

                foreach ($employeeDetails as $employeeDetail) {
                    fputcsv($output, $employeeDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "employee_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $employeeDetails = $this->employeeModel->exportEmployee($columns, $ids);

                $rowNumber = 2;
                foreach ($employeeDetails as $employeeDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $employeeDetail[$column]);
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
    public function getEmployeePersonalDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Personal Information Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);
        $employeeAddress = $employeeDetails['private_address'] . ', ' . $employeeDetails['private_address_city_name'] . ', ' . $employeeDetails['private_address_state_name'] . ', ' . $employeeDetails['private_address_country_name'];
        $privateAddressCityID = $employeeDetails['private_address_city_id'] == 0 ? '' : $employeeDetails['private_address_city_id'];
        $civilStatusID = $employeeDetails['civil_status_id'] == 0 ? '' : $employeeDetails['civil_status_id'];
        $religionID = $employeeDetails['religion_id'] == 0 ? '' : $employeeDetails['religion_id'];
        $bloodTypeID = $employeeDetails['blood_type_id'] == 0 ? '' : $employeeDetails['blood_type_id'];

        $response = [
            'success' => true,
            'fullName' => $employeeDetails['full_name'] ?? null,
            'firstName' => $employeeDetails['first_name'] ?? null,
            'middleName' => $employeeDetails['middle_name'] ?? null,
            'lastName' => $employeeDetails['last_name'] ?? null,
            'suffix' => $employeeDetails['suffix'] ?? null,
            'employeeAddress' => $employeeAddress,
            'privateAddress' => $employeeDetails['private_address'] ?? null,
            'privateAddressCityID' => $privateAddressCityID,
            'civilStatusID' => $civilStatusID,
            'religionID' => $religionID,
            'bloodTypeID' => $bloodTypeID,
            'height' => $employeeDetails['height'] ?? null,
            'weight' => $employeeDetails['weight'] ?? null,
            'nickname' => $employeeDetails['nickname'] ?? null,
            'dependents' => $employeeDetails['dependents'] ?? null,
            'homeWorkDistance' => $employeeDetails['home_work_distance'] ?? null,
            'civilStatusName' => $employeeDetails['civil_status_name'] ?? null,
            'religionName' => $employeeDetails['religion_name'] ?? null,
            'bloodTypeName' => $employeeDetails['blood_type_name'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeePINCodeDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee PIN Code Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'pinCode' => $employeeDetails['pin_code'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeBadgeIDDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Badge ID Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'badgeID' => $employeeDetails['badge_id'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeePrivateEmailDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Private Email Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'privateEmail' => $employeeDetails['private_email'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeePrivatePhoneDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Private Phone Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'privatePhone' => $employeeDetails['private_phone'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeePrivateTelephoneDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Private Telephone Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'privateTelephone' => $employeeDetails['private_telephone'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeNationalityDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Nationality Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'nationalityName' => $employeeDetails['nationality_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeGenderDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Gender Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'genderName' => $employeeDetails['gender_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeBirthdayDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Date of Birth Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'birthday' => $this->systemModel->checkDate('summary', $employeeDetails['birthday'], '', 'M d, Y', '')
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeePlaceOfBirthDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Place of Birth Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'placeOfBirth' => $employeeDetails['place_of_birth']
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>