<?php
    $addSystemActionRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 12);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">System Action</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="button" class="btn btn-dark dropdown-toggle action-dropdown mb-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                           echo $createAccess['total'] > 0 ? '<li><a class="dropdown-item" href="'. $pageLink .'&new">Create</a></li>' : '';
                           echo $deleteAccess['total'] > 0 ? '<li><button class="dropdown-item" type="button" id="delete-system-action">Delete</button></li>' : '';
                        ?>
                    </ul>
                </div>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                            <button class="btn btn-info mb-0 me-0 px-4" data-bs-toggle="modal" id="edit-details" data-bs-target="#system-action-modal">Edit</button>
                                                        </div>' : '';
                ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Display Name:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="system_action_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Description:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="system_action_description_summary">--</p>
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
                    <h5 class="card-title mb-0">Role Permission</h5>
                    <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                        <?php
                            echo $addSystemActionRoleAccess['total'] > 0 ? '<button class="btn btn-success d-flex align-items-center mb-0" data-bs-toggle="modal" data-bs-target="#role-permission-assignment-modal" id="assign-role-permission">Assign</button>' : '';
                        ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="role-permission-table" class="table w-100 text-nowrap dataTable">
                            <thead class="text-dark">
                                <tr>
                                    <th>Role</th>
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

<div class="modal fade" id="system-action-modal" tabindex="-1" aria-labelledby="system-action-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Edit System Action Details</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="system-action-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label" for="system_action_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="system_action_name" name="system_action_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="system_action_description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control maxlength" id="system_action_description" name="system_action_description" maxlength="200" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="system-action-form" class="btn btn-success" id="submit-data">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div id="role-permission-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="role-permission-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Assign Role Permission</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="role-permission-assignment-form" class="btn btn-success" id="submit-assignment">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php 
    $logNotesAccess['total'] > 0 ? require_once('components/view/_log_notes_modal.php') : ''; 
    require_once('components/view/_internal_log_notes.php');
?>