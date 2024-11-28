(function($) {
    'use strict';

    $(function() {
        generateDropdownOptions('filter city options');
        generateDropdownOptions('state options');
        generateDropdownOptions('country options');

        if($('#work-location-table').length){
            workLocationTable('#work-location-table');
        }

        $(document).on('click','#delete-work-location',function() {
            let work_location_id = [];
            const transaction = 'delete multiple work location';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    work_location_id.push(element.value);
                }
            });
    
            if(work_location_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Work Locations Deletion',
                    text: 'Are you sure you want to delete these work locations?',
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
                            url: 'apps/employee/work-location/controller/work-location-controller.php',
                            dataType: 'json',
                            data: {
                                work_location_id: work_location_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#work-location-table');
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
                showNotification('Deletion Multiple Work Locations Error', 'Please select the work locations you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('work_location');
        });

        $(document).on('click','#submit-export',function() {
            exportData('work_location');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#work-location-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#work-location-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });

        $(document).on('click','#apply-filter',function() {
            workLocationTable('#work-location-table');
        });

        $(document).on('click', '#reset-filter', function() {
            $('#city_filter').val(null).trigger('change');
            $('#state_filter').val(null).trigger('change');
            $('#country_filter').val(null).trigger('change');
            
            workLocationTable('#work-location-table');
        });
    });
})(jQuery);

function workLocationTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'work location table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');
    const city_filter = $('#city_filter').val();
    const state_filter = $('#state_filter').val();
    const country_filter = $('#country_filter').val();

    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'WORK_LOCATION_NAME' }
    ];

    const columnDefs = [
        { width: '1%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const employee = {
        ajax: { 
            url: 'apps/employee/work-location/view/_work_location_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link,
                city_filter: city_filter,
                state_filter: state_filter,
                country_filter: country_filter
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
    $(datatable_name).dataTable(employee);
}

function generateDropdownOptions(type){
    switch (type) {
        case 'filter city options':
            
            $.ajax({
                url: 'apps/settings/city/view/_city_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#city_filter').select2({
                        data: response
                    });
                },
                error: function(xhr, status, error) {
                    handleSystemError(xhr, status, error);
                }
            });
            break;
        case 'state options':
            
            $.ajax({
                url: 'apps/settings/state/view/_state_generation.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#state_filter').select2({
                        data: response
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
                    type : type,
                    multiple : 1
                },
                success: function(response) {
                    $('#country_filter').select2({
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