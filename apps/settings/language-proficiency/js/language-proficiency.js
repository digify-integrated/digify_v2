(function($) {
    'use strict';

    $(function() {
        if($('#language-proficiency-table').length){
            languageProficiencyTable('#language-proficiency-table');
        }

        $(document).on('click','#delete-language-proficiency',function() {
            let language_proficiency_id = [];
            const transaction = 'delete multiple language proficiency';

            $('.datatable-checkbox-children').each((index, element) => {
                if ($(element).is(':checked')) {
                    language_proficiency_id.push(element.value);
                }
            });
    
            if(language_proficiency_id.length > 0){
                Swal.fire({
                    title: 'Confirm Multiple Language Proficiencies Deletion',
                    text: 'Are you sure you want to delete these language proficiencies?',
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
                            url: 'apps/settings/language-proficiency/controller/language-proficiency-controller.php',
                            dataType: 'json',
                            data: {
                                language_proficiency_id: language_proficiency_id,
                                transaction : transaction
                            },
                            success: function (response) {
                                if (response.success) {
                                    showNotification(response.title, response.message, response.messageType);
                                    reloadDatatable('#language-proficiency-table');
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
                showNotification('Deletion Multiple Language Proficiency Error', 'Please select the language proficiencies you wish to delete.', 'danger');
            }
        });

        $(document).on('click','#export-data',function() {
            generateExportColumns('language_proficiency');
        });

        $(document).on('click','#submit-export',function() {
            exportData('language_proficiency');
        });

        $('#datatable-search').on('keyup', function () {
            var table = $('#language-proficiency-table').DataTable();
            table.search(this.value).draw();
        });

        $('#datatable-length').on('change', function() {
            var table = $('#language-proficiency-table').DataTable();
            var length = $(this).val(); 
            table.page.len(length).draw();
        });
    });
})(jQuery);

function languageProficiencyTable(datatable_name) {
    toggleHideActionDropdown();

    const type = 'language proficiency table';
    const page_id = $('#page-id').val();
    const page_link = document.getElementById('page-link').getAttribute('href');


    const columns = [ 
        { data: 'CHECK_BOX' },
        { data: 'LANGUAGE_PROFICIENCY_NAME' }
    ];

    const columnDefs = [
        { width: '1%', bSortable: false, targets: 0, responsivePriority: 1 },
        { width: 'auto', targets: 1, responsivePriority: 2 }
    ];

    const lengthMenu = [[10, 5, 25, 50, 100, -1], [10, 5, 25, 50, 100, 'All']];

    const settings = {
        ajax: { 
            url: 'apps/settings/language-proficiency/view/_language_proficiency_generation.php',
            method: 'POST',
            dataType: 'json',
            data: {
                type: type,
                page_id: page_id,
                page_link: page_link
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