<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Metronic - The World's #1 Selling Tailwind CSS & Bootstrap Admin Template by KeenThemes</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" type="image/png" href="./assets/media/logos/favicon.svg" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <link href="./assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
        <link href="./assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    </head>
    <body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
        
        <div class="d-flex flex-column flex-root" id="kt_app_root">
            <!--begin::Page bg image-->
            <style>
                body {
                    background-image: url("./assets/media/backgrounds/login-bg.jpg");
                }
            </style>
            <!--end::Page bg image-->

            <!--begin::Authentication - Sign-in -->
             <div class="position-relative overflow-hidden auth-bg min-vh-100 w-100 d-flex align-items-center justify-content-center">
                <div class="d-flex align-items-center justify-content-center w-100">
                    <div class="row justify-content-center w-100 my-5 my-xl-0">
                        <div class="col-md-5 d-flex flex-column justify-content-center">
                            <div class="card mb-0 bg-body auth-login m-auto w-100">
                                <div class="row gx-0">
                                    <div class="col-xl-12">
                                        <div class="row justify-content-center py-4">
                                            <div class="col-lg-11">
                                                <div class="card-body">
                                                    <a href="index.php" class="text-nowrap logo-img d-block mb-3">
                                                        <img src="./assets/media/logos/logo-dark.svg" class="dark-logo" alt="Logo-Dark" />
                                                    </a>
                                                    <h2 class="mb-2 mt-4 fs-6 fw-bolder">Welcome to <span class="text-primary">Digify Integrated Solutions</span></h2>
                                                    <p class="mb-9">Your Partner in Progress, Redefining Digital Excellence</p>
                                                    <form id="signin-form" method="post" action="#">
                                                        <div class="mb-3">
                                                            <label for="username" class="form-label">Username</label>
                                                            <input type="text" class="form-control" id="username" name="username" autocomplete="off">
                                                        </div>
                                                        <div class="position-relative mb-3">    
                                                            <input class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off"/>

                                                            <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                                                <i class="ki-outline ki-eye-slash fs-2"></i>                    <i class="ki-outline ki-eye fs-2 d-none"></i>                </span>
                                                        </div>
                                                        <button id="signin" type="submit" class="btn btn-primary w-100">Login</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Authentication - Sign-in-->
        </div>

        <!--begin::Global Javascript Bundle(mandatory for all pages)-->
        <script src="./assets/plugins/global/plugins.bundle.js"></script>
        <script src="./assets/js/scripts.bundle.js"></script>
        <!--end::Global Javascript Bundle-->

        <!--end::Javascript-->
    </body>
    <!--end::Body-->
</html>
