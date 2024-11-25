<?php
    require('components/view/_required_php.php');   
    require('components/view/_page_details.php');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <?php require_once('components/view/_head_meta_tags.php'); ?>
    <?php require_once('components/view/_head_stylesheet.php'); ?>
    <link href="./assets/plugins/datatables/datatables.bundle.css" rel="stylesheet" type="text/css"/>
</head>

<?php 
    require_once('components/view/_theme_script.php');
?>

<body id="kt_app_body" data-kt-app-header-fixed-mobile="true" data-kt-app-toolbar-enabled="true"  class="app-default" data-kt-app-page-loading-enabled="true" data-kt-app-page-loading="on">
    <?php 
        require_once('components/view/_preloader.php');
    ?>
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <?php 
                require_once('components/view/_header.php');
            ?>
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <?php 
                    require_once('components/view/_breadcrumbs.php');
                ?>
                <div class="app-container container-xxl">
                    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                        <div class="d-flex flex-column flex-column-fluid">
                            <div id="kt_app_content" class="app-content flex-column-fluid">
                                <?php 
                                    if($newRecord){
                                        require_once('apps/settings/city/view/_city_new.php');
                                    }
                                    else if(!empty($detailID)){
                                        require_once('apps/settings/city/view/_city_details.php');
                                    }
                                    else if(isset($_GET['import']) && !empty($_GET['import'])){
                                        require_once('components/view/_import.php');
                                    }
                                    else{
                                        require_once('apps/settings/city/view/_city.php');
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
        require_once('components/view/_error_modal.php');
        require_once('components/view/_required_js.php');
        
    ?>
    <script src="./assets/plugins/datatables/datatables.bundle.js"></script>
    
    <?php
        $version = rand();

        if ($newRecord) {
            $scriptFile = './apps/settings/city/js/city-new.js';
        } 
        elseif (!empty($detailID)) {
            $scriptFile = './apps/settings/city/js/city-details.js';
        } 
        elseif (isset($_GET['import']) && !empty($_GET['import'])) {
            $scriptFile = './components/js/import.js'; 
        } 
        else {
            $scriptFile = './apps/settings/city/js/city.js';
        }

        $scriptLink = '<script src="' . $scriptFile . '?v=' . $version . '"></script>';

        echo $scriptLink;
    ?>
</body>

</html>