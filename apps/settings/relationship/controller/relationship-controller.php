<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../relationship/model/relationship-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new RelationshipController(new RelationshipModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class RelationshipController {
    private $relationshipModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(RelationshipModel $relationshipModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->relationshipModel = $relationshipModel;
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
                case 'add relationship':
                    $this->addRelationship();
                    break;
                case 'update relationship':
                    $this->updateRelationship();
                    break;
                case 'get relationship details':
                    $this->getRelationshipDetails();
                    break;
                case 'delete relationship':
                    $this->deleteRelationship();
                    break;
                case 'delete multiple relationship':
                    $this->deleteMultipleRelationship();
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
    public function addRelationship() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $relationshipName = filter_input(INPUT_POST, 'relationship_name', FILTER_SANITIZE_STRING);
        
        $relationshipID = $this->relationshipModel->saveRelationship(null, $relationshipName, $userID);
    
        $response = [
            'success' => true,
            'relationshipID' => $this->securityModel->encryptData($relationshipID),
            'title' => 'Save Relationship',
            'message' => 'The relationship has been saved successfully.',
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
    public function updateRelationship() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $relationshipID = filter_input(INPUT_POST, 'relationship_id', FILTER_VALIDATE_INT);
        $relationshipName = filter_input(INPUT_POST, 'relationship_name', FILTER_SANITIZE_STRING);
    
        $checkRelationshipExist = $this->relationshipModel->checkRelationshipExist($relationshipID);
        $total = $checkRelationshipExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Relationship',
                'message' => 'The relationship does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->relationshipModel->saveRelationship($relationshipID, $relationshipName, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Relationship',
            'message' => 'The relationship has been saved successfully.',
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
    public function deleteRelationship() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $relationshipID = filter_input(INPUT_POST, 'relationship_id', FILTER_VALIDATE_INT);
        
        $checkRelationshipExist = $this->relationshipModel->checkRelationshipExist($relationshipID);
        $total = $checkRelationshipExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Relationship',
                'message' => 'The relationship does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $relationshipDetails = $this->relationshipModel->getRelationship($relationshipID);
        $appLogoPath = !empty($relationshipDetails['app_logo']) ? str_replace('../', '../../../../apps/', $relationshipDetails['app_logo']) : null;

        if(file_exists($appLogoPath)){
            if (!unlink($appLogoPath)) {
                $response = [
                    'success' => false,
                    'title' => 'Delete Relationship',
                    'message' => 'The app logo cannot be deleted due to an error.',
                    'messageType' => 'error'
                ];
                    
                echo json_encode($response);
                exit;
            }
        }

        $this->relationshipModel->deleteRelationship($relationshipID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Relationship',
            'message' => 'The relationship has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleRelationship() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['relationship_id']) && !empty($_POST['relationship_id'])) {
            $relationshipIDs = $_POST['relationship_id'];
    
            foreach($relationshipIDs as $relationshipID){
                $checkRelationshipExist = $this->relationshipModel->checkRelationshipExist($relationshipID);
                $total = $checkRelationshipExist['total'] ?? 0;

                if($total > 0){
                    $relationshipDetails = $this->relationshipModel->getRelationship($relationshipID);
                    $appLogoPath = !empty($relationshipDetails['app_logo']) ? str_replace('../', '../../../../apps/', $relationshipDetails['app_logo']) : null;

                    if(file_exists($appLogoPath)){
                        if (!unlink($appLogoPath)) {
                            $response = [
                                'success' => false,
                                'title' => 'Delete Multiple Relationships',
                                'message' => 'The app logo cannot be deleted due to an error.',
                                'messageType' => 'error'
                            ];
                            
                            echo json_encode($response);
                            exit;
                        }
                    }
                    
                    $this->relationshipModel->deleteRelationship($relationshipID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Relationships',
                'message' => 'The selected relationships have been deleted successfully.',
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
                $filename = "relationship_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $relationshipDetails = $this->relationshipModel->exportRelationship($columns, $ids);

                foreach ($relationshipDetails as $relationshipDetail) {
                    fputcsv($output, $relationshipDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "relationship_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $relationshipDetails = $this->relationshipModel->exportRelationship($columns, $ids);

                $rowNumber = 2;
                foreach ($relationshipDetails as $relationshipDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $relationshipDetail[$column]);
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
    public function getRelationshipDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $relationshipID = filter_input(INPUT_POST, 'relationship_id', FILTER_VALIDATE_INT);

        $checkRelationshipExist = $this->relationshipModel->checkRelationshipExist($relationshipID);
        $total = $checkRelationshipExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Relationship Details',
                'message' => 'The relationship does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $relationshipDetails = $this->relationshipModel->getRelationship($relationshipID);

        $response = [
            'success' => true,
            'relationshipName' => $relationshipDetails['relationship_name'] ?? null,
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>