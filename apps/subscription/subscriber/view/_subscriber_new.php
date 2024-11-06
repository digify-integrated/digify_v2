<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Subscriber Details</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            
        </div>
    </div>
    <div class="card-body">
        <form id="subscriber-form" method="post" action="#">
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="subscriber_name">
                    <span class="required">Subscriber Name</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="subscriber_name" name="subscriber_name" maxlength="500" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="company_name">
                    <span class="required">Company</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="company_name" name="company_name" maxlength="200" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="phone">
                    <span class="required">Phone</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="phone" name="phone" maxlength="50" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="email">
                    <span class="required">Email</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="email" class="form-control form-control-solid maxlength" id="email" name="email" maxlength="50" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="subscription_tier_id">
                    <span class="required">Subscription Tier</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <select id="subscription_tier_id" name="subscription_tier_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="billing_cycle_id">
                    <span class="required">Billing Cycle</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <select id="billing_cycle_id" name="billing_cycle_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="false"></select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="subscriber-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>