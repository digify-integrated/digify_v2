<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">App Module Details</h5>
    </div>
    <div class="card-body">
        <form id="app-module-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="app_module_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="app_module_name" name="app_module_name" maxlength="100" autocomplete="off">
            </div>
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="app_module_description">
                    <span class="required">Description</span>
                </label>

                <textarea class="form-control" id="app_module_description" name="app_module_description" maxlength="500" rows="3"></textarea>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="menu_item_id">
                            <span class="required">Default Page</span>
                        </label>

                        <select id="menu_item_id" name="menu_item_id" class="form-select" data-control="select2" data-allow-clear="false"></select>
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="order_sequence">
                            <span class="required">Order Sequence</span>
                        </label>

                        <input type="number" class="form-control" id="order_sequence" name="order_sequence" min="0">
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