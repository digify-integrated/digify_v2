<?php
    $addMenuItemRoleAccess = $authenticationModel->checkSystemActionAccessRights($userID, 9);
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Menu Item</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="button" class="btn btn-dark dropdown-toggle action-dropdown mb-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                           echo $createAccess['total'] > 0 ? '<li><a class="dropdown-item" href="'. $pageLink .'&new">Create</a></li>' : '';
                           echo $deleteAccess['total'] > 0 ? '<li><button class="dropdown-item" type="button" id="delete-menu-item">Delete</button></li>' : '';
                        ?>
                    </ul>
                </div>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                            <button class="btn btn-info mb-0 me-0 px-4" data-bs-toggle="modal" id="edit-details" data-bs-target="#menu-item-modal">Edit</button>
                                                        </div>' : '';
                ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Display Name:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="menu_item_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Menu Item:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="menu_group_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Icon:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="menu_item_icon_summary">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">URL:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="menu_item_url_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Parent Menu Item:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="parent_menu_item_summary">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Order Sequence:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="order_sequence_summary">--</p>
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
                            echo $addMenuItemRoleAccess['total'] > 0 ? '<button class="btn btn-success d-flex align-items-center mb-0" data-bs-toggle="modal" data-bs-target="#role-permission-assignment-modal" id="assign-role-permission">Assign</button>' : '';
                        ?> 
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="role-permission-table" class="table w-100 text-nowrap dataTable">
                            <thead class="text-dark">
                                <tr>
                                    <th>Role</th>
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

<div class="modal fade" id="menu-item-modal" tabindex="-1" aria-labelledby="menu-item-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Edit Menu Item Details</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="menu-item-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="menu_item_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="menu_item_name" name="menu_item_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="menu_group_id">Menu Group <span class="text-danger">*</span></label>
                            <div class="mb-3">
                                <select id="menu_group_id" name="menu_group_id" class="select2 form-control"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="order_sequence">Order Sequence <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="order_sequence" name="order_sequence" min="0">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="parent_id">Parent Menu Item</label>
                            <div class="mb-3">
                                <select id="parent_id" name="parent_id" class="select2 form-control"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="menu_item_icon">Icon</label>
                                <input type="text" class="form-control maxlength" id="menu_item_icon" name="menu_item_icon" maxlength="50" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="menu_item_url">URL</label>
                                <input type="text" class="form-control maxlength" id="menu_item_url" name="menu_item_url" maxlength="50" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="menu-item-form" class="btn btn-success" id="submit-data">Save changes</button>
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