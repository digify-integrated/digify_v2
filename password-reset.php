<?php
    require('components/configurations/config.php');
    require('apps/security/authentication/model/authentication-model.php');
    require('components/model/database-model.php');
    require('components/model/security-model.php');

    $databaseModel = new DatabaseModel();
    $securityModel = new SecurityModel();
    $authenticationModel = new AuthenticationModel($databaseModel, $securityModel);

    $pageTitle = 'Password Reset';

    if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['token']) && !empty($_GET['token'])) {
        $id = $_GET['id'];
        $token = $_GET['token'];
        $userID = $securityModel->decryptData($id);
        $token = $securityModel->decryptData($token);

        $loginCredentialsDetails = $authenticationModel->getLoginCredentials($userID, null);
        $resetToken =  $securityModel->decryptData($loginCredentialsDetails['reset_token']);
        $resetTokenExpiryDate = $securityModel->decryptData($loginCredentialsDetails['reset_token_expiry_date']);

        if($token != $resetToken || strtotime(date('Y-m-d H:i:s')) > strtotime($resetTokenExpiryDate)){
            header('location: 404.php');
            exit;
        }
    }
    else{
        header('location: index.php');
        exit;
    }

    require('components/configurations/session-check.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php 
        require_once('components/view/_head_meta_tags.php'); 
        require_once('components/view/_head_stylesheet.php');
    ?>
</head>

<?php 
    require_once('components/view/_theme_script.php');
?>

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on">
    <?php 
        require_once('components/view/_preloader.php');
    ?>
    <div class="d-flex flex-column flex-root" id="kt_app_root" style="background-image: url('./assets/images/backgrounds/login-bg.jpg');">
        <div class="d-flex flex-column flex-column-fluid flex-lg-row align-items-center justify-content-center">
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-5">
                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-10">
                    <div class="d-flex flex-center flex-column flex-column-fluid px-lg-5 pb-5">
                        <form class="form w-100" id="password-reset-form" method="post" action="#">
                            <img src="./assets/images/logos/logo-dark.svg" class="mb-5" alt="Logo-Dark" />
                            <h2 class="mb-2 mt-4 fs-1 fw-bolder">Password Reset</h2>
                            <p class="mb-10 fs-5">Enter your new password</p>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <button class="btn btn-light bg-transparent password-addon" type="button">
                                        <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-5">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <button class="btn btn-light bg-transparent password-addon" type="button">
                                        <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button id="reset" type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('components/view/_error_modal.php'); ?>
    <?php require_once('components/view/_required_js.php'); ?>

    <script src="./apps/security/authentication/js/password-reset.js?v=<?php echo rand(); ?>"></script>
</body>
</html>