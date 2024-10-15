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
        case 'user account table':
            $filterAppModule = isset($_POST['app_module_filter']) && is_array($_POST['app_module_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['app_module_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateUserAccountTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $userAccountID = $row['user_account_id'];
                $fileAs = $row['file_as'];
                $email = $row['email'];
                $username = $row['username'];
                $locked = $securityModel->decryptData($row['locked']);
                $active = $securityModel->decryptData($row['active']);
                $lastConnectionDate = empty($securityModel->decryptData($row['last_connection_date'])) ? 'Never Connected' : $systemModel->checkDate('empty', $securityModel->decryptData($row['last_connection_date']), '', 'm/d/Y h:i:s a', '');
                $passwordExpiryDate = $systemModel->checkDate('empty', $securityModel->decryptData($row['password_expiry_date']), '', 'm/d/Y', '');
                $profilePicture = $systemModel->checkImage(str_replace('../', './apps/', $row['profile_picture'])  ?? null, 'profile');

                $userAccountIDEncrypted = $securityModel->encryptData($userAccountID);

                $activeBadge = $active == 'Yes' ? '<span class="badge rounded-pill text-bg-success">Active</span>' : '<span class="badge rounded-pill text-bg-danger">Inactive</span>';
                $lockedBadge = $locked == 'Yes' ? '<span class="badge rounded-pill text-bg-danger">Yes</span>' : '<span class=" badge rounded-pill text-bg-success">No</span>';

                $response[] = [
                    'CHECK_BOX' => '<input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $userAccountID .'">',
                    'USER_ACCOUNT' => '<div class="d-flex align-items-center">
                                            <img src="'. $profilePicture .'" alt="avatar" class="rounded-circle" width="35" height="35" />
                                            <div class="ms-3">
                                                <div class="user-meta-info">
                                                    <h6 class="user-name mb-0">'. $fileAs .'</h6>
                                                </div>
                                            </div>
                                        </div>',
                    'USERNAME' => $username,
                    'EMAIL' => $email,
                    'USER_ACCOUNT_STATUS' => $activeBadge,
                    'LOCK_STATUS' => $lockedBadge,
                    'PASSWORD_EXPIRY_DATE' => $passwordExpiryDate,
                    'LAST_CONNECTION_DATE' => $lastConnectionDate,
                    'LINK' => $pageLink .'&id='. $userAccountIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'user account options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateUserAccountOptions()');
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
                    'id' => $row['user_account_id'],
                    'text' => $row['user_account_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>