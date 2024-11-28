(function($) {
    'use strict';

    $(function() {        
        //generateDropdownOptions('employee options');

        if($('#employee-table').length){
            //employeeTable('#employee-table');
        }

        if($('#employee-card').length){
            let offset = 0;
            const limit = 8;
            let isFetching = false

            function employeeCards(clearExisting) {
                if (isFetching) return;
                isFetching = true;
            
                const type = 'employee cards';
                const page_id = $('#page-id').val();
                const page_link = document.getElementById('page-link').getAttribute('href');            
            
                var search_value = $('#datatable-search').val();
                var filter_by_company = $('#company_filter').val();
                var filter_by_department = $('#department_filter').val();
                var filter_by_job_position = $('#job_position_filter').val();
                var filter_by_employee_status = $('#employee_status_filter').val();
                var filter_by_employment_type = $('#employment_type_filter').val();
                var filter_by_gender = $('#gender_filter').val();
                var filter_by_civil_status = $('#civil_status_filter').val();
            
                $.ajax({
                    type: 'POST',
                    url: 'components/employee/view/_employee_generation.php',
                    dataType: 'json',
                    data: {
                        page_id: page_id,
                        page_link: page_link,
                        limit: limit,
                        offset: offset,
                        search_value: search_value,
                        filter_by_company: filter_by_company,
                        filter_by_department: filter_by_department,
                        filter_by_job_position: filter_by_job_position,
                        filter_by_employee_status: filter_by_employee_status,
                        filter_by_employment_type: filter_by_employment_type,
                        filter_by_gender: filter_by_gender,
                        filter_by_civil_status: filter_by_civil_status,
                        type: type
                    },
                    beforeSend: function() {
                        if (clearExisting) {
                            $('#employee-card').empty();
                            offset = 0;
                        }
                    },
                    success: function(response) {
                        response.forEach(card => {
                            $('#employee-card').append(card.EMPLOYEE_CARD);
                        });
            
                        offset += limit;
                        isFetching = false;
                    },
                    error: function(xhr, status, error) {
                        var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
                        if (xhr.responseText) {
                            fullErrorMessage += `, Response: ${xhr.responseText}`;
                        }
                        console.error(fullErrorMessage);
                        isFetching = false;
                    }
                });
            }
            
            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() == $(document).height()) {
                    employeeCards(false);
                }
            });
            
            $('#datatable-search').on('keyup', function() {
                offset = 0;
                employeeCards(true);
            });
            
            $(document).on('click','#apply-filter',function() {
                offset = 0;
                employeeCards(true);            
                $('#filter-offcanvas').offcanvas('hide');
            });
        
            employeeCards(true);
        }

        $(document).on('click','#delete-employee',function() {
            let employee_id = [];
            const transaction = 'delete multiple employee';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    employee_id.push(element.value);
                }
            });
    
            if(employee_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Employees Deletion',
                    text: 'Are you sure you want to delete these employees?',
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
                                employee_id: employee_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#employee-table');
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
                showNotification('Deletion Multiple Employee Error', 'Please select the employees you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('employee');
        });

        $(document).on('click','#submit-export',function() {
            exportData('employee');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#employee-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#employee-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            employeeTable('#employee-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#parent_employee_filter').val(null).trigger('change');
            $('#manager_filter').val(null).trigger('change');
            
            employeeTable('#employee-table');
        });
    });
})(jQuery);

function employeeTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'employee table';
    const page_id = $('#page-id').val();
    const parent_employee_filter = $('#parent_employee_filter').val();
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
            url: 'apps/employee/employee/view/_employee_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                parent_employee_filter: parent_employee_filter,
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
        case 'employee options':
            
            $.ajax({
                url: 'apps/employee/employee/view/_employee_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#parent_employee_filter').select2({
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