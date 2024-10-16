<div class="card mb-6">
    <div class="card-header border-0 pt-6 pb-6">
        <div class="card-title">
            Import Records
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <?php
                    echo $importAccess['total'] > 0 ? '<button class="btn btn-success mb-0 me-2 px-4" data-bs-toggle="modal" id="upload-file" data-bs-target="#upload-modal">Upload File</button>' : '';
                ?>
                <a href="<?php echo $pageLink; ?>" class="btn btn-warning d-flex align-items-center me-2 mb-0">Cancel</a>
                <?php
                    echo $importAccess['total'] > 0 ? '<button class="btn btn-success mb-0 me-2 px-4" type="submit" form="upload-form">Import</button><button class="btn btn-info mb-0 me-2 px-4" id="reset-import">Reset</button>' : '';
                ?>
            </div>
        </div>
    </div>
</div>

<div class="card card-flush">
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