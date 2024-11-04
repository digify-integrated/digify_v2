$(document).ready(function () {  
    $('#password-reset-form').validate({
        rules: {
            new_password: {
                required: true,
                password_strength: true
            },
            confirm_password: {
                required: true,
                equalTo: '#new_password'
            }
          },
        messages: {
            new_password: {
                required: 'Enter the password'
            },
            confirm_password: {
                required: 'Enter the confirm password',
                equalTo: 'The passwords you entered do not match'
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
            const transaction = 'password reset';
    
            $.ajax({
                type: 'POST',
                url: 'apps/settings/authentication/controller/authentication-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('reset');
                },
                success: function(response) {
                    if (response.success) {
                        setNotification(response.title, response.message, response.messageType);
                        
                        window.location.href = 'index.php';
                    } 
                    else {
                        if (response.passwordExist) {
                            showNotification(response.title, response.message, response.messageType);
                        }
                        else{
                            setNotification(response.title, response.message, response.messageType);
                            window.location.href = 'index.php';
                        }
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('reset');
                }
            });
    
            return false;
        }
    });
});