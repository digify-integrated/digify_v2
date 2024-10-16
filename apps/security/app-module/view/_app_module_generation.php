<?php
require('../../../../components/configurations/session.php');
require('../../../../components/configurations/config.php');
require('../../../../components/model/database-model.php');
require('../../../../components/model/system-model.php');
require('../../../../components/model/security-model.php');
require('../../../../apps/security/authentication/model/authentication-model.php');

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$securityModel = new SecurityModel();
$authenticationModel = new AuthenticationModel($databaseModel, $securityModel);

if(isset($_POST['type']) && !empty($_POST['type'])){
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'app module table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateAppModuleTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $appModuleID = $row['app_module_id'];
                $appModuleName = $row['app_module_name'];
                $appModuleDescription = $row['app_module_description'];
                $orderSequence = $row['order_sequence'];
                $appLogo = $systemModel->checkImage(str_replace('../', './apps/', $row['app_logo'])  ?? null, 'app module logo');

                $appModuleIDEncrypted = $securityModel->encryptData($appModuleID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $appModuleID .'">
                                    </div>',
                    'APP_MODULE_NAME' => '<div class="d-flex align-items-center">
                                            <img src="'. $appLogo .'" alt="app-logo" width="45" />
                                            <div class="ms-3">
                                                <div class="user-meta-info">
                                                    <h6 class="mb-0">'. $appModuleName .'</h6>
                                                    <small class="text-wrap">'. $appModuleDescription .'</small>
                                                </div>
                                            </div>
                                        </div>',
                    'LINK' => $pageLink .'&id='. $appModuleIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'app module options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateAppModuleOptions()');
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
                    'id' => $row['app_module_id'],
                    'text' => $row['app_module_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>