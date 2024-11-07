<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Role Details</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            
        </div>
    </div>
    <div class="card-body">
        <form id="role-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="role_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="role_name" name="role_name" maxlength="100" autocomplete="off">
            </div>
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="role_description">
                    <span class="required">Description</span>
                </label>

                <textarea class="form-control" id="role_description" name="role_description" maxlength="200" rows="3"></textarea>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="role-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>