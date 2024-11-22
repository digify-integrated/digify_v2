(function($) {
    'use strict';    

    $(function() {
        displayDetails('get security setting details');

        if($('#update-max-failed-login-form').length){
            updateMaxFailedLoginForm();
        }

        if($('#update-max-failed-otp-attempt-form').length){
            updateMaxFailedOTPForm();
        }

        if($('#update-default-forgot-password-link-form').length){
            updateDefaultForgotPasswordLinkForm();
        }

        if($('#update-password-expiry-duration-form').length){
            updatePasswordExpiryDurationForm();
        }

        if($('#update-session-timeout-duration-form').length){
            updateSessionTimeoutDurationForm();
        }

        if($('#update-otp-duration-form').length){
            updateOTPDurationForm();
        }

        if($('#update-reset-password-token-duration-form').length){
            updateResetPasswordTokenDurationForm();
        }

        $(document).on('click', '[data-toggle-section]', function () {
            const section = $(this).data('toggle-section');
            toggleSection(section);
        });
    });
})(jQuery);

function updateMaxFailedLoginForm(){
    $('#update-max-failed-login-form').validate({
        rules: {
            max_failed_login: {
                required: true
            }
        },
        messages: {
            max_failed_login: {
                required: 'Enter the new max failed login attempt'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {            
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update max failed login attempt';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_max_failed_login_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_max_failed_login');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_max_failed_login_submit');
                }
            });
        
            return false;
        }
    });
}

function updateMaxFailedOTPForm(){
    $('#update-max-failed-otp-attempt-form').validate({
        rules: {
            max_failed_otp_attempt: {
                required: true
            }
        },
        messages: {
            max_failed_otp_attempt: {
                required: 'Enter the new max failed OTP attempt'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update max failed OTP attempt';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_max_failed_otp_attempt_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_max_failed_otp_attempt');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_max_failed_otp_attempt_submit');
                }
            });
        
            return false;
        }
    });
}

function updateDefaultForgotPasswordLinkForm(){
    $('#update-default-forgot-password-link-form').validate({
        rules: {
            default_forgot_password_link: {
                required: true
            }
        },
        messages: {
            default_forgot_password_link: {
                required: 'Enter the new default forgot password link'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update default forgot password link';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_default_forgot_password_link_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_default_forgot_password_link');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_default_forgot_password_link_submit');
                }
            });
        
            return false;
        }
    });
}

function updatePasswordExpiryDurationForm(){
    $('#update-password-expiry-duration-form').validate({
        rules: {
            password_expiry_duration: {
                required: true
            }
        },
        messages: {
            password_expiry_duration: {
                required: 'Enter the new password expiry duration'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update password expiry duration';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_password_expiry_duration_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_password_expiry_duration');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_password_expiry_duration_submit');
                }
            });
        
            return false;
        }
    });
}

function updateSessionTimeoutDurationForm(){
    $('#update-session-timeout-duration-form').validate({
        rules: {
            session_timeout_duration: {
                required: true
            }
        },
        messages: {
            session_timeout_duration: {
                required: 'Enter the new session timeout duration'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update session timeout duration';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_session_timeout_duration_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_session_timeout_duration');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_session_timeout_duration_submit');
                }
            });
        
            return false;
        }
    });
}

function updateOTPDurationForm(){
    $('#update-otp-duration-form').validate({
        rules: {
            otp_duration: {
                required: true
            }
        },
        messages: {
            otp_duration: {
                required: 'Enter the new OTP duration'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update OTP duration';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_otp_duration_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_otp_duration');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_otp_duration_submit');
                }
            });
        
            return false;
        }
    });
}

function updateResetPasswordTokenDurationForm(){
    $('#update-reset-password-token-duration-form').validate({
        rules: {
            reset_password_token_duration: {
                required: true
            }
        },
        messages: {
            reset_password_token_duration: {
                required: 'Enter the new reset password token duration'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Action Needed: Issue Detected', error, 'error', 2500);
        },
        highlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.addClass('is-invalid');
        },
        unhighlight: function(element) {
            const $element = $(element);
            const $target = $element.hasClass('select2-hidden-accessible') ? $element.next().find('.select2-selection') : $element;
            $target.removeClass('is-invalid');
        },
        submitHandler: function(form) {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update reset password token duration';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_reset_password_token_duration_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get security setting details');
                        toggleSection('change_reset_password_token_duration');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('update_reset_password_token_duration_submit');
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get security setting details':
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/security-setting/controller/security-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {
                        $('#max_failed_login_summary').text(response.maxFailedLoginAttempt);
                        $('#max_failed_otp_attempt_summary').text(response.maxFailedOTPAttempt);
                        $('#default_forgot_password_link_summary').text(response.defaultPasswordLink);
                        $('#password_expiry_duration_summary').text(response.passwordExpiryDuration);
                        $('#session_timeout_duration_summary').text(response.sessionTimeoutDuration);
                        $('#otp_duration_summary').text(response.otpDuration);
                        $('#reset_password_token_duration_summary').text(response.resetPasswordTokenDuration);
                    } 
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = page_link;
                        }
                        else {
                            showNotification(response.title, response.message, response.messageType);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}

function toggleSection(section) {
    $(`#${section}_button`).toggleClass('d-none');
    $(`#${section}`).toggleClass('d-none');
    $(`#${section}_edit`).toggleClass('d-none');

    const formName = section.replace(/^change_/, '').replace(/_/g, '-');
    resetForm(`update-${formName}-form`);
}