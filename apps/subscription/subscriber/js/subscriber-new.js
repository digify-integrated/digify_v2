(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('subscription tier options');
        generateDropdownOptions('billing cycle options');

        if($('#subscriber-form').length){
            subscriberForm();
        }
    });
})(jQuery);

function subscriberForm(){
    $('#subscriber-form').validate({
        rules: {
            subscriber_name: {
                required: true
            },
            company_name: {
                required: true
            },
            phone: {
                required: true
            },
            email: {
                required: true
            },
            subscription_tier_id: {
                required: true
            },
            billing_cycle_id: {
                required: true
            }
        },
        messages: {
            subscriber_name: {
                required: 'Enter the subscriber name'
            },
            company_name: {
                required: 'Enter the company'
            },
            phone: {
                required: 'Enter the phone'
            },
            email: {
                required: 'Enter the email'
            },
            subscription_tier_id: {
                required: 'Choose the subscription tier'
            },
            billing_cycle_id: {
                required: 'Choose the billing cycle'
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
            const transaction = 'add subscriber';
            const page_link = document.getElementById('page-link').getAttribute('href');
          
            $.ajax({
                type: 'POST',
                url: 'apps/subscription/subscriber/controller/subscriber-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        setNotification(response.title, response.message, response.messageType);
                        window.location = page_link + '&id=' + response.subscriberID;
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

function generateDropdownOptions(type){
    switch (type) {
        case 'subscription tier options':
            
            $.ajax({
                url: 'apps/subscription/subscription-tier/view/_subscription_tier_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#subscription_tier_id').select2({
                        data: response
                    }).on('change', function (e) {
                        $(this).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'billing cycle options':
            
            $.ajax({
                url: 'apps/subscription/billing-cycle/view/_billing_cycle_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#billing_cycle_id').select2({
                        data: response
                    }).on('change', function (e) {
                        $(this).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}