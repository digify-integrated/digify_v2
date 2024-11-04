<?php
    $addSystemActionRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 12);
?>
<div class="card mb-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">System Action Details</h3>
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-system-action">
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
        <form id="system-action-form" method="post" action="#">
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="system_action_name">
                    <span class="required">Display Name</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="system_action_name" name="system_action_name" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-0">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="system_action_description">
                    <span class="required">Description</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <textarea class="form-control form-control-solid maxlength" id="system_action_description" name="system_action_description" maxlength="200" rows="3" <?php echo $disabled ?>></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
        echo ($writeAccess['total'] > 0) ? ' <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" form="system-action-form" class="btn btn-primary" id="submit-data">Save</button>
                                            </div>' : '';
    ?>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <?php require('./components/view/_datatable_search.php') ?>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <?php
                    echo $addSystemActionRoleAccess['total'] > 0 ? '<button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#role-permission-assignment-modal" id="assign-role-permission"><i class="ki-outline ki-plus fs-2"></i> Assign</button>' : '';
                ?>
            </div>
        </div>
    </div>
    <div class="card-body pt-9">
        <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5 gs-7" id="role-permission-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800">
                    <th>Role</th>
                    <th>Access</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600"></tbody>
        </table>
    </div>
</div>

<div id="role-permission-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="role-permission-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Assign Role</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <form id="role-permission-assignment-form" method="post" action="#">
                    <div class="row">
                        <div class="col-12">
                            <select multiple="multiple" size="20" id="role_id" name="role_id[]"></select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="role-permission-assignment-form" class="btn btn-primary" id="submit-assignment">Assign</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>