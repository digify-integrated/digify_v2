(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('app module options');
        generateDropdownOptions('menu item options');

        if($('#menu-item-table').length){
            menuItemTable('#menu-item-table');
        }

        $(document).on('click','#delete-menu-item',function() {
            let menu_item_id = [];
            const transaction = 'delete multiple menu item';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    menu_item_id.push(element.value);
                }
            });
    
            if(menu_item_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Menu Items Deletion',
                    text: 'Are you sure you want to delete these menu items?',
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
                            url: 'apps/settings/menu-item/controller/menu-item-controller.php',
                            dataType: 'json',
                            data: {
                                menu_item_id: menu_item_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#menu-item-table');
                                }
                                else {
                                    if (response.isInactive || response.userNotExist || response.userInactive || response.userLocked || response.sessionExpired) {
                                        setNotification(response.title, response.message, response.messageType);
                                        window.location = 'logout.php?logout';
                                    }
                                    else {
                                        showNotification(response.title, response.message, response.messageType);
                                    }
                                }
                            },
                            error: function(xhr, status, error) {
                                handleSystemError(xhr, status, error);
                            },
                            complete: function(){
                                toggleHideActionDropdown();
                            }
                        });
                        
                        return false;
                    }
                });
            }
            else{
                showNotification('Deletion Multiple Menu Item Error', 'Please select the menu items you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('menu_item');
        });

        $(document).on('click','#submit-export',function() {
            exportData('menu_item');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#menu-item-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#menu-item-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            menuItemTable('#menu-item-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#app_module_filter').val(null).trigger('change');
            $('#parent_id_filter').val(null).trigger('change');
            
            menuItemTable('#menu-item-table');
        });
    });
})(jQuery);

function menuItemTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'menu item table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const app_module_filter = $('#app_module_filter').val();
    const parent_id_filter = $('#parent_id_filter').val();


    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'MENU_ITEM_NAME' },
        { data: 'APP_MODULE_NAME' },
        { data: 'PARENT_NAME' },
        { data: 'ORDER_SEQUENCE' }
    ];

    const columnDefs = [
        { width: '5%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 },
        { width: 'auto', targets: 3, responsivePriority: 4 },
        { width: 'auto', targets: 4, responsivePriority: 5 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/menu-item/view/_menu_item_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                app_module_filter: app_module_filter,
                parent_id_filter: parent_id_filter
            },
            dataSrc: '',
            error: function(xhr, status, error) {
                handleSystemError(xhr, status, error);
            }
        },
        lengthChange: false,
        order: [[1, 'asc']],
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

function generateDropdownOptions(type){
    switch (type) {
        case 'app module options':
            
            $.ajax({
                url: 'apps/settings/app-module/view/_app_module_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#app_module_filter').select2({
                        data: response
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'menu item options':
            
            $.ajax({
                url: 'apps/settings/menu-item/view/_menu_item_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#parent_id_filter').select2({
                        data: response
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
    }
}