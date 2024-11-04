<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../security-setting/model/security-setting-model.php';
require_once '../../role/model/role-model.php';
require_once '../../menu-item/model/menu-item-model.php';
require_once '../../system-action/model/system-action-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new RoleController(new RoleModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new MenuItemModel(new DatabaseModel), new SystemActionModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class RoleController {
    private $roleModel;
    private $menuItemModel;
    private $systemActionModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(RoleModel $roleModel, AuthenticationModel $authenticationModel, MenuItemModel $menuItemModel, SystemActionModel $systemActionModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->roleModel = $roleModel;
        $this->menuItemModel = $menuItemModel;
        $this->systemActionModel = $systemActionModel;
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
                case 'add role':
                    $this->addRole();
                    break;
                case 'update role':
                    $this->updateRole();
                    break;
                case 'update role permission':
                    $this->updateRolePermission();
                    break;
                case 'update menu item permission':
                    $this->updateMenuItemPermission();
                    break;
                case 'update role system action permission':
                    $this->updateRoleSystemActionPermission();
                    break;
                case 'update system action permission':
                    $this->updateSystemActionPermission();
                    break;
                case 'assign menu item role permission':
                    $this->assignMenuItemRolePermission();
                    break;
                case 'assign system action role permission':
                    $this->assignSystemActionRolePermission();
                    break;
                case 'assign role menu item permission':
                    $this->assignRoleMenuItemPermission();
                    break;
                case 'assign role system action permission':
                    $this->assignRoleSystemActionPermission();
                    break;
                case 'get role details':
                    $this->getRoleDetails();
                    break;
                case 'delete role':
                    $this->deleteRole();
                    break;
                case 'delete multiple role':
                    $this->deleteMultipleRole();
                    break;
                case 'delete role permission':
                    $this->deleteRolePermission();
                    break;
                case 'delete menu item permission':
                    $this->deleteRolePermission();
                    break;
                case 'delete role system action permission':
                    $this->deleteRoleSystemActionPermission();
                    break;
                case 'delete system action permission':
                    $this->deleteRoleSystemActionPermission();
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
    public function addRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $roleName = filter_input(INPUT_POST, 'role_name', FILTER_SANITIZE_STRING);
        $roleDescription = filter_input(INPUT_POST, 'role_description', FILTER_SANITIZE_STRING);
        
        $roleID = $this->roleModel->saveRole(null, $roleName, $roleDescription, $userID);
    
        $response = [
            'success' => true,
            'roleID' => $this->securityModel->encryptData($roleID),
            'title' => 'Save Role',
            'message' => 'The role has been saved successfully.',
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
    public function updateRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
        $roleName = filter_input(INPUT_POST, 'role_name', FILTER_SANITIZE_STRING);
        $roleDescription = filter_input(INPUT_POST, 'role_description', FILTER_SANITIZE_STRING);
    
        $checkRoleExist = $this->roleModel->checkRoleExist($roleID);
        $total = $checkRoleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Role',
                'message' => 'The role does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $roleID = $this->roleModel->saveRole($roleID, $roleName, $roleDescription, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Role',
            'message' => 'The role has been saved successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateRolePermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $rolePermissionID = filter_input(INPUT_POST, 'role_permission_id', FILTER_VALIDATE_INT);
        $accessType = filter_input(INPUT_POST, 'access_type', FILTER_SANITIZE_STRING);
        $access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_INT);
    
        $checkRolePermissionExist = $this->roleModel->checkRolePermissionExist($rolePermissionID);
        $total = $checkRolePermissionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Role Permission',
                'message' => 'The role permission does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->roleModel->updateRolePermission($rolePermissionID, $accessType, $access, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Role Permission',
            'message' => 'The role permission has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateMenuItemPermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $rolePermissionID = filter_input(INPUT_POST, 'role_permission_id', FILTER_VALIDATE_INT);
        $accessType = filter_input(INPUT_POST, 'access_type', FILTER_SANITIZE_STRING);
        $access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_INT);
    
        $checkRolePermissionExist = $this->roleModel->checkRolePermissionExist($rolePermissionID);
        $total = $checkRolePermissionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Menu Item Permission',
                'message' => 'The menu item permission does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->roleModel->updateRolePermission($rolePermissionID, $accessType, $access, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Menu Item Permission',
            'message' => 'The menu item permission has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateRoleSystemActionPermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $rolePermissionID = filter_input(INPUT_POST, 'role_permission_id', FILTER_VALIDATE_INT);
        $access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_INT);
    
        $checkRoleSystemActionPermissionExist = $this->roleModel->checkRoleSystemActionPermissionExist($rolePermissionID);
        $total = $checkRoleSystemActionPermissionExist['total'] ?? 0;
        
        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update Role Permission',
                'message' => 'The role permission does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->roleModel->updateRoleSystemActionPermission($rolePermissionID, $access, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update Role Permission',
            'message' => 'The role permission has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateSystemActionPermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $rolePermissionID = filter_input(INPUT_POST, 'role_permission_id', FILTER_VALIDATE_INT);
        $access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_INT);
    
        $checkRoleSystemActionPermissionExist = $this->roleModel->checkRoleSystemActionPermissionExist($rolePermissionID);
        $total = $checkRoleSystemActionPermissionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Update System Action Permission',
                'message' => 'The role permission does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->roleModel->updateRoleSystemActionPermission($rolePermissionID, $access, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Update System Action Permission',
            'message' => 'The system action permission has been updated successfully.',
            'messageType' => 'success'
        ];
        
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Assign methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function assignMenuItemRolePermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['menu_item_id']) && !empty($_POST['menu_item_id'])) {
            if(!isset($_POST['role_id']) || empty($_POST['role_id'])){
                $response = [
                    'success' => false,
                    'title' => 'Permission Selection Required',
                    'message' => 'Please select the role(s) you wish to assign to the menu item.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $userID = $_SESSION['user_account_id'];
            $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
            $roleIDs = $_POST['role_id'];

            $menuItemDetails = $this->menuItemModel->getMenuItem($menuItemID);
            $menuItemName = $menuItemDetails['menu_item_name'] ?? null;

            foreach ($roleIDs as $roleID) {
                $roleDetails = $this->roleModel->getRole($roleID);
                $roleName = $roleDetails['role_name'] ?? null;

                $this->roleModel->insertRolePermission($roleID, $roleName, $menuItemID, $menuItemName, $userID);
            }
    
            $response = [
                'success' => true,
                'title' => 'Assign Role Success',
                'message' => 'The role has been assigned successfully.',
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
    public function assignSystemActionRolePermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['system_action_id']) && !empty($_POST['system_action_id'])) {
            if(!isset($_POST['role_id']) || empty($_POST['role_id'])){
                $response = [
                    'success' => false,
                    'title' => 'Permission Selection Required',
                    'message' => 'Please select the role(s) you wish to assign to the menu item.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $userID = $_SESSION['user_account_id'];
            $systemActionID = filter_input(INPUT_POST, 'system_action_id', FILTER_VALIDATE_INT);
            $roleIDs = $_POST['role_id'];

            $systemActionDetails = $this->systemActionModel->getSystemAction($systemActionID);
            $systemActionName = $systemActionDetails['system_action_name'] ?? null;

            foreach ($roleIDs as $roleID) {
                $roleDetails = $this->roleModel->getRole($roleID);
                $roleName = $roleDetails['role_name'] ?? null;

                $this->roleModel->insertRoleSystemActionPermission($roleID, $roleName, $systemActionID, $systemActionName, $userID);
            }
    
            $response = [
                'success' => true,
                'title' => 'Assign Role Success',
                'message' => 'The role has been assigned successfully.',
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
    public function assignRoleMenuItemPermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['role_id']) && !empty($_POST['role_id'])) {
            if(!isset($_POST['menu_item_id']) || empty($_POST['menu_item_id'])){
                $response = [
                    'success' => false,
                    'title' => 'Permission Selection Required',
                    'message' => 'Please select the menu item(s) you wish to assign to the role.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $userID = $_SESSION['user_account_id'];
            $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $menuItemIDs = $_POST['menu_item_id'];
    
            $roleDetails = $this->roleModel->getRole($roleID);
            $roleName = $roleDetails['role_name'] ?? null;

            foreach ($menuItemIDs as $menuItemID) {
                $menuItemDetails = $this->menuItemModel->getMenuItem($menuItemID);
                $menuItemName = $menuItemDetails['menu_item_name'] ?? null;

                $this->roleModel->insertRolePermission($roleID, $roleName, $menuItemID, $menuItemName, $userID);
            }
    
            $response = [
                'success' => true,
                'title' => 'Assign Menu Item Success',
                'message' => 'The menu item has been assigned successfully.',
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
    public function assignRoleSystemActionPermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['role_id']) && !empty($_POST['role_id'])) {
            if(!isset($_POST['system_action_id']) || empty($_POST['system_action_id'])){
                $response = [
                    'success' => false,
                    'title' => 'Permission Selection Required',
                    'message' => 'Please select the system action(s) you wish to assign to the role.',
                    'messageType' => 'error'
                ];
                
                echo json_encode($response);
                exit;
            }

            $userID = $_SESSION['user_account_id'];
            $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $systemActionIDs = $_POST['system_action_id'];

            $roleDetails = $this->roleModel->getRole($roleID);
            $roleName = $roleDetails['role_name'] ?? null;

            foreach ($systemActionIDs as $systemActionID) {
                $systemActionDetails = $this->systemActionModel->getSystemAction($systemActionID);
                $systemActionName = $systemActionDetails['system_action_name'] ?? null;

                $this->roleModel->insertRoleSystemActionPermission($roleID, $roleName, $systemActionID, $systemActionName, $userID);
            }
    
            $response = [
                'success' => true,
                'title' => 'Assign Role Success',
                'message' => 'The role has been assigned successfully.',
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
    #   Delete methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
        
        $checkRoleExist = $this->roleModel->checkRoleExist($roleID);
        $total = $checkRoleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Role',
                'message' => 'The role does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->roleModel->deleteRole($roleID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Role',
            'message' => 'The role has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleRole() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['role_id']) && !empty($_POST['role_id'])) {
            $roleIDs = $_POST['role_id'];
    
            foreach($roleIDs as $roleID){
                $checkRoleExist = $this->roleModel->checkRoleExist($roleID);
                $total = $checkRoleExist['total'] ?? 0;

                if($total > 0){
                    $this->roleModel->deleteRole($roleID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Role',
                'message' => 'The selected roles have been deleted successfully.',
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
    public function deleteRolePermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $rolePermissionID = filter_input(INPUT_POST, 'role_permission_id', FILTER_VALIDATE_INT);
        
        $checkRolePermissionExist = $this->roleModel->checkRolePermissionExist($rolePermissionID);
        $total = $checkRolePermissionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Role Permission Error',
                'message' => 'The role permission does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->roleModel->deleteRolePermission($rolePermissionID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Role Permission Success',
            'message' => 'The role permission has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteRoleSystemActionPermission() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $rolePermissionID = filter_input(INPUT_POST, 'role_permission_id', FILTER_VALIDATE_INT);
        
        $checkRoleSystemActionPermissionExist = $this->roleModel->checkRoleSystemActionPermissionExist($rolePermissionID);
        $total = $checkRoleSystemActionPermissionExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Role Permission Error',
                'message' => 'The role permission does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->roleModel->deleteRoleSystemActionPermission($rolePermissionID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Role Permission Success',
            'message' => 'The role permission has been deleted successfully.',
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
                $filename = "role_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $roleDetails = $this->roleModel->exportRole($columns, $ids);

                foreach ($roleDetails as $roleDetail) {
                    fputcsv($output, $roleDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "role_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $roleDetails = $this->roleModel->exportRole($columns, $ids);

                $rowNumber = 2;
                foreach ($roleDetails as $roleDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $roleDetail[$column]);
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
    public function getRoleDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

        $checkRoleExist = $this->roleModel->checkRoleExist($roleID);
        $total = $checkRoleExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Role Details',
                'message' => 'The role does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $roleDetails = $this->roleModel->getRole($roleID);

        $response = [
            'success' => true,
            'roleName' => $roleDetails['role_name'] ?? null,
            'roleDescription' => $roleDetails['role_description'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>