(function($) {
    'use strict';    

    $(function() {
        //displayDetails('get account settings details');

        if($('#update-full-name-form').length){
            updateFullNameForm();
        }

        if($('#update-username-form').length){
            updateUsernameForm();
        }

        if($('#update-email-form').length){
            updateEmailForm();
        }

        if($('#update-phone-form').length){
            updatePhoneForm();
        }

        if($('#update-password-form').length){
            updatePasswordForm();
        }

        if($('#login-session-table').length){
            loginSessionTable('#login-session-table');
        }

        $(document).on('click', '#change_full_name_button', function() {
            toggleFullNameSections();
        });
        
        $(document).on('click', '#update_full_name_cancel', function() {
            toggleFullNameSections();
        });
    
        $(document).on('click', '#change_username_button', function() {
            toggleUsernameSections();
        });
        
        $(document).on('click', '#update_username_cancel', function() {
            toggleUsernameSections();
        });
    
        $(document).on('click', '#change_email_button', function() {
            toggleEmailSections();
        });
        
        $(document).on('click', '#update_email_cancel', function() {
            toggleEmailSections();
        });
    
        $(document).on('click', '#change_phone_button', function() {
            togglePhoneSections();
        });
        
        $(document).on('click', '#update_phone_cancel', function() {
            togglePhoneSections();
        });
        
        $(document).on('click', '#change_password_button', function() {
            togglePasswordSections();
        });
        
        $(document).on('click', '#update_password_cancel', function() {
            togglePasswordSections();
        });

        $(document).on('change','#profile_picture',function() {
            if ($(this).val() !== '' && $(this)[0].files.length > 0) {
                const transaction = 'update profile picture';
                const user_account_id = $('#details-id').text();
                var formData = new FormData();
                formData.append('profile_picture', $(this)[0].files[0]);
                formData.append('transaction', transaction);
                formData.append('user_account_id', user_account_id);
        
                $.ajax({
                    type: 'POST',
                    url: 'apps/settings/user-account/controller/user-account-controller.php',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.title, response.message, response.messageType);
                            displayDetails('get user account details');
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
            }
        });

        $(document).on('change','#two-factor-authentication',function() {
            const user_account_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href');
            var checkbox = document.getElementById('two-factor-authentication');
            var transaction = (checkbox).checked ? 'enable two factor authentication' : 'disable two factor authentication';

            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: {
                    user_account_id : user_account_id,
                    transaction : transaction
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
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
        });

        $('#login-session-datatable-length').on('change', function() {
            var table = $('#login-session-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        if($('#log-notes-main').length){
            const user_account_id = $('#details-id').text();

            logNotes('user_account', user_account_id);
        }
    });
})(jQuery);

function updateFullNameForm(){
    $('#update-full-name-form').validate({
        rules: {
            full_name: {
                required: true
            }
        },
        messages: {
            full_name: {
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
            const transaction = 'update full name';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_full_name_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
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
                    enableFormSubmitButton('update_full_name_submit');
                    logNotes('user_account', user_account_id);
                }
            });
        
            return false;
        }
    });
}

function updateUsernameForm(){
    $('#update-username-form').validate({
        rules: {
            username: {
                required: true
            }
        },
        messages: {
            username: {
                required: 'Enter the new username'
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
            const transaction = 'update username';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_username_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
                        toggleUsernameSections();
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
                    enableFormSubmitButton('update_username_submit');
                    logNotes('user_account', user_account_id);
                }
            });
        
            return false;
        }
    });
}

function updateEmailForm(){
    $('#update-email-form').validate({
        rules: {
            email: {
                required: true
            }
        },
        messages: {
            email: {
                required: 'Enter the new email address'
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
            const transaction = 'update email';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_email_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
                        toggleEmailSections();
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
                    enableFormSubmitButton('update_email_submit');
                    logNotes('user_account', user_account_id);
                }
            });
        
            return false;
        }
    });
}

function updatePhoneForm(){
    $('#update-phone-form').validate({
        rules: {
            phone: {
                required: true
            }
        },
        messages: {
            phone: {
                required: 'Enter the new phone'
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
            const transaction = 'update phone';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_phone_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
                        togglePhoneSections();
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
                    enableFormSubmitButton('update_phone_submit');
                    logNotes('user_account', user_account_id);
                }
            });
        
            return false;
        }
    });
}

