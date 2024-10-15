<?php
    require('components/configurations/config.php');
    require('components/model/database-model.php');

    $databaseModel = new DatabaseModel();

    $pageTitle = 'Digify';

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

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
    <div class="d-flex flex-column flex-root" id="kt_app_root" style="background-image: url('./assets/images/backgrounds/login-bg.jpg');">
        <div class="d-flex flex-column flex-column-fluid flex-lg-row align-items-center justify-content-center">
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-5">
                <div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-10">
                    <div class="d-flex flex-center flex-column flex-column-fluid px-lg-5 pb-5">
                        <form class="form w-100" id="signin-form" method="post" action="#">
                            <img src="./assets/images/logos/logo-dark.svg" class="mb-5" alt="Logo-Dark" />
                            <h2 class="mb-2 mt-4 fs-1 fw-bolder">Welcome to Digify Integrated Solutions</h2>
                            <p class="mb-10 fs-5">Your Partner in Progress, Redefining Digital Excellence</p>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" autocomplete="off">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password">
                                    <button class="btn btn-light bg-transparent password-addon" type="button">
                                        <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <a href="forgot-password.php" class="link-primary">Forgot Password?</a>
                            </div>

                            <div class="d-grid">
                                <button id="signin" type="submit" class="btn btn-primary">Sign In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('components/view/_error_modal.php'); ?>
    <?php require_once('components/view/_required_js.php'); ?>

    <script src="./apps/security/authentication/js/index.js?v=<?php echo rand(); ?>"></script>
</body>
</html>