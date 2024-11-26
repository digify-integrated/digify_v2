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
        case 'contact information type table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateContactInformationTypeTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $contactInformationTypeID = $row['contact_information_type_id'];
                $contactInformationTypeName = $row['contact_information_type_name'];
                $contactInformationTypeIDEncrypted = $securityModel->encryptData($contactInformationTypeID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $contactInformationTypeID .'">
                                    </div>',
                    'CONTACT_INFORMATION_TYPE_NAME' => $contactInformationTypeName,
                    'LINK' => $pageLink .'&id='. $contactInformationTypeIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'contact information type options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateContactInformationTypeOptions()');
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
                    'id' => $row['contact_information_type_id'],
                    'text' => $row['contact_information_type_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>