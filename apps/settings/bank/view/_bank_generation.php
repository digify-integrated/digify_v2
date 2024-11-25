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
    $userID = $_SESSION['user_account_id'];
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $logNotesAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'log notes');
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'bank table':
            $filterState = isset($_POST['state_filter']) && is_array($_POST['state_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['state_filter'])) . "'" 
            : null;

            $filterCountry = isset($_POST['country_filter']) && is_array($_POST['country_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['country_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateBankTable(:filterState, :filterCountry)');
            $sql->bindValue(':filterState', $filterState, PDO::PARAM_STR);
            $sql->bindValue(':filterCountry', $filterCountry, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $bankID = $row['bank_id'];
                $bankName = $row['bank_name'];
                $stateName = $row['state_name'];
                $countryName = $row['country_name'];

                $bankIDEncrypted = $securityModel->encryptData($bankID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $bankID .'">
                                    </div>',
                    'CITY_NAME' => $bankName,
                    'STATE_NAME' => $stateName,
                    'COUNTRY_NAME' => $countryName,
                    'LINK' => $pageLink .'&id='. $bankIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
        
        # -------------------------------------------------------------
        case 'bank options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateBankOptions()');
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
                    'id' => $row['bank_id'],
                    'text' => $row['bank_name'] . ', ' . $row['state_name'] . ', ' . $row['country_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
        
        # -------------------------------------------------------------
        case 'filter bank options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateBankOptions()');
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
                    'id' => $row['bank_id'],
                    'text' => $row['bank_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>