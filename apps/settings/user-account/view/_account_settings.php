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

                        <div class="fw-bold mt-5">Status</div>
                        <div class="text-gray-600" id="status_side_summary"></div>

                        <div class="fw-bold mt-5">Locked Status</div>
                        <div class="text-gray-600" id="locked_status_side_summary"></div>

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
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#subscription_tab" aria-selected="false" tabindex="-1" role="tab">Subscription</a>
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
            <div class="tab-pane fade" id="subscription_tab" role="tabpanel">
                <div class="card  mb-5 mb-xl-10">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-7">
                                <h3 class="mb-2">Active until Dec 09, 2024</h3>
                                <p class="fs-6 text-gray-600 fw-semibold mb-6 mb-lg-15">We will send you a notification upon Subscription expiration </p>
                                
                                <div class="fs-5 mb-2">
                                    <span class="text-gray-800 fw-bold me-1">$24.99</span> 
                                    <span class="text-gray-600 fw-semibold">Per Month</span>
                                </div>
                                
                                <div class="fs-6 text-gray-600 fw-semibold">
                                    Extended Pro Package. Up to 100 Agents &amp; 25 Projects
                                </div>
                            </div>
                            
                            <div class="col-lg-5">
                                <div class="d-flex text-muted fw-bold fs-5 mb-3">
                                    <span class="flex-grow-1 text-gray-800">Users</span>
                                    <span class="text-gray-800">86 of 100 Used</span>
                                </div>

                                <div class="progress h-8px bg-light-primary mb-2">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 86%" aria-valuenow="86" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                
                                <div class="fs-6 text-gray-600 fw-semibold mb-10">14 Users remaining until your plan requires update</div>
                                
                                <div class="d-flex justify-content-end pb-0 px-0">
                                    <a href="#" class="btn btn-light btn-active-light-primary me-2" id="kt_account_billing_cancel_subscription_btn">Cancel Subscription</a>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pricing_plan_modal">Upgrade Plan</button>
                                </div>
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

