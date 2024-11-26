(function($) {
    'use strict';

    $(function() {
        if($('#bank-form').length){
            bankForm();
        }
    });
})(jQuery);

function bankForm(){
    $('#bank-form').validate({
        rules: {
            bank_name: {
                required: true
            },
            bank_identifier_code: {
                required: true
            }
        },
        messages: {
            bank_name: {
                required: 'Enter the display name'
            },
            bank_identifier_code: {
                required: 'Choose the bank identifier code'
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
            const transaction = 'add bank';
            const page_link = document.getElementById('page-link').getAttribute('href');
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/bank/controller/bank-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        setNotification(response.title, response.message, response.messageType);
                        window.location = page_link + '&id=' + response.bankID;
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