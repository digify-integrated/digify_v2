<div class="card mb-6">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <?php require('./components/view/_datatable_search.php') ?>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-menu-item">
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
                        <div class="px-7 py-5" data-kt-user-table-filter="form">
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="app_module_filter">App Module:</label>
                                <select id="app_module_filter" name="app_module_filter" multiple="multiple" class="form-select form-select-solid" data-control="select2" data-allow-clear="false"></select>
                            </div>
                            <div class="mb-10">
                                <label class="form-label fs-6 fw-semibold" for="parent_id_filter">Parent Menu:</label>
                                <select id="parent_id_filter" name="parent_id_filter" multiple="multiple" class="form-select form-select-solid" data-control="select2" data-allow-clear="false"></select>
                            </div>
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
    <div class="card-body pt-9">
        <div class="table-responsive">
            <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5 text-nowrap" id="menu-item-table">
                <thead>
                    <tr class="text-start text-gray-800 fw-bold fs-7 text-uppercase gs-0">
                        <th>
                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                <input class="form-check-input" id="datatable-checkbox" type="checkbox">
                            </div>
                        </th>
                        <th>Menu Item</th>
                        <th>App Module</th>
                        <th>Parent Menu</th>
                        <th>Order Sequence</th>
                    </tr>
                </thead>
                <tbody class="fw-semibold text-gray-800"></tbody>
            </table>
        </div>
    </div>
</div>

<?php
    $exportAccess['total'] > 0 ? require('./components/view/_export_modal.php') : '';
?>