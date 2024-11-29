<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Currency Details</h5>
    </div>
    <div class="card-body">
        <form id="currency-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="currency_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="currency_name" name="currency_name" maxlength="100" autocomplete="off">
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="symbol">
                            <span class="required">Symbol</span>
                        </label>

                        <input type="text" class="form-control" id="symbol" name="symbol" maxlength="5" autocomplete="off">
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="shorthand">
                            <span class="required">Shorthand</span>
                        </label>

                        <input type="text" class="form-control" id="shorthand" name="shorthand" maxlength="10" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="currency-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>