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
                <button type="button" class="btn btn-light-primary me-3 show menu-dropdown" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <i class="ki-outline ki-filter fs-2"></i>        Filter
                </button>
                <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true" style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate(-326px, 337px);" data-popper-placement="bottom-end">
    <!--begin::Header-->
    <div class="px-7 py-5">
        <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
    </div>
    <!--end::Header-->

    <!--begin::Separator-->
    <div class="separator border-gray-200"></div>
    <!--end::Separator-->

    <!--begin::Content-->
    <div class="px-7 py-5" data-kt-user-table-filter="form">
        <!--begin::Input group-->
        <div class="mb-10" data-select2-id="select2-data-119-jb14">
            <label class="form-label fs-6 fw-semibold">Role:</label>
            <select class="form-select form-select-solid fw-bold select2-hidden-accessible" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true" data-select2-id="select2-data-1-j8v5" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                <option data-select2-id="select2-data-3-s32u"></option>
                <option value="Administrator" data-select2-id="select2-data-126-6l59">Administrator</option>
                <option value="Analyst" data-select2-id="select2-data-127-1llk">Analyst</option>
                <option value="Developer" data-select2-id="select2-data-128-fyic">Developer</option>
                <option value="Support" data-select2-id="select2-data-129-4324">Support</option>
                <option value="Trial" data-select2-id="select2-data-130-jn4t">Trial</option>
            </select><span class="select2 select2-container select2-container--bootstrap5 select2-container--below" dir="ltr" data-select2-id="select2-data-2-qzcv" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single form-select form-select-solid fw-bold" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-m3kh-container" aria-controls="select2-m3kh-container"><span class="select2-selection__rendered" id="select2-m3kh-container" role="textbox" aria-readonly="true" title="Select option"><span class="select2-selection__placeholder">Select option</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="mb-10">
            <label class="form-label fs-6 fw-semibold">Two Step Verification:</label>
            <select class="form-select form-select-solid fw-bold select2-hidden-accessible" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true" data-select2-id="select2-data-4-qm7m" tabindex="-1" aria-hidden="true" data-kt-initialized="1">
                <option data-select2-id="select2-data-6-tli9"></option>
                <option value="Enabled">Enabled</option>
            </select><span class="select2 select2-container select2-container--bootstrap5" dir="ltr" data-select2-id="select2-data-5-oefs" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single form-select form-select-solid fw-bold" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-s2tn-container" aria-controls="select2-s2tn-container"><span class="select2-selection__rendered" id="select2-s2tn-container" role="textbox" aria-readonly="true" title="Select option"><span class="select2-selection__placeholder">Select option</span></span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
        </div>
        <!--end::Input group-->

        <!--begin::Actions-->
        <div class="d-flex justify-content-end">
            <button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
            <button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
        </div>
        <!--end::Actions-->
    </div>
    <!--end::Content-->
</div>
            </div>
        </div>
    </div>
    <div class="card-body pt-9">
        <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5" id="menu-item-table">
            <thead>
                <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                    <th class="all">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" id="datatable-checkbox" type="checkbox">
                        </div>
                    </th>
                    <th>Menu Item</th>
                    <th>Menu Group</th>
                    <th>App Module</th>
                    <th>Parent Menu Item</th>
                    <th>Order Sequence</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600"></tbody>
        </table>
    </div>
</div>

<?php
    $exportAccess['total'] > 0 ? require('./components/view/_export_modal.php') : '';
?>