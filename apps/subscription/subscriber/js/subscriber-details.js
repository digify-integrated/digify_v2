(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('subscription tier options');
        generateDropdownOptions('billing cycle options');

        displayDetails('get subscriber details');

        if($('#subscriber-form').length){
            subscriberForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get subscriber details');
        });

        $(document).on('click','#delete-subscriber',function() {
            const subscriber_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete subscriber';
    
            Swal.fire({
                title: 'Confirm Subscriber Deletion',
                text: 'Are you sure you want to delete this subscriber?',
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
                        url: 'apps/subscription/subscriber/controller/subscriber-controller.php',
                        dataType: 'json',
                        data: {
                            subscriber_id : subscriber_id, 
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
            const subscriber_id = $('#details-id').text();

            logNotes('subscriber', subscriber_id);
        });
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
            },
            subscriber_status: {
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
            },
            subscriber_status: {
                required: 'Choose the subscriber status'
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
            const subscriber_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update subscriber';
          
            $.ajax({
                type: 'POST',
                url: 'apps/subscription/subscriber/controller/subscriber-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&subscriber_id=' + encodeURIComponent(subscriber_id),
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
                    logNotesMain('subscriber', subscriber_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get subscriber details':
            var subscriber_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/subscription/subscriber/controller/subscriber-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    subscriber_id : subscriber_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('subscriber-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#subscriber_name').val(response.subscriberName);
                        $('#company_name').val(response.companyName);
                        $('#phone').val(response.phone);
                        $('#email').val(response.email);

                        $('#subscription_tier_id').val(response.subscriptionTierID).trigger('change');
                        $('#billing_cycle_id').val(response.billingCycleID).trigger('change');
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