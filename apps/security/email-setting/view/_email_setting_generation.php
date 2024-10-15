<?php
require_once '../../global/config/session.php';
require_once '../../global/config/config.php';
require_once '../../global/model/database-model.php';
require_once '../../global/model/system-model.php';
require_once '../../email-setting/model/email-setting-model.php';
require_once '../../global/model/security-model.php';
require_once '../../global/model/global-model.php';

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$emailSettingModel = new EmailSettingModel($databaseModel);
$securityModel = new SecurityModel();
$globalModel = new GlobalModel($databaseModel, $securityModel);

if(isset($_POST['type']) && !empty($_POST['type'])){
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        #
        # Type: email setting table
        # Description:
        # Generates the email setting table.
        #
        # Parameters: None
        #
        # Returns: Array
        #
        # -------------------------------------------------------------
        case 'email setting table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateEmailSettingTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $emailSettingDeleteAccess = $globalModel->checkAccessRights($userID, $pageID, 'delete');

            foreach ($options as $row) {
                $emailSettingID = $row['email_setting_id'];
                $emailSettingName = $row['email_setting_name'];
                $description = $row['email_setting_description'];

                $emailSettingIDEncrypted = $securityModel->encryptData($emailSettingID);

                $deleteButton = '';
                if($emailSettingDeleteAccess['total'] > 0){
                    $deleteButton = '<a href="javascript:void(0);" class="text-danger ms-3 delete-email-setting" data-email-setting-id="' . $emailSettingID . '" title="Delete Email Setting">
                                        <i class="ti ti-trash fs-5"></i>
                                    </a>';
                }

                $response[] = [
                    'CHECK_BOX' => '<input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $emailSettingID .'">',
                    'EMAIL_SETTING' => '<div class="d-flex align-items-center">
                                                <div class="ms-3">
                                                    <div class="user-meta-info">
                                                        <h6 class="user-name mb-0">'. $emailSettingName .'</h6>
                                                        <small>'. $description .'</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>',
                    'ACTION' => '<div class="action-btn">
                                    <a href="'. $pageLink .'&id='. $emailSettingIDEncrypted .'" class="text-info" title="View Details">
                                        <i class="ti ti-eye fs-5"></i>
                                    </a>
                                   '. $deleteButton .'
                                </div>'
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        #
        # Type: email setting options
        # Description:
        # Generates the email setting options.
        #
        # Parameters: None
        #
        # Returns: Array
        #
        # -------------------------------------------------------------
        case 'email setting options':
            $sql = $databaseModel->getConnection()->prepare('CALL generateEmailSettingOptions()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $response[] = [
                'id' => '',
                'text' => '--'
            ];

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['email_setting_id'],
                    'text' => $row['email_setting_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>