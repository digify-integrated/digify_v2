<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Billing Cycle Details</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            
        </div>
    </div>
    <div class="card-body">
        <form id="billing-cycle-form" method="post" action="#">
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="billing_cycle_name">
                    <span class="required">Display Name</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="billing_cycle_name" name="billing_cycle_name" maxlength="100" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="billing_cycle_description">
                    <span class="required">Description</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <textarea class="form-control form-control-solid maxlength" id="billing_cycle_description" name="billing_cycle_description" maxlength="500" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-0">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="order_sequence">
                    <span class="required">Order Sequence</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="number" class="form-control form-control-solid" id="order_sequence" name="order_sequence" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="billing-cycle-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>