$(document).ready(function () {  
    $('#signin-form').validate({
        rules: {
            username: {
                required: true,
            },
            password: {
                required: true
            }
        },
        messages: {
            username: {
                required: 'Enter the username',
            },
            password: {
                required: 'Enter the password'
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
            const transaction = 'authenticate';
    
            $.ajax({
                type: 'POST',
                url: 'apps/settings/authentication/controller/authentication-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'JSON',
                beforeSend: function() {
                    disableFormSubmitButton('signin');
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirectLink;
                    }
                    else {
                        if (response.passwordExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location.href = response.redirectLink;
                        } else {
                            showNotification(response.title, response.message, response.messageType);
                            enableFormSubmitButton('signin');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                    enableFormSubmitButton('signin');
                }
            });
    
            return false;
        }
    });
});