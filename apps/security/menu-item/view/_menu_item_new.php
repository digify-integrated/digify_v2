<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">App Module Form</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            
        </div>
    </div>
    <div class="card-body">
        <form id="app-module-form" method="post" action="#">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="fs-6 fw-semibold form-label" for="app_module_name"><span class="required">Display Name</span></label>
                        <input type="text" class="form-control form-control-solid maxlength" id="app_module_name" name="app_module_name" maxlength="100" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="fs-6 fw-semibold form-label" for="app_module_description"><span class="required">Description</span></label>
                        <textarea class="form-control form-control-solid maxlength" id="app_module_description" name="app_module_description" maxlength="500" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="fs-6 fw-semibold form-label" for="menu_item_id"><span class="required">Default Page</span></label>
                    <div class="mb-3">
                        <select id="menu_item_id" name="menu_item_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="true"></select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fs-6 fw-semibold form-label" for="order_sequence"><span class="required">Order Sequence</span></label>
                        <input type="number" class="form-control form-control-solid" id="order_sequence" name="order_sequence" min="0">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer d-flex justify-content-end py-6 px-9">
        <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
        <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save</button>
    </div>
</div>