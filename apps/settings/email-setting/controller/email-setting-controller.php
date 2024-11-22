<?php
session_start();

require_once '../../../../components/configurations/config.php';
require_once '../../../../components/model/database-model.php';
require_once '../../../../components/model/security-model.php';
require_once '../../../../components/model/system-model.php';
require_once '../../authentication/model/authentication-model.php';
require_once '../../email-setting/model/email-setting-model.php';
require_once '../../security-setting/model/security-setting-model.php';

require_once '../../../../assets/plugins/PhpSpreadsheet/autoload.php';

$controller = new EmailSettingController(new EmailSettingModel(new DatabaseModel), new AuthenticationModel(new DatabaseModel, new SecurityModel), new SecurityModel(), new SystemModel());
$controller->handleRequest();

# -------------------------------------------------------------
class EmailSettingController {
    private $emailSettingModel;
    private $authenticationModel;
    private $securityModel;
    private $systemModel;

    # -------------------------------------------------------------
    public function __construct(EmailSettingModel $emailSettingModel, AuthenticationModel $authenticationModel, SecurityModel $securityModel, SystemModel $systemModel) {
        $this->emailSettingModel = $emailSettingModel;
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
                case 'add email setting':
                    $this->addEmailSetting();
                    break;
                case 'update email setting':
                    $this->updateEmailSetting();
                    break;
                case 'get email setting details':
                    $this->getEmailSettingDetails();
                    break;
                case 'delete email setting':
                    $this->deleteEmailSetting();
                    break;
                case 'delete multiple email setting':
                    $this->deleteMultipleEmailSetting();
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
    public function addEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $userID = $_SESSION['user_account_id'];
        $emailSettingName = filter_input(INPUT_POST, 'email_setting_name', FILTER_SANITIZE_STRING);
        $emailSettingDescription = filter_input(INPUT_POST, 'email_setting_description', FILTER_SANITIZE_STRING);
        $mailHost = filter_input(INPUT_POST, 'mail_host', FILTER_SANITIZE_STRING);
        $port = filter_input(INPUT_POST, 'port', FILTER_SANITIZE_STRING);
        $mailUsername = filter_input(INPUT_POST, 'mail_username', FILTER_SANITIZE_STRING);
        $mailPassword = $this->securityModel->encryptData($_POST['mail_password']);
        $mailFromName = filter_input(INPUT_POST, 'mail_from_name', FILTER_SANITIZE_STRING);
        $mailFromEmail = filter_input(INPUT_POST, 'mail_from_email', FILTER_SANITIZE_STRING);
        $mailEncryption = filter_input(INPUT_POST, 'mail_encryption', FILTER_SANITIZE_STRING);
        $smtpAuth = filter_input(INPUT_POST, 'smtp_auth', FILTER_VALIDATE_INT);
        $smtpAutoTLS = filter_input(INPUT_POST, 'smtp_auto_tls', FILTER_VALIDATE_INT);
        
        $emailSettingID = $this->emailSettingModel->saveEmailSetting(null, $emailSettingName, $emailSettingDescription, $mailHost, $port, $smtpAuth, $smtpAutoTLS, $mailUsername, $mailPassword, $mailEncryption, $mailFromName, $mailFromEmail, $userID);
    
        $response = [
            'success' => true,
            'emailSettingID' => $this->securityModel->encryptData($emailSettingID),
            'title' => 'Save Email Setting',
            'message' => 'The email setting has been saved successfully.',
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
    public function updateEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        
        $userID = $_SESSION['user_account_id'];
        $emailSettingID = filter_input(INPUT_POST, 'email_setting_id', FILTER_VALIDATE_INT);
        $emailSettingName = filter_input(INPUT_POST, 'email_setting_name', FILTER_SANITIZE_STRING);
        $emailSettingDescription = filter_input(INPUT_POST, 'email_setting_description', FILTER_SANITIZE_STRING);
        $mailHost = filter_input(INPUT_POST, 'mail_host', FILTER_SANITIZE_STRING);
        $port = filter_input(INPUT_POST, 'port', FILTER_SANITIZE_STRING);
        $mailUsername = filter_input(INPUT_POST, 'mail_username', FILTER_SANITIZE_STRING);
        $mailPassword = $this->securityModel->encryptData($_POST['mail_password']);
        $mailFromName = filter_input(INPUT_POST, 'mail_from_name', FILTER_SANITIZE_STRING);
        $mailFromEmail = filter_input(INPUT_POST, 'mail_from_email', FILTER_SANITIZE_STRING);
        $mailEncryption = filter_input(INPUT_POST, 'mail_encryption', FILTER_SANITIZE_STRING);
        $smtpAuth = filter_input(INPUT_POST, 'smtp_auth', FILTER_VALIDATE_INT);
        $smtpAutoTLS = filter_input(INPUT_POST, 'smtp_auto_tls', FILTER_VALIDATE_INT);
    
        $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
        $total = $checkEmailSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Save Email Setting',
                'message' => 'The email setting does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $this->emailSettingModel->saveEmailSetting($emailSettingID, $emailSettingName, $emailSettingDescription, $mailHost, $port, $smtpAuth, $smtpAutoTLS, $mailUsername, $mailPassword, $mailEncryption, $mailFromName, $mailFromEmail, $userID);
            
        $response = [
            'success' => true,
            'title' => 'Save Email Setting',
            'message' => 'The email setting has been saved successfully.',
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
    public function deleteEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $emailSettingID = filter_input(INPUT_POST, 'email_setting_id', FILTER_VALIDATE_INT);
        
        $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
        $total = $checkEmailSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Delete Email Setting',
                'message' => 'The email setting does not exist.',
                'messageType' => 'error'
            ];
                
            echo json_encode($response);
            exit;
        }

        $this->emailSettingModel->deleteEmailSetting($emailSettingID);
                
        $response = [
            'success' => true,
            'title' => 'Delete Email Setting',
            'message' => 'The email setting has been deleted successfully.',
            'messageType' => 'success'
        ];
            
        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function deleteMultipleEmailSetting() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (isset($_POST['email_setting_id']) && !empty($_POST['email_setting_id'])) {
            $emailSettingIDs = $_POST['email_setting_id'];
    
            foreach($emailSettingIDs as $emailSettingID){
                $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
                $total = $checkEmailSettingExist['total'] ?? 0;

                if($total > 0){
                    $this->emailSettingModel->deleteEmailSetting($emailSettingID);
                }
            }
                
            $response = [
                'success' => true,
                'title' => 'Delete Multiple Email Settings',
                'message' => 'The selected email settings have been deleted successfully.',
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
                $filename = "email_setting_export_" . date('Y-m-d_H-i-s') . ".csv";
            
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                
                $output = fopen('php://output', 'w');

                fputcsv($output, $tableColumns);
                
                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $emailSettingDetails = $this->emailSettingModel->exportEmailSetting($columns, $ids);

                foreach ($emailSettingDetails as $emailSettingDetail) {
                    fputcsv($output, $emailSettingDetail);
                }

                fclose($output);
                exit;
            }
            else {
                ob_start();
                $filename = "email_setting_export_" . date('Y-m-d_H-i-s') . ".xlsx";

                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $colIndex = 'A';
                foreach ($tableColumns as $column) {
                    $sheet->setCellValue($colIndex . '1', ucfirst(str_replace('_', ' ', $column)));
                    $colIndex++;
                }

                $columns = implode(", ", $tableColumns);
                
                $ids = implode(",", array_map('intval', $exportIDs));
                $emailSettingDetails = $this->emailSettingModel->exportEmailSetting($columns, $ids);

                $rowNumber = 2;
                foreach ($emailSettingDetails as $emailSettingDetail) {
                    $colIndex = 'A';
                    foreach ($tableColumns as $column) {
                        $sheet->setCellValue($colIndex . $rowNumber, $emailSettingDetail[$column]);
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
    public function getEmailSettingDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
    
        $userID = $_SESSION['user_account_id'];
        $emailSettingID = filter_input(INPUT_POST, 'email_setting_id', FILTER_VALIDATE_INT);

        $checkEmailSettingExist = $this->emailSettingModel->checkEmailSettingExist($emailSettingID);
        $total = $checkEmailSettingExist['total'] ?? 0;

        if($total === 0){
            $response = [
                'success' => false,
                'notExist' => true,
                'title' => 'Get Email Setting Details',
                'message' => 'The email setting does not exist.',
                'messageType' => 'error'
            ];
            
            echo json_encode($response);
            exit;
        }

        $emailSettingDetails = $this->emailSettingModel->getEmailSetting($emailSettingID);

        $response = [
            'success' => true,
            'emailSettingName' => $emailSettingDetails['email_setting_name'] ?? null,
            'emailSettingDescription' => $emailSettingDetails['email_setting_description'] ?? null,
            'mailHost' => $emailSettingDetails['mail_host'] ?? null,
            'port' => $emailSettingDetails['port'] ?? null,
            'smtpAuth' => $emailSettingDetails['smtp_auth'] ?? null,
            'smtpAutoTLS' => $emailSettingDetails['smtp_auto_tls'] ?? null,
            'mailUsername' => $emailSettingDetails['mail_username'] ?? null,
            'mailPassword' => $this->securityModel->decryptData($emailSettingDetails['mail_password']),
            'mailEncryption' => $emailSettingDetails['mail_encryption'] ?? null,
            'mailFromName' => $emailSettingDetails['mail_from_name'] ?? null,
            'mailFromEmail' => $emailSettingDetails['mail_from_email'] ?? null
        ];

        echo json_encode($response);
        exit;
    }
    # -------------------------------------------------------------
}
# -------------------------------------------------------------

?>