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
    $userID = $_SESSION['user_account_id'];
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $logNotesAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'log notes');
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'menu item table':
            $filterAppModule = isset($_POST['app_module_filter']) && is_array($_POST['app_module_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['app_module_filter'])) . "'" 
            : null;
            $filterParentID = isset($_POST['parent_id_filter']) && is_array($_POST['parent_id_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['parent_id_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateMenuItemTable(:filterAppModule, :filterParentID)');
            $sql->bindValue(':filterAppModule', $filterAppModule, PDO::PARAM_STR);
            $sql->bindValue(':filterParentID', $filterParentID, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $menuItemID = $row['menu_item_id'];
                $menuItemName = $row['menu_item_name'];
                $appModuleName = $row['app_module_name'];
                $parentName = !empty($row['parent_name']) ? $row['parent_name'] : '-';
                $orderSequence = $row['order_sequence'];

                $menuItemIDEncrypted = $securityModel->encryptData($menuItemID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $menuItemID .'">
                                    </div>',
                    'MENU_ITEM_NAME' => $menuItemName,
                    'APP_MODULE_NAME' => $appModuleName,
                    'PARENT_NAME' => $parentName,
                    'ORDER_SEQUENCE' => $orderSequence,
                    'LINK' => $pageLink .'&id='. $menuItemIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'menu item assigned role table':
            $menuItemID = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);

            $sql = $databaseModel->getConnection()->prepare('CALL generateMenuItemAssignedRoleTable(:menuItemID)');
            $sql->bindValue(':menuItemID', $menuItemID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            $updateRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 10);
            $deleteRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 11);

            $disabled = ($updateRoleAccess['total'] == 0) ? 'disabled' : '';
            $deleteButton = '';

            foreach ($options as $row) {
                $rolePermissionID = $row['role_permission_id'];
                $roleName = $row['role_name'];
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
                    $deleteButton = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light-danger btn-active-danger  delete-role-permission" data-role-permission-id="' . $rolePermissionID . '" title="Delete Role Permission">
                                        <i class="ki-outline ki-trash m-0 fs-5"></i>
                                    </a>';
                }

                if($logNotesAccess['total'] > 0){
                    $logNotes = '<a href="javascript:void(0);" class="btn btn-sm btn-icon btn-light btn-active-light-primary view-role-permission-log-notes" data-role-permission-id="' . $rolePermissionID . '" data-bs-toggle="modal" data-bs-target="#log-notes-modal" title="View Log Notes">
                                    <i class="ki-outline ki-shield-search m-0 fs-5"></i>
                                </a>';
                }

                $readAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="read" ' . $readAccessChecked . ' '. $disabled .' />
                                    </div>';

                $writeAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="write" ' . $writeAccessChecked . ' '. $disabled .' />
                                    </div>';

                $createAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="create" ' . $createAccessChecked . ' '. $disabled .' />
                                    </div>';

                $deleteAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="delete" ' . $deleteAccessChecked . ' '. $disabled .' />
                                    </div>';

                $importAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="import" ' . $importAccessChecked . ' '. $disabled .' />
                                    </div>';

                $exportAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="export" ' . $exportAccessChecked . ' '. $disabled .' />
                                    </div>';

                $logNotesAccessButton = '<div class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                        <input class="form-check-input update-role-permission" type="checkbox" data-role-permission-id="' . $rolePermissionID . '" data-access-type="log notes" ' . $logNotesAccessChecked . ' '. $disabled .' />
                                    </div>';

                $response[] = [
                    'ROLE_NAME' => $roleName,
                    'READ_ACCESS' => $readAccessButton,
                    'WRITE_ACCESS' => $writeAccessButton,
                    'CREATE_ACCESS' => $createAccessButton,
                    'DELETE_ACCESS' => $deleteAccessButton,
                    'IMPORT_ACCESS' => $importAccessButton,
                    'EXPORT_ACCESS' => $exportAccessButton,
                    'LOG_NOTES_ACCESS' => $logNotesAccessButton,
                    'ACTION' => '<div class="d-flex justify-content-end gap-3">
                                    '. $logNotes .'
                                    '. $deleteButton .'
                                </div>'
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'menu item options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;
            $menuItemID = isset($_POST['menu_item_id']) ? filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT) : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateMenuItemOptions(:menuItemID)');
            $sql->bindValue(':menuItemID', $menuItemID, PDO::PARAM_INT);
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
                    'id' => $row['menu_item_id'],
                    'text' => $row['menu_item_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>