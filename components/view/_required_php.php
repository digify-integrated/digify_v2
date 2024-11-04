<?php

    require('components/configurations/session.php');
    require('components/configurations/config.php');
    require('components/model/database-model.php');
    require('components/model/system-model.php');
    require('components/model/security-model.php');
    require('apps/settings/authentication/model/authentication-model.php');
    require('apps/settings/app-module/model/app-module-model.php');
    require('apps/settings/menu-item/model/menu-item-model.php');

    $databaseModel = new DatabaseModel();
    $systemModel = new SystemModel();
    $securityModel = new SecurityModel();
    $authenticationModel = new AuthenticationModel($databaseModel, $securityModel);
    $appModuleModel = new AppModuleModel($databaseModel);
    $menuItemModel = new MenuItemModel($databaseModel);

    $loginCredentialsDetails = $authenticationModel->getLoginCredentials($userID, null);
    $userFileAs = $loginCredentialsDetails['file_as'];
    $userAccountName = $loginCredentialsDetails['username'];
    $userEmail = $loginCredentialsDetails['email'];
    $multipleSession = $loginCredentialsDetails['multiple_session'];
    $profilePicture = $systemModel->checkImage($loginCredentialsDetails['profile_picture'] ?? null, 'profile');
    $sessionToken = $securityModel->decryptData($loginCredentialsDetails['session_token']);
    $isActive = $securityModel->decryptData($loginCredentialsDetails['active']);
    $isLocked = $securityModel->decryptData($loginCredentialsDetails['locked']);
    
    if ($isActive == 'No' || $isLocked == 'Yes' || ($_SESSION['session_token'] != $sessionToken && $multipleSession == 'No')) {
        header('location: logout.php?logout');
        exit;
    }

?>