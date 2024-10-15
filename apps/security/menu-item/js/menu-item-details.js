(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('menu group options');
        generateDropdownOptions('menu item options');

        displayDetails('get menu item details');

        if($('#menu-item-form').length){
            menuItemForm();
        }

        if($('#role-permission-assignment-form').length){
            rolePermissionAssignmentForm();
        }

        if($('#role-permission-table').length){
            rolePermissionTable('#role-permission-table');
        }

        $(document).on('click','#edit-details',function() {
            displayDetails('get menu item details');
        });

        $(document).on('click','#delete-menu-item',function() {
            const menu_item_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'delete menu item';
    
            Swal.fire({
                title: 'Confirm Menu Item Deletion',
                text: 'Are you sure you want to delete this menu item?',
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
                        url: 'apps/security/menu-item/controller/menu-item-controller.php',
                        dataType: 'json',
                        data: {
                            menu_item_id : menu_item_id, 
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

        $(document).on('click','.update-role-permission',function() {
            const role_permission_id = $(this).data('role-permission-id');
            const access_type = $(this).data('access-type');
            const transaction = 'update role permission';
            const access = $(this).is(':checked') ? '1' : '0';
            
            $.ajax({
                type: 'POST',
                url: 'apps/security/role/controller/role-controller.php',
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
            const transaction = 'delete role permission';
    
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
                        url: 'apps/security/role/controller/role-controller.php',
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

        $(document).on('click','#assign-role-permission',function() {
            generateDropdownOptions('menu item role dual listbox options');
        });
        
        if($('#log-notes-modal').length){
            $(document).on('click','.view-role-permission-log-notes',function() {
                const role_permission_id = $(this).data('role-permission-id');

                logNotes('role_permission', role_permission_id);
            });
        }

        if($('#log-notes-main').length){
            const menu_item_id = $('#details-id').text();

            logNotesMain('menu_item', menu_item_id);
        }

        if($('#internal-notes').length){
            const menu_item_id = $('#details-id').text();

            internalNotes('menu_item', menu_item_id);
        }

        if($('#internal-notes-form').length){
            const menu_item_id = $('#details-id').text();

            internalNotesForm('menu_item', menu_item_id);
        }
    });
})(jQuery);

function menuItemForm(){
    $('#menu-item-form').validate({
        rules: {
            menu_item_name: {
                required: true
            },
            menu_group_id: {
                required: true
            },
            order_sequence: {
                required: true
            }
        },
        messages: {
            menu_item_name: {
                required: 'Enter the display name'
            },
            menu_group_id: {
                required: 'Choose the menu group'
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
            const menu_item_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            const transaction = 'update menu item';
          
            $.ajax({
                type: 'POST',
                url: 'apps/security/menu-item/controller/menu-item-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&menu_item_id=' + encodeURIComponent(menu_item_id),
                dataType: 'json',
                beforeSend: function() {
                    disableFormSubmitButton('submit-data');
                },
                success: function (response) {
                    if (response.success) {
                        showNotification(response.title, response.message, response.messageType);
                        displayDetails('get menu item details');
                        $('#menu-item-modal').modal('hide');
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
                    logNotesMain('menu_item', menu_item_id);
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
            const menu_item_id = $('#details-id').text();
            const transaction = 'assign menu item role permission';
          
            $.ajax({
                type: 'POST',
                url: 'apps/security/role/controller/role-controller.php',
                data: $(form).serialize() + '&transaction=' + transaction + '&menu_item_id=' + menu_item_id,
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
    const type = 'menu item assigned role table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const menu_item_id = $('#details-id').text();

    const columns = [ 
        { data: 'ROLE_NAME' },
        { data: 'READ_ACCESS' },
        { data: 'WRITE_ACCESS' },
        { data: 'CREATE_ACCESS' },
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

    const lengthMenu = [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/security/menu-item/view/_menu_item_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                menu_item_id: menu_item_id
            },
            dataSrc: '',
            error: function(xhr, status, error) {
                handleSystemError(xhr, status, error);
            }
        },
        dom: 'Brtip',
        lengthChange: false,
        order: [[0, 'asc']],
        columns: columns,
        columnDefs: columnDefs,
        lengthMenu: lengthMenu,
        responsive: {
            details: {
                type: 'inline',
                display: $.fn.dataTable.Responsive.display.childRow,
                renderer: function (api, rowIdx, columns) {
                    let data = $.map(columns, function (col) {
                        return col.hidden ? `<tr><td>${col.title}:</td><td>${col.data}</td></tr>` : '';
                    }).join('');
                    return data ? $('<table/>').append(data) : false;
                }
            }
        },
        autoWidth: false,
        language: {
            emptyTable: 'No data found',
            sLengthMenu: '_MENU_',
            info: '_START_ - _END_ of _TOTAL_ items',
            loadingRecords: 'Just a moment while we fetch your data...'
        },
        fnDrawCallback: function(oSettings) {
            readjustDatatableColumn();

            $(`${datatable_name} tbody`).on('click', 'tr td:nth-child(n+2)', function () {
                const rowData = $(datatable_name).DataTable().row($(this).closest('tr')).data();
                if (rowData && rowData.LINK) {
                    window.location.href = rowData.LINK;
                }
            });
        }
    };

    destroyDatatable(datatable_name);
    $(datatable_name).dataTable(settings);
}

function displayDetails(transaction){
    switch (transaction) {
        case 'get menu item details':
            var menu_item_id = $('#details-id').text();
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            
            $.ajax({
                url: 'apps/security/menu-item/controller/menu-item-controller.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    menu_item_id : menu_item_id, 
                    transaction : transaction
                },
                beforeSend: function(){
                    resetModalForm('menu-item-form');
                },
                success: function(response) {
                    if (response.success) {
                        $('#menu_item_name').val(response.menuItemName);
                        $('#menu_item_icon').val(response.menuItemIcon);
                        $('#menu_item_url').val(response.menuItemURL);
                        $('#order_sequence').val(response.orderSequence);

                        $('#menu_group_id').val(response.menuGroupID).trigger('change');
                        $('#parent_id').val(response.parentID).trigger('change');
                        
                        $('#menu_item_name_summary').text(response.menuItemName);
                        $('#menu_group_name_summary').text(response.menuGroupName);
                        $('#parent_menu_item_summary').text(response.parentName);
                        $('#menu_item_icon_summary').text(response.menuItemIcon);
                        $('#menu_item_url_summary').text(response.menuItemURL);
                        $('#order_sequence_summary').text(response.orderSequence);
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
        case 'menu group options':

            $.ajax({
                url: 'apps/security/menu-group/view/_menu_group_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type
                },
                success: function(response) {
                    $('#menu_group_id').select2({
                        data: response,
                        dropdownParent: $('#menu-item-modal').closest('.modal')
                    }).on('change', function (e) {
                        $(e.target).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'menu item options':
            var menu_item_id = $('#details-id').text();    

            $.ajax({
                url: 'apps/security/menu-item/view/_menu_item_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    menu_item_id : menu_item_id
                },
                success: function(response) {
                    $('#parent_id').select2({
                        data: response,
                        dropdownParent: $('#menu-item-modal').closest('.modal')
                    }).on('change', function (e) {
                        $(e.target).valid()
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'menu item role dual listbox options':
            var menu_item_id = $('#details-id').text();
        
            $.ajax({
                url: 'apps/security/role/view/_role_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    menu_item_id : menu_item_id
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