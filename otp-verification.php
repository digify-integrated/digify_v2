<?php
    require('components/configurations/config.php');
    require('apps/security/authentication/model/authentication-model.php');
    require('components/model/database-model.php');
    require('components/model/security-model.php');

    $databaseModel = new DatabaseModel();
    $securityModel = new SecurityModel();
    $authenticationModel = new AuthenticationModel($databaseModel, $securityModel);

    $pageTitle = 'OTP Verification';

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $userID = $securityModel->decryptData($id);
 
        $checkLoginCredentialsExist = $authenticationModel->checkLoginCredentialsExist($userID, null);
        $total = $checkLoginCredentialsExist['total'] ?? 0;
 
        if($total > 0){
            $loginCredentialsDetails = $authenticationModel->getLoginCredentials($userID, null);
            $emailObscure = $securityModel->obscureEmail($loginCredentialsDetails['email']);
        }
        else{
            header('location: 404.php');
            exit;
        }
    }
    else {
        header('location: 404.php');
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
                        <form class="form w-100" id="otp-form" method="post" action="#">
                            <img src="./assets/images/logos/logo-dark.svg" class="mb-5" alt="Logo-Dark" />
                            <h2 class="mb-2 mt-4 fs-1 fw-bolder">Two Step Verification</h2>
                            <p class="mb-8 fs-5">Enter the verification code we sent to </p>
                            <div class="fw-bold text-gray-900 fs-3 mb-8"><?php echo $emailObscure; ?></div>
                            <input type="hidden" id="user_account_id" name="user_account_id" value="<?php echo $userID; ?>">
                            <div class="mb-8">
                                <label class="form-label fw-semibold">Type your 6 digit security code</label>
                                <div class="d-flex align-items-center gap-2 gap-sm-3">
                                    <input type="text" class="form-control text-center otp-input" id="otp_code_1" name="otp_code_1" autocomplete="off" maxlength="1">
                                    <input type="text" class="form-control text-center otp-input" id="otp_code_2" name="otp_code_2" autocomplete="off" maxlength="1">
                                    <input type="text" class="form-control text-center otp-input" id="otp_code_3" name="otp_code_3" autocomplete="off" max length="1">
                                    <input type="text" class="form-control text-center otp-input" id="otp_code_4" name="otp_code_4" autocomplete="off" maxlength="1">
                                    <input type="text" class="form-control text-center otp-input" id="otp_code_5" name="otp_code_5" autocomplete="off" maxlength="1">
                                    <input type="text" class="form-control text-center otp-input" id="otp_code_6" name="otp_code_6" autocomplete="off" maxlength="1">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button id="verify" type="submit" class="btn btn-primary">Verify</button>
                            </div>

                            <div class="d-flex align-items-center mt-4">
                                <p class="fs-12 mb-0 fw-medium">Didn't get the code?</p>
                                <a class="text-primary fw-semibold ms-2" id="resend-link" href="javascript:void(0)">Resend</a>
                                <span class="text-primary fw-semibold ms-2 d-none" id="countdown"></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('components/view/_error_modal.php'); ?>
    <?php require_once('components/view/_required_js.php'); ?>

    <script src="./apps/security/authentication/js/otp-verification.js?v=<?php echo rand(); ?>"></script>
</body>
</html>