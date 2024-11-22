<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../notification-setting/model/notification-setting-model.php';
require_once '../../email-setting/model/email-setting-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new NotificationSettingController(new NotificationSettingModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new EmailSettingModel(new DatabaseModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class NotificationSettingController {
    private $notificationSettingModel;
    private $authenticationModel;
    private $emailSettingModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(NotificationSettingModel $notificationSettingModel, AuthenticationModel $authenticationModel,EmailSettingModel $emailSettingModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->notificationSettingModel = $notificationSettingModel;
        $this->authenticationModel = $authenticationModel;
        $this->emailSettingModel = $emailSettingModel;
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
                case 'add notification setting':
                    $this->addNotificationSetting();
                    break;
                case 'update notification setting':
                    $this->updateNotificationSetting();
                    break;
                case 'get notification setting details':
                    $this->getNotificationSettingDetails();
                    break;
                case 'delete notification setting':
                    $this->deleteNotificationSetting();
                    break;
                case 'delete multiple notification setting':
                    $this->deleteMultipleNotificationSetting();
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
    public function addNotificationSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $notificationSettingName = filter_input(INPUT_POST, 'notification_setting_name', FILTER_SANITIZE_STRING);
        $notificationSettingDescription = filter_input(INPUT_POST, 'notification_setting_description', FILTER_SANITIZE_STRING);
        
        $notificationSettingID = $this->notificationSettingModel->saveNotificationSetting(null, $notificationSettingName, $notificationSettingDescription, $userID);
    
        $response = [
            'success' => true,
            'notificationSettingID' => $this->securityModel->encryptData($notificationSettingID),
            'title' => 'Save Notification Setting',
            'message' => 'The notification setting has been saved successfully.',
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
    public function updateNotificationSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $notificationSettingID = filter_input(INPUT_POST, 'notification_setting_id', FILTER_VALIDATE_INT);
        $notificationSettingName = filter_input(INPUT_POST, 'notification_setting_name', FILTER_SANITIZE_STRING);
        $notificationSettingDescription = filter_input(INPUT_POST, 'notification_setting_description', FILTER_SANITIZE_STRING);
    
        $checkNotificationSettingExist = $this->notificationSettingModel->checkNotificationSettingExist($notificationSettingID);
        $total = $checkNotificationSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Notification Setting',
                'message' => 'The notification setting does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->notificationSettingModel->saveNotificationSetting($notificationSettingID, $notificationSettingName, $notificationSettingDescription, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Notification Setting',
            'message' => 'The notification setting has been saved successfully.',
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
    public function deleteNotificationSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $notificationSettingID = filter_input(INPUT_POST, 'notification_setting_id', FILTER_VALIDATE_INT);
        
        $checkNotificationSettingExist = $this->notificationSettingModel->checkNotificationSettingExist($notificationSettingID);
        $total = $checkNotificationSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Notification Setting',
                'message' => 'The notification setting does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->notificationSettingModel->deleteNotificationSetting($notificationSettingID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Notification Setting',
            'message' => 'The notification setting has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleNotificationSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['notification_setting_id']) && !empty($_POST['notification_setting_id'])) {
            $notificationSettingIDs = $_POST['notification_setting_id'];
    
            foreach($notificationSettingIDs as $notificationSettingID){
                $checkNotificationSettingExist = $this->notificationSettingModel->checkNotificationSettingExist($notificationSettingID);
                $total = $checkNotificationSettingExist['total'] ?? 0;

                if($total > 0){
                    $this->notificationSettingModel->deleteNotificationSetting($notificationSettingID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Notification Settings',
                'message' => 'The selected notification settings have been deleted successfully.',
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
                $filename = "notification_setting_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $notificationSettingDetails = $this->notificationSettingModel->exportNotificationSetting($columns, $ids);

                foreach ($notificationSettingDetails as $notificationSettingDetail) {
                    fputcsv($output, $notificationSettingDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "notification_setting_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $notificationSettingDetails = $this->notificationSettingModel->exportNotificationSetting($columns, $ids);

                $rowNumber = 2;
                foreach ($notificationSettingDetails as $notificationSettingDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $notificationSettingDetail[$column]);
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
    public function getNotificationSettingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $notificationSettingID = filter_input(INPUT_POST, 'notification_setting_id', FILTER_VALIDATE_INT);

        $checkNotificationSettingExist = $this->notificationSettingModel->checkNotificationSettingExist($notificationSettingID);
        $total = $checkNotificationSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Notification Setting Details',
                'message' => 'The notification setting does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $notificationSettingDetails = $this->notificationSettingModel->getNotificationSetting($notificationSettingID);

        $response = [
            'success' => true,
            'notificationSettingName' => $notificationSettingDetails['notification_setting_name'] ?? null,
            'notificationSettingDescription' => $notificationSettingDetails['notification_setting_description'] ?? null,
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>