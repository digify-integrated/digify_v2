(function($) {
    'use strict';

    $(function() {
        if($('#email-setting-form').length){
            emailSettingForm();
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
            const transaction = 'add email setting';
            const page_link = document.getElementById('page-link').getAttribute('href');
          
            $.ajax({
                type: 'POST',
                url: 'components/email-setting/controller/email-setting-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        setNotification(response.title, response.message, response.messageType);
                        window.location = page_link + '&id=' + response.emailSettingID;
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
                    var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                    if (xhr.responseText) {
                        fullErrorMessage += `, Response: ${xhr.responseText}`;
                    }
                    showErrorDialog(fullErrorMessage);
                },
                complete: function() {
                    enableFormSubmitButton('submit-data');
                }
            });
        
            return false;
        }
    });
}