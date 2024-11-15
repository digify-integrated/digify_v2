<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Company Details</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            
        </div>
    </div>
    <div class="card-body">
        <form id="company-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="company_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="company_name" name="company_name" maxlength="100" autocomplete="off">
            </div>
            
            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="address">
                            <span class="required">Address</span>
                        </label>

                        <input type="text" class="form-control" id="address" name="address" maxlength="1000" autocomplete="off">
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="city_id">
                            <span class="required">City</span>
                        </label>

                        <select id="city_id" name="city_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="tax_id">
                            Tax Identification Number
                        </label>

                        <input type="text" class="form-control" id="tax_id" name="tax_id" maxlength="100" autocomplete="off">
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="currency_id">
                        Currency
                        </label>

                        <select id="currency_id" name="currency_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="phone">
                            Phone
                        </label>

                        <input type="text" class="form-control" id="phone" name="phone" maxlength="20" autocomplete="off">
                    </div>

                </div>
                
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="telephone">
                            Telephone
                        </label>

                        <input type="text" class="form-control" id="telephone" name="telephone" maxlength="20" autocomplete="off">
                    </div>

                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="email">
                            Email
                        </label>

                        <input type="email" class="form-control" id="email" name="email" maxlength="250" autocomplete="off">
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="website">
                            Website
                        </label>

                        <input type="text" class="form-control" id="website" name="website" maxlength="250" autocomplete="off">
                    </div>

                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="company-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>