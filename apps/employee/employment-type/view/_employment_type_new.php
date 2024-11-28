<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Employment Type Details</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            
        </div>
    </div>
    <div class="card-body">
        <form id="employment-type-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="employment_type_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="employment_type_name" name="employment_type_name" maxlength="100" autocomplete="off">
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="employment-type-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>