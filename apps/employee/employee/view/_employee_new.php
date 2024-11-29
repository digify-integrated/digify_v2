<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Employee Details</h5>
    </div>
    <div class="card-body">
        <form id="employee-form" method="post" action="#">
            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="first_name">
                            <span class="required">First Name</span>
                        </label>

                        <input type="text" class="form-control" id="first_name" name="first_name" maxlength="300" autocomplete="off">
                    </div>
                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="last_name">
                            <span class="required">Last Name</span>
                        </label>

                        <input type="text" class="form-control" id="last_name" name="last_name" maxlength="300" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">                
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="middle_name">
                            Middle Name
                        </label>

                        <input type="text" class="form-control" id="middle_name" name="middle_name" maxlength="300" autocomplete="off">
                    </div>
                </div>
                
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="suffix">
                            Suffix
                        </label>

                        <input type="text" class="form-control" id="suffix" name="suffix" maxlength="10" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="employee-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>