<div class="card mb-6">
    <div class="card-header border-0">
        <div class="card-title">
            <?php require('./components/view/_datatable_search.php') ?>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <div class="d-flex flex-wrap my-1">
                    <ul class="nav nav-pills me-5" role="tablist">
                        <li class="nav-item m-0" role="presentation">
                            <a class="btn btn-icon btn-light btn-color-muted btn-active-primary me-3 active" data-bs-toggle="tab" href="#tab_card_view" aria-selected="true" role="tab">
                                <i class="ki-outline ki-element-11 fs-1"></i>
                            </a>
                        </li>

                        <li class="nav-item m-0" role="presentation">
                            <a class="btn btn-icon btn-light btn-color-muted btn-active-primary" data-bs-toggle="tab" href="#tab_table_view" aria-selected="false" role="tab" tabindex="-1">
                                <i class="ki-outline ki-element-8 fs-2"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <?php
                    if ($deleteAccess['total'] > 0 || $exportAccess['total'] > 0) {
                        $action = '<a href="#" class="btn btn-light-primary btn-flex btn-center btn-active-light-primary show menu-dropdown action-dropdown me-3 d-none" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true" style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate(-60px, 539px);" data-popper-placement="bottom-end">';
                    
                        if ($exportAccess['total'] > 0) {
                            $action .= '<div class="menu-item px-3">
                                            <a href="javascript:void(0);" class="menu-link px-3" data-bs-toggle="modal" id="export-data" data-bs-target="#export-modal">
                                                Export
                                            </a>
                                        </div>';
                        }
                    
                        if ($deleteAccess['total'] > 0) {
                            $action .= '<div class="menu-item px-3">
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-employee">
                                                Delete
                                            </a>
                                        </div>';
                        }
                    
                        $action .= '</div>';
                    
                        echo $action;
                    }
                ?>
            <div>
                <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end"><i class="ki-outline ki-filter fs-2"></i> Filter</button>
                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                    <div class="px-7 py-5">
                        <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                    </div>
                    <div class="separator border-gray-200"></div>
                        <div class="px-7 py-5 mh-300px overflow-auto" data-kt-user-table-filter="form">
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="company_filter">Company:</label>
                                <select id="company_filter" name="company_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false"></select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="department_filter">Department:</label>
                                <select id="department_filter" name="department_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false"></select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="department_filter">Job Position:</label>
                                <select id="job_position_filter" name="job_position_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false"></select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="employee_status_filter">Employee Status:</label>
                                <select id="employee_status_filter" name="employee_status_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false">
                                    <option value="Active">Active</option>
                                    <option value="Archived">Archived</option>
                                </select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="work_location_filter">Work Location:</label>
                                <select id="work_location_filter" name="work_location_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false"></select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="employment_type_filter">Employment Type:</label>
                                <select id="employment_type_filter" name="employment_type_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false"></select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label fs-6 fw-semibold" for="gender_filter">Gender:</label>
                                <select id="gender_filter" name="gender_filter" multiple="multiple" class="form-select" data-control="select2" data-allow-clear="false"></select>
                            </div>
                        </div>
                        <div class="px-7 py-5" data-kt-user-table-filter="form">                            
                            <div class="d-flex justify-content-end">
                                <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" id="reset-filter" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                <button type="button" class="btn btn-primary fw-semibold px-6" id="apply-filter" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-content">
    <div id="tab_card_view" class="tab-pane fade active show" role="tabpanel">
        <div class="row g-6 g-xl-9" id="employee-card">
            
        </div>
    </div>

    <div id="tab_table_view" class="tab-pane fade" role="tabpanel">
        <div class="card mb-6">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5 text-nowrap" id="employee-table">
                        <thead>
                            <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                                <th>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" id="datatable-checkbox" type="checkbox">
                                    </div>
                                </th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Job Position</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-800"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $exportAccess['total'] > 0 ? require('./components/view/_export_modal.php') : '';
?>