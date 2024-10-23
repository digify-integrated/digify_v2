<div class="card mb-6">
    <div class="card-header border-0 pt-6 pb-6">
        <div class="card-title">
            Import Records
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <?php
                    echo $importAccess['total'] > 0 ? '<button type="button" class="btn btn-flex btn-primary me-2" data-bs-toggle="modal" id="upload-file" data-bs-target="#upload-modal">
                        <i class="ki-outline ki-folder-up fs-2"></i>
                        Upload File
                    </button>' : '';
                ?>
                <a href="<?php echo $pageLink; ?>" class="btn btn-warning d-flex align-items-center me-2 mb-0">Cancel</a>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush upload-file-default-preview" id="import-default-background">
    <div class="card-body">
        <div class="row justify-content-center w-100 upload-file-default-preview" id="import-default-background">
            <div class="col-lg-6">
                <div class="text-center">
                    <img src="./assets/images/default/import-logo.svg" alt="import-logo" class="img-fluid" width="100">
                    <h1 class="fw-semibold my-7 fs-4">Upload a CSV file to import</h1>
                    <h6 class="fw-semibold mb-7">CSV files are recommended as formatting is automatic.</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush d-none upload-file-preview">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 mt-3 mt-md-0">
                <div class="table-responsive">
                    <table id="upload-file-preview-table" class="table w-100 table-row-bordered table-striped text-wrap"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="upload-modal" class="modal fade" tabindex="-1" aria-labelledby="upload-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Upload File</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="upload-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" id="import_table_name" name="import_table_name" value="<?php echo $importTableName; ?>">
                            <input type="file" class="form-control" id="import_file" name="import_file" accept=".csv">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="upload-form" class="btn btn-primary" id="submit-upload">Upload File</button>
            </div>
        </div>
    </div>
</div>