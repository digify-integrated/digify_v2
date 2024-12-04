(function($) {
    'use strict';

    $(function() {
        displayDetails('get email setting details');

        if($('#email-setting-form').length){
            emailSettingForm();
        }

        $(document).on('click','#delete-email-setting',function() {
            const email_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete email setting';
    
            Swal.fire({
                title: 'Confirm Email Setting Deletion',
                text: 'Are you sure you want to delete this email setting?',
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
                        url: 'apps/settings/email-setting/controller/email-setting-controller.php',
                        dataType: 'json',
                        data: {
                            email_setting_id : email_setting_id, 
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

        $(document).on('click','#log-notes-main',function() {
            const email_setting_id = $('#details-id').text();

            logNotes('email_setting', email_setting_id);
        });
    });
})(jQuery);

function emailSettingForm(){
    $('#email-setting-form').validate({
        rules: {
            email_setting_name: {
                required: true
            },
            email_setting_description: {
                required: true
            },
            mail_host: {
                required: true
            },
            port: {
                required: true
            },
            mail_username: {
                required: true
            },
            mail_password: {
                required: true
            },
            mail_from_name: {
                required: true
            },
            mail_from_email: {
                required: true
            }
        },
        messages: {
            email_setting_name: {
                required: 'Enter the display name'
            },
            email_setting_description: {
                required: 'Enter the description'
            },
            mail_host: {
                required: 'Enter the host'
            },
            port: {
                required: 'Enter the port'
            },
            mail_username: {
                required: 'Enter the email username'
            },
            mail_password: {
                required: 'Enter the email password'
            },
            mail_from_name: {
                required: 'Enter the mail from name'
            },
            mail_from_email: {
                required: 'Enter the mail from email'
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
            const email_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update email setting';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/email-setting/controller/email-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&email_setting_id=' + encodeURIComponent(email_setting_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
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
                },
                complete: function() {
                    enableFormSubmitButton('submit-data');
                    logNotes('email_setting', email_setting_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get email setting details':
            var email_setting_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/email-setting/controller/email-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    email_setting_id : email_setting_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('email-setting-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#email_setting_name').val(response.emailSettingName);
                        $('#email_setting_description').val(response.emailSettingDescription);
                        $('#mail_host').val(response.mailHost);
                        $('#port').val(response.port);
                        $('#mail_username').val(response.mailUsername);
                        $('#mail_password').val(response.mailPassword);
                        $('#mail_from_name').val(response.mailFromName);
                        $('#mail_from_email').val(response.mailFromEmail);

                        $('#mail_encryption').val(response.mailEncryption).trigger('change');
                        $('#smtp_auth').val(response.smtpAuth).trigger('change');
                        $('#smtp_auto_tls').val(response.smtpAutoTLS).trigger('change');
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