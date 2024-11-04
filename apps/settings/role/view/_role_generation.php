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
        case 'role table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateRoleTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $roleID = $row['role_id'];
                $roleName = $row['role_name'];
                $roleDescription = $row['role_description'];

                $roleIDEncrypted = $securityModel->encryptData($roleID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $roleID .'">
                                    </div>',
                    'ROLE_NAME' => '<div class="d-flex flex-column">
                                                <a href="#" class="fs-5 text-gray-900 fw-bold">'. $roleName .'</a>
                                                <div class="fs-6 fw-semibold text-gray-500">'. $roleDescription .'</div>
                                            </div>',
                    'LINK' => $pageLink .'&id='. $roleIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'role assigned menu item table':
            $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

            $sql = $databaseModel->getConnection()->prepare('CALL generateRoleAssignedMenuItemTable(:roleID)');
            $sql->bindValue(':roleID', $roleID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $updateRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 10);
            $deleteRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 11);

            $disabled = ($updateRoleAccess['total'] == 0) ? 'disabled' : '';
            $deleteButton = '';
            $logNotes = '';

            foreach ($options as $row) {
                $rolePermissionID = $row['role_permission_id'];
                $menuItemName = $row['menu_item_name'];
                $readAccessRights = $row['read_access'];
                $writeAccessRights = $row['write_access'];
                $createAccessRights = $row['create_access'];
                $deleteAccessRights = $row['delete_access'];
                $importAccessRights = $row['import_access'];
                $exportAccessRights = $row['export_access'];
                $logNotesAccessRights = $row['log_notes_access'];

                $readAccessChecked = $readAccessRights ? 'checked' : '';
                $writeAccessChecked = $writeAccessRights ? 'checked' : '';
                $createAccessChecked = $createAccessRights ? 'checked' : '';
                $deleteAccessChecked = $deleteAccessRights ? 'checked' : '';
                $importAccessChecked = $importAccessRights ? 'checked' : '';
                $exportAccessChecked = $exportAccessRights ? 'checked' : '';
                $logNotesAccessChecked = $logNotesAccessRights ? 'checked' : '';

                if($deleteRoleAccess['total'] > 0){
                    $deleteButton = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light-danger btn-active-danger  delete-menu-item-permission" data-role-permission-id="' . $rolePermissionID . '" title="Delete Role Permission">
                                        <i class="ki-outline ki-trash m-0 fs-5"></i>
                                    </a>';
                }

                if($logNotesAccess['total'] > 0){
                    $logNotes = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light btn-active-light-primary view-menu-item-permission-log-notes" data-role-permission-id="' . $rolePermissionID . '" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View Log Notes">
                                    <i class="ki-outline ki-shield-search m-0 fs-5"></i>
                                </a>';
                }

                $readAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="read" ' . $readAccessChecked . ' '. $disabled .' />
                                    </div>';

                $writeAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="write" ' . $writeAccessChecked . ' '. $disabled .' />
                                    </div>';

                $createAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="create" ' . $createAccessChecked . ' '. $disabled .' />
                                    </div>';

                $deleteAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="delete" ' . $deleteAccessChecked . ' '. $disabled .' />
                                    </div>';

                $importAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="import" ' . $importAccessChecked . ' '. $disabled .' />
                                    </div>';

                $exportAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="export" ' . $exportAccessChecked . ' '. $disabled .' />
                                    </div>';

                $logNotesAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-menu-item-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="log notes" ' . $logNotesAccessChecked . ' '. $disabled .' />
                                    </div>';

                $response[] = [
                    'MENU_ITEM_NAME' => $menuItemName,
                    'READ_ACCESS' => $readAccessButton,
                    'WRITE_ACCESS' => $writeAccessButton,
                    'CREATE_ACCESS' => $createAccessButton,
                    'DELETE_ACCESS' => $deleteAccessButton,
                    'IMPORT_ACCESS' => $importAccessButton,
                    'EXPORT_ACCESS' => $exportAccessButton,
                    'LOG_NOTES_ACCESS' => $logNotesAccessButton,
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
        case 'role assigned system action table':
            $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

            $sql = $databaseModel->getConnection()->prepare('CALL generateRoleAssignedSystemActionTable(:roleID)');
            $sql->bindValue(':roleID', $roleID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $updateRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 13);
            $deleteRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 14);

            $disabled = ($updateRoleAccess['total'] == 0) ? 'disabled' : '';
            $deleteButton = '';

            foreach ($options as $row) {
                $roleSystemActionPermissionID = $row['role_system_action_permission_id'];
                $systemActionName = $row['system_action_name'];
                $roleAccess = $row['system_action_access'];

                $roleAccessChecked = $roleAccess ? 'checked' : '';

                if($deleteRoleAccess['total'] > 0){
                    $deleteButton = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light-danger btn-active-danger  delete-system-action-permission" data-role-permission-id="' . $roleSystemActionPermissionID . '" title="Delete Role Permission">
                                        <i class="ki-outline ki-trash m-0 fs-5"></i>
                                    </a>';
                }

                if($logNotesAccess['total'] > 0){
                    $logNotes = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light btn-active-light-primary view-system-action-permission-log-notes" data-role-permission-id="' . $roleSystemActionPermissionID . '" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View Log Notes">
                                    <i class="ki-outline ki-shield-search m-0 fs-5"></i>
                                </a>';
                }

                $roleAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-system-action-permission" type="checkbox" data-role-permission-id="' . $roleSystemActionPermissionID . '" ' . $roleAccessChecked . ' '. $disabled .' />
                                    </div>';

                $response[] = [
                    'SYSTEM_ACTION_NAME' => $systemActionName,
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
        case 'menu item role dual listbox options':
            $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
            $sql = $databaseModel->getConnection()->prepare('CALL generateMenuItemRoleDualListBoxOptions(:menuItemID)');
            $sql->bindValue(':menuItemID', $menuItemID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['role_id'],
                    'text' => $row['role_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'system action role dual listbox options':
            $systemActionID = filter_input(INPUT_POST, 'system_action_id', FILTER_VALIDATE_INT);
            $sql = $databaseModel->getConnection()->prepare('CALL generateSystemActionRoleDualListBoxOptions(:systemActionID)');
            $sql->bindValue(':systemActionID', $systemActionID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['role_id'],
                    'text' => $row['role_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'role menu item dual listbox options':
            $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $sql = $databaseModel->getConnection()->prepare('CALL generateRoleMenuItemDualListBoxOptions(:roleID)');
            $sql->bindValue(':roleID', $roleID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['menu_item_id'],
                    'text' => $row['menu_item_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'role system action dual listbox options':
            $roleID = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $sql = $databaseModel->getConnection()->prepare('CALL generateRoleSystemActionDualListBoxOptions(:roleID)');
            $sql->bindValue(':roleID', $roleID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['system_action_id'],
                    'text' => $row['system_action_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'role options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateRoleOptions()');
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
                    'id' => $row['role_id'],
                    'text' => $row['role_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>