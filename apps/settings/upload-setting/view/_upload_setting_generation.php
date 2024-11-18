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
        case 'upload setting table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateUploadSettingTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $uploadSettingID = $row['upload_setting_id'];
                $uploadSettingName = $row['upload_setting_name'];
                $uploadSettingDescription = $row['upload_setting_description'];
                $maxFileSize = $row['max_file_size'];

                $uploadSettingIDEncrypted = $securityModel->encryptData($uploadSettingID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $uploadSettingID .'">
                                    </div>',
                    'UPLOAD_SETTING_NAME' => '<div class="d-flex align-items-center">
                                    <div>
                                        <div class="user-meta-info">
                                            <h6 class="mb-0">'. $uploadSettingName .'</h6>
                                            <small class="text-wrap fs-7 text-gray-500">'. $uploadSettingDescription .'</small>
                                        </div>
                                    </div>
                                </div>',
                    'MAX_FILE_SIZE' => $maxFileSize . ' kb',
                    'LINK' => $pageLink .'&id='. $uploadSettingIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>