(function($) {
    'use strict';

    $(function() {
        displayDetails('get address type details');

        if($('#address-type-form').length){
            addressTypeForm();
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get address type details');
        });

        $(document).on('click','#delete-address-type',function() {
            const address_type_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete address type';
    
            Swal.fire({
                title: 'Confirm Address Type Deletion',
                text: 'Are you sure you want to delete this address type?',
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
                        url: 'apps/settings/address-type/controller/address-type-controller.php',
                        dataType: 'json',
                        data: {
                            address_type_id : address_type_id, 
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
            const address_type_id = $('#details-id').text();

            logNotes('address_type', address_type_id);
        });
    });
})(jQuery);

function addressTypeForm(){
    $('#address-type-form').validate({
        rules: {
            address_type_name: {
                required: true
            }
        },
        messages: {
            address_type_name: {
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
            const address_type_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update address type';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/address-type/controller/address-type-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&address_type_id=' + encodeURIComponent(address_type_id),
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
                    logNotesMain('address_type', address_type_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get address type details':
            var address_type_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/address-type/controller/address-type-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    address_type_id : address_type_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('address-type-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#address_type_name').val(response.addressTypeName);
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