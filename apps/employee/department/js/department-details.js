(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('parent department options');

        displayDetails('get department details');

        if($('#department-form').length){
            departmentForm();
        }

        $(document).on('click','#delete-department',function() {
            const department_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete department';
    
            Swal.fire({
                title: 'Confirm Department Deletion',
                text: 'Are you sure you want to delete this department?',
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
                        url: 'apps/employee/department/controller/department-controller.php',
                        dataType: 'json',
                        data: {
                            department_id : department_id, 
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
            const department_id = $('#details-id').text();

            logNotes('department', department_id);
        });
    });
})(jQuery);

function departmentForm(){
    $('#department-form').validate({
        rules: {
            department_name: {
                required: true
            },
            department: {
                required: true
            },
            file_type_id: {
                required: true
            }
        },
        messages: {
            department_name: {
                required: 'Enter the display name'
            },
            department: {
                required: 'Enter the department'
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
            const department_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update department';
          
            $.ajax({
                type: 'POST',
                url: 'apps/employee/department/controller/department-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&department_id=' + encodeURIComponent(department_id),
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
                    logNotesMain('department', department_id);
                }
            });
        
            return false;
        }
    });
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get department details':
            var department_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/employee/department/controller/department-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    department_id : department_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('department-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#department_name').val(response.departmentName);
                        
                        $('#parent_department_id').val(response.parentDepartmentID).trigger('change');
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
        case 'parent department options':
            var department_id = $('#details-id').text();
            
            $.ajax({
                url: 'apps/employee/department/view/_department_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    department_id : department_id
                },
                success: function(response) {
                    $('#parent_department_id').select2({
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