<?php
    if (!isset($_GET['page_id']) || empty($_GET['page_id']) || !isset($_GET['app_module_id']) || empty($_GET['app_module_id'])) {
        header('location: apps.php');
        exit;
    }

    $appModuleID = $securityModel->decryptData($_GET['app_module_id']);
    $pageID = $securityModel->decryptData($_GET['page_id']);
    
    $pageDetails = $menuItemModel->getMenuItem($pageID);
    $pageTitle = $pageDetails['menu_item_name'] ?? null;
    $pageURL = $pageDetails['menu_item_url'] ?? null;
    $tableName = $pageDetails['table_name'] ?? null;
    $pageLink = $pageURL . '?app_module_id=' . $securityModel->encryptData($appModuleID) . '&page_id=' . $securityModel->encryptData($pageID);
    
    $appModuleDetails = $appModuleModel->getAppModule($appModuleID);
    $appModuleName = $appModuleDetails['app_module_name'];
    $appLogo = $systemModel->checkImage($appModuleDetails['app_logo'], 'app module logo');

    $readAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'read');
    $writeAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'write');
    $deleteAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'delete');
    $createAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'create');
    $importAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'import');
    $exportAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'export');
    $logNotesAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'log notes');

    if ($readAccess['total'] == 0) {
        header('location: 404.php');
        exit;
    }

    $newRecord = null;
    $importRecord = null;
    $detailID = null;
    $importTableName = null;

    if(isset($_GET['id'])){
        if(empty($_GET['id'])){
            header('location: apps.php');
            exit;
        }
    
        $detailID = $securityModel->decryptData($_GET['id']);
    }

    if(isset($_GET['new'])){
        if($createAccess['total'] == 0){
            header('location: ' . $pageLink);
            exit;
        }

        $newRecord = isset($_GET['new']);
    }

    if(isset($_GET['import']) && empty($_GET['import'])){
        header('location:' . $pageLink);
        exit;
    }
    else if(isset($_GET['import']) && !empty($_GET['import'])){
        if($importAccess['total'] == 0){
            header('location: ' . $pageLink);
            exit;
        }

        $importRecord = isset($_GET['import']);
        $importTableName = $securityModel->decryptData($_GET['import']);
    }
   
    $disabled = ($writeAccess['total'] == 0) ? 'disabled' : '';
?>