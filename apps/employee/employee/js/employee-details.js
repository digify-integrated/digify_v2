(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('city options');
        generateDropdownOptions('country options');
        generateDropdownOptions('civil status options');
        generateDropdownOptions('religion options');
        generateDropdownOptions('blood type options');
        generateDropdownOptions('gender options');

        displayDetails('get employee personal details');
        displayDetails('get employee pin code details');
        displayDetails('get employee badge id details');
        displayDetails('get employee private email details');
        displayDetails('get employee private phone details');
        displayDetails('get employee private telephone details');
        displayDetails('get employee nationality details');
        displayDetails('get employee gender details');
        displayDetails('get employee birthday details');
        displayDetails('get employee place of birth details');

        if($('#personal-details-form').length){
            personalDetailsForm();
        }

        if($('#update-pin-code-form').length){
            updatePINCodeForm();
        }

        if($('#update-badge-id-form').length){
            updateBadgeIDForm();
        }

        if($('#update-private-email-form').length){
            updatePrivateEmailForm();
        }

        if($('#update-private-phone-form').length){
            updatePrivatePhoneForm();
        }

        if($('#update-private-telephone-form').length){
            updatePrivateTelephoneForm();
        }

        if($('#update-nationality-form').length){
            updateNationalityForm();
        }

        if($('#update-gender-form').length){
            updateGenderForm();
        }

        if($('#update-birthday-form').length){
            updateBirthdayForm();
        }

        if($('#update-place-of-birth-form').length){
            updatePlaceOfBirthForm();
        }

        $(document).on('click','#delete-employee',function() {
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete employee';
    
            Swal.fire({
                title: 'Confirm Employee Deletion',
                text: 'Are you sure you want to delete this employee?',
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
                        url: 'apps/employee/employee/controller/employee-controller.php',
                        dataType: 'json',
                        data: {
                            employee_id : employee_id, 
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

        $(document).on('click', '[data-toggle-section]', function () {
            const section = $(this).data('toggle-section');
            toggleSection(section);
        });

        $(document).on('click','#log-notes-main',function() {
            const employee_id = $('#details-id').text();

            logNotes('employee', employee_id);
        });
    });
})(jQuery);

function personalDetailsForm(){
    $('#personal-details-form').validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            private_address: {
                required: true
            },
            private_address_city_id: {
                required: true
            }
        },
        messages: {
            first_name: {
                required: 'Enter the first name'
            },
            last_name: {
                required: 'Enter the last name'
            },
            private_address: {
                required: 'Enter the address'
            },
            private_address_city_id: {
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update personal information';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-personal-details');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        $('#update_personal_details_modal').modal('hide');
                        displayDetails('get employee personal details');
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
                    enableFormSubmitButton('submit-personal-details');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updatePINCodeForm(){
    $('#update-pin-code-form').validate({
        rules: {
            pin_code: {
                required: true
            }
        },
        messages: {
            pin_code: {
                required: 'Enter the new PIN code'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee PIN code';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_pin_code_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_pin_code');
                        displayDetails('get employee pin code details');
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
                    enableFormSubmitButton('update_pin_code_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateBadgeIDForm(){
    $('#update-badge-id-form').validate({
        rules: {
            badge_id: {
                required: true
            }
        },
        messages: {
            badge_id: {
                required: 'Enter the new badge ID'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee badge id';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_badge_id_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_badge_id');
                        displayDetails('get employee badge id details');
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
                    enableFormSubmitButton('update_badge_id_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updatePrivateEmailForm(){
    $('#update-private-email-form').validate({
        rules: {
            private_email: {
                required: true
            }
        },
        messages: {
            private_email: {
                required: 'Enter the new private email'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee private email';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_private_email_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_private_email');
                        displayDetails('get employee private email details');
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
                    enableFormSubmitButton('update_private_email_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updatePrivatePhoneForm(){
    $('#update-private-phone-form').validate({
        rules: {
            private_phone: {
                required: true
            }
        },
        messages: {
            private_phone: {
                required: 'Enter the new private phone'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee private phone';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_private_phone_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_private_phone');
                        displayDetails('get employee private phone details');
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
                    enableFormSubmitButton('update_private_phone_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updatePrivateTelephoneForm(){
    $('#update-private-telephone-form').validate({
        rules: {
            private_telephone: {
                required: true
            }
        },
        messages: {
            private_telephone: {
                required: 'Enter the new private telephone'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee private telephone';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_private_telephone_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_private_telephone');
                        displayDetails('get employee private telephone details');
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
                    enableFormSubmitButton('update_private_telephone_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateNationalityForm(){
    $('#update-nationality-form').validate({
        rules: {
            nationality_id: {
                required: true
            }
        },
        messages: {
            nationality_id: {
                required: 'Choose the new nationality'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee nationality';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_nationality_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_nationality');
                        displayDetails('get employee nationality details');
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
                    enableFormSubmitButton('update_nationality_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateGenderForm(){
    $('#update-gender-form').validate({
        rules: {
            gender_id: {
                required: true
            }
        },
        messages: {
            gender_id: {
                required: 'Choose the new gender'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee gender';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_gender_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_gender');
                        displayDetails('get employee gender details');
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
                    enableFormSubmitButton('update_gender_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateBirthdayForm(){
    $('#update-birthday-form').validate({
        rules: {
            birthday: {
                required: true
            }
        },
        messages: {
            birthday: {
                required: 'Choose the new date of birth'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee birthday';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_birthday_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_birthday');
                        displayDetails('get employee birthday details');
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
                    enableFormSubmitButton('update_birthday_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updatePlaceOfBirthForm(){
    $('#update-place-of-birth-form').validate({
        rules: {
            place_of_birth: {
                required: true
            }
        },
        messages: {
            place_of_birth: {
                required: 'Choose the new place of birth'
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
            const employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update employee place of birth';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_place_of_birth_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_place_of_birth');
                        displayDetails('get employee place of birth details');
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
                    enableFormSubmitButton('update_place_of_birth_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get employee personal details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('personal-details-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#first_name').val(response.firstName);
                        $('#middle_name').val(response.middleName);
                        $('#last_name').val(response.lastName);
                        $('#suffix').val(response.suffix);
                        $('#private_address').val(response.privateAddress);
                        $('#nickname').val(response.nickname);
                        $('#dependents').val(response.dependents);
                        $('#home_work_distance').val(response.homeWorkDistance);
                        $('#height').val(response.height);
                        $('#weight').val(response.weight);

                        $('#employee-name-summary').text(response.fullName);
                        $('#nickname-summary').text(response.nickname);
                        $('#private-address-summary').text(response.employeeAddress);
                        $('#home-work-distance-summary').text(response.homeWorkDistance + ' km');
                        $('#civil-status-summary').text(response.civilStatusName);
                        $('#dependents-summary').text(response.dependents);
                        $('#religion-summary').text(response.religionName);
                        $('#blood-type-summary').text(response.bloodTypeName);
                        $('#height-summary').text(response.height + ' cm');
                        $('#weight-summary').text(response.weight + ' kg');
                        
                        $('#private_address_city_id').val(response.privateAddressCityID).trigger('change');
                        $('#civil_status_id').val(response.civilStatusID).trigger('change');
                        $('#religion_id').val(response.religionID).trigger('change');
                        $('#blood_type_id').val(response.bloodTypeID).trigger('change');
                        
                        viewPersonalInformationSummary(response);
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
        case 'get employee pin code details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-pin-code-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#pin_code_summary').text(response.pinCode);
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
        case 'get employee badge id details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-badge-id-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#badge_id_summary').text(response.badgeID);
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
        case 'get employee private email details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-private-email-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#private_email_summary').text(response.privateEmail);
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
        case 'get employee private phone details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-private-phone-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#private_phone_summary').text(response.privatePhone);
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
        case 'get employee private telephone details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-private-telephone-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#private_telephone_summary').text(response.privateTelephone);
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
        case 'get employee nationality details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-nationality-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#nationality_summary').text(response.nationalityName);
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
        case 'get employee gender details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-gender-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#gender_summary').text(response.genderName);
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
        case 'get employee birthday details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-birthday-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#birthday_summary').text(response.birthday);
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
        case 'get employee place of birth details':
            var employee_id = $('#details-id').text();
            var page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('update-place-of-birth-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#place_of_birth_summary').text(response.placeOfBirth);
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
                    $('#private_address_city_id').select2({
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
        case 'country options':
            $.ajax({
                url: 'apps/settings/country/view/_country_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#nationality_id').select2({
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
        case 'civil status options':
            $.ajax({
                url: 'apps/settings/civil-status/view/_civil_status_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#civil_status_id').select2({
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
        case 'religion options':
            $.ajax({
                url: 'apps/settings/religion/view/_religion_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#religion_id').select2({
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
        case 'blood type options':
            $.ajax({
                url: 'apps/settings/blood-type/view/_blood_type_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#blood_type_id').select2({
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
        case 'gender options':
            $.ajax({
                url: 'apps/settings/gender/view/_gender_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#gender_id').select2({
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

function toggleSection(section) {
    $(`#${section}_button`).toggleClass('d-none');
    $(`#${section}`).toggleClass('d-none');
    $(`#${section}_edit`).toggleClass('d-none');

    const formName = section.replace(/^change_/, '').replace(/_/g, '-');
    resetForm(`update-${formName}-form`);
}

function viewPersonalInformationSummary(response) {
    const summaries = {
        fullName: '#employee-name-summary',
        nickname: '#nickname-summary',
        employeeAddress: '#private-address-summary',
        homeWorkDistance: '#home-work-distance-summary',
        civilStatusName: '#civil-status-summary',
        dependents: '#dependents-summary',
        religionName: '#religion-summary',
        bloodTypeName: '#blood-type-summary',
        height: '#height-summary',
        weight: '#weight-summary'
    };

    for (const key in summaries) {
        if (response[key] !== null && response[key] !== undefined && String(response[key]).trim() !== "") {
            let value = response[key];

            if (key === "homeWorkDistance") {
                value += " km";
            } else if (key === "height") {
                value += " cm";
            } else if (key === "weight") {
                value += " kg";
            }

            $(summaries[key]).text(value);

            $(summaries[key]).closest('.personal-information-group').removeClass('d-none');
        }
    }
}