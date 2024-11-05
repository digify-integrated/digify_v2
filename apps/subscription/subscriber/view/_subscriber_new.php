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
                    <span class="required">Display Name</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="subscriber_name" name="subscriber_name" maxlength="100" autocomplete="off">
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