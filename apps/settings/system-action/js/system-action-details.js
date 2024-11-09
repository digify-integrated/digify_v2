(function($) {
    'use strict';

    $(function() {
        displayDetails('get system action details');

        if($('#system-action-form').length){
            systemActionForm();
        }

        if($('#role-permission-assignment-form').length){
            rolePermissionAssignmentForm();
        }

        if($('#role-permission-table').length){
            rolePermissionTable('#role-permission-table');
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get system action details');
        });

        $(document).on('click','#delete-system-action',function() {
            const system_action_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete system action';
    
            Swal.fire({
                title: 'Confirm System Action Deletion',
                text: 'Are you sure you want to delete this system action?',
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
                        url: 'apps/settings/system-action/controller/system-action-controller.php',
                        dataType: 'json',
                        data: {
                            system_action_id : system_action_id, 
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

        $(document).on('click','#assign-role-permission',function() {
            generateDropdownOptions('system action role dual listbox options');
        });

        $(document).on('click','.update-role-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const access_type = $(this).data('access-type');
            const transaction = 'update role system action permission';
            const access = $(this).is(':checked') ? '1' : '0';
            
            $.ajax({
                type: 'POST',
                url: 'apps/settings/role/controller/role-controller.php',
                dataType: 'json',
                data: {
                    role_permission_id : role_permission_id, 
                    access_type : access_type,
                    access : access,
                    transaction : transaction
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
                            reloadDatatable('#role-permission-table');
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
        });

        $(document).on('click','.delete-role-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const transaction = 'delete role system action permission';
    
            Swal.fire({
                title: 'Confirm Role Permission Deletion',
                text: 'Are you sure you want to delete this role permission?',
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
                        url: 'apps/settings/role/controller/role-controller.php',
                        dataType: 'json',
                        data: {
                            role_permission_id : role_permission_id, 
                            transaction : transaction
                        },
                        success: function (response) {
                            if (response.success) {
                                showNotification(response.title, response.message, response.messageType);
                                reloadDatatable('#role-permission-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#role-permission-table');
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

        $(document).on('click','.view-role-permission-log-notes',function() {
            const role_system_action_permission_id = $(this).data('role-permission-id');

            logNotes('role_system_action_permission', role_system_action_permission_id);
        });

        $(document).on('click','#log-notes-main',function() {
            const system_action_id = $('#details-id').text();

            logNotes('system_action', system_action_id);
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#role-permission-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#role-permission-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });
    });
})(jQuery);

function systemActionForm(){
    $('#system-action-form').validate({
        rules: {
            system_action_name: {
                required: true
            },
            system_action_description: {
                required: true
            }
        },
        messages: {
            system_action_name: {
                required: 'Enter the display name'
            },
            system_action_description: {
                required: 'Ether the description'
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
            const system_action_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update system action';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/system-action/controller/system-action-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&system_action_id=' + encodeURIComponent(system_action_id),
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
                    logNotes('system_action', system_action_id);
                }
            });
        
            return false;
        }
    });
}

function rolePermissionAssignmentForm(){
    $('#role-permission-assignment-form').validate({
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
            const system_action_id = $('#details-id').text();
            const transaction = 'assign system action role permission';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/role/controller/role-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&system_action_id=' + system_action_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-assignment');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        reloadDatatable('#role-permission-table');
                        $('#role-permission-assignment-modal').modal('hide');
                    }
                    else {
                        if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'logout.php?logout';
                        }
                        else if (response.notExist) {
                            setNotification(response.title, response.message, response.messageType);
                            window.location = 'role.php';
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
                    enableFormSubmitButton('submit-assignment');
                }
            });
        
            return false;
        }
    });
}

function rolePermissionTable(datatable_name) {
    const type = 'system action assigned role table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const system_action_id = $('#details-id').text();

    const columns = [ 
        { data: 'ROLE_NAME' },
        { data: 'ACCESS' },
        { data: 'ACTION' }
    ];

    const columnDefs = [
        { width: 'auto', targets: 0, responsivePriority: 1 },
        { width: 'auto', bSortable: false, targets: 1, responsivePriority: 2 },
        { width: '5%', bSortable: false, targets: 2, responsivePriority: 1 }
    ];
    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/system-action/view/_system_action_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                system_action_id: system_action_id
            },
            dataSrc: '',
            error: function(xhr, status, error) {
                handleSystemError(xhr, status, error);
            }
        },
        lengthChange: false,
        order: [[0, 'asc']],
        columns: columns,
        columnDefs: columnDefs,
        lengthMenu: lengthMenu,
        autoWidth: false,
        language: {
            emptyTable: 'No data found',
            sLengthMenu: '_MENU_',
            info: '_START_ - _END_ of _TOTAL_ items',
            loadingRecords: 'Just a moment while we fetch your data...'
        },
        fnDrawCallback: function(oSettings) {
            readjustDatatableColumn();
        }
    };

    destroyDatatable(datatable_name);
    $(datatable_name).dataTable(settings);
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get system action details':
            var system_action_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/system-action/controller/system-action-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    system_action_id : system_action_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetForm('system-action-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#system_action_name').val(response.systemActionName);
                        $('#system_action_description').val(response.systemActionDescription);
                        
                        $('#system_action_name_summary').text(response.systemActionName);
                        $('#system_action_description_summary').text(response.systemActionDescription);
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
        case 'system action role dual listbox options':
            var system_action_id = $('#details-id').text();
        
            $.ajax({
                url: 'apps/settings/role/view/_role_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    system_action_id : system_action_id
                },
                success: function(response) {
                    var select = document.getElementById('role_id');
        
                    select.options.length = 0;
        
                    response.forEach(function(opt) {
                        var option = new Option(opt.text, opt.id);
                        select.appendChild(option);
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                },
                complete: function(){
                    if($('#role_id').length){
                        $('#role_id').bootstrapDualListbox({
                            nonSelectedListLabel: 'Non-selected',
                            selectedListLabel: 'Selected',
                            preserveSelectionOnMove: 'moved',
                            moveOnSelect: false,
                            helperSelectNamePostfix: false
                        });
        
                        $('#role_id').bootstrapDualListbox('refresh', true);
        
                        initializeDualListBoxIcon();
                    }
                }
            });
            break;
    }
}