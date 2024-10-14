<?php
session_start();

require_once '../../components/configurations/config.php';
require_once '../../components/model/database-model.php';
require_once '../../components/model/security-model.php';
require_once '../../components/model/system-model.php';
require_once '../../components/model/export-model.php';
require_once '../../apps/security/authentication/model/authentication-model.php';

require_once '../../assets/libs/PhpSpreadsheet/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Category;

$controller = new ExportController(new ExportModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class ExportController {
    private $exportModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(ExportModel $exportModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->exportModel = $exportModel;
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
    #   Export methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function exportData() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $exportTo = $_POST['export_to'];
        $exportIDs = $_POST['export_id']; 
        $tableColumns = $_POST['table_column'];
        $tableName = $_POST['table_name'];
            
        if ($exportTo == 'csv') {
            $filename = $tableName . '_export_' . date('Y-m-d_H-i-s') . ".csv";
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
                
            $output = fopen('php://output', 'w');

            fputcsv($output, $tableColumns);
                
            $columns = implode(", ", $tableColumns);
                
            $ids = implode(",", array_map('intval', $exportIDs));
            $appModuleDetails = $this->exportModel->exportData($tableName, $columns, $ids);

            foreach ($appModuleDetails as $appModuleDetail) {
                fputcsv($output, $appModuleDetail);
            }

            fclose($output);
            exit;
        }
        else {
            ob_start();
            $filename = $tableName . '_export_' . date('Y-m-d_H-i-s') . ".xlsx";

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $colIndex = 'A';
            foreach ($tableColumns as $column) {
                $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                $colIndex++;
            }

            $columns = implode(", ", $tableColumns);
                
            $ids = implode(",", array_map('intval', $exportIDs));
            $appModuleDetails = $this->exportModel->exportData($tableName, $columns, $ids);

            $rowNumber = 2;
            foreach ($appModuleDetails as $appModuleDetail) {
                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . $rowNumber, $appModuleDetail[$column]);
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
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>