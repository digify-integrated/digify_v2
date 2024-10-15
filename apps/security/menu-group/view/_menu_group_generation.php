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
        case 'menu group table':
            $filterAppModule = isset($_POST['app_module_filter']) && is_array($_POST['app_module_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['app_module_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateMenuGroupTable(:filterAppModule)');
            $sql->bindValue(':filterAppModule', $filterAppModule, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $menuGroupID = $row['menu_group_id'];
                $menuGroupName = $row['menu_group_name'];
                $appModuleName = $row['app_module_name'];
                $orderSequence = $row['order_sequence'];

                $menuGroupIDEncrypted = $securityModel->encryptData($menuGroupID);

                $response[] = [
                    'CHECK_BOX' => '<input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $menuGroupID .'">',
                    'MENU_GROUP_NAME' => $menuGroupName,
                    'APP_MODULE_NAME' => $appModuleName,
                    'ORDER_SEQUENCE' => $orderSequence,
                    'LINK' => $pageLink .'&id='. $menuGroupIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'menu group options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateMenuGroupOptions()');
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
                    'id' => $row['menu_group_id'],
                    'text' => $row['menu_group_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>