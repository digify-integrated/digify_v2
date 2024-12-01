(function($) {
    'use strict';

    $(function() {
        setHereClassForMenu('#kt_app_header_menu', '.menu-item');

        checkNotification();
        passwordAddOn();

        $(document).on('click','#discard-create',function() {
            const page_link = document.getElementById('page-link').getAttribute('href'); 
            discardCreate(page_link);
        });

        $(document).on('click','#copy-error-message',function() {
            copyToClipboard('error-dialog');
        });

        $(document).on('click','#datatable-checkbox',function() {
            var status = $(this).is(':checked') ? true : false;
            $('.datatable-checkbox-children').prop('checked',status);
    
            toggleActionDropdown();
        });

        if ($('.filter-daterange').length) {
            $('.filter-daterange').daterangepicker({
                autoApply: false,
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [
                      moment().subtract(1, 'month').startOf('month'),
                      moment().subtract(1, 'month').endOf('month'),
                    ],
                },
                alwaysShowCalendars: true,
                showDropdowns: true
            });
        
            $('.filter-daterange').val('');

            $('.clear-daterange').on('click', function() {
                $(this).closest('.row').find('.filter-daterange').val('').trigger('cancel.daterangepicker');
            });
        }

        if ($('.daterange-picker').length) {
            $('.daterange-picker').daterangepicker({
                    showDropdowns: true,
                    autoApply: false,
                    minYear: 1901,
                    maxYear: parseInt(moment().format("YYYY"),12)
                }
            );

            $('.daterange-picker').val('');
        }

        if ($('.datepicker').length) {
            $('.datepicker').flatpickr({
                enableTime: false,
                dateFormat: "M d, Y"
            });
        }

        $(document).on('click','.datatable-checkbox-children',function() {
            toggleActionDropdown();
        });
    });
})(jQuery);

function setHereClassForMenu(menuContainerSelector, menuSelector) {
    var currentUrl = window.location.href.split('?')[0];

    var menuContainer = document.querySelector(menuContainerSelector);
    if (!menuContainer) return; 

    var firstLevelMenuItems = menuContainer.querySelectorAll(menuSelector);

    firstLevelMenuItems.forEach(function(menuItem) {
        var hasChildMatch = false;

        var childLinks = menuItem.querySelectorAll('.menu-sub .menu-link');

        if (childLinks.length > 0) {
            childLinks.forEach(function(childLink) {
                if (childLink && childLink.href) {
                    var childLinkHref = childLink.href.split('?')[0];
                    if (childLinkHref === currentUrl) {
                        hasChildMatch = true; 
                        childLink.classList.add('active');
                    }
                }
            });

            if (hasChildMatch) {
                menuItem.classList.add('here');
                menuItem.classList.add('show');
            }
        }
        else {
            var menuLink = menuItem.querySelector('.menu-link');

            if (menuLink && menuLink.closest('a') && menuLink.closest('a').href.split('?')[0] === currentUrl) {
                menuItem.classList.add('here');
            }
        }
    });
}

function discardCreate(windows_location){
    Swal.fire({
        title: 'Discard Changes Confirmation',
        text: 'You are about to discard your changes. Proceeding will permanently erase any unsaved modifications. Are you sure you want to continue?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Discard',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-warning mt-2',
            cancelButton: 'btn btn-secondary ms-2 mt-2'
        },
        buttonsStyling: false
    }).then(function(result) {
        if (result.value) {
            window.location = windows_location;
        }
    });
}

function passwordAddOn(){
    if ($('.password-addon').length) {
        $('.password-addon').on('click', function() {
            const inputField = $(this).siblings('input');
            const eyeIcon = $(this).find('i');

            if (inputField.length) {
                const isPassword = inputField.attr('type') === 'password';
                inputField.attr('type', isPassword ? 'text' : 'password');
                eyeIcon.toggleClass('ki-eye-slash ki-eye');
            }
        });

        $('.password-addon').attr('tabindex', -1);
    }
}

function checkOptionExist(element, option) {
    $(element).val(option).trigger('change');
}

function initializeDualListBoxIcon(){
    $('.moveall i').removeClass().addClass('ki-duotone ki-right');
    $('.removeall i').removeClass().addClass('ki-duotone ki-left');
    $('.move i').removeClass().addClass('ki-duotone ki-right');
    $('.remove i').removeClass().addClass('ki-duotone ki-left');

    $('.moveall').removeClass('btn-default').addClass('btn-primary');
    $('.removeall').removeClass('btn-default').addClass('btn-primary');
    $('.move').removeClass('btn-default').addClass('btn-primary');
    $('.remove').removeClass('btn-default').addClass('btn-primary');
}

function resetForm(form_id) {
    var form = document.getElementById(form_id);

    $(form).find('.select2').each(function() {
        $(this).val('').trigger('change.select2');
    });
  
    form.querySelectorAll('.is-invalid').forEach(function(element) {
        element.classList.remove('is-invalid');
    });

    form.reset();
}

function reloadDatatable(datatable_name) {
    toggleHideActionDropdown();

    if ($.fn.DataTable.isDataTable(datatable_name)) {
        $(datatable_name).DataTable().ajax.reload(null, false);
    }
}

