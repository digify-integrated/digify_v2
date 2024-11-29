
<div class="d-flex flex-column flex-lg-row">
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body">
                <div class="d-flex flex-center flex-column py-5">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <img src="./assets/images/default/default-avatar.jpg" alt="image">
                    </div>
                    
                    <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">
                        Emma Smith
                    </a>
                        
                    <div class="mb-9">
                        <div class="badge badge-lg badge-light-primary d-inline">Administrator</div>
                    </div>
                </div>
                <div class="d-flex flex-stack fs-4 py-3">
                    <div class="fw-bold rotate">
                        Employment Details
                    </div>
                </div>

                <div class="separator"></div>

                <div id="kt_user_view_details" class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bold mt-5">Account ID</div>
                        <div class="text-gray-600">ID-45453423</div>
                        
                        <div class="fw-bold mt-5">Email</div>
                        <div class="text-gray-600"><a href="#" class="text-gray-600 text-hover-primary">info@keenthemes.com</a></div>

                        <div class="fw-bold mt-5">Address</div>
                        <div class="text-gray-600">101 Collin Street, <br>Melbourne 3000 VIC<br>Australia</div>
                            
                        <div class="fw-bold mt-5">Language</div>
                        <div class="text-gray-600">English</div>
                            
                        <div class="fw-bold mt-5">Last Login</div>
                        <div class="text-gray-600">25 Oct 2024, 10:10 pm</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-lg-row-fluid ms-lg-15">
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#private-information-tab" aria-selected="false" role="tab" tabindex="-1">Private Information</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#work-information-tab" aria-selected="true" role="tab">Work Information</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#resume-tab" aria-selected="false" role="tab" tabindex="-1">Resume</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#settings-tab" aria-selected="false" role="tab" tabindex="-1">Settings</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="private-information-tab" role="tabpanel">
                <div class="card card-flush">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Personal Details</h3>
                        </div>
                    </div>

                    <form id="app-module-form" class="form" method="post" action="#">
                        <div class="card-body border-top p-9">
                            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                                <div class="col">
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mt-3" for="first_name">
                                            <span class="required">First Name</span>
                                        </label>

                                        <input type="text" class="form-control" id="first_name" name="first_name" maxlength="300" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mt-3" for="last_name">
                                            <span class="required">Last Name</span>
                                        </label>

                                        <input type="text" class="form-control" id="last_name" name="last_name" maxlength="300" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">                
                                <div class="col">
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mt-3" for="middle_name">
                                            Middle Name
                                        </label>

                                        <input type="text" class="form-control" id="middle_name" name="middle_name" maxlength="300" autocomplete="off">
                                    </div>
                                </div>
                                
                                <div class="col">
                                    <div class="fv-row mb-7">
                                        <label class="fs-6 fw-semibold form-label mt-3" for="suffix">
                                            Suffix
                                        </label>

                                        <input type="text" class="form-control" id="suffix" name="suffix" maxlength="10" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                    <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                                    <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                </div>' : '';
                        ?>
                    </form>
                </div>
            </div>
            <div class="tab-pane fade" id="work-information-tab" role="tabpanel">
                
            </div>
            <div class="tab-pane fade" id="resume-tab" role="tabpanel">
                
            </div>
            <div class="tab-pane fade" id="settings-tab" role="tabpanel">
                
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>