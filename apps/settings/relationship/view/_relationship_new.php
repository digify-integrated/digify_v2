<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Relationship Details</h5>
    </div>
    <div class="card-body">
        <form id="relationship-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="relationship_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="relationship_name" name="relationship_name" maxlength="100" autocomplete="off">
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="relationship-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>