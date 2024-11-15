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
        case 'company table':
            $filterCity = isset($_POST['city_filter']) && is_array($_POST['city_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['city_filter'])) . "'" 
            : null;

            $filterState = isset($_POST['state_filter']) && is_array($_POST['state_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['state_filter'])) . "'" 
            : null;

            $filterCountry = isset($_POST['country_filter']) && is_array($_POST['country_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['country_filter'])) . "'" 
            : null;

            $filterCurrency = isset($_POST['currency_filter']) && is_array($_POST['currency_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['currency_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateCompanyTable(:filterCity, :filterState, :filterCountry, :filterCurrency)');
            $sql->bindValue(':filterCity', $filterCity, PDO::PARAM_STR);
            $sql->bindValue(':filterState', $filterState, PDO::PARAM_STR);
            $sql->bindValue(':filterCountry', $filterCountry, PDO::PARAM_STR);
            $sql->bindValue(':filterCurrency', $filterCurrency, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $companyID = $row['company_id'];
                $companyName = $row['company_name'];
                $address = $row['address'];
                $cityName = $row['city_name'];
                $stateName = $row['state_name'];
                $countryName = $row['country_name'];
                $companyLogo = $systemModel->checkImage(str_replace('../', './apps/', $row['company_logo'])  ?? null, 'company logo');

                $companyAddress = $address . ', ' . $cityName . ', ' . $stateName . ', ' . $countryName;

                $companyIDEncrypted = $securityModel->encryptData($companyID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $companyID .'">
                                    </div>',
                    'COMPANY_NAME' => '<div class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <div class="symbol-label">
                                            <img src="'. $companyLogo .'" alt="'. $companyName .'" class="w-100">
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold mb-1">'. $companyName .'</span>
                                        <small class="text-gray-600">'. $companyAddress .'</small>
                                    </div>
                                </div>',
                    'LINK' => $pageLink .'&id='. $companyIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
        
        # -------------------------------------------------------------
        case 'company options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateCompanyOptions()');
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
                    'id' => $row['company_id'],
                    'text' => $row['company_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>