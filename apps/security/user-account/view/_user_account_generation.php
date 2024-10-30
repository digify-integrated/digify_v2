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
            $userAccountStatusFilter = $_POST['user_account_status_filter'];
            $userAccountLockStatusFilter = $_POST['user_account_lock_status_filter'];
            $passwordExpiryDateFilter = $_POST['password_expiry_date_filter'];
            $lastConnectionDateFilter = $_POST['last_connection_date_filter'];

            $passwordExpiryStartDateFormatted = null;
            $passwordExpiryEndDateFormatted = null;

            if (!empty($passwordExpiryDateFilter)) {
                $passwordExpiryDates = explode(' - ', $passwordExpiryDateFilter);
                
                $passwordExpiryStartDate = DateTime::createFromFormat('m/d/Y', trim($passwordExpiryDates[0]));
                $passwordExpiryEndDate = DateTime::createFromFormat('m/d/Y', trim($passwordExpiryDates[1]));
            
                if ($passwordExpiryStartDate && $passwordExpiryEndDate) {
                    $passwordExpiryStartDateFormatted = $passwordExpiryStartDate->format('Y-m-d');
                    $passwordExpiryEndDateFormatted = $passwordExpiryEndDate->format('Y-m-d');
                }
            }

            $lastConnectionStartDateFormatted = null;
            $lastConnectionEndDateFormatted = null;

            if (!empty($lastConnectionDateFilter)) {
                $lastConnectionDates = explode(' - ', $lastConnectionDateFilter);
                
                $lastConnectionStartDate = DateTime::createFromFormat('m/d/Y', trim($lastConnectionDates[0]));
                $lastConnectionEndDate = DateTime::createFromFormat('m/d/Y', trim($lastConnectionDates[1]));
            
                if ($lastConnectionStartDate && $lastConnectionEndDate) {
                    $lastConnectionStartDateFormatted = $lastConnectionStartDate->format('Y-m-d');
                    $lastConnectionEndDateFormatted = $lastConnectionEndDate->format('Y-m-d');
                }
            }
           
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

                $activeBadge = $active == 'Yes' ? '<span class="badge badge-light-success">Active</span>' : '<span class="badge badge-light-danger">Inactive</span>';
                $lockedBadge = $locked == 'Yes' ? '<span class="badge badge-light-danger">Yes</span>' : '<span class=" badge badge-light-success">No</span>';

                if(!empty($userAccountStatusFilter) && $userAccountStatusFilter == $active){
                    
                }

                if(!empty($userAccountLockStatusFilter) && $userAccountLockStatusFilter == $locked){

                }

                if(!empty($passwordExpiryStartDateFormatted) && !empty($passwordExpiryEndDateFormatted) && $passwordExpiryStartDateFormatted >=  $passwordExpiryDate && $passwordExpiryEndDateFormatted <= $passwordExpiryDate ){

                }

                if(!empty($lastConnectionStartDateFormatted) && !empty($lastConnectionEndDateFormatted) && $lastConnectionStartDateFormatted >=  $lastConnectionDate && $lastConnectionEndDateFormatted <= $lastConnectionDate ){

                }

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
                                            <span class="text-gray-800 mb-1">'. $fileAs .'</span>
                                            <span class="text-gray-600">'. $email .'</span>
                                        </div>
                                    </div>',
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