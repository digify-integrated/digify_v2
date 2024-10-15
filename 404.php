<?php
    $pageTitle = '404';
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

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat" style="background-image: url('./assets/images/auth/bg1.jpg');>
    <div class="d-flex flex-column flex-center flex-column-fluid">
        <div class="d-flex flex-column flex-center text-center p-10">     
            <div class="card card-flush w-lg-650px py-5">
                <div class="card-body py-15 py-lg-20">
                    <h1 class="fw-bolder fs-2hx text-gray-900 mb-4">
                        Oops!
                    </h1>
                    <div class="fw-semibold fs-6 text-gray-500 mb-7">
                        We can't find that page.
                    </div>
                    <div class="mb-3">
                        <img src="assets/images/404-error.png" class="mw-100 mh-300px theme-light-show" alt=""/>
                        <img src="assets/images/404-error-dark.png" class="mw-100 mh-300px theme-dark-show" alt=""/>
                    </div>
                    <div class="mb-0">
                        <a href="index.php" class="btn btn-sm btn-primary">Return Home</a>
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