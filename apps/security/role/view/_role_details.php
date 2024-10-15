<?php
    $addMenuItemRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 9);
    $addSystemActionRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 12);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Role</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="button" class="btn btn-dark dropdown-toggle action-dropdown mb-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                           echo $createAccess['total'] > 0 ? '<li><a class="dropdown-item" href="'. $pageLink .'&new">Create</a></li>' : '';
                           echo $deleteAccess['total'] > 0 ? '<li><button class="dropdown-item" type="button" id="delete-role">Delete</button></li>' : '';
                        ?>
                    </ul>
                </div>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                            <button class="btn btn-info mb-0 me-0 px-4" data-bs-toggle="modal" id="edit-details" data-bs-target="#role-modal">Edit</button>
                                                        </div>' : '';
                ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-3">Display Name:</label>
                                <div class="col-md-9">
                                <p class="form-control-static" id="role_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-3">Description:</label>
                                <div class="col-md-9">
                                <p class="form-control-static" id="role_description_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="datatables">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">Menu Item Permission</h5>
                    <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                        <?php
                            echo $addMenuItemRoleAccess['total'] > 0 ? '<button class="btn btn-success d-flex align-items-center mb-0" data-bs-toggle="modal" data-bs-target="#menu-item-permission-assignment-modal" id="assign-menu-item-permission">Assign</button>' : '';
                        ?>                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="menu-item-permission-table" class="table w-100 text-nowrap dataTable">
                            <thead class="text-dark">
                                <tr>
                                    <th>Menu Item</th>
                                    <th>Read</th>
                                    <th>Create</th>
                                    <th>Write</th>
                                    <th>Delete</th>
                                    <th>Import</th>
                                    <th>Export</th>
                                    <th>Log Notes</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="datatables">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="card-title mb-0">System Action Permission</h5>
                    <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                        <?php
                            echo $addSystemActionRoleAccess['total'] > 0 ? '<button class="btn btn-success d-flex align-items-center mb-0" data-bs-toggle="modal" data-bs-target="#system-action-permission-assignment-modal" id="assign-system-action-permission">Assign</button>' : '';
                        ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="system-action-permission-table" class="table w-100 text-nowrap dataTable">
                            <thead class="text-dark">
                                <tr>
                                    <th>System Action</th>
                                    <th>Access</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="role-modal" tabindex="-1" aria-labelledby="role-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Edit Role Details</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="role-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label" for="role_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="role_name" name="role_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="role_description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control maxlength" id="role_description" name="role_description" maxlength="200" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="role-form" class="btn btn-success" id="submit-data">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div id="menu-item-permission-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="menu-item-permission-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Assign Menu Item Permission</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="menu-item-permission-assignment-form" class="btn btn-success" id="submit-menu-item-assignment">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div id="system-action-permission-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="system-action-permission-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Assign System Action Permission</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="system-action-permission-assignment-form" class="btn btn-success" id="submit-system-action-assignment">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php 
    $logNotesAccess['total'] > 0 ? require_once('components/view/_log_notes_modal.php') : ''; 
    require_once('components/view/_internal_log_notes.php');
?>