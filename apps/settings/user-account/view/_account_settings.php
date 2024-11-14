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
                    <div class="fs-3 text-gray-800 fw-bold mb-3" id="full_name_side_summary"></div>
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
                                <input class="form-check-input" type="checkbox" id="two-factor-authentication" <?php echo $disabled; ?>>
                                <span class="form-check-label fw-semibold text-muted" for="two-factor-authentication"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-lg-row-fluid ms-lg-15">
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#details_tab" aria-selected="true" role="tab">Details</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#logs_tab" aria-selected="false" tabindex="-1" role="tab">Logs</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="details_tab" role="tabpanel">
                <div class="card mb-5 mb-xl-10">
                    <div class="card-header border-0" role="button">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">User Account Details</h3>
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
                                    <form id="update-full-name-form">
                                        <div class="row mb-6">
                                            <div class="col-lg-12 mb-4 mb-lg-0">
                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                    <label for="full_name" class="form-label fs-6 fw-bold mb-3">Enter New Full Name</label>
                                                    <input type="text" class="form-control" maxlength="300" id="full_name" name="full_name" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button id="update_full_name_submit" form="update-full-name-form" type="submit" class="btn btn-primary me-2 px-6">Update Full Name</button>
                                            <button id="update_full_name_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div id="change_full_name_button" class="ms-auto">
                                                                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                        </div>' : '';
                                ?>
                            </div>

                            <div class="separator separator-dashed my-6"></div>

                            <div class="d-flex flex-wrap align-items-center">
                                <div id="change_username">
                                    <div class="fs-6 fw-bold mb-1">Username</div>
                                    <div class="fw-semibold text-gray-600" id="username_summary"></div>
                                </div>
                                        
                                <div id="change_username_edit" class="flex-row-fluid d-none">
                                    <form id="update-username-form">
                                        <div class="row mb-6">
                                            <div class="col-lg-12 mb-4 mb-lg-0">
                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                    <label for="username" class="form-label fs-6 fw-bold mb-3">Enter New Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button id="update_username_submit" form="update-username-form" type="submit" class="btn btn-primary me-2 px-6">Update Username</button>
                                            <button id="update_username_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                        
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div id="change_username_button" class="ms-auto">
                                                                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                        </div>' : '';
                                ?>
                            </div>

                            <div class="separator separator-dashed my-6"></div>

                            <div class="d-flex flex-wrap align-items-center">
                                <div id="change_email">
                                    <div class="fs-6 fw-bold mb-1">Email Address</div>
                                    <div class="fw-semibold text-gray-600" id="email_summary"></div>
                                </div>
                                        
                                <div id="change_email_edit" class="flex-row-fluid d-none">
                                    <form id="update-email-form">
                                        <div class="row mb-6">
                                            <div class="col-lg-12 mb-4 mb-lg-0">
                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                    <label for="email" class="form-label fs-6 fw-bold mb-3">Enter New Email Address</label>
                                                    <input type="email" class="form-control" id="email" name="email" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button id="update_email_submit" form="update-email-form" type="submit" class="btn btn-primary me-2 px-6">Update Email</button>
                                            <button id="update_email_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div id="change_email_button" class="ms-auto">
                                                                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                        </div>' : '';
                                ?>
                            </div>

                            <div class="separator separator-dashed my-6"></div>

                            <div class="d-flex flex-wrap align-items-center">
                                <div id="change_phone">
                                    <div class="fs-6 fw-bold mb-1">Phone</div>
                                    <div class="fw-semibold text-gray-600" id="phone_summary"></div>
                                </div>
                                        
                                <div id="change_phone_edit" class="flex-row-fluid d-none">
                                    <form id="update-phone-form">
                                        <div class="row mb-6">
                                            <div class="col-lg-12 mb-4 mb-lg-0">
                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                    <label for="phone" class="form-label fs-6 fw-bold mb-3">Enter New Phone</label>
                                                    <input type="text" class="form-control" id="phone" name="phone" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button id="update_phone_submit" form="update-phone-form" type="submit" class="btn btn-primary me-2 px-6">Update Phone</button>
                                            <button id="update_phone_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                        
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div id="change_phone_button" class="ms-auto">
                                                                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                        </div>' : '';
                                ?>
                            </div>
                                    
                            <div class="separator separator-dashed my-6"></div>
                                    
                            <div class="d-flex flex-wrap align-items-center mb-0">
                                <div id="change_password" class="">
                                    <div class="fs-6 fw-bold mb-1">Password</div>
                                    <div class="fw-semibold text-gray-600">************</div>
                                </div>
                                        
                                <div id="change_password_edit" class="flex-row-fluid d-none">
                                    <form id="update-password-form">
                                        <div class="row mb-1">
                                            <div class="col-lg-12">
                                                <div class="fv-row mb-0 fv-plugins-icon-container">
                                                    <label for="new_password" class="form-label fs-6 fw-bold mb-3">New Password</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" name="new_password" id="new_password">
                                                        <button class="btn btn-light bg-transparent password-addon" type="button">
                                                            <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex mt-5">
                                            <button id="update_password_submit" form="update-password-form" type="submit" class="btn btn-primary me-2 px-6">Update Password</button>
                                            <button id="update_password_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                                        
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div id="change_password_button" class="ms-auto">
                                                                            <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                        </div>' : '';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="logs_tab" role="tabpanel">
                <div class="card mb-5 mb-lg-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Login Sessions</h3>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                <select id="login-session-datatable-length" class="form-select w-auto">
                                    <option value="-1">All</option>
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5 text-nowrap" id="login-session-table">
                                <thead>
                                    <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
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
    </div>
</div>