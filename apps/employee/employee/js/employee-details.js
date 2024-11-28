(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('parent employee options');

        displayDetails('get employee details');

        if($('#employee-form').length){
            employeeForm();
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

        $(document).on('click','#log-notes-main',function() {
            const employee_id = $('#details-id').text();

            logNotes('employee', employee_id);
        });
    });
})(jQuery);

function employeeForm(){
    $('#employee-form').validate({
        rules: {
            employee_name: {
                required: true
            },
            employee: {
                required: true
            },
            file_type_id: {
                required: true
            }
        },
        messages: {
            employee_name: {
                required: 'Enter the display name'
            },
            employee: {
                required: 'Enter the employee'
            },
            file_type_id: {
                required: 'Select the file type'
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
            const transaction = 'update employee';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/employee/controller/employee-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&employee_id=' + encodeURIComponent(employee_id),
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
                    logNotesMain('employee', employee_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get employee details':
            var employee_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/employee/controller/employee-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    employee_id : employee_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('employee-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#employee_name').val(response.employeeName);
                        
                        $('#parent_employee_id').val(response.parentEmployeeID).trigger('change');
                        $('#manager_id').val(response.managerID).trigger('change');
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
                    $('#parent_employee_id').select2({
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