<?php
    $addFileExtensionAccess  = $globalModel->checkSystemActionAccessRights($userID, 15);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Upload Setting</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="button" class="btn btn-dark dropdown-toggle mb-0" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php
                            echo $createAccess['total'] > 0 ? '<li><a class="dropdown-item" href="'. $pageLink .'&new">Create Upload Setting</a></li>' : '';
                            echo $deleteAccess['total'] > 0 ? '<li><button class="dropdown-item" type="button" id="delete-upload-setting">Delete Upload Setting</button></li>' : '';
                        ?>
                    </ul>
                </div>
                <?php
                    echo $writeAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                        <button class="btn btn-info mb-0 px-4" data-bs-toggle="modal" id="edit-details" data-bs-target="#upload-setting-modal" id="edit-details">Edit</button>
                                                    </div>' : '';
                ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-3">
                        <p class="mb-1 fs-2">Display Name</p>
                        <h6 class="fw-semibold mb-0" id="upload_setting_name_summary">--</h6>
                    </div>
                    <div class="col-lg-6 mb-3">
                        <p class="mb-1 fs-2">Max File Size</p>
                        <h6 class="fw-semibold mb-0" id="max_file_size_summary">--</h6>
                    </div>
                    <div class="col-lg-12 mb-0">
                        <p class="mb-1 fs-2">Description</p>
                        <h6 class="fw-semibold mb-0" id="upload_setting_description_summary">--</h6>
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
                    <h5 class="card-title mb-0">File Extension</h5>
                    <?php
                        echo $addFileExtensionAccess['total'] > 0 ? '<div class="card-actions cursor-pointer ms-auto d-flex button-group">
                                                                        <button class="btn btn-success d-flex align-items-center mb-0" data-bs-toggle="modal" data-bs-target="#file-extension-assignment-modal" id="assign-file-extension">Assign</button>
                                                                    </div>' : '';
                    ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="assigned-file-extension-table" class="table border table-striped table-hover align-middle text-wrap mb-0">
                            <thead class="text-dark">
                                <tr>
                                    <th>File Extension</th>
                                    <th>Actions</th>
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

<div id="upload-setting-modal" class="modal fade" tabindex="-1" aria-labelledby="upload-setting-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Edit Upload Setting Details</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="upload-setting-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="upload_setting_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="upload_setting_name" name="upload_setting_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="max_file_size">Max File Size <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="max_file_size" name="max_file_size" min="1">
                                    <span class="input-group-text">kb</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label" for="upload_setting_description">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control maxlength" id="upload_setting_description" name="upload_setting_description" maxlength="200" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="upload-setting-form" class="btn btn-success" id="submit-data">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div id="file-extension-assignment-modal" class="modal fade" tabindex="-1" aria-labelledby="file-extension-assignment-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Assign File Extension</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="file-extension-assignment-form" method="post" action="#">
                    <div class="row">
                        <div class="col-12">
                            <select multiple="multiple" size="20" id="file_extension_id" name="file_extension_id[]"></select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="file-extension-assignment-form" class="btn btn-success" id="submit-assignment">Save changes</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/global/view/_log_notes_offcanvas.php'); ?>
<?php require_once('components/global/view/_internal_log_notes.php'); ?>