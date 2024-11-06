(function($) {
    'use strict';

    $(function() {
        displayDetails('get billing cycle details');

        if($('#billing-cycle-form').length){
            billingCycleForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get billing cycle details');
        });

        $(document).on('click','#delete-billing-cycle',function() {
            const billing_cycle_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete billing cycle';
    
            Swal.fire({
                title: 'Confirm Billing Cycle Deletion',
                text: 'Are you sure you want to delete this billing cycle?',
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
                        url: 'apps/subscription/billing-cycle/controller/billing-cycle-controller.php',
                        dataType: 'json',
                        data: {
                            billing_cycle_id : billing_cycle_id, 
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
            const billing_cycle_id = $('#details-id').text();

            logNotes('billing_cycle', billing_cycle_id);
        });
    });
})(jQuery);

function billingCycleForm(){
    $('#billing-cycle-form').validate({
        rules: {
            billing_cycle_name: {
                required: true
            }
        },
        messages: {
            billing_cycle_name: {
                required: 'Enter the display name'
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
            const billing_cycle_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update billing cycle';
          
            $.ajax({
                type: 'POST',
                url: 'apps/subscription/billing-cycle/controller/billing-cycle-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&billing_cycle_id=' + encodeURIComponent(billing_cycle_id),
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
                    logNotesMain('billing_cycle', billing_cycle_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get billing cycle details':
            var billing_cycle_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/subscription/billing-cycle/controller/billing-cycle-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    billing_cycle_id : billing_cycle_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('billing-cycle-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#billing_cycle_name').val(response.billingCycleName);
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