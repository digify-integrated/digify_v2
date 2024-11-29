<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Upload Setting Details</h5>
    </div>
    <div class="card-body">
        <form id="upload-setting-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="upload_setting_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="upload_setting_name" name="upload_setting_name" maxlength="100" autocomplete="off">
            </div>
            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="menu_item_id">
                            <span class="required">Description</span>
                        </label>

                        <input type="text" class="form-control" id="upload_setting_description" name="upload_setting_description" maxlength="200" autocomplete="off">
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="max_file_size">
                            <span class="required">Max File Size</span>
                        </label>
                        
                        <div class="input-group mb-5">
                            <input type="number" class="form-control" id="max_file_size" name="max_file_size" min="1" step="1">
                            <span class="input-group-text">kb</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="upload-setting-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>