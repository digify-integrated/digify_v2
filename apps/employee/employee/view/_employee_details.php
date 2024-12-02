<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <div class="me-7 mb-4">
                <div class="symbol symbol-50px symbol-lg-75px symbol-fixed position-relative">
                    <img src="./assets/images/default/default-avatar.jpg" alt="image">
                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px"></div>
                </div>
            </div>

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-gray-900 fs-2 fw-bold me-1">Max Smith</span>
                        </div>

                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-profile-circle fs-4 me-1"></i> Developer
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-geolocation fs-4 me-1"></i> SF, Bay Area
                            </a>
                            <a href="#" class="d-flex align-items-center text-gray-500 text-hover-primary mb-2">
                                <i class="ki-outline ki-sms fs-4"></i> max@kt.com
                            </a>
                        </div>
                    </div>
                    
                    <div class="d-flex my-4">
                        <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_offer_a_deal">Hire Me</a>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#private-information-tab" aria-selected="false" role="tab" tabindex="-1">Private Information</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#work-information-tab" aria-selected="true" role="tab">Work Information</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab" href="#resume-tab" aria-selected="false" role="tab" tabindex="-1">Resume</a>
            </li>
        </ul>
    </div>
</div>


<div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="private-information-tab" role="tabpanel">
                <div class="card card-flush mb-6">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Personal Details</h3>
                        </div>
                    </div>

                    <form id="app-module-form" class="form" method="post" action="#">
                        <div class="card-body border-top p-9">
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label required fw-semibold fs-6">Full Name</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-3 fv-row fv-plugins-icon-container">
                                            <input type="text" class="form-control mb-3 mb-lg-0" id="first_name" name="first_name" maxlength="300" placeholder="First Name" autocomplete="off">
                                        </div>
                                        <div class="col-lg-3 fv-row fv-plugins-icon-container">
                                            <input type="text" class="form-control mb-3 mb-lg-0" id="middle_name" name="middle_name" maxlength="300" placeholder="Middle Name" autocomplete="off">
                                        </div>
                                        <div class="col-lg-3 fv-row fv-plugins-icon-container">
                                            <input type="text" class="form-control mb-3 mb-lg-0" id="last_name" name="last_name" maxlength="300" placeholder="Last Name" autocomplete="off">
                                        </div>
                                        <div class="col-lg-3 fv-row fv-plugins-icon-container">
                                            <input type="text" class="form-control mb-0" id="suffix" name="suffix" maxlength="10" placeholder="Suffix" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Nickname</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="text" class="form-control mb-3 mb-lg-0" id="nickname" name="nickname" maxlength="100" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Civil Status</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="civil_status_id" name="civil_status_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Number of Dependent</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input type="number" class="form-control" id="dependents" name="dependents" min="0" step="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Religion</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="religion_id" name="religion_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Blood Type</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="blood_type_id" name="blood_type_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Height</label>
                                <div class="col-lg-9">
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
                            <div class="row">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Weight</label>
                                <div class="col-lg-9">
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
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                    <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                </div>' : '';
                        ?>
                    </form>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-flush mb-6">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Personal Contact</h3>
                                </div>
                            </div>

                            <form id="app-module-form" class="form" method="post" action="#">
                                <div class="card-body border-top p-9">
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
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Private Email</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="email" class="form-control mb-3 mb-lg-0" id="private_email" name="private_email" maxlength="255" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Private Phone</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="private_phone" name="private_phone" maxlength="20" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Private Telephone</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="private_telephone" name="private_telephone" maxlength="20" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Home-Work Distance</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <div class="input-group mb-5">
                                                        <input type="number" class="form-control" id="home_work_distance" name="home_work_distance" min="0" step="0.01">
                                                        <span class="input-group-text">km</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                            <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-flush">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Citizenship</h3>
                                </div>
                            </div>

                            <form id="app-module-form" class="form" method="post" action="#">
                                <div class="card-body border-top p-9">
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Nationality</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select id="nationality_id" name="nationality_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Gender</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <select id="gender_id" name="gender_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Date of Birth</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input class="form-control mb-3 mb-lg-0 datepicker" id="birthday" name="birthday" type="text" readonly="readonly">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Place of Birth</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="place_of_birth" name="place_of_birth" maxlength="100" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                            <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane fade" id="work-information-tab" role="tabpanel">
                <div class="card card-flush mb-6">
                    <div class="card-header border-0">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Work Information</h3>
                        </div>
                    </div>

                    <form id="app-module-form" class="form" method="post" action="#">
                        <div class="card-body border-top p-9">
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label required fw-semibold fs-6">Company</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="company_id" name="company_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label required fw-semibold fs-6">Department</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="department_id" name="department_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label required fw-semibold fs-6">Job Position</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="job_position_id" name="job_position_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Manager</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="manager_id" name="manager_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Time-Off Approver</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="time_off_approver_id" name="time_off_approver_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-3 col-form-label fw-semibold fs-6">Work Location</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <select id="work_location_id" name="work_location_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-lg-3 col-form-label required fw-semibold fs-6">On-Board Date</label>
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                            <input class="form-control mb-3 mb-lg-0 datepicker" id="on_board_date" name="on_board_date" type="text" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                    <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                </div>' : '';
                        ?>
                    </form>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card card-flush mb-6">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Work Contact</h3>
                                </div>
                            </div>

                            <form id="app-module-form" class="form" method="post" action="#">
                                <div class="card-body border-top p-9">
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Work Email</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="email" class="form-control mb-3 mb-lg-0" id="work_email" name="work_email" maxlength="255" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Work Phone</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="work_phone" name="work_phone" maxlength="20" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Work Telephone</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="work_telephone" name="work_telephone" maxlength="20" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                            <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-flush">
                            <div class="card-header border-0">
                                <div class="card-title m-0">
                                    <h3 class="fw-bold m-0">Settings</h3>
                                </div>
                            </div>

                            <form id="app-module-form" class="form" method="post" action="#">
                                <div class="card-body border-top p-9">
                                    <div class="row mb-6">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">PIN Code</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="pin_code" name="pin_code" maxlength="100" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Badge ID</label>
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                                    <input type="text" class="form-control mb-3 mb-lg-0" id="badge_id" name="badge_id" maxlength="100" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                            <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="resume-tab" role="tabpanel">
                
            </div>
        </div>

<?php require_once('components/view/_log_notes_modal.php'); ?>