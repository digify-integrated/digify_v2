<div class="d-flex flex-column flex-lg-row">
    <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body">
                <div class="d-flex flex-center flex-column py-5">
                    <div class="image-input image-input-outline mb-7" data-kt-image-input="true">
                        <div class="image-input-wrapper w-120px h-120px" id="app_thumbnail" style="background-image: url(./assets/images/default/default-avatar.jpg)"></div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change employee image" data-bs-original-title="Change employee image" data-kt-initialized="1">
                                                                    <i class="ki-outline ki-pencil fs-7"></i>
                                                                    <input type="file" id="employee_image" name="employee_image" accept=".png, .jpg, .jpeg">
                                                                </label>' : '';
                        ?>
                    </div>

                    <span class="fs-3 text-gray-800 fw-bold mb-3" id="employee_name">Emma Smith</span>

                    <div class="mb-0">
                        <div class="badge badge-lg badge-light-primary d-inline">Data Center Staff</div>
                    </div>
                </div>
                
                <div class="d-flex flex-stack fs-4 py-3">
                    <div class="fw-bold rotate collapsible active" data-bs-toggle="collapse" href="#employee_view_details" role="button" aria-expanded="true" aria-controls="employee_view_details">
                        Details
                        <span class="ms-2 rotate-180"><i class="ki-outline ki-down fs-3"></i></span>
                    </div>

                    <?php
                            echo ($writeAccess['total'] > 0) ? '<span data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-original-title="Edit employee details" data-kt-initialized="1">
                                                                    <a href="javascript:void(0);" class="btn btn-sm btn-light-primary" id="update-personal-details-button" data-bs-toggle="modal" data-bs-target="#update_personal_details_modal">
                                                                        Edit
                                                                    </a>
                                                                </span>' : '';
                    ?>
                </div>

                <div class="separator"></div>

                <div id="employee_view_details" class="collapse show">
                    <div class="pb-5 fs-6">
                        <div class="fw-bold mt-5">Nickname</div>
                        <div class="text-gray-600" id="nickname-summary">--</div>
                        
                        <div class="fw-bold mt-5">Private Address</div>
                        <div class="text-gray-600" id="private-address-summary">--</div>
                        
                        <div class="fw-bold mt-5">Home-Work Distance</div>
                        <div class="text-gray-600" id="home-work-distance-summary">--</div>
                        
                        <div class="fw-bold mt-5">Civil Status</div>
                        <div class="text-gray-600" id="civil-status-summary">--</div>
                        
                        <div class="fw-bold mt-5">Number of Dependents</div>
                        <div class="text-gray-600" id="dependents-summary">--</div>
                        
                        <div class="fw-bold mt-5">Religion</div>
                        <div class="text-gray-600" id="religion-summary">--</div>
                        
                        <div class="fw-bold mt-5">Blood Type</div>
                        <div class="text-gray-600" id="blood-type-summary">--</div>
                        
                        <div class="fw-bold mt-5">Height</div>
                        <div class="text-gray-600" id="height-summary">--</div>
                        
                        <div class="fw-bold mt-5">Weight</div>
                        <div class="text-gray-600" id="weight-summary">--</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card pt-4 mb-6 mb-xl-9">
            <div class="card-header">
                <div class="card-title">
                    <h2>Settings</h2>
                </div>
            </div>
                            
            <div class="card-body pb-5">
                <div class="d-flex flex-wrap align-items-center">
                    <div id="change_pin_code">
                        <div class="fs-6 fw-bold mb-1">PIN Code</div>
                        <div class="fw-semibold text-gray-600" id="pin_code_summary"></div>
                    </div>
                                        
                    <div id="change_pin_code_edit" class="flex-row-fluid d-none">
                        <form id="update-pin-code-form">
                            <div class="row mb-6">
                                <div class="col-lg-12 mb-4 mb-lg-0">
                                    <div class="fv-row mb-0 fv-plugins-icon-container">
                                        <label for="pin_code" class="form-label fs-6 fw-bold mb-3">Enter New PIN Code</label>
                                        <input type="text" class="form-control mb-3 mb-lg-0" id="pin_code" name="pin_code" maxlength="100" autocomplete="off" <?php echo $disabled; ?>>
                                    </div>
                                </div>
                            </div>
                                        
                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                        <button id="update_pin_code_submit" form="update-pin-code-form" type="submit" class="btn btn-primary me-2 px-6">Update PIN Code</button>
                                                                        <button id="update_pin_code_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_pin_code">Cancel</button>
                                                                    </div>' : '';
                            ?>
                        </form>
                    </div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div id="change_pin_code_button" class="ms-auto" data-toggle-section="change_pin_code">
                                                                <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                            </div>' : '';
                    ?>
                </div>

                <div class="separator separator-dashed my-6"></div>
                
                <div class="d-flex flex-wrap align-items-center">
                    <div id="change_badge_id">
                        <div class="fs-6 fw-bold mb-1">Badge ID</div>
                        <div class="fw-semibold text-gray-600" id="badge_id_summary"></div>
                    </div>
                                        
                    <div id="change_badge_id_edit" class="flex-row-fluid d-none">
                        <form id="update-badge-id-form">
                            <div class="row mb-6">
                                <div class="col-lg-12 mb-4 mb-lg-0">
                                    <div class="fv-row mb-0 fv-plugins-icon-container">
                                        <label for="badge_id" class="form-label fs-6 fw-bold mb-3">Enter New Badge ID</label>
                                        <input type="text" class="form-control mb-3 mb-lg-0" id="badge_id" name="badge_id" maxlength="100" autocomplete="off" <?php echo $disabled; ?>>
                                    </div>
                                </div>
                            </div>
                                        
                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                        <button id="update_badge_id_submit" form="update-badge-id-form" type="submit" class="btn btn-primary me-2 px-6">Update Badge ID</button>
                                                                        <button id="update_badge_id_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_badge_id">Cancel</button>
                                                                    </div>' : '';
                            ?>
                        </form>
                    </div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div id="change_badge_id_button" class="ms-auto" data-toggle-section="change_badge_id">
                                                                <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                            </div>' : '';
                    ?>
                </div>
            </div>
        </div>
        
        <div class="card pt-4 mb-6 mb-xl-9">
            <div class="card-header">
                <div class="card-title flex-column">
                    <h2 class="mb-1">Language</h2>
                </div>

                <div class="card-toolbar">
                    <button type="button" class="btn btn-light-primary btn-sm">
                        Add Language
                    </button>
                </div>
            </div>
            
            <div class="card-body pb-5">
                <div class="d-flex flex-stack">
                    <div class="d-flex flex-column">
                        <span>English</span>
                        <span class="text-muted fs-6">Native</span>
                    </div>

                    <div class="d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto me-5" data-bs-toggle="modal" data-bs-target="#kt_modal_add_one_time_password">
                            <i class="ki-outline ki-pencil fs-3"></i>
                        </button>
                        
                        <button type="button" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto" id="kt_users_delete_two_step">
                            <i class="ki-outline ki-trash fs-3"></i>
                        </button>
                    </div>
                </div>

                <div class="separator separator-dashed my-5"></div>
            </div>
        </div>
    </div>
    
    <div class="flex-lg-row-fluid ms-lg-15">
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#private_details_tab" aria-selected="false" role="tab" tabindex="-1">Private Details</a>
            </li>
            
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-kt-countup-tabs="true" data-bs-toggle="tab" href="#work_information_tab" data-kt-initialized="1" aria-selected="true" role="tab">Work Information</a>
            </li>            
            
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#work_experience_tab" aria-selected="false" tabindex="-1" role="tab">Work Experience</a>
            </li>
            
            <li class="nav-item ms-auto">
                <!--begin::Action menu-->
                <a href="#" class="btn btn-primary ps-7" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    Actions
                    <i class="ki-outline ki-down fs-2 me-0"></i>
                </a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">
                            Payments
                        </div>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link px-5">
                            Create invoice
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link flex-stack px-5">
                            Create payments

                            <span class="ms-2" data-bs-toggle="tooltip" aria-label="Specify a target name for future usage and reference" data-bs-original-title="Specify a target name for future usage and reference" data-kt-initialized="1">
                                <i class="ki-outline ki-information fs-7"></i>
                            </span>
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5" data-kt-menu-trigger="hover" data-kt-menu-placement="left-start">
                        <a href="#" class="menu-link px-5">
                            <span class="menu-title">Subscription</span>
                            <span class="menu-arrow"></span>
                        </a>

                        <!--begin::Menu sub-->
                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-5">
                                    Apps
                                </a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-5">
                                    Billing
                                </a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-5">
                                    Statements
                                </a>
                            </div>
                            <!--end::Menu item-->

                            <!--begin::Menu separator-->
                            <div class="separator my-2"></div>
                            <!--end::Menu separator-->

                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <div class="menu-content px-3">
                                    <label class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input w-30px h-20px" type="checkbox" value="" name="notifications" checked="" id="kt_user_menu_notifications" />
                                        <span class="form-check-label text-muted fs-6" for="kt_user_menu_notifications">
                                            Notifications
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu sub-->
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu separator-->
                    <div class="separator my-3"></div>
                    <!--end::Menu separator-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">
                            Account
                        </div>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link px-5">
                            Reports
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5 my-1">
                        <a href="#" class="menu-link px-5">
                            Account Settings
                        </a>
                    </div>
                    <!--end::Menu item-->

                    <!--begin::Menu item-->
                    <div class="menu-item px-5">
                        <a href="#" class="menu-link text-danger px-5">
                            Delete customer
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
                <!--end::Menu-->
            </li>            
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade active show" id="private_details_tab" role="tabpanel">
                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Private Contact</h2>
                        </div>
                    </div>
                    
                    <div class="card-body pb-5">
                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_private_email">
                                <div class="fs-6 fw-bold mb-1">Private Email</div>
                                <div class="fw-semibold text-gray-600" id="private_email_summary"></div>
                            </div>
                                        
                            <div id="change_private_email_edit" class="flex-row-fluid d-none">
                                <form id="update-private-email-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="private_email" class="form-label fs-6 fw-bold mb-3">Enter New Private Email</label>
                                                <input type="text" class="form-control" maxlength="300" id="private_email" name="private_email" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_private_email_submit" form="update-private-email-form" type="submit" class="btn btn-primary me-2 px-6">Update Private Email</button>
                                                                                <button id="update_private_email_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_private_email">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_private_email_button" class="ms-auto" data-toggle-section="change_private_email">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_private_phone">
                                <div class="fs-6 fw-bold mb-1">Private Phone</div>
                                <div class="fw-semibold text-gray-600" id="private_phone_summary"></div>
                            </div>
                                        
                            <div id="change_private_phone_edit" class="flex-row-fluid d-none">
                                <form id="update-private-phone-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="private_phone" class="form-label fs-6 fw-bold mb-3">Enter New Private Phone</label>
                                                <input type="text" class="form-control" maxlength="300" id="private_phone" name="private_phone" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_private_phone_submit" form="update-private-phone-form" type="submit" class="btn btn-primary me-2 px-6">Update Private Phone</button>
                                                                                <button id="update_private_phone_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_private_phone">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_private_phone_button" class="ms-auto" data-toggle-section="change_private_phone">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_private_telephone">
                                <div class="fs-6 fw-bold mb-1">Private Telephone</div>
                                <div class="fw-semibold text-gray-600" id="private_telephone_summary"></div>
                            </div>
                                        
                            <div id="change_private_telephone_edit" class="flex-row-fluid d-none">
                                <form id="update-private-telephone-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="private_telephone" class="form-label fs-6 fw-bold mb-3">Enter New Private Telephone</label>
                                                <input type="text" class="form-control" maxlength="300" id="private_telephone" name="private_telephone" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_private_telephone_submit" form="update-private-telephone-form" type="submit" class="btn btn-primary me-2 px-6">Update Private Telephone</button>
                                                                                <button id="update_private_telephone_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_private_telephone">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_private_telephone_button" class="ms-auto" data-toggle-section="change_private_telephone">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Citizenship</h2>
                        </div>
                    </div>
                    
                    <div class="card-body pb-5">
                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_nationality">
                                <div class="fs-6 fw-bold mb-1">Nationality</div>
                                <div class="fw-semibold text-gray-600" id="nationality_summary"></div>
                            </div>
                                        
                            <div id="change_nationality_edit" class="flex-row-fluid d-none">
                                <form id="update-nationality-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="nationality_id" class="form-label fs-6 fw-bold mb-3">Select New Nationality</label>
                                                <select id="nationality_id" name="nationality_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_nationality_submit" form="update-nationality-form" type="submit" class="btn btn-primary me-2 px-6">Update Nationality</button>
                                                                                <button id="update_nationality_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_nationality">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_nationality_button" class="ms-auto" data-toggle-section="change_nationality">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_gender">
                                <div class="fs-6 fw-bold mb-1">Gender</div>
                                <div class="fw-semibold text-gray-600" id="gender_summary"></div>
                            </div>
                                        
                            <div id="change_gender_edit" class="flex-row-fluid d-none">
                                <form id="update-gender-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="gender_id" class="form-label fs-6 fw-bold mb-3">Enter New Gender</label>
                                                <select id="gender_id" name="gender_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_gender_submit" form="update-gender-form" type="submit" class="btn btn-primary me-2 px-6">Update Gender</button>
                                                                                <button id="update_gender_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_gender">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_gender_button" class="ms-auto" data-toggle-section="change_gender">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_birthday">
                                <div class="fs-6 fw-bold mb-1">Date of Birth</div>
                                <div class="fw-semibold text-gray-600" id="birthday_summary"></div>
                            </div>
                                        
                            <div id="change_birthday_edit" class="flex-row-fluid d-none">
                                <form id="update-birthday-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="birthday" class="form-label fs-6 fw-bold mb-3">Enter New Date of Birth</label>
                                                <input class="form-control mb-3 mb-lg-0 datepicker" id="birthday" name="birthday" type="text" readonly="readonly" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_birthday_submit" form="update-birthday-form" type="submit" class="btn btn-primary me-2 px-6">Update Date of Birth</button>
                                                                                <button id="update_birthday_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_birthday">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_birthday_button" class="ms-auto" data-toggle-section="change_birthday">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_place_of_birth">
                                <div class="fs-6 fw-bold mb-1">Place of Birth</div>
                                <div class="fw-semibold text-gray-600" id="place_of_birth_summary"></div>
                            </div>
                                        
                            <div id="change_place_of_birth_edit" class="flex-row-fluid d-none">
                                <form id="update-place-of-birth-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="place_of_birth" class="form-label fs-6 fw-bold mb-3">Enter New Place of Birth</label>
                                                <input type="text" class="form-control mb-3 mb-lg-0" id="place_of_birth" name="place_of_birth" maxlength="100" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_place_of_birth_submit" form="update-place-of-birth-form" type="submit" class="btn btn-primary me-2 px-6">Update Place of Birth</button>
                                                                                <button id="update_place_of_birth_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_place_of_birth">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_place_of_birth_button" class="ms-auto" data-toggle-section="change_place_of_birth">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Educational Background</h3>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row gx-9 gy-6">
                            <div class="col-xl-12" data-kt-billing-element="address">
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <div class="d-flex flex-column py-2">
                                        <div class="d-flex align-items-center fs-5 fw-bold mb-5">
                                            Address 1
                                        </div>

                                        <div class="fs-6 fw-semibold text-gray-600">
                                            Ap #285-7193 Ullamcorper Avenue<br />
                                            Amesbury HI 93373<br />
                                            US
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center py-2">
                                        <button class="btn btn-sm btn-light btn-active-light-primary me-3" data-kt-billing-action="address-delete">
                                            <span class="indicator-label"> Delete</span>
                                            
                                            <span class="indicator-progress"> Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address">Edit</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-12">
                                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed flex-stack h-xl-100 mb-10 p-6">
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">This is a very important note!</h4>

                                            <div class="fs-6 text-gray-700 pe-7">Writing headlines for blog posts is much science and probably cool audience</div>
                                        </div>
                                        
                                        <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address"> New Educational Background</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Emergency Contact</h3>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row gx-9 gy-6">
                            <div class="col-xl-6" data-kt-billing-element="address">
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <div class="d-flex flex-column py-2">
                                        <div class="d-flex align-items-center fs-5 fw-bold mb-5">
                                            Address 1
                                        </div>

                                        <div class="fs-6 fw-semibold text-gray-600">
                                            Ap #285-7193 Ullamcorper Avenue<br />
                                            Amesbury HI 93373<br />
                                            US
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center py-2">
                                        <button class="btn btn-sm btn-light btn-active-light-primary me-3" data-kt-billing-action="address-delete">
                                            <span class="indicator-label"> Delete</span>
                                            
                                            <span class="indicator-progress"> Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address">Edit</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed flex-stack h-xl-100 mb-10 p-6">
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">This is a very important note!</h4>

                                            <div class="fs-6 text-gray-700 pe-7">Writing headlines for blog posts is much science and probably cool audience</div>
                                        </div>
                                        
                                        <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address"> New Emergency Contact</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>License</h3>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row gx-9 gy-6">
                            <div class="col-xl-6" data-kt-billing-element="address">
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <div class="d-flex flex-column py-2">
                                        <div class="d-flex align-items-center fs-5 fw-bold mb-5">
                                            Address 1
                                        </div>

                                        <div class="fs-6 fw-semibold text-gray-600">
                                            Ap #285-7193 Ullamcorper Avenue<br />
                                            Amesbury HI 93373<br />
                                            US
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center py-2">
                                        <button class="btn btn-sm btn-light btn-active-light-primary me-3" data-kt-billing-action="address-delete">
                                            <span class="indicator-label"> Delete</span>
                                            
                                            <span class="indicator-progress"> Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address">Edit</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed flex-stack h-xl-100 mb-10 p-6">
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">This is a very important note!</h4>

                                            <div class="fs-6 text-gray-700 pe-7">Writing headlines for blog posts is much science and probably cool audience</div>
                                        </div>
                                        
                                        <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address"> New License</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="work_information_tab" role="tabpanel">
                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Work Information</h2>
                        </div>
                    </div>
                            
                    <div class="card-body pb-5">
                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_company">
                                <div class="fs-6 fw-bold mb-1">Company</div>
                                <div class="fw-semibold text-gray-600" id="company_summary"></div>
                            </div>
                                                
                            <div id="change_company_edit" class="flex-row-fluid d-none">
                                <form id="update-company-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="company_id" class="form-label fs-6 fw-bold mb-3">Choose New Company</label>
                                                <select id="company_id" name="company_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_company_submit" form="update-company-form" type="submit" class="btn btn-primary me-2 px-6">Update Company</button>
                                                                                <button id="update_company_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_company">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_company_button" class="ms-auto" data-toggle-section="change_company">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_department">
                                <div class="fs-6 fw-bold mb-1">Department</div>
                                <div class="fw-semibold text-gray-600" id="department_summary"></div>
                            </div>
                                                
                            <div id="change_department_edit" class="flex-row-fluid d-none">
                                <form id="update-department-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="department_id" class="form-label fs-6 fw-bold mb-3">Choose New Department</label>
                                                <select id="department_id" name="department_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_department_submit" form="update-department-form" type="submit" class="btn btn-primary me-2 px-6">Update Department</button>
                                                                                <button id="update_department_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_department">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_department_button" class="ms-auto" data-toggle-section="change_department">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_job_position">
                                <div class="fs-6 fw-bold mb-1">Job Position</div>
                                <div class="fw-semibold text-gray-600" id="job_position_summary"></div>
                            </div>
                                                
                            <div id="change_job_position_edit" class="flex-row-fluid d-none">
                                <form id="update-job-position-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="job_position_id" class="form-label fs-6 fw-bold mb-3">Choose New Job Position</label>
                                                <select id="job_position_id" name="job_position_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_job_position_submit" form="update-job-position-form" type="submit" class="btn btn-primary me-2 px-6">Update Job Position</button>
                                                                                <button id="update_job_position_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_job_position">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_job_position_button" class="ms-auto" data-toggle-section="change_job_position">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_manager">
                                <div class="fs-6 fw-bold mb-1">Manager</div>
                                <div class="fw-semibold text-gray-600" id="manager_summary"></div>
                            </div>
                                                
                            <div id="change_manager_edit" class="flex-row-fluid d-none">
                                <form id="update-manager-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="manager_id" class="form-label fs-6 fw-bold mb-3">Choose New Manager</label>
                                                <select id="manager_id" name="manager_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_manager_submit" form="update-manager-form" type="submit" class="btn btn-primary me-2 px-6">Update Manager</button>
                                                                                <button id="update_manager_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_manager">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_manager_button" class="ms-auto" data-toggle-section="change_manager">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_time_off_approver">
                                <div class="fs-6 fw-bold mb-1">Time-Off Approver</div>
                                <div class="fw-semibold text-gray-600" id="time_off_approver_summary"></div>
                            </div>
                                                
                            <div id="change_time_off_approver_edit" class="flex-row-fluid d-none">
                                <form id="update-time-off-approver-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="time_off_approver_id" class="form-label fs-6 fw-bold mb-3">Choose New Time-Off Approver</label>
                                                <select id="time_off_approver_id" name="time_off_approver_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_time_off_approver_submit" form="update-time-off-approver-form" type="submit" class="btn btn-primary me-2 px-6">Update Time-Off Approver</button>
                                                                                <button id="update_time_off_approver_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_time_off_approver">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_time_off_approver_button" class="ms-auto" data-toggle-section="change_time_off_approver">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_work_location">
                                <div class="fs-6 fw-bold mb-1">Work Location</div>
                                <div class="fw-semibold text-gray-600" id="work_location_summary"></div>
                            </div>
                                                
                            <div id="change_work_location_edit" class="flex-row-fluid d-none">
                                <form id="update-work-location-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="work_location_id" class="form-label fs-6 fw-bold mb-3">Choose New Work Location</label>
                                                <select id="work_location_id" name="work_location_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_work_location_submit" form="update-work-location-form" type="submit" class="btn btn-primary me-2 px-6">Update Work Location</button>
                                                                                <button id="update_work_location_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_work_location">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_work_location_button" class="ms-auto" data-toggle-section="change_work_location">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_on_board_date">
                                <div class="fs-6 fw-bold mb-1">On-Board Date</div>
                                <div class="fw-semibold text-gray-600" id="on_board_date_summary"></div>
                            </div>
                                                
                            <div id="change_on_board_date_edit" class="flex-row-fluid d-none">
                                <form id="update-on-board-date-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="on_board_date" class="form-label fs-6 fw-bold mb-3">Choose New On-Board Date</label>
                                                <input class="form-control mb-3 mb-lg-0 datepicker" id="on_board_date" name="on_board_date" type="text" readonly="readonly" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                                
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_on_board_date_submit" form="update-on-board-date-form" type="submit" class="btn btn-primary me-2 px-6">Update On-Board Date</button>
                                                                                <button id="update_on_board_date_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_on_board_date">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_on_board_date_button" class="ms-auto" data-toggle-section="change_on_board_date">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Work Contact</h2>
                        </div>
                    </div>
                            
                    <div class="card-body pb-5">
                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_work_email">
                                <div class="fs-6 fw-bold mb-1">Work Email</div>
                                <div class="fw-semibold text-gray-600" id="work_email_summary"></div>
                            </div>
                                        
                            <div id="change_work_email_edit" class="flex-row-fluid d-none">
                                <form id="update-work-email-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="work_email" class="form-label fs-6 fw-bold mb-3">Enter New Work Email</label>
                                                <input type="text" class="form-control" maxlength="300" id="work_email" name="work_email" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_work_email_submit" form="update-work-email-form" type="submit" class="btn btn-primary me-2 px-6">Update Work Email</button>
                                                                                <button id="update_work_email_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_work_email">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_work_email_button" class="ms-auto" data-toggle-section="change_work_email">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_work_phone">
                                <div class="fs-6 fw-bold mb-1">Work Phone</div>
                                <div class="fw-semibold text-gray-600" id="work_phone_summary"></div>
                            </div>
                                        
                            <div id="change_work_phone_edit" class="flex-row-fluid d-none">
                                <form id="update-work-phone-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="work_phone" class="form-label fs-6 fw-bold mb-3">Enter New Work Phone</label>
                                                <input type="text" class="form-control" maxlength="300" id="work_phone" name="work_phone" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_work_phone_submit" form="update-work-phone-form" type="submit" class="btn btn-primary me-2 px-6">Update Work Phone</button>
                                                                                <button id="update_work_phone_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_work_phone">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_work_phone_button" class="ms-auto" data-toggle-section="change_work_phone">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <div class="d-flex flex-wrap align-items-center">
                            <div id="change_work_telephone">
                                <div class="fs-6 fw-bold mb-1">Work Telephone</div>
                                <div class="fw-semibold text-gray-600" id="work_telephone_summary"></div>
                            </div>
                                        
                            <div id="change_work_telephone_edit" class="flex-row-fluid d-none">
                                <form id="update-work-telephone-form">
                                    <div class="row mb-6">
                                        <div class="col-lg-12 mb-4 mb-lg-0">
                                            <div class="fv-row mb-0 fv-plugins-icon-container">
                                                <label for="work_telephone" class="form-label fs-6 fw-bold mb-3">Enter New Work Telephone</label>
                                                <input type="text" class="form-control" maxlength="300" id="work_telephone" name="work_telephone" autocomplete="off" <?php echo $disabled; ?>>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    <?php
                                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                                <button id="update_work_telephone_submit" form="update-work-telephone-form" type="submit" class="btn btn-primary me-2 px-6">Update Work Telephone</button>
                                                                                <button id="update_work_telephone_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_work_telephone">Cancel</button>
                                                                            </div>' : '';
                                    ?>
                                </form>
                            </div>

                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div id="change_work_telephone_button" class="ms-auto" data-toggle-section="change_work_telephone">
                                                                        <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                    </div>' : '';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="work_experience_tab" role="tabpanel">
                <div class="card pt-4 mb-6 mb-xl-9">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Work Experience</h3>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row gx-9 gy-6">
                            <div class="col-xl-12" data-kt-billing-element="address">
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <div class="d-flex flex-column py-2">
                                        <div class="d-flex align-items-center fs-5 fw-bold mb-5">
                                            Address 1
                                        </div>

                                        <div class="fs-6 fw-semibold text-gray-600">
                                            Ap #285-7193 Ullamcorper Avenue<br />
                                            Amesbury HI 93373<br />
                                            US
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center py-2">
                                        <button class="btn btn-sm btn-light btn-active-light-primary me-3" data-kt-billing-action="address-delete">
                                            <span class="indicator-label"> Delete</span>
                                            
                                            <span class="indicator-progress"> Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span> </span>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address">Edit</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-12">
                                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed flex-stack h-xl-100 mb-10 p-6">
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">This is a very important note!</h4>

                                            <div class="fs-6 text-gray-700 pe-7">Writing headlines for blog posts is much science and probably cool audience</div>
                                        </div>
                                        
                                        <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#kt_modal_new_address"> New Work Experience</a>
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

