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
            $filterFileType = isset($_POST['file_type_filter']) && is_array($_POST['file_type_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['file_type_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateFileExtensionTable(:filterFileType)');
            $sql->bindValue(':filterFileType', $filterFileType, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $fileExtensionID = $row['file_extension_id'];
                $fileExtensionName = $row['file_extension_name'];
                $fileExtension = $row['file_extension'];
                $fileTypeName = $row['file_type_name'];

                $fileExtensionIDEncrypted = $securityModel->encryptData($fileExtensionID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $fileExtensionID .'">
                                    </div>',
                    'FILE_EXTENSION' => $fileExtensionName . ' (.' . $fileExtension . ')',
                    'FILE_TYPE' => $fileTypeName,
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
                    'text' => $row['file_extension_name'] . ' (.' . $row['file_extension'] . ')'
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>