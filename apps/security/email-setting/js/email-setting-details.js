(function($) {
    'use strict';

    $(function() {
        displayDetails('get email setting details');

        if($('#email-setting-form').length){
            emailSettingForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get email setting details');
        });

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
                        url: 'components/email-setting/controller/email-setting-controller.php',
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
                            var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                            if (xhr.responseText) {
                                fullErrorMessage += `, Response: ${xhr.responseText}`;
                            }
                            showErrorDialog(fullErrorMessage);
                        }
                    });
                    return false;
                }
            });
        });

        if($('#log-notes-main').length){
            const email_setting_id = $('#details-id').text();

            logNotesMain('email_setting', email_setting_id);
        }

        if($('#internal-notes').length){
            const email_setting_id = $('#details-id').text();

            internalNotes('email_setting', email_setting_id);
        }

        if($('#internal-notes-form').length){
            const email_setting_id = $('#details-id').text();

            internalNotesForm('email_setting', email_setting_id);
        }
    });
})(jQuery);

function emailSettingForm(){
    $('#email-setting-form').validate({
        rules: {
            email_setting_name: {
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
            },
            email_setting_description: {
                required: true
            }
        },
        messages: {
            email_setting_name: {
                required: 'Enter the display name'
            },
            mail_host: {
                required: 'Enter the host'
            },
            port: {
                required: 'Enter the port'
            },
            mail_username: {
                required: 'Enter the mail username'
            },
            mail_password: {
                required: 'Enter the mail password'
            },
            mail_from_name: {
                required: 'Enter the mail from name'
            },
            mail_from_email: {
                required: 'Enter the mail from email'
            },
            email_setting_description: {
                required: 'Enter the description'
            }
        },
        errorPlacement: function(error, element) {
            showNotification('Attention Required: Error Found', error, 'error', 2000);
        },
        highlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').addClass('is-invalid');
            }
            else {
                inputElement.addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            var inputElement = $(element);
            if (inputElement.hasClass('select2-hidden-accessible')) {
                inputElement.next().find('.select2-selection').removeClass('is-invalid');
            }
            else {
                inputElement.removeClass('is-invalid');
            }
        },
        submitHandler: function(form) {
            const email_setting_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update email setting';
          
            $.ajax({
                type: 'POST',
                url: 'components/email-setting/controller/email-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&email_setting_id=' + email_setting_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get email setting details');
                        $('#email-setting-modal').modal('hide');
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                },
                complete: function() {
                    enableFormSubmitButton('submit-data');
                    logNotesMain('email_setting', email_setting_id);
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
            const page_link = document.getElementById('page-link').getAttribute('href');
            
            $.ajax({
                url: 'components/email-setting/controller/email-setting-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    email_setting_id : email_setting_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('email-setting-form');
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
                        $('#mail_encryption').val(response.mailEncryption);
                        $('#smtp_auth').val(response.smtpAuth);
                        $('#smtp_auto_tls').val(response.smtpAutoTLS);
                        
                        $('#email_setting_name_summary').text(response.emailSettingName);
                        $('#email_setting_description_summary').text(response.emailSettingDescription);
                        $('#mail_host_summary').text(response.mailHost);
                        $('#port_summary').text(response.port);
                        $('#mail_username_summary').text(response.mailUsername);
                        $('#mail_from_name_summary').text(response.mailFromName);
                        $('#mail_from_email_summary').text(response.mailFromEmail);
                        $('#mail_encryption_summary').text(response.mailEncryption);
                        $('#smtp_auth_summary').text(response.smtpAuthSummary);
                        $('#smtp_auto_tls_summary').text(response.smtpAutoTLSSummary);
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                }
            });
            break;
    }
}