function destroyDatatable(datatable_name) {
    if ($.fn.DataTable.isDataTable(datatable_name)) {
        $(datatable_name).DataTable().clear().destroy();
    }
}

function clearDatatable(datatable_name) {
    $(datatable_name).DataTable().clear().draw();
}

function readjustDatatableColumn() {
    const adjustDataTable = () => {
        const tables = $.fn.dataTable.tables({ visible: true, api: true });
        tables.columns.adjust().fixedColumns().relayout();
    };

    $('a[data-bs-toggle="tab"], a[data-bs-toggle="pill"], #System-Modal').on('shown.bs.tab shown.bs.modal', adjustDataTable);
}

function toggleActionDropdown(){
    const inputElements = Array.from(document.querySelectorAll('.datatable-checkbox-children'));
    const multipleAction = $('.action-dropdown');
    const checkedValue = inputElements.filter(chk => chk.checked).length;

    multipleAction.toggleClass('d-none', checkedValue === 0);
}

function toggleHideActionDropdown(){
    $('.action-dropdown').addClass('d-none');
    $('#datatable-checkbox').prop('checked', false);
}

function handleColorTheme(e) {
    $('html').attr('data-color-theme', e);
    $(e).prop('checked', !0);
}

function copyToClipboard(elementID) {
    const text = document.getElementById(elementID)?.textContent || '';

    if (!text) {
        showNotification('Copy Error', 'No text found', 'danger');
        return;
    }

    navigator.clipboard.writeText(text)
        .then(() => showNotification('Copy Successful', 'Text copied to clipboard', 'success'))
        .catch(() => showNotification('Copy Error', 'Failed to copy text', 'danger'));
}

function showErrorDialog(error){
    const errorDialogElement = document.getElementById('error-dialog');

    if (errorDialogElement) {
        errorDialogElement.innerHTML = error;
        $('#system-error-modal').modal('show');
    }
    else {
        console.error('Error dialog element not found.');
    }    
}

function updateFormSubmitButton(buttonId, disabled) {
    try {
        const submitButton = document.querySelector(`#${buttonId}`);
    
        if (submitButton) {
            submitButton.disabled = disabled;
        }
        else {
            console.error(`Button with ID '${buttonId}' not found.`);
        }
    }
    catch (error) {
        console.error(error);
    }
}

function disableFormSubmitButton(buttonId) {
    updateFormSubmitButton(buttonId, true);
}

function enableFormSubmitButton(buttonId) {
    updateFormSubmitButton(buttonId, false);
}

function handleSystemError(xhr, status, error) {
    let fullErrorMessage = `XHR status: ${status}, Error: ${error}${xhr.responseText ? `, Response: ${xhr.responseText}` : ''}`;
    showErrorDialog(fullErrorMessage);
}

function showNotification(notificationTitle, notificationMessage, notificationType, timeOut = 2000) {
    const validNotificationTypes = ['success', 'info', 'warning', 'error'];
    const isDuplicate = isDuplicateNotification(notificationMessage);

    if (!validNotificationTypes.includes(notificationType)) {
        console.error('Invalid notification type:', notificationType);
        return;
    }

    const toastrOptions = {
        closeButton: true,
        progressBar: true,
        newestOnTop: true,
        preventDuplicates: true,
        preventOpenDuplicates: true,
        positionClass: 'toastr-top-right',
        timeOut: timeOut,
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut',
        escapeHtml: false
    };

    if (!isDuplicate) {
        toastr.options = toastrOptions;
        toastr[notificationType](notificationMessage, notificationTitle);
    }
}

function isDuplicateNotification(message) {
    let isDuplicate = false;
    
    $('.toastr').each(function() {
        if ($(this).find('.toastr-message').text() === message[0].innerText) {
            isDuplicate = true;
            return false;
        }
    });

    return isDuplicate;
}
  
function setNotification(notificationTitle, notificationMessage, notificationType){
    sessionStorage.setItem('notificationTitle', notificationTitle);
    sessionStorage.setItem('notificationMessage', notificationMessage);
    sessionStorage.setItem('notificationType', notificationType);
}
  
function checkNotification() {
    const { 
        'notificationTitle': notificationTitle, 
        'notificationMessage': notificationMessage, 
        'notificationType': notificationType 
    } = sessionStorage;
    
    if (notificationTitle && notificationMessage && notificationType) {
        sessionStorage.removeItem('notificationTitle');
        sessionStorage.removeItem('notificationMessage');
        sessionStorage.removeItem('notificationType');

        showNotification(notificationTitle, notificationMessage, notificationType);
    }
}

function logNotes(databaseTable, referenceID){
    const type = 'log notes';

    $.ajax({
        type: 'POST',
        url: 'components/view/_log_notes_generation.php',
        dataType: 'json',
        data: { type: type, 'database_table': databaseTable, 'reference_id': referenceID },
        success: function (result) {
            document.getElementById('log-notes').innerHTML = result[0].LOG_NOTES;
        },
        error: function(xhr, status, error) {
            handleSystemError(xhr, status, error);
        }
    });
}

