(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('city options');

        displayDetails('get work location details');

        if($('#work-location-form').length){
            workLocationForm();
        }

        $(document).on('click','#delete-work-location',function() {
            const work_location_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete work location';
    
            Swal.fire({
                title: 'Confirm Work Location Deletion',
                text: 'Are you sure you want to delete this work location?',
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
                        url: 'apps/employee/work-location/controller/work-location-controller.php',
                        dataType: 'json',
                        data: {
                            work_location_id : work_location_id, 
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
            const work_location_id = $('#details-id').text();

            logNotes('work_location', work_location_id);
        });
    });
})(jQuery);

function workLocationForm(){
    $('#work-location-form').validate({
        rules: {
            work_location_name: {
                required: true
            },
            address: {
                required: true
            },
            city_id: {
                required: true
            }
        },
        messages: {
            work_location_name: {
                required: 'Enter the display name'
            },
            address: {
                required: 'Enter the address'
            },
            city_id: {
                required: 'Choose the city'
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
            const work_location_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update work location';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/work-location/controller/work-location-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&work_location_id=' + encodeURIComponent(work_location_id),
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
                    logNotes('work location', work_location_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get work location details':
            var work_location_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/work-location/controller/work-location-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    work_location_id : work_location_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('work-location-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#work_location_name').val(response.workLocationName);
                        $('#address').val(response.address);
                        $('#phone').val(response.phone);
                        $('#telephone').val(response.telephone);
                        $('#email').val(response.email);
                        
                        $('#city_id').val(response.cityID).trigger('change');
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
        case 'city options':
            
            $.ajax({
                url: 'apps/settings/city/view/_city_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#city_id').select2({
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