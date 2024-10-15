<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../app-module/model/app-module-model.php';
require_once '../../menu-group/model/menu-group-model.php';

require_once '../../../../assets/libs/PhpSpreadsheet/autoload.php';

$controller = new MenuGroupController(new MenuGroupModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new AppModuleModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class MenuGroupController {
    private $menuGroupModel;
    private $appModuleModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(MenuGroupModel $menuGroupModel, AuthenticationModel $authenticationModel, AppModuleModel $appModuleModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->menuGroupModel = $menuGroupModel;
        $this->appModuleModel = $appModuleModel;
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
                case 'add menu group':
                    $this->addMenuGroup();
                    break;
                case 'update menu group':
                    $this->updateMenuGroup();
                    break;
                case 'get menu group details':
                    $this->getMenuGroupDetails();
                    break;
                case 'delete menu group':
                    $this->deleteMenuGroup();
                    break;
                case 'delete multiple menu group':
                    $this->deleteMultipleMenuGroup();
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
    public function addMenuGroup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $menuGroupName = filter_input(INPUT_POST, 'menu_group_name', FILTER_SANITIZE_STRING);
        $appModuleID = filter_input(INPUT_POST, 'app_module_id', FILTER_VALIDATE_INT);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);

        $appModuleDetails = $this->appModuleModel->getAppModule($appModuleID);
        $appModuleName = $appModuleDetails['app_module_name'] ?? '';
        
        $menuGroupID = $this->menuGroupModel->saveMenuGroup(null, $menuGroupName, $appModuleID, $appModuleName, $orderSequence, $userID);
    
        $response = [
            'success' => true,
            'menuGroupID' => $this->securityModel->encryptData($menuGroupID),
            'title' => 'Save Menu Group',
            'message' => 'The menu group has been saved successfully.',
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
    public function updateMenuGroup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $menuGroupID = filter_input(INPUT_POST, 'menu_group_id', FILTER_VALIDATE_INT);
        $menuGroupName = filter_input(INPUT_POST, 'menu_group_name', FILTER_SANITIZE_STRING);
        $appModuleID = filter_input(INPUT_POST, 'app_module_id', FILTER_VALIDATE_INT);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
    
        $checkMenuGroupExist = $this->menuGroupModel->checkMenuGroupExist($menuGroupID);
        $total = $checkMenuGroupExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Menu Group',
                'message' => 'The menu group does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $appModuleDetails = $this->appModuleModel->getAppModule($appModuleID);
        $appModuleName = $appModuleDetails['app_module_name'] ?? '';

        $this->menuGroupModel->saveMenuGroup($menuGroupID, $menuGroupName, $appModuleID, $appModuleName, $orderSequence, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Menu Group',
            'message' => 'The menu group has been saved successfully.',
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
    public function deleteMenuGroup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $menuGroupID = filter_input(INPUT_POST, 'menu_group_id', FILTER_VALIDATE_INT);
        
        $checkMenuGroupExist = $this->menuGroupModel->checkMenuGroupExist($menuGroupID);
        $total = $checkMenuGroupExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Menu Group',
                'message' => 'The menu group does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->menuGroupModel->deleteMenuGroup($menuGroupID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Menu Group',
            'message' => 'The menu group has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleMenuGroup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['menu_group_id']) && !empty($_POST['menu_group_id'])) {
            $menuGroupIDs = $_POST['menu_group_id'];
    
            foreach($menuGroupIDs as $menuGroupID){
                $checkMenuGroupExist = $this->menuGroupModel->checkMenuGroupExist($menuGroupID);
                $total = $checkMenuGroupExist['total'] ?? 0;

                if($total > 0){
                    $this->menuGroupModel->deleteMenuGroup($menuGroupID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Menu Group',
                'message' => 'The selected menu groups have been deleted successfully.',
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
                $filename = "menu_group_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $menuGroupDetails = $this->menuGroupModel->exportMenuGroup($columns, $ids);

                foreach ($menuGroupDetails as $menuGroupDetail) {
                    fputcsv($output, $menuGroupDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "menu_group_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $menuGroupDetails = $this->menuGroupModel->exportMenuGroup($columns, $ids);

                $rowNumber = 2;
                foreach ($menuGroupDetails as $menuGroupDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $menuGroupDetail[$column]);
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
    public function getMenuGroupDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $menuGroupID = filter_input(INPUT_POST, 'menu_group_id', FILTER_VALIDATE_INT);

        $checkMenuGroupExist = $this->menuGroupModel->checkMenuGroupExist($menuGroupID);
        $total = $checkMenuGroupExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Menu Group Details',
                'message' => 'The menu group does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $menuGroupDetails = $this->menuGroupModel->getMenuGroup($menuGroupID);

        $response = [
            'success' => true,
            'menuGroupName' => $menuGroupDetails['menu_group_name'] ?? null,
            'appModuleID' => $menuGroupDetails['app_module_id'] ?? null,
            'appModuleName' => $menuGroupDetails['app_module_name'] ?? null,
            'orderSequence' => $menuGroupDetails['order_sequence'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>