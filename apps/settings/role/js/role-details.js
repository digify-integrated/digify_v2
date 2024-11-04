(function($) {
    'use strict';

    $(function() {
        displayDetails('get role details');

        if($('#role-form').length){
            roleForm();
        }

        if($('#menu-item-permission-assignment-form').length){
            menuItemPermissionAssignmentForm();
        }

        if($('#system-action-permission-assignment-form').length){
            systemActionPermissionAssignmentForm();
        }

        if($('#menu-item-permission-table').length){
            menuItemPermissionTable('#menu-item-permission-table');
        }

        if($('#system-action-permission-table').length){
            systemActionPermissionTable('#system-action-permission-table');
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get role details');
        });

        $(document).on('click','#delete-role',function() {
            const role_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete role';
    
            Swal.fire({
                title: 'Confirm Role Deletion',
                text: 'Are you sure you want to delete this role?',
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
                            role_id : role_id, 
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

        $(document).on('click','#assign-menu-item-permission',function() {
            generateDropdownOptions('role menu item dual listbox options');
        });

        $(document).on('click','.update-menu-item-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const access_type = $(this).data('access-type');
            const transaction = 'update menu item permission';
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
                            reloadDatatable('#menu-item-permission-table');
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

        $(document).on('click','.delete-menu-item-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const transaction = 'delete menu item permission';
    
            Swal.fire({
                title: 'Confirm Menu Item Permission Deletion',
                text: 'Are you sure you want to delete this menu item permission?',
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
                                reloadDatatable('#menu-item-permission-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#menu-item-permission-table');
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

        $(document).on('click','#assign-system-action-permission',function() {
            generateDropdownOptions('role system action dual listbox options');
        });

        $(document).on('click','.update-system-action-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const access_type = $(this).data('access-type');
            const transaction = 'update system action permission';
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
                            reloadDatatable('#system-action-permission-table');
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

        $(document).on('click','.delete-system-action-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const transaction = 'delete system action permission';
    
            Swal.fire({
                title: 'Confirm System Action Permission Deletion',
                text: 'Are you sure you want to delete this system action permission?',
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
                                reloadDatatable('#system-action-permission-table');
                            }
                            else {
                                if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                    setNotification(response.title, response.message, response.messageType);
                                    window.location = 'logout.php?logout';
                                }
                                else if (response.notExist) {
                                    setNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#system-action-permission-table');
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
            const role_id = $('#details-id').text();

            logNotes('role', role_id);
        });

        $(document).on('click','.view-menu-item-permission-log-notes',function() {
            const role_permission_id = $(this).data('role-permission-id');

            logNotes('role_permission', role_permission_id);
        });

        $(document).on('click','.view-system-action-permission-log-notes',function() {
            const role_system_action_permission_id  = $(this).data('role-permission-id');

            logNotes('role_system_action_permission', role_system_action_permission_id );
        });

        $('#menu-item-permission-datatable-search').on('keyup', function () {
            var table = $('#menu-item-permission-table').DataTable();
            table.search(this.value).draw();
        });

        $('#menu-item-permission-datatable-length').on('change', function() {
            var table = $('#menu-item-permission-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $('#system-action-permission-datatable-search').on('keyup', function () {
            var table = $('#system-action-permission-table').DataTable();
            table.search(this.value).draw();
        });

        $('#system-action-permission-datatable-length').on('change', function() {
            var table = $('#system-action-permission-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });
    });
})(jQuery);

function roleForm(){
    $('#role-form').validate({
        rules: {
            role_name: {
                required: true
            },
            role_description: {
                required: true
            }
        },
        messages: {
            role_name: {
                required: 'Enter the display name'
            },
            role_description: {
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
            const role_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update role';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/role/controller/role-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&role_id=' + encodeURIComponent(role_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get role details');
                        $('#role-modal').modal('hide');
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
                    logNotesMain('role', role_id);
                }
            });
        
            return false;
        }
    });
}

function menuItemPermissionAssignmentForm(){
    $('#menu-item-permission-assignment-form').validate({
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
            const role_id = $('#details-id').text();
            const transaction = 'assign role menu item permission';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/role/controller/role-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&role_id=' + role_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-assignment');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        reloadDatatable('#menu-item-permission-table');
                        $('#menu-item-permission-assignment-modal').modal('hide');
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
                    enableFormSubmitButton('submit-menu-item-assignment');
                }
            });
        
            return false;
        }
    });
}

function systemActionPermissionAssignmentForm(){
    $('#system-action-permission-assignment-form').validate({
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
            const role_id = $('#details-id').text();
            const transaction = 'assign role system action permission';
          
            $.ajax({
                type: 'POST',
                url: 'apps/settings/role/controller/role-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&role_id=' + role_id,
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-assignment');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        reloadDatatable('#system-action-permission-table');
                        $('#system-action-permission-assignment-modal').modal('hide');
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
                    enableFormSubmitButton('submit-system-action-assignment');
                }
            });
        
            return false;
        }
    });
}

function menuItemPermissionTable(datatable_name) {
    const type = 'role assigned menu item table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const role_id = $('#details-id').text();

    const columns = [ 
        { data: 'MENU_ITEM_NAME' },
        { data: 'READ_ACCESS' },
        { data: 'CREATE_ACCESS' },
        { data: 'WRITE_ACCESS' },
        { data: 'DELETE_ACCESS' },
        { data: 'IMPORT_ACCESS' },
        { data: 'EXPORT_ACCESS' },
        { data: 'LOG_NOTES_ACCESS' },
        { data: 'ACTION' }
    ];

    const columnDefs = [
        { width: 'auto', targets: 0, responsivePriority: 1 },
        { width: 'auto', bSortable: false, targets: 1, responsivePriority: 2 },
        { width: 'auto', bSortable: false, targets: 2, responsivePriority: 3 },
        { width: 'auto', bSortable: false, targets: 3, responsivePriority: 4 },
        { width: 'auto', bSortable: false, targets: 4, responsivePriority: 5 },
        { width: 'auto', bSortable: false, targets: 5, responsivePriority: 6 },
        { width: 'auto', bSortable: false, targets: 6, responsivePriority: 7 },
        { width: 'auto', bSortable: false, targets: 7, responsivePriority: 8 },
        { width: 'auto', bSortable: false, targets: 8, responsivePriority: 1 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/role/view/_role_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                role_id: role_id
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

function systemActionPermissionTable(datatable_name) {
    const type = 'role assigned system action table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const role_id = $('#details-id').text();

    const columns = [ 
        { data: 'SYSTEM_ACTION_NAME' },
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
            url: 'apps/settings/role/view/_role_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                role_id: role_id
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
        case 'get role details':
            var role_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/settings/role/controller/role-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    role_id : role_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('role-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#role_name').val(response.roleName);
                        $('#role_description').val(response.roleDescription);
                        
                        $('#role_name_summary').text(response.roleName);
                        $('#role_description_summary').text(response.roleDescription);
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
        case 'role menu item dual listbox options':
            var role_id = $('#details-id').text();
        
            $.ajax({
                url: 'apps/settings/role/view/_role_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    role_id : role_id
                },
                success: function(response) {
                    var select = document.getElementById('menu_item_id');
        
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
                    if($('#menu_item_id').length){
                        $('#menu_item_id').bootstrapDualListbox({
                            nonSelectedListLabel: 'Non-selected',
                            selectedListLabel: 'Selected',
                            preserveSelectionOnMove: 'moved',
                            moveOnSelect: false,
                            helperSelectNamePostfix: false
                        });
        
                        $('#menu_item_id').bootstrapDualListbox('refresh', true);
        
                        initializeDualListBoxIcon();
                    }
                }
            });
            break;
        case 'role system action dual listbox options':
            var role_id = $('#details-id').text();
            
            $.ajax({
                url: 'apps/settings/role/view/_role_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    role_id : role_id
                },
                success: function(response) {
                    var select = document.getElementById('system_action_id');
            
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
                    if($('#system_action_id').length){
                        $('#system_action_id').bootstrapDualListbox({
                            nonSelectedListLabel: 'Non-selected',
                            selectedListLabel: 'Selected',
                            preserveSelectionOnMove: 'moved',
                            moveOnSelect: false,
                            helperSelectNamePostfix: false
                        });
            
                        $('#system_action_id').bootstrapDualListbox('refresh', true);
            
                        initializeDualListBoxIcon();
                    }
                }
            });
            break;
    }
}