(function($) {
    'use strict';

    $(function() {
        displayDetails('get subscription tier details');

        if($('#subscription-tier-form').length){
            subscriptionTierForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get subscription tier details');
        });

        $(document).on('click','#delete-subscription-tier',function() {
            const subscription_tier_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete subscription tier';
    
            Swal.fire({
                title: 'Confirm Subscription Tier Deletion',
                text: 'Are you sure you want to delete this subscription tier?',
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
                        url: 'apps/subscription/subscription-tier/controller/subscription-tier-controller.php',
                        dataType: 'json',
                        data: {
                            subscription_tier_id : subscription_tier_id, 
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
            const subscription_tier_id = $('#details-id').text();

            logNotes('subscription_tier', subscription_tier_id);
        });
    });
})(jQuery);

function subscriptionTierForm(){
    $('#subscription-tier-form').validate({
        rules: {
            subscription_tier_name: {
                required: true
            },
            subscription_tier_description: {
                required: true
            },
            menu_item_id: {
                required: true
            },
            order_sequence: {
                required: true
            }
        },
        messages: {
            subscription_tier_name: {
                required: 'Enter the display name'
            },
            subscription_tier_description: {
                required: 'Enter the description'
            },
            menu_item_id: {
                required: 'Select the default page'
            },
            order_sequence: {
                required: 'Enter the order sequence'
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
            const subscription_tier_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update subscription tier';
          
            $.ajax({
                type: 'POST',
                url: 'apps/subscription/subscription-tier/controller/subscription-tier-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&subscription_tier_id=' + encodeURIComponent(subscription_tier_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get subscription tier details');
                        $('#subscription-tier-modal').modal('hide');
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
                    logNotesMain('subscription_tier', subscription_tier_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get subscription tier details':
            var subscription_tier_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/subscription/subscription-tier/controller/subscription-tier-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    subscription_tier_id : subscription_tier_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('subscription-tier-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#subscription_tier_name').val(response.subscriptionTierName);
                        $('#subscription_tier_description').val(response.subscriptionTierDescription);
                        $('#order_sequence').val(response.orderSequence);
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