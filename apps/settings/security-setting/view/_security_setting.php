<?php
    $disabled = $writeAccess['total'] > 0 ? '' : 'disabled';
?>
<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0" role="button">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Security Setting</h3>
        </div>
    </div>
                            
    <div>
        <div class="card-body border-top p-9">
            <div class="row">
                <div class="col-lg-6">
                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_max_failed_login">
                            <div class="fs-6 fw-bold mb-1">Max Failed Login Attempt</div>
                            <div class="fw-semibold text-gray-600" id="max_failed_login_summary"></div>
                        </div>
                                                
                        <div id="change_max_failed_login_edit" class="flex-row-fluid d-none">
                            <form id="update-max-failed-login-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="max_failed_login" class="form-label fs-6 fw-bold mb-3">Enter New Max Failed Login Attempt</label>
                                            <input type="number" class="form-control" id="max_failed_login" name="max_failed_login" min="0" step="1" <?php echo $disabled; ?>>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_max_failed_login_submit" form="update-full-name-form" type="submit" class="btn btn-primary me-2 px-6">Update Max Failed Login Attempt</button>
                                                                            <button id="update_max_failed_login_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_max_failed_login">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_max_failed_login_button" class="ms-auto" data-toggle-section="change_max_failed_login">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_max_failed_otp_attempt">
                            <div class="fs-6 fw-bold mb-1">Max Failed OTP Attempt</div>
                            <div class="fw-semibold text-gray-600" id="max_failed_otp_attempt_summary"></div>
                        </div>
                                                
                        <div id="change_max_failed_otp_attempt_edit" class="flex-row-fluid d-none">
                            <form id="update-max-failed-otp-attempt-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="max_failed_otp_attempt" class="form-label fs-6 fw-bold mb-3">Enter New Max Failed OTP Attempt</label>
                                            <input type="number" class="form-control" id="max_failed_otp_attempt" name="max_failed_otp_attempt" min="0" step="1" <?php echo $disabled; ?>>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_max_failed_otp_attempt_submit" form="update-max-failed-otp-attempt-form" type="submit" class="btn btn-primary me-2 px-6">Update Max Failed OTP Attempt</button>
                                                                            <button id="update_max_failed_otp_attempt_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_max_failed_otp_attempt">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_max_failed_otp_attempt_button" class="ms-auto" data-toggle-section="change_max_failed_otp_attempt">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_default_forgot_password_link">
                            <div class="fs-6 fw-bold mb-1">Default Forgot Password Link</div>
                            <div class="fw-semibold text-gray-600" id="default_forgot_password_link_summary"></div>
                        </div>
                                                
                        <div id="change_default_forgot_password_link_edit" class="flex-row-fluid d-none">
                            <form id="update-default-forgot-password-link-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="default_forgot_password_link" class="form-label fs-6 fw-bold mb-3">Enter New Default Forgot Password Link</label>
                                            <input type="text" class="form-control" id="default_forgot_password_link" name="default_forgot_password_link" autocomplete="off" <?php echo $disabled; ?>>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_default_forgot_password_link_submit" form="update-default-forgot-password-link-form" type="submit" class="btn btn-primary me-2 px-6">Update Default Forgot Password Link</button>
                                                                            <button id="update_default_forgot_password_link_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_default_forgot_password_link">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_default_forgot_password_link_button" class="ms-auto" data-toggle-section="change_default_forgot_password_link">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_password_expiry_duration">
                            <div class="fs-6 fw-bold mb-1">Password Expiry Duration</div>
                            <div class="fw-semibold text-gray-600" id="password_expiry_duration_summary"></div>
                        </div>
                                                
                        <div id="change_password_expiry_duration_edit" class="flex-row-fluid d-none">
                            <form id="update-password-expiry-duration-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="password_expiry_duration" class="form-label fs-6 fw-bold mb-3">Enter New Password Expiry Duration</label>
                                            <div class="input-group mb-5">
                                                <input type="number" class="form-control" id="password_expiry_duration" name="password_expiry_duration" min="1" step="1" aria-invalid="false" <?php echo $disabled; ?>>
                                                <span class="input-group-text">days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_password_expiry_duration_submit" form="update-password-expiry-duration-form" type="submit" class="btn btn-primary me-2 px-6">Update Password Expiry Duration</button>
                                                                            <button id="update_password_expiry_duration_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_password_expiry_duration">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_password_expiry_duration_button" class="ms-auto" data-toggle-section="change_password_expiry_duration">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>

                    <div class="separator separator-dashed my-6"></div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_session_timeout_duration">
                            <div class="fs-6 fw-bold mb-1">Session Timeout Duration</div>
                            <div class="fw-semibold text-gray-600" id="session_timeout_duration_summary"></div>
                        </div>
                                                
                        <div id="change_session_timeout_duration_edit" class="flex-row-fluid d-none">
                            <form id="update-session-timeout-duration-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="session_timeout_duration" class="form-label fs-6 fw-bold mb-3">Enter New Session Timeout Duration</label>
                                            <div class="input-group mb-5">
                                                <input type="number" class="form-control" id="session_timeout_duration" name="session_timeout_duration" min="1" step="1" aria-invalid="false" <?php echo $disabled; ?>>
                                                <span class="input-group-text">minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_session_timeout_duration_submit" form="update-session-timeout-duration-form" type="submit" class="btn btn-primary me-2 px-6">Update Session Timeout Duration</button>
                                                                            <button id="update_session_timeout_duration_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_session_timeout_duration">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_session_timeout_duration_button" class="ms-auto" data-toggle-section="change_session_timeout_duration">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_otp_duration">
                            <div class="fs-6 fw-bold mb-1">OTP Duration</div>
                            <div class="fw-semibold text-gray-600" id="otp_duration_summary"></div>
                        </div>
                                                
                        <div id="change_otp_duration_edit" class="flex-row-fluid d-none">
                            <form id="update-otp-duration-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="otp_duration" class="form-label fs-6 fw-bold mb-3">Enter New OTP Duration</label>
                                            <div class="input-group mb-5">
                                                <input type="number" class="form-control" id="otp_duration" name="otp_duration" min="1" step="1" aria-invalid="false" <?php echo $disabled; ?>>
                                                <span class="input-group-text">minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_otp_duration_submit" form="update-otp-duration-form" type="submit" class="btn btn-primary me-2 px-6">Update OTP Duration</button>
                                                                            <button id="update_otp_duration_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_otp_duration">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_otp_duration_button" class="ms-auto" data-toggle-section="change_otp_duration">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="d-flex flex-wrap align-items-center">
                        <div id="change_reset_password_token_duration">
                            <div class="fs-6 fw-bold mb-1">Reset Password Token Duration</div>
                            <div class="fw-semibold text-gray-600" id="reset_password_token_duration_summary"></div>
                        </div>
                                                
                        <div id="change_reset_password_token_duration_edit" class="flex-row-fluid d-none">
                            <form id="update-reset-password-token-duration-form">
                                <div class="row mb-6">
                                    <div class="col-lg-12 mb-4 mb-lg-0">
                                        <div class="fv-row mb-0 fv-plugins-icon-container">
                                            <label for="reset_password_token_duration" class="form-label fs-6 fw-bold mb-3">Enter New Reset Password Token Duration</label>
                                            <div class="input-group mb-5">
                                                <input type="number" class="form-control" id="reset_password_token_duration" name="reset_password_token_duration" min="1" step="1" aria-invalid="false" <?php echo $disabled; ?>>
                                                <span class="input-group-text">minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php
                                    echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                            <button id="update_reset_password_token_duration_submit" form="update-reset-password-token-duration-form" type="submit" class="btn btn-primary me-2 px-6">Update Reset Password Token Duration</button>
                                                                            <button id="update_reset_password_token_duration_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary px-6" data-toggle-section="change_reset_password_token_duration">Cancel</button>
                                                                        </div>' : '';
                                ?>
                            </form>
                        </div>

                        <?php
                            echo ($writeAccess['total'] > 0) ? '<div id="change_reset_password_token_duration_button" class="ms-auto" data-toggle-section="change_reset_password_token_duration">
                                                                    <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                                </div>' : '';
                        ?>
                    </div>
                    
                    <div class="separator separator-dashed my-6"></div>
                </div>
            </div>
        </div>
    </div>
</div>