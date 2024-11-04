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
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $logNotesAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'log notes');
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'system action table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateSystemActionTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $systemActionID = $row['system_action_id'];
                $systemActionName = $row['system_action_name'];
                $systemActionDescription = $row['system_action_description'];

                $systemActionIDEncrypted = $securityModel->encryptData($systemActionID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $systemActionID .'">
                                    </div>',
                    'SYSTEM_ACTION_NAME' => '<div class="d-flex flex-column">
                                                <a href="#" class="fs-5 text-gray-900 fw-bold">'. $systemActionName .'</a>
                                                <div class="fs-6 fw-semibold text-gray-500">'. $systemActionDescription .'</div>
                                            </div>',
                    'LINK' => $pageLink .'&id='. $systemActionIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'system action assigned role table':
            $systemActionID = filter_input(INPUT_POST, 'system_action_id', FILTER_VALIDATE_INT);

            $sql = $databaseModel->getConnection()->prepare('CALL generateSystemActionAssignedRoleTable(:systemActionID)');
            $sql->bindValue(':systemActionID', $systemActionID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $updateRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 13);
            $deleteRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 14);

            $disabled = ($updateRoleAccess['total'] == 0) ? 'disabled' : '';
            $deleteButton = '';

            foreach ($options as $row) {
                $roleSystemActionPermissionID = $row['role_system_action_permission_id'];
                $roleName = $row['role_name'];
                $roleAccess = $row['system_action_access'];

                $roleAccessChecked = $roleAccess ? 'checked' : '';

                if($deleteRoleAccess['total'] > 0){
                    $deleteButton = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light-danger btn-active-danger  delete-role-permission" data-role-permission-id="' . $roleSystemActionPermissionID . '" title="Delete Role Permission">
                                        <i class="ki-outline ki-trash m-0 fs-5"></i>
                                    </a>';
                }

                if($logNotesAccess['total'] > 0){
                    $logNotes = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light btn-active-light-primary view-role-permission-log-notes" data-role-permission-id="' . $roleSystemActionPermissionID . '" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View Log Notes">
                                    <i class="ki-outline ki-shield-search m-0 fs-5"></i>
                                </a>';
                }

                $roleAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $roleSystemActionPermissionID . '" ' . $roleAccessChecked . ' '. $disabled .' />
                                    </div>';

                $response[] = [
                    'ROLE_NAME' => $roleName,
                    'ACCESS' => $roleAccessButton,
                    'ACTION' => '<div class="d-flex gap-2">
                                    '. $logNotes .'
                                    '. $deleteButton .'
                                </div>'
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'system action options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateSystemActionOptions()');
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
                    'id' => $row['system_action_id'],
                    'text' => $row['system_action_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>