$(document).ready(function () {  
    $('#forgot-password-form').validate({
        rules: {
            email: {
                required: true,
            }
        },
        messages: {
            email: {
                required: 'Enter the email',
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
            const transaction = 'forgot password';
    
            $.ajax({
                type: 'POST',
                url: 'apps/security/authentication/controller/authentication-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('forgot-password');
                },
                success: function(response) {
                    if (response.success) {
                        setNotification(response.title, response.message, response.messageType);
                        
                        window.location.href = 'index.php';
                    } 
                    else {
                        showNotification(response.title, response.message, response.messageType);
                    }
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function() {
                    enableFormSubmitButton('forgot-password');
                }
            });
    
            return false;
        }
    });
});