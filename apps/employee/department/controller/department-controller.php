<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../../settings/authentication/model/authentication-model.php';
require_once '../../department/model/department-model.php';
require_once '../../../settings/security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new DepartmentController(new DepartmentModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class DepartmentController {
    private $departmentModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(DepartmentModel $departmentModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->departmentModel = $departmentModel;
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
                case 'add department':
                    $this->addDepartment();
                    break;
                case 'update department':
                    $this->updateDepartment();
                    break;
                case 'get department details':
                    $this->getDepartmentDetails();
                    break;
                case 'delete department':
                    $this->deleteDepartment();
                    break;
                case 'delete multiple department':
                    $this->deleteMultipleDepartment();
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
    public function addDepartment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $departmentName = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_STRING);
        $parentDepartmentID = filter_input(INPUT_POST, 'parent_department_id', FILTER_VALIDATE_INT);
        $managerID = filter_input(INPUT_POST, 'manager_id', FILTER_VALIDATE_INT);

        $parentDepartmentDetails = $this->departmentModel->getDepartment($parentDepartmentID);
        $parentDepartmentName = $parentDepartmentDetails['department_name'] ?? '';
        
        $departmentID = $this->departmentModel->saveDepartment(null, $departmentName, $parentDepartmentID, $parentDepartmentName, $managerID, '', $userID);
    
        $response = [
            'success' => true,
            'departmentID' => $this->securityModel->encryptData($departmentID),
            'title' => 'Save Department',
            'message' => 'The department has been saved successfully.',
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
    public function updateDepartment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $departmentID = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
        $departmentName = filter_input(INPUT_POST, 'department_name', FILTER_SANITIZE_STRING);
        $parentDepartmentID = filter_input(INPUT_POST, 'parent_department_id', FILTER_VALIDATE_INT);
        $managerID = filter_input(INPUT_POST, 'manager_id', FILTER_VALIDATE_INT);
    
        $checkDepartmentExist = $this->departmentModel->checkDepartmentExist($departmentID);
        $total = $checkDepartmentExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Department',
                'message' => 'The department does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $parentDepartmentDetails = $this->departmentModel->getDepartment($parentDepartmentID);
        $parentDepartmentName = $parentDepartmentDetails['department_name'] ?? '';
        
        $this->departmentModel->saveDepartment($departmentID, $departmentName, $parentDepartmentID, $parentDepartmentName, $managerID, '', $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Department',
            'message' => 'The department has been saved successfully.',
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
    public function deleteDepartment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $departmentID = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);
        
        $checkDepartmentExist = $this->departmentModel->checkDepartmentExist($departmentID);
        $total = $checkDepartmentExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Department',
                'message' => 'The department does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->departmentModel->deleteDepartment($departmentID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Department',
            'message' => 'The department has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleDepartment() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['department_id']) && !empty($_POST['department_id'])) {
            $departmentIDs = $_POST['department_id'];
    
            foreach($departmentIDs as $departmentID){
                $checkDepartmentExist = $this->departmentModel->checkDepartmentExist($departmentID);
                $total = $checkDepartmentExist['total'] ?? 0;

                if($total > 0){                    
                    $this->departmentModel->deleteDepartment($departmentID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Departments',
                'message' => 'The selected departments have been deleted successfully.',
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
                $filename = "department_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $departmentDetails = $this->departmentModel->exportDepartment($columns, $ids);

                foreach ($departmentDetails as $departmentDetail) {
                    fputcsv($output, $departmentDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "department_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $departmentDetails = $this->departmentModel->exportDepartment($columns, $ids);

                $rowNumber = 2;
                foreach ($departmentDetails as $departmentDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $departmentDetail[$column]);
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
    public function getDepartmentDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $departmentID = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT);

        $checkDepartmentExist = $this->departmentModel->checkDepartmentExist($departmentID);
        $total = $checkDepartmentExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Department Details',
                'message' => 'The department does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $departmentDetails = $this->departmentModel->getDepartment($departmentID);

        $parentDepartmentID = $departmentDetails['parent_department_id'] == 0 ? '' : $departmentDetails['parent_department_id'];
        $managerID = $departmentDetails['manager_id'] == 0 ? '' : $departmentDetails['manager_id']; 

        $response = [
            'success' => true,
            'departmentName' => $departmentDetails['department_name'] ?? null,
            'parentDepartmentID' => $parentDepartmentID,
            'managerID' => $managerID
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>