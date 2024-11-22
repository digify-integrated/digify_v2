<?php
require('../../../../components/configurations/session.php');
require('../../../../components/configurations/config.php');
require('../../../../components/model/database-model.php');
require('../../../../components/model/system-model.php');
require('../../../../components/model/security-model.php');
require('../../../../apps/settings/authentication/model/authentication-model.php');

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$securityModel = new SecurityModel();
$authenticationModel = new AuthenticationModel($databaseModel, $securityModel);

if(isset($_POST['type']) && !empty($_POST['type'])){
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'notification setting table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateNotificationSettingTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $notificationSettingID = $row['notification_setting_id'];
                $notificationSettingName = $row['notification_setting_name'];
                $notificationSettingDescription = $row['notification_setting_description'];

                $notificationSettingIDEncrypted = $securityModel->encryptData($notificationSettingID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $notificationSettingID .'">
                                    </div>',
                    'NOTIFICATION_SETTING_NAME' => '<div class="d-flex align-items-center">
                                    <div>
                                        <div class="user-meta-info">
                                            <h6 class="mb-0">'. $notificationSettingName .'</h6>
                                            <small class="text-wrap fs-7 text-gray-500">'. $notificationSettingDescription .'</small>
                                        </div>
                                    </div>
                                </div>',
                    'LINK' => $pageLink .'&id='. $notificationSettingIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>