<div class="modal fade" id="pricing_plan_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content rounded">
            <div class="modal-header justify-content-end border-0 pb-0">
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="ki-outline ki-cross fs-1"></i>
                </div>
            </div>

            <div class="modal-body pt-0 pb-15 px-5 px-xl-20">
                <div class="mb-13 text-center">
                    <h1 class="mb-3">Upgrade a Plan</h1>

                    <div class="text-muted fw-semibold fs-5">If you need more info, please check <a href="#" class="link-primary fw-bold">Pricing Guidelines</a>.</div>
                </div>

                <div class="d-flex flex-column">
                    <div class="nav-group nav-group-outline mx-auto" data-kt-buttons="true">
                        <button class="btn btn-color-gray-500 btn-active btn-active-secondary px-6 py-3 me-2 active" data-kt-plan="month">
                            Monthly
                        </button>
                        <button class="btn btn-color-gray-500 btn-active btn-active-secondary px-6 py-3" data-kt-plan="annual">
                            Annual
                        </button>
                    </div>

                    <div class="row mt-10">
                        <div class="col-lg-6 mb-10 mb-lg-0">
                            <div class="nav flex-column">
                                <label class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 active mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_startup">
                                    <div class="d-flex align-items-center me-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-success flex-shrink-0 me-6">
                                            <input class="form-check-input" type="radio" name="plan" checked="checked" value="startup" />
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center fs-2 fw-bold flex-wrap">
                                                Starter
                                            </div>
                                            <div class="fw-semibold opacity-75">
                                                Best for Small Teams or Solopreneurs
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5">
                                        <span class="mb-2">Php</span>

                                        <span class="fs-3x fw-bold" data-kt-plan-price-month="400" data-kt-plan-price-annual="4,200"> 400 </span>

                                        <span class="fs-7 opacity-50">
                                            /
                                            <span data-kt-element="period">Mon</span>
                                        </span>
                                    </div>
                                </label>
                                <label class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_advanced">
                                    <div class="d-flex align-items-center me-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-success flex-shrink-0 me-6">
                                            <input class="form-check-input" type="radio" name="plan" value="advanced" />
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center fs-2 fw-bold flex-wrap">
                                                Advanced
                                            </div>
                                            <div class="fw-semibold opacity-75">
                                                Best for 100+ team size
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5">
                                        <span class="mb-2">$</span>

                                        <span class="fs-3x fw-bold" data-kt-plan-price-month="339" data-kt-plan-price-annual="3399"> 339 </span>

                                        <span class="fs-7 opacity-50">
                                            /
                                            <span data-kt-element="period">Mon</span>
                                        </span>
                                    </div>
                                </label>
                                
                                <label class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_enterprise">
                                    <div class="d-flex align-items-center me-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-success flex-shrink-0 me-6">
                                            <input class="form-check-input" type="radio" name="plan" value="enterprise" />
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center fs-2 fw-bold flex-wrap">
                                                Enterprise
                                                <span class="badge badge-light-success ms-2 py-2 px-3 fs-7">Popular</span>
                                            </div>
                                            <div class="fw-semibold opacity-75">
                                                Best value for 1000+ team
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5">
                                        <span class="mb-2">$</span>

                                        <span class="fs-3x fw-bold" data-kt-plan-price-month="999" data-kt-plan-price-annual="9999"> 999 </span>

                                        <span class="fs-7 opacity-50">
                                            /
                                            <span data-kt-element="period">Mon</span>
                                        </span>
                                    </div>
                                </label>
                                
                                <label class="nav-link btn btn-outline btn-outline-dashed btn-color-dark btn-active btn-active-primary d-flex flex-stack text-start p-6 mb-6" data-bs-toggle="tab" data-bs-target="#kt_upgrade_plan_custom">
                                    <div class="d-flex align-items-center me-2">
                                        <div class="form-check form-check-custom form-check-solid form-check-success flex-shrink-0 me-6">
                                            <input class="form-check-input" type="radio" name="plan" value="custom" />
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center fs-2 fw-bold flex-wrap">
                                                Custom
                                            </div>
                                            <div class="fw-semibold opacity-75">
                                                Requet a custom license
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5">
                                        <a href="#" class="btn btn-sm btn-success">Contact Us</a>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="tab-content rounded h-100 bg-light p-10">
                                <div class="tab-pane fade show active" id="kt_upgrade_plan_startup">
                                    <div class="pb-5">
                                        <h2 class="fw-bold text-gray-900">What’s in Startup Plan?</h2>

                                        <div class="text-muted fw-semibold">
                                            Optimal for 10+ team size and new startup
                                        </div>
                                    </div>
                                    
                                    <div class="pt-1">
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Up to 10 Active Users </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Up to 30 Project Integrations </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Analytics Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-muted flex-grow-1"> Finance Module </span>
                                            <i class="ki-outline ki-cross-circle fs-1"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-muted flex-grow-1"> Accounting Module </span>
                                            <i class="ki-outline ki-cross-circle fs-1"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-muted flex-grow-1"> Network Platform </span>
                                            <i class="ki-outline ki-cross-circle fs-1"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold fs-5 text-muted flex-grow-1"> Unlimited Cloud Space </span>
                                            <i class="ki-outline ki-cross-circle fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="kt_upgrade_plan_advanced">
                                    <div class="pb-5">
                                        <h2 class="fw-bold text-gray-900">What’s in Startup Plan?</h2>

                                        <div class="text-muted fw-semibold">
                                            Optimal for 100+ team size and grown company
                                        </div>
                                    </div>
                                    
                                    <div class="pt-1">
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Up to 10 Active Users </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Up to 30 Project Integrations </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Analytics Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Finance Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Accounting Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-muted flex-grow-1"> Network Platform </span>
                                            <i class="ki-outline ki-cross-circle fs-1"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold fs-5 text-muted flex-grow-1"> Unlimited Cloud Space </span>
                                            <i class="ki-outline ki-cross-circle fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="kt_upgrade_plan_enterprise">
                                    <div class="pb-5">
                                        <h2 class="fw-bold text-gray-900">What’s in Startup Plan?</h2>

                                        <div class="text-muted fw-semibold">
                                            Optimal for 1000+ team and enterpise
                                        </div>
                                    </div>
                                    
                                    <div class="pt-1">
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Up to 10 Active Users </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Up to 30 Project Integrations </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Analytics Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Finance Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Accounting Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Network Platform </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Unlimited Cloud Space </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="tab-pane fade" id="kt_upgrade_plan_custom">
                                    <div class="pb-5">
                                        <h2 class="fw-bold text-gray-900">What’s in Startup Plan?</h2>

                                        <div class="text-muted fw-semibold">
                                            Optimal for corporations
                                        </div>
                                    </div>
                                    
                                    <div class="pt-1">
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Unlimited Users </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Unlimited Project Integrations </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Analytics Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Finance Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Accounting Module </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-7">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Network Platform </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <span class="fw-semibold fs-5 text-gray-700 flex-grow-1"> Unlimited Cloud Space </span>
                                            <i class="ki-outline ki-check-circle fs-1 text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-center flex-row-fluid pt-12">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary" id="pricing_plan_modal_btn">
                        <span class="indicator-label"> Upgrade Plan</span>
                        
                        <span class="indicator-progress"> Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>