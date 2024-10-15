<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">App Module</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="button" class="btn btn-dark dropdown-toggle action-dropdown mb-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                           echo $createAccess['total'] > 0 ? '<li><a class="dropdown-item" href="'. $pageLink .'&new">Create</a></li>' : '';
                           echo $deleteAccess['total'] > 0 ? '<li><button class="dropdown-item" type="button" id="delete-user-account">Delete</button></li>' : '';
                        ?>
                    </ul>
                </div>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                            <button class="btn btn-info mb-0 me-0 px-4" data-bs-toggle="modal" id="edit-details" data-bs-target="#user-account-modal">Edit</button>
                                                        </div>' : '';
                ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="form-label col-md-3">Display Name:</label>
                                <div class="col-md-9">
                                <p class="form-control-static" id="user_account_name_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="form-label col-md-3">Description:</label>
                                <div class="col-md-9">
                                <p class="form-control-static" id="user_account_description_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="form-label col-md-3">Default Page:</label>
                                <div class="col-md-9">
                                <p class="form-control-static" id="menu_item_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="form-label col-md-3">Order Sequence:</label>
                                <div class="col-md-9">
                                <p class="form-control-static" id="order_sequence_summary">--</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">App Module Logo</h5>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                            <button class="btn btn-primary mb-0 me-0" data-bs-toggle="modal" id="update-app-logo" data-bs-target="#app-logo-modal">Upload</button>
                                                        </div>' : '';
                ?>
            </div>
            <div class="card-body p-4">
                <div class="text-center">
                    <img src="./assets/images/default/user-account-logo.png" alt="" id="user_account_logo" width="80" height="80">
                    <p class="fs-2 text-center mb-0 mt-2">
                      Set the user account image. Only *.png, *.jpg and *.jpeg image files are accepted.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="user-account-modal" tabindex="-1" aria-labelledby="user-account-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Edit App Module Details</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user-account-form" method="post" action="#">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="user_account_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="user_account_name" name="user_account_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label" for="user_account_description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control maxlength" id="user_account_description" name="user_account_description" maxlength="100" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label" for="menu_item_id">Default Page <span class="text-danger">*</span></label>
                            <div class="mb-3">
                                <select id="menu_item_id" name="menu_item_id" class="select2 form-control"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                <button type="submit" form="user-account-form" class="btn btn-success" id="submit-data">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div id="app-logo-modal" class="modal fade" tabindex="-1" aria-labelledby="app-logo-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">App Logo</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="app-logo-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="file" class="form-control" id="app_logo" name="app_logo">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="app-logo-form" class="btn btn-success" id="submit-app-logo">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php 
    $logNotesAccess['total'] > 0 ? require_once('components/view/_log_notes_modal.php') : ''; 
    require_once('components/view/_internal_log_notes.php');
?>