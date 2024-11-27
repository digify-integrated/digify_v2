<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../language-proficiency/model/language-proficiency-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new LanguageProficiencyController(new LanguageProficiencyModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class LanguageProficiencyController {
    private $languageProficiencyModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(LanguageProficiencyModel $languageProficiencyModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->languageProficiencyModel = $languageProficiencyModel;
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
                case 'add language proficiency':
                    $this->addLanguageProficiency();
                    break;
                case 'update language proficiency':
                    $this->updateLanguageProficiency();
                    break;
                case 'get language proficiency details':
                    $this->getLanguageProficiencyDetails();
                    break;
                case 'delete language proficiency':
                    $this->deleteLanguageProficiency();
                    break;
                case 'delete multiple language proficiency':
                    $this->deleteMultipleLanguageProficiency();
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
    public function addLanguageProficiency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $languageProficiencyName = filter_input(INPUT_POST, 'language_proficiency_name', FILTER_SANITIZE_STRING);
        $languageProficiencyDescription = filter_input(INPUT_POST, 'language_proficiency_description', FILTER_SANITIZE_STRING);
        
        $languageProficiencyID = $this->languageProficiencyModel->saveLanguageProficiency(null, $languageProficiencyName, $languageProficiencyDescription, $userID);
    
        $response = [
            'success' => true,
            'languageProficiencyID' => $this->securityModel->encryptData($languageProficiencyID),
            'title' => 'Save Language Proficiency',
            'message' => 'The language proficiency has been saved successfully.',
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
    public function updateLanguageProficiency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $languageProficiencyID = filter_input(INPUT_POST, 'language_proficiency_id', FILTER_VALIDATE_INT);
        $languageProficiencyName = filter_input(INPUT_POST, 'language_proficiency_name', FILTER_SANITIZE_STRING);
        $languageProficiencyDescription = filter_input(INPUT_POST, 'language_proficiency_description', FILTER_SANITIZE_STRING);
    
        $checkLanguageProficiencyExist = $this->languageProficiencyModel->checkLanguageProficiencyExist($languageProficiencyID);
        $total = $checkLanguageProficiencyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Language Proficiency',
                'message' => 'The language proficiency does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $languageProficiencyID = $this->languageProficiencyModel->saveLanguageProficiency($languageProficiencyID, $languageProficiencyName, $languageProficiencyDescription, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Language Proficiency',
            'message' => 'The language proficiency has been saved successfully.',
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
    public function deleteLanguageProficiency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $languageProficiencyID = filter_input(INPUT_POST, 'language_proficiency_id', FILTER_VALIDATE_INT);
        
        $checkLanguageProficiencyExist = $this->languageProficiencyModel->checkLanguageProficiencyExist($languageProficiencyID);
        $total = $checkLanguageProficiencyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Language Proficiency',
                'message' => 'The language proficiency does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->languageProficiencyModel->deleteLanguageProficiency($languageProficiencyID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Language Proficiency',
            'message' => 'The language proficiency has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleLanguageProficiency() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['language_proficiency_id']) && !empty($_POST['language_proficiency_id'])) {
            $languageProficiencyIDs = $_POST['language_proficiency_id'];
    
            foreach($languageProficiencyIDs as $languageProficiencyID){
                $checkLanguageProficiencyExist = $this->languageProficiencyModel->checkLanguageProficiencyExist($languageProficiencyID);
                $total = $checkLanguageProficiencyExist['total'] ?? 0;

                if($total > 0){
                    $this->languageProficiencyModel->deleteLanguageProficiency($languageProficiencyID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Language Proficiencys',
                'message' => 'The selected language proficiencys have been deleted successfully.',
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
                $filename = "language_proficiency_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $languageProficiencyDetails = $this->languageProficiencyModel->exportLanguageProficiency($columns, $ids);

                foreach ($languageProficiencyDetails as $languageProficiencyDetail) {
                    fputcsv($output, $languageProficiencyDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "language_proficiency_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $languageProficiencyDetails = $this->languageProficiencyModel->exportLanguageProficiency($columns, $ids);

                $rowNumber = 2;
                foreach ($languageProficiencyDetails as $languageProficiencyDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $languageProficiencyDetail[$column]);
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
    public function getLanguageProficiencyDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $languageProficiencyID = filter_input(INPUT_POST, 'language_proficiency_id', FILTER_VALIDATE_INT);

        $checkLanguageProficiencyExist = $this->languageProficiencyModel->checkLanguageProficiencyExist($languageProficiencyID);
        $total = $checkLanguageProficiencyExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Language Proficiency Details',
                'message' => 'The language proficiency does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $languageProficiencyDetails = $this->languageProficiencyModel->getLanguageProficiency($languageProficiencyID);

        $response = [
            'success' => true,
            'languageProficiencyName' => $languageProficiencyDetails['language_proficiency_name'] ?? null,
            'languageProficiencyDescription' => $languageProficiencyDetails['language_proficiency_description'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>