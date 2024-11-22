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
            const transaction = 'add email setting';
            const page_link = document.getElementById('page-link').getAttribute('href');
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/email-setting/controller/email-setting-controller.php',
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
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('submit-data');
                }
            });
        
            return false;
        }
    });
}