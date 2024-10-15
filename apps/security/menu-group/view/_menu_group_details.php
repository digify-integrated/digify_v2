<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Menu Group</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="button" class="btn btn-dark dropdown-toggle action-dropdown mb-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                           echo $createAccess['total'] > 0 ? '<li><a class="dropdown-item" href="'. $pageLink .'&new">Create</a></li>' : '';
                           echo $deleteAccess['total'] > 0 ? '<li><button class="dropdown-item" type="button" id="delete-menu-group">Delete</button></li>' : '';
                        ?>
                    </ul>
                </div>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                            <button class="btn btn-info mb-0 me-0 px-4" data-bs-toggle="modal" id="edit-details" data-bs-target="#menu-group-modal">Edit</button>
                                                        </div>' : '';
                ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">Display Name:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="menu_group_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="form-label col-md-4">App Module:</label>
                                <div class="col-md-8">
                                <p class="form-control-static" id="app_module_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
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

<div class="modal fade" id="menu-group-modal" tabindex="-1" aria-labelledby="menu-group-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Edit Menu Group Details</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="menu-group-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label" for="menu_group_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="menu_group_name" name="menu_group_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label" for="app_module_id">App Module <span class="text-danger">*</span></label>
                            <div class="mb-3">
                                <select id="app_module_id" name="app_module_id" class="select2 form-control"></select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="order_sequence">Order Sequence <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="order_sequence" name="order_sequence" min="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="menu-group-form" class="btn btn-success" id="submit-data">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_internal_log_notes.php'); ?>