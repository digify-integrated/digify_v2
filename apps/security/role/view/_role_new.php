<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Role Form</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            <button type="submit" form="role-form" class="btn btn-success mb-0" id="submit-data">Save</button>
            <button type="button" id="discard-create" class="btn btn-outline-danger mb-0">Discard</button>
        </div>
    </div>
    <div class="card-body">
        <form id="role-form" method="post" action="#">
            <div class="row">
                 <div class="col-lg-12">
                    <div class="mb-3">
                        <label class="form-label" for="role_name">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control maxlength" id="role_name" name="role_name" maxlength="100" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label" for="role_description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control maxlength" id="role_description" name="role_description" maxlength="200" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>