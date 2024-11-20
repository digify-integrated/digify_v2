(function($) {
    'use strict';    

    $(function() {
        displayDetails('get account settings details');

        if($('#update-full-name-form').length){
            updateFullNameForm();
        }

        $(document).on('click', '#change_max_failed_login_button', function() {
            toggleFullNameSections();
        });
        
        $(document).on('click', '#update_max_failed_login_cancel', function() {
            toggleFullNameSections();
        });
    });
})(jQuery);

function updateFullNameForm(){
    $('#update-full-name-form').validate({
        rules: {
            max_failed_login: {
                required: true
            }
        },
        messages: {
            max_failed_login: {
                required: 'Enter the new full name'
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
            const user_account_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update acccount settings full name';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_max_failed_login_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get account settings details');
                        toggleFullNameSections();
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

function displayDetails(transaction){
    switch (transaction) {
        case 'get account settings details':
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {                        
                        $('#max_failed_login_side_summary').text(response.fileAs);
                        $('#email_side_summary').text(response.email);
                        $('#username_side_summary').text(response.username);
                        $('#phone_side_summary').text(response.phoneSummary);
                        $('#password_expiry_date_side_summary').text(response.passwordExpiryDate);
                        $('#last_password_date_side_summary').text(response.lastPasswordChange);
                        $('#last_connection_date_side_summary').text(response.lastConnectionDate);
                        $('#max_failed_login_summary').text(response.fileAs);
                        $('#username_summary').text(response.username);
                        $('#email_summary').text(response.email);
                        $('#phone_summary').text(response.phoneSummary);

                        document.getElementById('two-factor-authentication').checked = response.twoFactorAuthentication === 'Yes';

                        document.getElementById('profile_picture_image').style.backgroundImage = `url(${response.profilePicture})`;
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

function toggleFullNameSections() {
    $('#change_max_failed_login_button').toggleClass('d-none');
    $('#change_max_failed_login').toggleClass('d-none');
    $('#change_max_failed_login_edit').toggleClass('d-none');

    resetForm('update-full-name-form');
}