function updatePasswordForm(){
    $('#update-password-form').validate({
        rules: {
            new_password: {
                required: true,
                password_strength: true
            }
          },
        messages: {
            new_password: {
                required: 'Enter the new password'
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
            const transaction = 'update password';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_password_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
                        togglePasswordSections();
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
                    enableFormSubmitButton('update_password_submit');
                }
            });
        
            return false;
        }
    });
}

function loginSessionTable(datatable_name) {
    const type = 'login session table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const user_account_id = $('#details-id').text();

    const columns = [ 
        { data: 'LOCATION' },
        { data: 'LOGIN_STATUS' },
        { data: 'DEVICE' },
        { data: 'IP_ADDRESS' },
        { data: 'LOGIN_DATE' }
    ];

    const columnDefs = [
        { width: 'auto', targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 },
        { width: 'auto', targets: 3, responsivePriority: 4 },
        { width: 'auto', targets: 4, type: 'date', responsivePriority: 5 },
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/user-account/view/_user_account_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                user_account_id: user_account_id
            },
            dataSrc: '',
            error: function(xhr, status, error) {
                handleSystemError(xhr, status, error);
            }
        },
        lengthChange: false,
        order: [[4, 'desc']],
        columns: columns,
        columnDefs: columnDefs,
        lengthMenu: lengthMenu,
        autoWidth: false,
        language: {
            emptyTable: 'No data found',
            sLengthMenu: '_MENU_',
            info: '_START_ - _END_ of _TOTAL_ items',
            loadingRecords: 'Just a moment while we fetch your data...'
        },
        fnDrawCallback: function(oSettings) {
            readjustDatatableColumn();
        }
    };

    destroyDatatable(datatable_name);
    $(datatable_name).dataTable(settings);
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get user account details':
            var user_account_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    user_account_id : user_account_id, 
                    transaction : transaction
                },
                success: function(response) {
                    if (response.success) {                        
                        $('#full_name_side_summary').text(response.fileAs);
                        $('#email_side_summary').text(response.email);
                        $('#username_side_summary').text(response.username);
                        $('#phone_side_summary').text(response.phoneSummary);
                        $('#password_expiry_date_side_summary').text(response.passwordExpiryDate);
                        $('#last_password_date_side_summary').text(response.lastPasswordChange);
                        $('#last_connection_date_side_summary').text(response.lastConnectionDate);
                        $('#full_name_summary').text(response.fileAs);
                        $('#username_summary').text(response.username);
                        $('#email_summary').text(response.email);
                        $('#phone_summary').text(response.phoneSummary);

                        document.getElementById('two-factor-authentication').checked = response.twoFactorAuthentication === 'Yes';
                        document.getElementById('multiple-login-sessions').checked = response.multipleSession === 'Yes';

                        document.getElementById('profile_picture_image').style.backgroundImage = `url(${response.profilePicture})`;

                        document.getElementById('status_side_summary').innerHTML = response.activeBadge;
                        document.getElementById('locked_status_side_summary').innerHTML = response.lockedBadge;
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
    $('#change_full_name_button').toggleClass('d-none');
    $('#change_full_name').toggleClass('d-none');
    $('#change_full_name_edit').toggleClass('d-none');

    resetForm('update-full-name-form');
}

function toggleUsernameSections() {
    $('#change_username_button').toggleClass('d-none');
    $('#change_username').toggleClass('d-none');
    $('#change_username_edit').toggleClass('d-none');
    
    resetForm('update-username-form');
}

function toggleEmailSections() {
    $('#change_email_button').toggleClass('d-none');
    $('#change_email').toggleClass('d-none');
    $('#change_email_edit').toggleClass('d-none');

    resetForm('update-email-form');
}

function togglePhoneSections() {
    $('#change_phone_button').toggleClass('d-none');
    $('#change_phone').toggleClass('d-none');
    $('#change_phone_edit').toggleClass('d-none');

    resetForm('update-phone-form');
}
    
function togglePasswordSections() {
    $('#change_password_button').toggleClass('d-none');
    $('#change_password').toggleClass('d-none');
    $('#change_password_edit').toggleClass('d-none');

    resetForm('update-password-form');
}