(function($) {
    'use strict';

    $(function() {        
        generateDropdownOptions('department options');

        if($('#department-table').length){
            departmentTable('#department-table');
        }

        $(document).on('click','#delete-department',function() {
            let department_id = [];
            const transaction = 'delete multiple department';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    department_id.push(element.value);
                }
            });
    
            if(department_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Departments Deletion',
                    text: 'Are you sure you want to delete these departments?',
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
                                department_id: department_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#department-table');
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
                showNotification('Deletion Multiple Department Error', 'Please select the departments you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('department');
        });

        $(document).on('click','#submit-export',function() {
            exportData('department');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#department-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#department-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            departmentTable('#department-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#file_type_filter').val(null).trigger('change');
            
            departmentTable('#department-table');
        });
    });
})(jQuery);

function departmentTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'department table';
    const page_id = $('#page-id').val();
    const parent_department_filter = $('#parent_department_filter').val();
    const manager_filter = $('#manager_filter').val();
    const page_link = document.getElementById('page-link').getAttribute('href');

    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'DEPARTMENT_NAME' },
        { data: 'PARENT_DEPARTMENT_NAME' },
        { data: 'MANAGER_NAME' }
    ];

    const columnDefs = [
        { width: '5%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 },
        { width: 'auto', targets: 2, responsivePriority: 3 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/employee/department/view/_department_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                parent_department_filter: parent_department_filter,
                manager_filter: manager_filter
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
        case 'department options':
            
            $.ajax({
                url: 'apps/employee/department/view/_department_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#parent_department_filter').select2({
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