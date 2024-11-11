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
        case 'user account table':
            $userAccountStatusFilter = $_POST['user_account_status_filter'];
            $userAccountLockStatusFilter = $_POST['user_account_lock_status_filter'];
           
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
                $lastConnectionDate = empty($row['last_connection_date']) ? 'Never Connected' : $systemModel->checkDate('empty', $row['last_connection_date'], '', 'd M Y h:i:s a', '');
                $passwordExpiryDate = $systemModel->checkDate('empty', $securityModel->decryptData($row['password_expiry_date']), '', 'M d,Y', '');
                $profilePicture = $systemModel->checkImage(str_replace('../', './apps/', $row['profile_picture'])  ?? null, 'profile');

                $userAccountIDEncrypted = $securityModel->encryptData($userAccountID);

                $activeBadge = $active == 'Yes' ? '<span class="badge badge-light-success">Active</span>' : '<span class="badge badge-light-danger">Inactive</span>';
                $lockedBadge = $locked == 'Yes' ? '<span class="badge badge-light-danger">Yes</span>' : '<span class=" badge badge-light-success">No</span>';

                $allConditionsMet = true;

                if (!empty($userAccountStatusFilter) && $userAccountStatusFilter !== $active) {
                    $allConditionsMet = false;
                }

                if (!empty($userAccountLockStatusFilter) && $userAccountLockStatusFilter !== $locked) {
                    $allConditionsMet = false;
                }
                
                if ($allConditionsMet) {
                    $response[] = [
                        'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $userAccountID .'">
                                        </div>',
                        'USER_ACCOUNT' => '<div class="d-flex align-items-center">
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                <div class="symbol-label">
                                                    <img src="'. $profilePicture .'" alt="'. $fileAs .'" class="w-100">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold mb-1">'. $fileAs .'</span>
                                                <small class="text-gray-600">'. $email .'</small>
                                            </div>
                                        </div>',
                        'USER_ACCOUNT_STATUS' => $activeBadge,
                        'LOCK_STATUS' => $lockedBadge,
                        'LAST_CONNECTION_DATE' => $lastConnectionDate,
                        'LINK' => $pageLink .'&id='. $userAccountIDEncrypted
                    ];
                }
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'login session table':
            $userAccountID = filter_input(INPUT_POST, 'user_account_id', FILTER_VALIDATE_INT);
           
            $sql = $databaseModel->getConnection()->prepare('CALL generateUserAccountLoginSession(:userAccountID)');
            $sql->bindValue(':userAccountID', $userAccountID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $location = $row['location'];
                $device = $row['device'];
                $loginStatus = $row['login_status'];
                $ipAddress = $row['ip_address'];
                $loginDate = $systemModel->checkDate('empty', $row['login_date'], '', 'd M Y h:i:s a', '');

                $loginStatusBadge = $loginStatus == 'Ok' ? '<span class="badge badge-light-success">Ok</span>' : '<span class="badge badge-light-danger">'. $loginStatus .'</span>';

                $response[] = [
                    'LOCATION' => $location,
                    'LOGIN_STATUS' => $loginStatusBadge,
                    'DEVICE' => $device,
                    'IP_ADDRESS' => $ipAddress,
                    'LOGIN_DATE' => $loginDate
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