let selectedColumnsOrder = [];

function exportData(table_name) {
    const transaction = 'export data';
    var export_to = $('input[name="export_to"]:checked').val();

    var table_column = selectedColumnsOrder;

    let export_id = [];

    $('.datatable-checkbox-children').each((index, element) => {
        if ($(element).is(':checked')) {
            export_id.push(element.value);
        }
    });

    if (export_id.length === 0) {
        showNotification('Export Data', 'Choose the data you want to export.', 'error');
        return;
    }

    if (table_column.length === 0) {
        showNotification('Export Data', 'Choose the columns you want to export.', 'error');
        return;
    }

    $.ajax({
        type: 'POST',
        url: 'components/controller/export-controller.php',
        data: {
            transaction: transaction,
            export_id: export_id,
            export_to: export_to,
            table_column: table_column,
            table_name: table_name  
        },
        xhrFields: {
            responseType: 'blob'
        },
        beforeSend: function() {
            disableFormSubmitButton('submit-export');
        },
        success: function (response, status, xhr) {
            var filename = "";                   
            var disposition = xhr.getResponseHeader('Content-Disposition');

            if (disposition && disposition.indexOf('attachment') !== -1) {
                var matches = /filename="(.+)"/.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1];
                }
            }

            var blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename || "export." + export_to;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        error: function(xhr, status, error) {
            handleSystemError(xhr, status, error);
        },
        complete: function() {
            enableFormSubmitButton('submit-export');
        }
    });
}

function generateExportColumns(table_name) {
    const type = 'export options';
    
    $.ajax({
        url: 'components/view/_export_generation.php',
        method: 'POST',
        dataType: 'json',
        data: {
            type: type,
            table_name: table_name
        },
        success: function(response) {
            var select = document.getElementById('table_column');
            select.options.length = 0;

            response.forEach(function(opt) {
                var option = new Option(opt.text, opt.id);
                select.appendChild(option);
            });
        },
        error: function(xhr, status, error) {
            var fullErrorMessage = `XHR status: ${status}, Error: ${error}`;
            if (xhr.responseText) {
                fullErrorMessage += `, Response: ${xhr.responseText}`;
            }
            showErrorDialog(fullErrorMessage);
        },
        complete: function() {
            if ($('#table_column').length) {
                $('#table_column').bootstrapDualListbox({
                    nonSelectedListLabel: 'Non-selected',
                    selectedListLabel: 'Selected',
                    preserveSelectionOnMove: 'moved',
                    moveOnSelect: false,
                    helperSelectNamePostfix: false,
                    sortByInputOrder: true
                });

                $('#table_column').on('change', function() {
                    $('#table_column option:selected').each(function() {
                        let value = $(this).val();
                        if (!selectedColumnsOrder.includes(value)) {
                            selectedColumnsOrder.push(value);
                        }
                    });

                    $('#table_column option:not(:selected)').each(function() {
                        let value = $(this).val();
                        selectedColumnsOrder = selectedColumnsOrder.filter(item => item !== value);
                    });
                });

                $('#table_column').bootstrapDualListbox('refresh', true);
                initializeDualListBoxIcon();
            }
        }
    });
}

function getDeviceInfo() {
    const userAgent = navigator.userAgent;
    let device = 'Unknown Device';
    let browser = 'Unknown Browser';

    if (/Android/i.test(userAgent)) {
        device = 'Android';
    } else if (/iPhone|iPad|iPod/i.test(userAgent)) {
        device = 'iOS';
    } else if (/Windows NT/i.test(userAgent)) {
        device = 'Windows';
    } else if (/Mac OS/i.test(userAgent)) {
        device = 'Mac OS';
    } else if (/Linux/i.test(userAgent)) {
        device = 'Linux';
    }

    if (/Firefox/i.test(userAgent)) {
        browser = 'Firefox';
    } else if (/OPR|Opera/i.test(userAgent)) {
        browser = 'Opera';
    } else if (/Chrome/i.test(userAgent)) {
        browser = 'Chrome';
    } else if (/Safari/i.test(userAgent) && !/Chrome/i.test(userAgent)) {
        browser = 'Safari';
    } else if (/MSIE|Trident/i.test(userAgent)) {
        browser = 'Internet Explorer';
    }

    return `${browser} - ${device}`;
}

function initializeTinyMCE(tiny_mce_id, disabled = 0){
    var options = {
        selector: "#" + tiny_mce_id,
        height : "350",
        toolbar: [
            'styleselect fontselect fontsizeselect',
            'undo redo | cut copy paste | bold italic | link image | alignleft aligncenter alignright alignjustify',
            'bullist numlist | outdent indent | blockquote subscript superscript | advlist | autolink | lists charmap | print preview |  code | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol'
        ],
        plugins: 'advlist autolink link image lists charmap print preview table code',
        license_key: 'GPL2+'
    };

    if (KTThemeMode.getMode() === "dark") {
        options["skin"] = "oxide-dark";
        options["content_css"] = "dark";
    }

    tinymce.init(options);

    if(disabled){
        tinymce.activeEditor.mode.set('readonly');
    }
}