(function($) {
    'use strict';    

    $(function() {
        generateDropdownOptions('menu item options');

        displayDetails('get user account details');

        if($('#user-account-form').length){
            userAccountForm();
        }

        if($('#app-logo-form').length){
            updateAppLogoForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get user account details');
        });

        $(document).on('click','#delete-user-account',function() {
            const user_account_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete user account';
    
            Swal.fire({
                title: 'Confirm App Module Deletion',
                text: 'Are you sure you want to delete this user account?',
                icon: 'warning',
                showCancelButton: !0,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger mt-2',
                    cancelButton: 'btn btn-secondary ms-2 mt-2'
                },
                buttonsStyling: !1
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        url: 'apps/settings/user-account/controller/user-account-controller.php',
                        dataType: 'json',
                        data: {
                            user_account_id : user_account_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                setNotification(response.title, response.message, response.messageType);
                                window.location = page_link;
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
                    return false;
                }
            });
        });

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
        
        $(document).on('click', '#change_password_button', function() {
            togglePasswordSections();
        });
        
        $(document).on('click', '#update_password_cancel', function() {
            togglePasswordSections();
        });

        if($('#log-notes-main').length){
            const user_account_id = $('#details-id').text();

            logNotes('user_account', user_account_id);
        }

        if($('#internal-notes').length){
            const user_account_id = $('#details-id').text();

            internalNotes('user_account', user_account_id);
        }

        if($('#internal-notes-form').length){
            const user_account_id = $('#details-id').text();

            internalNotesForm('user_account', user_account_id);
        }
    });
})(jQuery);

function userAccountForm(){
    $('#user-account-form').validate({
        rules: {
            user_account_name: {
                required: true
            },
            user_account_description: {
                required: true
            },
            menu_item_id: {
                required: true
            },
            order_sequence: {
                required: true
            }
        },
        messages: {
            user_account_name: {
                required: 'Enter the display name'
            },
            user_account_description: {
                required: 'Enter the description'
            },
            menu_item_id: {
                required: 'Select the default page'
            },
            order_sequence: {
                required: 'Enter the order sequence'
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
            const transaction = 'update user account';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&user_account_id=' + encodeURIComponent(user_account_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
                        $('#user-account-modal').modal('hide');
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
                    enableFormSubmitButton('submit-data');
                    logNotesMain('user_account', user_account_id);
                }
            });
        
            return false;
        }
    });
}

function updateAppLogoForm(){
    $('#app-logo-form').validate({
        rules: {
            app_logo: {
                required: true
            }
        },
        messages: {
            app_logo: {
                required: 'Choose the app logo'
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
            const transaction = 'update app logo';

            var formData = new FormData(form);
            formData.append('user_account_id', encodeURIComponent(user_account_id));
            formData.append('transaction', transaction);
        
            $.ajax({
                type: 'POST',
                url: 'apps/settings/user-account/controller/user-account-controller.php',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-app-logo');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get user account details');
                        $('#app-logo-modal').modal('hide');
                    }
                    else {
                        if (response.isInactive || response.notExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
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
                    enableFormSubmitButton('submit-app-logo');
                }
            });
        
            return false;
        }
    });
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
                        $('#full_name').val(response.fileAs);
                        $('#username').val(response.username);
                        $('#email').val(response.email);
                        
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

function generateDropdownOptions(type){
    switch (type) {
        case 'menu item options':
            
            $.ajax({
                url: 'apps/settings/menu-item/view/_menu_item_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#menu_item_id').select2({
                        data: response,
                        dropdownParent: $('#menu_item_id').closest('.modal')
                    }).on('change', function (e) {
                        $(e.target).valid()
                    });
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
}

function toggleUsernameSections() {
    $('#change_username_button').toggleClass('d-none');
    $('#change_username').toggleClass('d-none');
    $('#change_username_edit').toggleClass('d-none');
}

function toggleEmailSections() {
    $('#change_email_button').toggleClass('d-none');
    $('#change_email').toggleClass('d-none');
    $('#change_email_edit').toggleClass('d-none');
}
    
function togglePasswordSections() {
    $('#change_password_button').toggleClass('d-none');
    $('#change_password').toggleClass('d-none');
    $('#change_password_edit').toggleClass('d-none');
}