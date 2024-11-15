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
        case 'file extension table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateFileExtensionTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $fileExtensionID = $row['file_extension_id'];
                $fileExtensionName = $row['file_extension_name'];
                $fileExtensionDescription = $row['file_extension_description'];
                $orderSequence = $row['order_sequence'];
                $appLogo = $systemModel->checkImage(str_replace('../', './apps/', $row['app_logo'])  ?? null, 'file extension logo');

                $fileExtensionIDEncrypted = $securityModel->encryptData($fileExtensionID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $fileExtensionID .'">
                                    </div>',
                    'APP_MODULE_NAME' => '<div class="d-flex align-items-center">
                                            <img src="'. $appLogo .'" alt="app-logo" width="45" />
                                            <div class="ms-3">
                                                <div class="user-meta-info">
                                                    <h6 class="mb-0">'. $fileExtensionName .'</h6>
                                                    <small class="text-wrap fs-7 text-gray-500">'. $fileExtensionDescription .'</small>
                                                </div>
                                            </div>
                                        </div>',
                    'LINK' => $pageLink .'&id='. $fileExtensionIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'file extension options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateFileExtensionOptions()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            if(!$multiple){
                $response[] = [
                    'id' => '',
                    'text' => '--'
                ];
            }            

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['file_extension_id'],
                    'text' => $row['file_extension_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>