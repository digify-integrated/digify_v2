<?php
    $addMenuItemRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 9);
    $addSystemActionRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 12);
?>
<div class="card mb-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Role Details</h3>
        </div>
        <?php
            if ($deleteAccess['total'] > 0) {
            $action = '<a href="#" class="btn btn-light-primary btn-flex btn-center btn-active-light-primary show menu-dropdown align-self-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true" style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate(-60px, 539px);" data-popper-placement="bottom-end">';
                    
                        if ($deleteAccess['total'] > 0) {
                            $action .= '<div class="menu-item px-3">
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-role">
                                                Delete
                                            </a>
                                        </div>';
                        }
                    
                        $action .= '</div>';
                    
                        echo $action;
                    }
                ?>
    </div>
    <div class="card-body">
        <form id="role-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="role_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="role_name" name="role_name" maxlength="100" autocomplete="off">
            </div>
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="role_description">
                    <span class="required">Description</span>
                </label>

                <textarea class="form-control" id="role_description" name="role_description" maxlength="200" rows="3"></textarea>
            </div>
        </form>
    </div>

    <?php
        echo ($writeAccess['total'] > 0) ? ' <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" form="role-form" class="btn btn-primary" id="submit-data">Save</button>
                                            </div>' : '';
    ?>
</div>

<div class="card mb-10">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1 me-3">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> <input type="text" class="form-control w-250px ps-12" id="menu-item-permission-datatable-search" placeholder="Search..." autocomplete="off" />
            </div>
            <select id="menu-item-permission-datatable-length" class="form-select w-auto">
                <option value="-1">All</option>
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <?php
                    echo $addMenuItemRoleAccess['total'] > 0 ? '<button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#menu-item-permission-assignment-modal" id="assign-menu-item-permission"><i class="ki-outline ki-plus fs-2"></i> Assign</button>' : '';
                ?> 
            </div>
        </div>
    </div>
    <div class="card-body pt-9">
        <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5 gs-7" id="menu-item-permission-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800">
                    <th>Menu Item</th>
                    <th>Read Access</th>
                    <th>Create Access</th>
                    <th>Write Access</th>
                    <th>Delete Access</th>
                    <th>Import Access</th>
                    <th>Export Access</th>
                    <th>Log Notes Access</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600"></tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1 me-3">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> <input type="text" class="form-control w-250px ps-12" id="system-action-permission-datatable-search" placeholder="Search..." autocomplete="off" />
            </div>
            <select id="system-action-permission-datatable-length" class="form-select w-auto">
                <option value="-1">All</option>
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <?php
                    echo $addSystemActionRoleAccess['total'] > 0 ? '<button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#system-action-permission-assignment-modal" id="assign-system-action-permission"><i class="ki-outline ki-plus fs-2"></i> Assign</button>' : '';
                ?>
            </div>
        </div>
    </div>
    <div class="card-body pt-9">
        <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5 gs-7" id="system-action-permission-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800">
                    <th>System Action</th>
                    <th>Access</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600"></tbody>
        </table>
    </div>
</div>

<div id="menu-item-permission-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="menu-item-permission-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Assign Menu Item</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <form id="menu-item-permission-assignment-form" method="post" action="#">
                    <div class="row">
                        <div class="col-12">
                            <select multiple="multiple" size="20" id="menu_item_id" name="menu_item_id[]"></select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="menu-item-permission-assignment-form" class="btn btn-primary" id="submit-assignment">Assign</button>
            </div>
        </div>
    </div>
</div>

<div id="system-action-permission-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="system-action-permission-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Assign System Action</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <form id="system-action-permission-assignment-form" method="post" action="#">
                    <div class="row">
                        <div class="col-12">
                            <select multiple="multiple" size="20" id="system_action_id" name="system_action_id[]"></select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="system-action-permission-assignment-form" class="btn btn-primary" id="submit-assignment">Assign</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>