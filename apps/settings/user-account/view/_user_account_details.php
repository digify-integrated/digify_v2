<div class="d-flex flex-column flex-lg-row">
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body">
                <div class="d-flex flex-center flex-column py-5">
                    <div class="image-input image-input-outline mb-7" data-kt-image-input="true">
                        <div class="image-input-wrapper w-125px h-125px" id="profile_picture_image" style="background-image: url(./assets/images/default/default-avatar.jpg)"></div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change image" data-bs-original-title="Change image" data-kt-initialized="1">
                                                                    <i class="ki-outline ki-pencil fs-7"></i>
                                                                    <input type="file" id="profile_picture" name="profile_picture" accept=".png, .jpg, .jpeg">
                                                                </label>' : '';
                        ?>
                    </div>
                    <div class="fs-3 text-gray-800 fw-bold mb-3" id="full_name_side_summary">Emma Smith</div>
                    <div class="mb-2">
                        <div class="text-gray-600" id="email_side_summary"></div>
                    </div>
                </div>
                
                <div class="d-flex flex-stack fs-4 py-3">
                    <div class="fw-bold">
                        Details
                    </div>
                </div>

                <div class="separator separator-dashed my-3"></div>

                <div>
                    <div class="pb-5 fs-6">
                        <div class="fw-bold mt-5">Username</div>
                        <div class="text-gray-600" id="username_side_summary"></div>

                        <div class="fw-bold mt-5">Phone</div>
                        <div class="text-gray-600" id="phone_side_summary"></div>

                        <div class="fw-bold mt-5">Password Expiry Date</div>
                        <div class="text-gray-600" id="password_expiry_date_side_summary"></div>

                        <div class="fw-bold mt-5">Last Password Change</div>
                        <div class="text-gray-600" id="last_password_date_side_summary"></div>

                        <div class="fw-bold mt-5">Last Login</div>
                        <div class="text-gray-600" id="last_connection_date_side_summary"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-5 mb-xl-8">
            <div class="card-header border-0">
                <div class="card-title">
                    <h3 class="fw-bold m-0">Security Settings</h3>
                </div>
            </div>
            
            <div class="card-body pt-2">                
                <div class="py-2">
                    <div class="d-flex flex-stack">
                        <div class="d-flex">
                            <div class="d-flex flex-column">
                                <div class="fs-5 text-gray-900 fw-bold">Two-factor Authentication</div>
                                <div class="fs-7 fw-semibold text-muted">Enhance security with 2FA, adding extra verification beyond passwords.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="two-factor-authentication">
                                <span class="form-check-label fw-semibold text-muted" for="two-factor-authentication"></span>
                            </label>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-5"></div>

                    <div class="d-flex flex-stack">
                        <div class="d-flex">
                            <div class="d-flex flex-column">
                                <div class="fs-5 text-gray-900 fw-bold">Multiple Login Sessions</div>
                                <div class="fs-7 fw-semibold text-muted">Track logins with Multiple Sessions, get alerts for unfamiliar activity, boost security.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <label class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" id="multiple-login-sessions">
                                <span class="form-check-label fw-semibold text-muted" for="multiple-login-sessions"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-lg-row-fluid ms-lg-15">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0" role="button">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Profile Details</h3>
                </div>
            </div>
                    
            <div>
                <div class="card-body border-top p-9">
                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_full_name">
                            <div class="fs-6 fw-bold mb-1">Full Name</div>
                            <div class="fw-semibold text-gray-600" id="full_name_summary"></div>
                        </div>
                                
                        <div id="change_full_name_edit" class="flex-row-fluid d-none">
                            <form id="full_name_form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="full_name" class="form-label fs-6 fw-bold mb-3">Enter New Full Name</label>
                                            <input type="text" class="form-control" maxlength="300" id="full_name" name="full_name" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <button id="update_full_name_submit" form="full_name_form" type="submit" class="btn btn-primary me-2 px-6">Update Full Name</button>
                                    <button id="update_full_name_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6">Cancel</button>
                                </div>
                            </form>
                        </div>
                                
                        <div id="change_full_name_button" class="ms-auto">
                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_username">
                            <div class="fs-6 fw-bold mb-1">Username</div>
                            <div class="fw-semibold text-gray-600" id="username_summary"></div>
                        </div>
                                
                        <div id="change_username_edit" class="flex-row-fluid d-none">
                            <form id="username_form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="username" class="form-label fs-6 fw-bold mb-3">Enter New Username</label>
                                            <input type="text" class="form-control form-control-lg" id="username" name="username">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <button id="update_username_submit" form="username_form" type="submit" class="btn btn-primary me-2 px-6">Update Username</button>
                                    <button id="update_username_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                </div>
                            </form>
                        </div>
                                
                        <div id="change_username_button" class="ms-auto">
                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_email">
                            <div class="fs-6 fw-bold mb-1">Email Address</div>
                            <div class="fw-semibold text-gray-600" id="email_summary"></div>
                        </div>
                                
                        <div id="change_email_edit" class="flex-row-fluid d-none">
                            <form id="update_email_form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="email" class="form-label fs-6 fw-bold mb-3">Enter New Email Address</label>
                                            <input type="email" class="form-control form-control-lg" id="email" name="email">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <button id="update_email_submit" form="update_email_form" type="submit" class="btn btn-primary me-2 px-6">Update Email</button>
                                    <button id="update_email_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                </div>
                            </form>
                        </div>
                                
                        <div id="change_email_button" class="ms-auto">
                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                        </div>
                    </div>
                            
                    <div class="separator separator-dashed my-6"></div>
                            
                    <div class="d-flex flex-wrap align-items-center mb-0">
                        <div id="change_password" class="">
                            <div class="fs-6 fw-bold mb-1">Password</div>
                            <div class="fw-semibold text-gray-600">************</div>
                        </div>
                                
                        <div id="change_password_edit" class="flex-row-fluid d-none">
                            <form id="password_form">
                                <div class="row mb-1">
                                    <div class="col-lg-6">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="new_pasword" class="form-label fs-6 fw-bold mb-3">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="new_pasword" id="new_pasword">
                                                <button class="btn btn-light bg-transparent password-addon" type="button">
                                                    <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="confirm_password" class="form-label fs-6 fw-bold mb-3">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                                                <button class="btn btn-light bg-transparent password-addon" type="button">
                                                    <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex mt-5">
                                    <button id="update_password_submit" form="password_form" type="submit" class="btn btn-primary me-2 px-6">Update Password</button>
                                    <button id="update_password_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                </div>
                            </form>
                        </div>
                                
                        <div id="change_password_button" class="ms-auto">
                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-5 mb-xl-10">
            <div class="card-header">
                <div class="card-title">
                    <h3>Role</h3>
                </div>
            </div>
            
            <div class="card-body">
                <div class="d-flex flex-stack">
                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                        <div class="flex-grow-1 me-2">
                            <div class="text-gray-800 fs-4 fw-bold">Administrator</div>
                                    
                            <span class="text-gray-500 fw-semibold d-block fs-7">Date Assigned : 11/08/2024 04:11:30 am</span>
                        </div>
                        <button class="btn btn-sm btn-light btn-active-light-primary me-3" data-kt-billing-action="address-delete">
                            Delete       
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-5 mb-lg-10">
            <div class="card-header">
                <div class="card-title">
                    <h3>Login Sessions</h3>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                        <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                            <tr>
                                <th class="min-w-250px">Location</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-150px">Device</th>
                                <th class="min-w-150px">IP Address</th>
                                <th class="min-w-150px">Time</th>
                            </tr>
                        </thead>
                        
                        <tbody class="fw-6 fw-semibold text-gray-600"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>