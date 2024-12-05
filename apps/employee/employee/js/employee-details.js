(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('city options');
        generateDropdownOptions('country options');
        generateDropdownOptions('civil status options');
        generateDropdownOptions('religion options');
        generateDropdownOptions('blood type options');
        generateDropdownOptions('gender options');
        generateDropdownOptions('company options');
        generateDropdownOptions('department options');
        generateDropdownOptions('job position options');
        generateDropdownOptions('parent employee options');
        generateDropdownOptions('work location options');
        generateDropdownOptions('employee language options');
        generateDropdownOptions('language proficiency options');

        displayDetails('get employee personal details');
        displayDetails('get employee image details');
        displayDetails('get employee pin code details');
        displayDetails('get employee badge id details');
        displayDetails('get employee private email details');
        displayDetails('get employee private phone details');
        displayDetails('get employee private telephone details');
        displayDetails('get employee nationality details');
        displayDetails('get employee gender details');
        displayDetails('get employee birthday details');
        displayDetails('get employee place of birth details');
        displayDetails('get employee company details');
        displayDetails('get employee department details');
        displayDetails('get employee job position details');
        displayDetails('get employee manager details');
        displayDetails('get employee time-off approver details');
        displayDetails('get employee work location details');
        displayDetails('get employee on-board date details');
        displayDetails('get employee work email details');
        displayDetails('get employee work phone details');
        displayDetails('get employee work telephone details');

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
        
        if($('#update-company-form').length){
            updateCompanyForm();
        }
        
        if($('#update-department-form').length){
            updateDepartmentForm();
        }
        
        if($('#update-job-position-form').length){
            updateJobPositionForm();
        }
        
        if($('#update-manager-form').length){
            updateManagerForm();
        }
        
        if($('#update-time-off-approver-form').length){
            updateTimeOffApproverForm();
        }
        
        if($('#update-work-location-form').length){
            updateWorkLocationForm();
        }
        
        if($('#update-on-board-date-form').length){
            updateOnBoardDateForm();
        }

        if($('#update-work-email-form').length){
            updateWorkEmailForm();
        }

        if($('#update-work-phone-form').length){
            updateWorkPhoneForm();
        }

        if($('#update-work-telephone-form').length){
            updateWorkTelephoneForm();
        }

        if($('#employee-language-form').length){
            saveEmployeeLanguage();
        }

        $(document).on('change','#employee_image',function() {
            if ($(this).val() !== '' && $(this)[0].files.length > 0) {
                const transaction = 'update employee image';
                const employee_id = $('#details-id').text();
                var formData = new FormData();
                formData.append('employee_image', $(this)[0].files[0]);
                formData.append('transaction', transaction);
                formData.append('employee_id', employee_id);
        
                $.ajax({
                    type: 'POST',
                    url: 'apps/employee/employee/controller/employee-controller.php',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.title, response.message, response.messageType);
                            displayDetails('get employee image details');
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
            }
        });

        $(document).on('click','#add-language',function() {
            resetForm('employee-language-form');
        });

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

        $(document).on('click','.delete-employee-language',function() {
            const employee_language_id = $(this).data('employee-language-id');
            const transaction = 'delete employee language';
    
            Swal.fire({
                title: 'Confirm Language Deletion',
                text: 'Are you sure you want to delete this language?',
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
                            employee_language_id : employee_language_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                languageSummary();
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    showNotification(response.title, response.message, response.messageType);
                                    languageSummary();
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

        if($('#language-summary').length){
            languageSummary();
        }

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

function languageSummary(){
    const employee_id = $('#details-id').text();
    const type = 'language list';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');

    $.ajax({
        type: 'POST',
        url: 'apps/employee/employee/view/_employee_generation.php',
        dataType: 'json',
        data: { 
            type : type, 
            employee_id : employee_id,
            page_id : page_id,
            page_link : page_link
        },
        success: function (result) {
            document.getElementById('language-summary').innerHTML = result[0].LANGUAGE_SUMMARY;
        }
    });
}

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
                required: 'Enter the new place of birth'
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

function updateCompanyForm(){
    $('#update-company-form').validate({
        rules: {
            company_id: {
                required: true
            }
        },
        messages: {
            company_id: {
                required: 'Choose the new company'
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
            const transaction = 'update employee company';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_company_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_company');
                        displayDetails('get employee company details');
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
                    enableFormSubmitButton('update_company_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateDepartmentForm(){
    $('#update-department-form').validate({
        rules: {
            department_id: {
                required: true
            }
        },
        messages: {
            department_id: {
                required: 'Choose the new department'
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
            const transaction = 'update employee department';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_department_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_department');
                        displayDetails('get employee department details');
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
                    enableFormSubmitButton('update_department_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateJobPositionForm(){
    $('#update-job-position-form').validate({
        rules: {
            job_position_id: {
                required: true
            }
        },
        messages: {
            job_position_id: {
                required: 'Choose the new job position'
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
            const transaction = 'update employee job position';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_job_position_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_job_position');
                        displayDetails('get employee job position details');
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
                    enableFormSubmitButton('update_job_position_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateManagerForm(){
    $('#update-manager-form').validate({
        rules: {
            manager_id: {
                required: true
            }
        },
        messages: {
            manager_id: {
                required: 'Choose the new manager'
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
            const transaction = 'update employee manager';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_manager_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_manager');
                        displayDetails('get employee manager details');
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
                    enableFormSubmitButton('update_manager_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateTimeOffApproverForm(){
    $('#update-time-off-approver-form').validate({
        rules: {
            time_off_approver_id: {
                required: true
            }
        },
        messages: {
            time_off_approver_id: {
                required: 'Choose the new time-off approver'
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
            const transaction = 'update employee time-off approver';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_time_off_approver_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_time_off_approver');
                        displayDetails('get employee time-off approver details');
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
                    enableFormSubmitButton('update_time_off_approver_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateWorkLocationForm(){
    $('#update-work-location-form').validate({
        rules: {
            work_location_id: {
                required: true
            }
        },
        messages: {
            work_location_id: {
                required: 'Choose the new work location'
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
            const transaction = 'update employee work location';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_work_location_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_work_location');
                        displayDetails('get employee work location details');
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
                    enableFormSubmitButton('update_work_location_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateOnBoardDateForm(){
    $('#update-on-board-date-form').validate({
        rules: {
            on_board_date: {
                required: true
            }
        },
        messages: {
            on_board_date: {
                required: 'Choose the new on-board date'
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
            const transaction = 'update employee on-board date';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_on_board_date_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_on_board_date');
                        displayDetails('get employee on-board date details');
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
                    enableFormSubmitButton('update_on_board_date_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateWorkEmailForm(){
    $('#update-work-email-form').validate({
        rules: {
            work_email: {
                required: true
            }
        },
        messages: {
            work_email: {
                required: 'Enter the new work email'
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
            const transaction = 'update employee work email';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_work_email_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_work_email');
                        displayDetails('get employee work email details');
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
                    enableFormSubmitButton('update_work_email_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateWorkPhoneForm(){
    $('#update-work-phone-form').validate({
        rules: {
            work_phone: {
                required: true
            }
        },
        messages: {
            work_phone: {
                required: 'Enter the new work phone'
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
            const transaction = 'update employee work phone';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_work_phone_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_work_phone');
                        displayDetails('get employee work phone details');
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
                    enableFormSubmitButton('update_work_phone_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function updateWorkTelephoneForm(){
    $('#update-work-telephone-form').validate({
        rules: {
            work_telephone: {
                required: true
            }
        },
        messages: {
            work_telephone: {
                required: 'Enter the new work telephone'
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
            const transaction = 'update employee work telephone';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('update_work_telephone_submit');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        toggleSection('change_work_telephone');
                        displayDetails('get employee work telephone details');
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
                    enableFormSubmitButton('update_work_telephone_submit');
                    logNotes('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function saveEmployeeLanguage(){
    $('#employee-language-form').validate({
        rules: {
            language_id: {
                required: true
            },
            language_proficiency_id: {
                required: true
            }
        },
        messages: {
            language_id: {
                required: 'Choose the language'
            },
            language_proficiency_id: {
                required: 'Choose the language proficiency'
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
            const transaction = 'save employee language';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-employee-language');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);

                        $('#employee_language_modal').modal('hide');
                        languageSummary();
                        generateDropdownOptions('employee language options');
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
                    enableFormSubmitButton('submit-employee-language');
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
        case 'get employee image details':
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
                success: function(response) {
                    if (response.success) {
                        document.getElementById('employee_image_thumbnail').style.backgroundImage = `url(${response.employeeImage})`;
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
        case 'get employee company details':
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
                    resetForm('update-company-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#company_summary').text(response.companyName);
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
        case 'get employee department details':
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
                    resetForm('update-department-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#department_summary').text(response.departmentName);
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
        case 'get employee job position details':
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
                    resetForm('update-job-position-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#job_position_summary').text(response.jobPositionName);
                        $('#job-position-summary').text(response.jobPositionName);
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
        case 'get employee manager details':
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
                    resetForm('update-manager-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#manager_summary').text(response.managerName);
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
        case 'get employee time-off approver details':
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
                    resetForm('update-time-off-approver-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#time_off_approver_summary').text(response.timeOffApproverName);
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
        case 'get employee work location details':
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
                    resetForm('update-work-location-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#work_location_summary').text(response.workLocationName);
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
        case 'get employee on-board date details':
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
                    resetForm('update-on-board-date-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#on_board_date_summary').text(response.onBoardDate);
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
        case 'get employee work email details':
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
                    resetForm('update-work-email-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#work_email_summary').text(response.workEmail);
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
        case 'get employee work phone details':
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
                    resetForm('update-work-phone-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#work_phone_summary').text(response.workPhone);
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
        case 'get employee work telephone details':
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
                    resetForm('update-work-telephone-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#work_telephone_summary').text(response.workTelephone);
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
        case 'company options':
            $.ajax({
                url: 'apps/settings/company/view/_company_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#company_id').select2({
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
        case 'department options':
            $.ajax({
                url: 'apps/employee/department/view/_department_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#department_id').select2({
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
        case 'job position options':
            $.ajax({
                url: 'apps/employee/job-position/view/_job_position_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#job_position_id').select2({
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
        case 'parent employee options':
            var employee_id = $('#details-id').text();

            $.ajax({
                url: 'apps/employee/employee/view/_employee_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    employee_id : employee_id
                },
                success: function(response) {
                    $('#manager_id').select2({
                        data: response
                    }).on('change', function (e) {
                        $(this).valid()
                    });

                    $('#time_off_approver_id').select2({
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
        case 'work location options':
            var employee_id = $('#details-id').text();

            $.ajax({
                url: 'apps/employee/work-location/view/_work_location_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    employee_id : employee_id
                },
                success: function(response) {
                    $('#work_location_id').select2({
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
        case 'employee language options':
            var employee_id = $('#details-id').text();

            $.ajax({
                url: 'apps/settings/language/view/_language_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    employee_id : employee_id
                },
                beforeSend: function() {
                    $('#language_id').empty();
                },
                success: function(response) {
                    $('#language_id').select2({
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
        case 'language proficiency options':
            $.ajax({
                url: 'apps/settings/language-proficiency/view/_language_proficiency_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#language_proficiency_id').select2({
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