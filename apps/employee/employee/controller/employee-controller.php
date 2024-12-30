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
require_once '../../../settings/company/model/company-model.php';
require_once '../../../employee/department/model/department-model.php';
require_once '../../../employee/job-position/model/job-position-model.php';
require_once '../../../employee/work-location/model/work-location-model.php';
require_once '../../../settings/upload-setting/model/upload-setting-model.php';
require_once '../../../settings/language/model/language-model.php';
require_once '../../../settings/language-proficiency/model/language-proficiency-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new EmployeeController(new EmployeeModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new CityModel(new DatabaseModel), new CountryModel(new DatabaseModel), new CivilStatusModel(new DatabaseModel), new ReligionModel(new DatabaseModel), new BloodTypeModel(new DatabaseModel), new GenderModel(new DatabaseModel), new CompanyModel(new DatabaseModel), new DepartmentModel(new DatabaseModel), new JobPositionModel(new DatabaseModel), new WorkLocationModel(new DatabaseModel), new LanguageModel(new DatabaseModel), new LanguageProficiencyModel(new DatabaseModel), new UploadSettingModel(new DatabaseModel), new SecurityModel(), new SystemModel());
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
    private $companyModel;
    private $departmentModel;
    private $jobPositionModel;
    private $workLocationModel;
    private $languageModel;
    private $languageProficiencyModel;
    private $uploadSettingModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(EmployeeModel $employeeModel, AuthenticationModel $authenticationModel, CityModel $cityModel, CountryModel $countryModel, CivilStatusModel $civilStatusModel, ReligionModel $religionModel, BloodTypeModel $bloodTypeModel, GenderModel $genderModel, CompanyModel $companyModel, DepartmentModel $departmentModel, JobPositionModel $jobPositionModel, WorkLocationModel $workLocationModel, LanguageModel $languageModel, LanguageProficiencyModel $languageProficiencyModel, UploadSettingModel $uploadSettingModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->employeeModel = $employeeModel;
        $this->authenticationModel = $authenticationModel;
        $this->cityModel = $cityModel;
        $this->countryModel = $countryModel;
        $this->civilStatusModel = $civilStatusModel;
        $this->religionModel = $religionModel;
        $this->bloodTypeModel = $bloodTypeModel;
        $this->genderModel = $genderModel;
        $this->companyModel = $companyModel;
        $this->departmentModel = $departmentModel;
        $this->jobPositionModel = $jobPositionModel;
        $this->workLocationModel = $workLocationModel;
        $this->uploadSettingModel = $uploadSettingModel;
        $this->languageModel = $languageModel;
        $this->languageProficiencyModel = $languageProficiencyModel;
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
                case 'save employee language':
                    $this->saveEmployeeLanguage();
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
                case 'update employee company':
                    $this->updateEmployeeCompany();
                    break;
                case 'update employee department':
                    $this->updateEmployeeDepartment();
                    break;
                case 'update employee job position':
                    $this->updateEmployeeJobPosition();
                    break;
                case 'update employee manager':
                    $this->updateEmployeeManager();
                    break;
                case 'update employee time-off approver':
                    $this->updateEmployeeTimeOffApprover();
                    break;
                case 'update employee work location':
                    $this->updateEmployeeWorkLocation();
                    break;
                case 'update employee on-board date':
                    $this->updateEmployeeOnBoardDate();
                    break;
                case 'update employee work email':
                    $this->updateEmployeeWorkEmail();
                    break;
                case 'update employee work phone':
                    $this->updateEmployeeWorkPhone();
                    break;
                case 'update employee work telephone':
                    $this->updateEmployeeWorkTelephone();
                    break;
                case 'get employee personal details':
                    $this->getEmployeePersonalDetails();
                    break;
                case 'get employee image details':
                    $this->getEmployeeImageDetails();
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
                case 'get employee company details':
                    $this->getEmployeeCompanyDetails();
                    break;
                case 'get employee department details':
                    $this->getEmployeeDepartmentDetails();
                    break;
                case 'get employee job position details':
                    $this->getEmployeeJobPositionDetails();
                    break;
                case 'get employee manager details':
                    $this->getEmployeeManagerDetails();
                    break;
                case 'get employee time-off approver details':
                    $this->getEmployeeTimeOffApproverDetails();
                    break;
                case 'get employee work location details':
                    $this->getEmployeeWorkLocationDetails();
                    break;
                case 'get employee on-board date details':
                    $this->getEmployeeOnBoardDateDetails();
                    break;
                case 'get employee work email details':
                    $this->getEmployeeWorkEmailDetails();
                    break;
                case 'get employee work phone details':
                    $this->getEmployeeWorkPhoneDetails();
                    break;
                case 'get employee work telephone details':
                    $this->getEmployeeWorkTelephoneDetails();
                    break;
                case 'get employee language details':
                    $this->getEmployeeLanguageDetails();
                    break;
                case 'delete employee':
                    $this->deleteEmployee();
                    break;
                case 'delete multiple employee':
                    $this->deleteMultipleEmployee();
                    break;
                case 'delete employee language':
                    $this->deleteEmployeeLanguage();
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
    public function updateEmployeeImage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];

        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $checkCompanyExist = $this->companyModel->checkCompanyExist($employeeID);
        $total = $checkCompanyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Employee Image',
                'message' => 'The employee image does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $employeeImageFileName = $_FILES['employee_image']['name'];
        $employeeImageFileSize = $_FILES['employee_image']['size'];
        $employeeImageFileError = $_FILES['employee_image']['error'];
        $employeeImageTempName = $_FILES['employee_image']['tmp_name'];
        $employeeImageFileExtension = explode('.', $employeeImageFileName);
        $employeeImageActualFileExtension = strtolower(end($employeeImageFileExtension));

        $uploadSetting = $this->uploadSettingModel->getUploadSetting(7);
        $maxFileSize = $uploadSetting['max_file_size'];

        $uploadSettingFileExtension = $this->uploadSettingModel->getUploadSettingFileExtension(7);
        $allowedFileExtensions = [];

        foreach ($uploadSettingFileExtension as $row) {
            $allowedFileExtensions[] = $row['file_extension'];
        }

        if (!in_array($employeeImageActualFileExtension, $allowedFileExtensions)) {
            $response = [
                'success' => false,
                'title' => 'Update Employee Image',
                'message' => 'The file uploaded is not supported.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if(empty($employeeImageTempName)){
            $response = [
                'success' => false,
                'title' => 'Update Employee Image',
                'message' => 'Please choose the employee image.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($employeeImageFileError){
            $response = [
                'success' => false,
                'title' => 'Update Employee Image',
                'message' => 'An error occurred while uploading the file.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }
            
        if($employeeImageFileSize > ($maxFileSize * 1024)){
            $response = [
                'success' => false,
                'title' => 'Update Employee Image',
                'message' => 'The employee image exceeds the maximum allowed size of ' . number_format($maxFileSize) . ' kb.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $fileName = $this->securityModel->generateFileName();
        $fileNew = $fileName . '.' . $employeeImageActualFileExtension;
            
        define('PROJECT_BASE_DIR', dirname(__DIR__));
        define('EMPLOYEE_IMAGE_DIR', 'image/');

        $directory = PROJECT_BASE_DIR . '/'. EMPLOYEE_IMAGE_DIR. $employeeID. '/';
        $fileDestination = $directory. $fileNew;
        $filePath = '../employee/employee/image/'. $employeeID . '/' . $fileNew;

        $directoryChecker = $this->securityModel->directoryChecker(str_replace('./', '../', $directory));

        if(!$directoryChecker){
            $response = [
                'success' => false,
                'title' => 'Update Employee Image Error',
                'message' => $directoryChecker,
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);
        $employeeImagePath = !empty($employeeDetails['employee_image']) ? str_replace('../', '../../../../apps/', $employeeDetails['employee_image']) : null;

        if(file_exists($employeeImagePath)){
            if (!unlink($employeeImagePath)) {
                $response = [
                    'success' => false,
                    'title' => 'Update Employee Image',
                    'message' => 'The employee image cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        if(!move_uploaded_file($employeeImageTempName, $fileDestination)){
            $response = [
                'success' => false,
                'title' => 'Update Employee Image',
                'message' => 'The employee image cannot be uploaded due to an error.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->employeeModel->updateEmployeeImage($employeeID, $filePath, $userID);

        $response = [
            'success' => true,
            'title' => 'Update Employee Image',
            'message' => 'The employee image has been updated successfully.',
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
    public function updateEmployeeCompany() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $companyID = filter_input(INPUT_POST, 'company_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Company',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $companyDetails = $this->companyModel->getCompany($companyID);
        $companyName = $companyDetails['company_name'] ?? null;
        
        $this->employeeModel->updateEmployeeCompany($employeeID, $companyID, $companyName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Company',
            'message' => 'The company has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeDepartment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $departmentID = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Department',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $departmentDetails = $this->departmentModel->getDepartment($departmentID);
        $departmentName = $departmentDetails['department_name'] ?? null;
        
        $this->employeeModel->updateEmployeeDepartment($employeeID, $departmentID, $departmentName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Department',
            'message' => 'The department has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeJobPosition() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $jobPositionID = filter_input(INPUT_POST, 'job_position_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Job Position',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $jobPositionDetails = $this->jobPositionModel->getJobPosition($jobPositionID);
        $jobPositionName = $jobPositionDetails['job_position_name'] ?? null;
        
        $this->employeeModel->updateEmployeeJobPosition($employeeID, $jobPositionID, $jobPositionName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Job Position',
            'message' => 'The job position has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeManager() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $managerID = filter_input(INPUT_POST, 'manager_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Manager',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $managerDetails = $this->employeeModel->getEmployee($managerID);
        $managerName = $managerDetails['full_name'] ?? null;
        
        $this->employeeModel->updateEmployeeManager($employeeID, $managerID, $managerName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Manager',
            'message' => 'The manager has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeTimeOffApprover() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $timeOffApproverID = filter_input(INPUT_POST, 'time_off_approver_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Time-Off Approver',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $timeOffApproverDetails = $this->employeeModel->getEmployee($timeOffApproverID);
        $timeOffApproverName = $timeOffApproverDetails['full_name'] ?? null;
        
        $this->employeeModel->updateEmployeeTimeOffApprover($employeeID, $timeOffApproverID, $timeOffApproverName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Time-Off Approver',
            'message' => 'The time-off approver has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeWorkLocation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $workLocationID = filter_input(INPUT_POST, 'work_location_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Work Location',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $workLocationDetails = $this->workLocationModel->getWorkLocation($workLocationID);
        $workLocationName = $workLocationDetails['work_location_name'] ?? null;
        
        $this->employeeModel->updateEmployeeWorkLocation($employeeID, $workLocationID, $workLocationName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Work Location',
            'message' => 'The work location has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeOnBoardDate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $onBoardDate = $this->systemModel->checkDate('empty', $_POST['on_board_date'], '', 'Y-m-d', '');
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee On-Board Date',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeeOnBoardDate($employeeID, $onBoardDate, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee On-Board Date',
            'message' => 'The on-board date has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeWorkEmail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $workEmail = filter_input(INPUT_POST, 'work_email', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Work Email',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeeWorkEmail($employeeID, $workEmail, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Work Email',
            'message' => 'The work email has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeWorkPhone() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $workPhone = filter_input(INPUT_POST, 'work_phone', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Work Phone',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeeWorkPhone($employeeID, $workPhone, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Work Phone',
            'message' => 'The work phone has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateEmployeeWorkTelephone() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $workTelephone = filter_input(INPUT_POST, 'work_telephone', FILTER_SANITIZE_STRING);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Work Telephone',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }
        
        $this->employeeModel->updateEmployeeWorkTelephone($employeeID, $workTelephone, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Work Telephone',
            'message' => 'The work telephone has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Save methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function saveEmployeeLanguage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employeeLanguageID = isset($_POST['employee_language_id']) ? filter_var($_POST['employee_language_id'], FILTER_VALIDATE_INT) : null;
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $languageID = filter_input(INPUT_POST, 'language_id', FILTER_VALIDATE_INT);
        $languageProficiencyID = filter_input(INPUT_POST, 'language_proficiency_id', FILTER_VALIDATE_INT);
    
        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employee Language',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $languageDetails = $this->languageModel->getLanguage($languageID);
        $languageName = $languageDetails['language_name'] ?? null;

        $languageProficiencyDetails = $this->languageProficiencyModel->getLanguageProficiency($languageProficiencyID);
        $languageProficiencyName = $languageProficiencyDetails['language_proficiency_name'] ?? null;
        
        $this->employeeModel->saveEmployeeLanguage($employeeLanguageID, $employeeID, $languageID, $languageName, $languageProficiencyID, $languageProficiencyName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employee Language',
            'message' => 'The employee language has been saved successfully.',
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
    public function deleteEmployeeLanguage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $employeeLanguageID = filter_input(INPUT_POST, 'employee_language_id', FILTER_VALIDATE_INT);
        
        $checkEmployeeLanguageExist = $this->employeeModel->checkEmployeeLanguageExist($employeeLanguageID);
        $total = $checkEmployeeLanguageExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Language',
                'message' => 'The language does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->employeeModel->deleteEmployeeLanguage($employeeLanguageID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Language',
            'message' => 'The language has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
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
    public function getEmployeeImageDetails() {
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
                'title' => 'Get Employee Image Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);
        $employeeImage = $this->systemModel->checkImage(str_replace('../', './apps/', $employeeDetails['employee_image'])  ?? null, 'profile');

        $response = [
            'success' => true,
            'employeeImage' => $employeeImage
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
            'placeOfBirth' => $employeeDetails['place_of_birth'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeCompanyDetails() {
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
                'title' => 'Get Employee Company Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'companyName' => $employeeDetails['company_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeDepartmentDetails() {
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
                'title' => 'Get Employee Department Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'departmentName' => $employeeDetails['department_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeJobPositionDetails() {
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
                'title' => 'Get Employee Job Position Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'jobPositionName' => $employeeDetails['job_position_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeManagerDetails() {
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
                'title' => 'Get Employee Manager Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'managerName' => $employeeDetails['manager_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeTimeOffApproverDetails() {
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
                'title' => 'Get Employee Time-Off Approver Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'timeOffApproverName' => $employeeDetails['time_off_approver_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeWorkLocationDetails() {
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
                'title' => 'Get Employee Work Location Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'workLocationName' => $employeeDetails['work_location_name'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeOnBoardDateDetails() {
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
                'title' => 'Get Employee On-Board Date Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'onBoardDate' => $this->systemModel->checkDate('summary', $employeeDetails['on_board_date'], '', 'M d, Y', '')
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeWorkEmailDetails() {
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
                'title' => 'Get Employee Work Email Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'workEmail' => $employeeDetails['work_email'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeWorkPhoneDetails() {
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
                'title' => 'Get Employee Work Phone Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'workPhone' => $employeeDetails['work_phone'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeWorkTelephoneDetails() {
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
                'title' => 'Get Employee Work Telephone Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeDetails = $this->employeeModel->getEmployee($employeeID);

        $response = [
            'success' => true,
            'workTelephone' => $employeeDetails['work_telephone'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getEmployeeLanguageDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $employeeLanguageID = filter_input(INPUT_POST, 'employee_language_id', FILTER_VALIDATE_INT);

        $checkEmployeeExist = $this->employeeModel->checkEmployeeExist($employeeID);
        $total = $checkEmployeeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employee Work Telephone Details',
                'message' => 'The employee does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employeeLanguageDetails = $this->employeeModel->getEmployeeLanguage($employeeLanguageID);

        $response = [
            'success' => true,
            'languageID' => $employeeLanguageDetails['language_id'] ?? '--',
            'languageProficiencyID' => $employeeLanguageDetails['language_proficiency_id'] ?? '--'
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>