<div id="update_personal_details_modal" class="modal fade" tabindex="-1" aria-labelledby="update_personal_details_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Personal Details</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <form id="personal-details-form" method="post" action="#">
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6" for="first_name">First Name</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="text" class="form-control mb-3 mb-lg-0" id="first_name" name="first_name" maxlength="300" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="middle_name">Middle Name</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="text" class="form-control mb-3 mb-lg-0" id="middle_name" name="middle_name" maxlength="300" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6" for="last_name">Last Name</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="text" class="form-control mb-3 mb-lg-0" id="last_name" name="last_name" maxlength="300" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="suffix">Suffix</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="text" class="form-control mb-3 mb-lg-0" id="suffix" name="suffix" maxlength="10" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Private Address</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <textarea class="form-control mb-3 mb-lg-0" id="private_address" name="private_address" maxlength="500" autocomplete="off"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <select id="private_address_city_id" name="private_address_city_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="nickname">Nickname</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="text" class="form-control mb-3 mb-lg-0" id="nickname" name="nickname" maxlength="100" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="civil_status_id">Civil Status</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <select id="civil_status_id" name="civil_status_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="dependents">Number of Dependents</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="number" class="form-control" id="dependents" name="dependents" min="0" step="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="religion_id">Religion</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <select id="religion_id" name="religion_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="blood_type_id">Blood Type</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <select id="blood_type_id" name="blood_type_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="height">Height</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <div class="input-group mb-5">
                                        <input type="number" class="form-control" id="height" name="height" min="0" step="0.01">
                                        <span class="input-group-text">cm</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="weight">Weight</label>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <div class="input-group mb-5">
                                        <input type="number" class="form-control" id="weight" name="weight" min="0" step="0.01">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="personal-details-form" class="btn btn-primary" id="submit-personal-details">Save</button>
            </div>
        </div>
    </div>
</div>


<?php require_once('components/view/_log_notes_modal.php'); ?>