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
require_once '../../menu-item/model/menu-item-model.php';

require_once '../../../../assets/libs/PhpSpreadsheet/autoload.php';

$controller = new MenuItemController(new MenuItemModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new AppModuleModel(new DatabaseModel), new MenuGroupModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class MenuItemController {
    private $menuItemModel;
    private $appModuleModel;
    private $menuGroupModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(MenuItemModel $menuItemModel, AuthenticationModel $authenticationModel, AppModuleModel $appModuleModel, MenuGroupModel $menuGroupModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->menuItemModel = $menuItemModel;
        $this->appModuleModel = $appModuleModel;
        $this->menuGroupModel = $menuGroupModel;
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
                case 'add menu item':
                    $this->addMenuItem();
                    break;
                case 'update menu item':
                    $this->updateMenuItem();
                    break;
                case 'get menu item details':
                    $this->getMenuItemDetails();
                    break;
                case 'delete menu item':
                    $this->deleteMenuItem();
                    break;
                case 'delete multiple menu item':
                    $this->deleteMultipleMenuItem();
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
    public function addMenuItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $menuItemName = filter_input(INPUT_POST, 'menu_item_name', FILTER_SANITIZE_STRING);
        $menuGroupID = filter_input(INPUT_POST, 'menu_group_id', FILTER_VALIDATE_INT);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
        $parentID = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);
        $menuItemIcon = filter_input(INPUT_POST, 'menu_item_icon', FILTER_SANITIZE_STRING);
        $menuItemURL = filter_input(INPUT_POST, 'menu_item_url', FILTER_SANITIZE_STRING);

        $menuGroupDetails = $this->menuGroupModel->getMenuGroup($menuGroupID);
        $menuGroupName = $menuGroupDetails['menu_group_name'] ?? '';
        $appModuleID = $menuGroupDetails['app_module_id'] ?? '';
        $appModuleName = $menuGroupDetails['app_module_name'] ?? '';

        $parentDetails = $this->menuItemModel->getMenuItem($parentID);
        $parentName = $parentDetails['menu_item_name'] ?? '';
        
        $menuItemID = $this->menuItemModel->saveMenuItem(null, $menuItemName, $menuItemURL, $menuItemIcon, $menuGroupID, $menuGroupName, $appModuleID, $appModuleName, $parentID, $parentName, $orderSequence, $userID);
    
        $response = [
            'success' => true,
            'menuItemID' => $this->securityModel->encryptData($menuItemID),
            'title' => 'Save Menu Item',
            'message' => 'The menu item has been saved successfully.',
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
    public function updateMenuItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
        $menuItemName = filter_input(INPUT_POST, 'menu_item_name', FILTER_SANITIZE_STRING);
        $menuGroupID = filter_input(INPUT_POST, 'menu_group_id', FILTER_VALIDATE_INT);
        $orderSequence = filter_input(INPUT_POST, 'order_sequence', FILTER_VALIDATE_INT);
        $parentID = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);
        $menuItemIcon = filter_input(INPUT_POST, 'menu_item_icon', FILTER_SANITIZE_STRING);
        $menuItemURL = filter_input(INPUT_POST, 'menu_item_url', FILTER_SANITIZE_STRING);
    
        $checkMenuItemExist = $this->menuItemModel->checkMenuItemExist($menuItemID);
        $total = $checkMenuItemExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Menu Item',
                'message' => 'The menu item does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $menuGroupDetails = $this->menuGroupModel->getMenuGroup($menuGroupID);
        $menuGroupName = $menuGroupDetails['menu_group_name'] ?? '';
        $appModuleID = $menuGroupDetails['app_module_id'] ?? '';
        $appModuleName = $menuGroupDetails['app_module_name'] ?? '';

        $parentDetails = $this->menuItemModel->getMenuItem($parentID);
        $parentName = $parentDetails['menu_item_name'] ?? '';

        $menuItemID = $this->menuItemModel->saveMenuItem($menuItemID, $menuItemName, $menuItemURL, $menuItemIcon, $menuGroupID, $menuGroupName, $appModuleID, $appModuleName, $parentID, $parentName, $orderSequence, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Menu Item',
            'message' => 'The menu item has been saved successfully.',
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
    public function deleteMenuItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
        
        $checkMenuItemExist = $this->menuItemModel->checkMenuItemExist($menuItemID);
        $total = $checkMenuItemExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Menu Item',
                'message' => 'The menu item does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->menuItemModel->deleteMenuItem($menuItemID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Menu Item',
            'message' => 'The menu item has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleMenuItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['menu_item_id']) && !empty($_POST['menu_item_id'])) {
            $menuItemIDs = $_POST['menu_item_id'];
    
            foreach($menuItemIDs as $menuItemID){
                $checkMenuItemExist = $this->menuItemModel->checkMenuItemExist($menuItemID);
                $total = $checkMenuItemExist['total'] ?? 0;

                if($total > 0){
                    $this->menuItemModel->deleteMenuItem($menuItemID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Menu Item',
                'message' => 'The selected menu items have been deleted successfully.',
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
                $filename = "menu_item_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $menuItemDetails = $this->menuItemModel->exportMenuItem($columns, $ids);

                foreach ($menuItemDetails as $menuItemDetail) {
                    fputcsv($output, $menuItemDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "menu_item_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $menuItemDetails = $this->menuItemModel->exportMenuItem($columns, $ids);

                $rowNumber = 2;
                foreach ($menuItemDetails as $menuItemDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $menuItemDetail[$column]);
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
    public function getMenuItemDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);

        $checkMenuItemExist = $this->menuItemModel->checkMenuItemExist($menuItemID);
        $total = $checkMenuItemExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Menu Item Details',
                'message' => 'The menu item does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $menuItemDetails = $this->menuItemModel->getMenuItem($menuItemID);

        $response = [
            'success' => true,
            'menuItemName' => $menuItemDetails['menu_item_name'] ?? null,
            'menuItemURL' => $menuItemDetails['menu_item_url'] ?? null,
            'menuItemIcon' => $menuItemDetails['menu_item_icon'] ?? null,
            'menuGroupID' => $menuItemDetails['menu_group_id'] ?? null,
            'menuGroupName' => $menuItemDetails['menu_group_name'] ?? null,
            'parentID' => $menuItemDetails['parent_id'] ?? null,
            'parentName' => $menuItemDetails['parent_name'] ?? null,
            'orderSequence' => $menuItemDetails['order_sequence'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>