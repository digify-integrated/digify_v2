<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../../settings/authentication/model/authentication-model.php';
require_once '../../employment-type/model/employment-type-model.php';
require_once '../../../settings/security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new EmploymentTypeController(new EmploymentTypeModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class EmploymentTypeController {
    private $employmentTypeModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(EmploymentTypeModel $employmentTypeModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->employmentTypeModel = $employmentTypeModel;
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
                case 'add employment type':
                    $this->addEmploymentType();
                    break;
                case 'update employment type':
                    $this->updateEmploymentType();
                    break;
                case 'get employment type details':
                    $this->getEmploymentTypeDetails();
                    break;
                case 'delete employment type':
                    $this->deleteEmploymentType();
                    break;
                case 'delete multiple employment type':
                    $this->deleteMultipleEmploymentType();
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
    public function addEmploymentType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $employmentTypeName = filter_input(INPUT_POST, 'employment_type_name', FILTER_SANITIZE_STRING);
        
        $employmentTypeID = $this->employmentTypeModel->saveEmploymentType(null, $employmentTypeName, $userID);
    
        $response = [
            'success' => true,
            'employmentTypeID' => $this->securityModel->encryptData($employmentTypeID),
            'title' => 'Save Employment Type',
            'message' => 'The employment type has been saved successfully.',
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
    public function updateEmploymentType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $employmentTypeID = filter_input(INPUT_POST, 'employment_type_id', FILTER_VALIDATE_INT);
        $employmentTypeName = filter_input(INPUT_POST, 'employment_type_name', FILTER_SANITIZE_STRING);
    
        $checkEmploymentTypeExist = $this->employmentTypeModel->checkEmploymentTypeExist($employmentTypeID);
        $total = $checkEmploymentTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Employment Type',
                'message' => 'The employment type does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->employmentTypeModel->saveEmploymentType($employmentTypeID, $employmentTypeName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Employment Type',
            'message' => 'The employment type has been saved successfully.',
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
    public function deleteEmploymentType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $employmentTypeID = filter_input(INPUT_POST, 'employment_type_id', FILTER_VALIDATE_INT);
        
        $checkEmploymentTypeExist = $this->employmentTypeModel->checkEmploymentTypeExist($employmentTypeID);
        $total = $checkEmploymentTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Employment Type',
                'message' => 'The employment type does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->employmentTypeModel->deleteEmploymentType($employmentTypeID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Employment Type',
            'message' => 'The employment type has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleEmploymentType() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['employment_type_id']) && !empty($_POST['employment_type_id'])) {
            $employmentTypeIDs = $_POST['employment_type_id'];
    
            foreach($employmentTypeIDs as $employmentTypeID){
                $checkEmploymentTypeExist = $this->employmentTypeModel->checkEmploymentTypeExist($employmentTypeID);
                $total = $checkEmploymentTypeExist['total'] ?? 0;

                if($total > 0){
                    $this->employmentTypeModel->deleteEmploymentType($employmentTypeID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Employment Types',
                'message' => 'The selected employment types have been deleted successfully.',
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
                $filename = "employment_type_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $employmentTypeDetails = $this->employmentTypeModel->exportEmploymentType($columns, $ids);

                foreach ($employmentTypeDetails as $employmentTypeDetail) {
                    fputcsv($output, $employmentTypeDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "employment_type_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $employmentTypeDetails = $this->employmentTypeModel->exportEmploymentType($columns, $ids);

                $rowNumber = 2;
                foreach ($employmentTypeDetails as $employmentTypeDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $employmentTypeDetail[$column]);
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
    public function getEmploymentTypeDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $employmentTypeID = filter_input(INPUT_POST, 'employment_type_id', FILTER_VALIDATE_INT);

        $checkEmploymentTypeExist = $this->employmentTypeModel->checkEmploymentTypeExist($employmentTypeID);
        $total = $checkEmploymentTypeExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Employment Type Details',
                'message' => 'The employment type does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $employmentTypeDetails = $this->employmentTypeModel->getEmploymentType($employmentTypeID);

        $response = [
            'success' => true,
            'employmentTypeName' => $employmentTypeDetails['employment_type_name'] ?? null,
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>