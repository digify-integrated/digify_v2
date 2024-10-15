<?php
require_once '../../global/config/session.php';
require_once '../../global/config/config.php';
require_once '../../global/model/database-model.php';
require_once '../../global/model/system-model.php';
require_once '../../notification-setting/model/notification-setting-model.php';
require_once '../../global/model/security-model.php';
require_once '../../global/model/global-model.php';

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$notificationSettingModel = new NotificationSettingModel($databaseModel);
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
        # Type: notification setting table
        # Description:
        # Generates the notification setting table.
        #
        # Parameters: None
        #
        # Returns: Array
        #
        # -------------------------------------------------------------
        case 'notification setting table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateNotificationSettingTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $notificationSettingDeleteAccess = $globalModel->checkAccessRights($userID, $pageID, 'delete');

            foreach ($options as $row) {
                $notificationSettingID = $row['notification_setting_id'];
                $notificationSettingName = $row['notification_setting_name'];
                $description = $row['notification_setting_description'];

                $notificationSettingIDEncrypted = $securityModel->encryptData($notificationSettingID);

                $deleteButton = '';
                if($notificationSettingDeleteAccess['total'] > 0){
                    $deleteButton = '<a href="javascript:void(0);" class="text-danger ms-3 delete-notification-setting" data-notification-setting-id="' . $notificationSettingID . '" title="Delete Notification Setting">
                                        <i class="ti ti-trash fs-5"></i>
                                    </a>';
                }

                $response[] = [
                    'CHECK_BOX' => '<input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $notificationSettingID .'">',
                    'NOTIFICATION_SETTING' => '<div class="d-flex align-items-center">
                                                <div class="ms-3">
                                                    <div class="user-meta-info">
                                                        <h6 class="user-name mb-0">'. $notificationSettingName .'</h6>
                                                        <small>'. $description .'</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>',
                    'ACTION' => '<div class="action-btn">
                                    <a href="'. $pageLink .'&id='. $notificationSettingIDEncrypted .'" class="text-info" title="View Details">
                                        <i class="ti ti-eye fs-5"></i>
                                    </a>
                                   '. $deleteButton .'
                                </div>'
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>