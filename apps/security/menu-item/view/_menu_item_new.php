<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="card-title mb-0">Menu Item Form</h5>
        <div class="card-actions cursor-pointer ms-auto d-flex button-group">
            <button type="submit" form="menu-item-form" class="btn btn-success mb-0" id="submit-data">Save</button>
            <button type="button" id="discard-create" class="btn btn-outline-danger mb-0">Discard</button>
        </div>
    </div>
    <div class="card-body">
        <form id="menu-item-form" method="post" action="#">
            <div class="row">
                 <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label" for="menu_item_name">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control maxlength" id="menu_item_name" name="menu_item_name" maxlength="100" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="menu_group_id">Menu Group <span class="text-danger">*</span></label>
                    <div class="mb-3">
                        <select id="menu_group_id" name="menu_group_id" class="select2 form-control"></select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label" for="order_sequence">Order Sequence <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="order_sequence" name="order_sequence" min="0">
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label" for="parent_id">Parent Menu Item</label>
                    <div class="mb-3">
                        <select id="parent_id" name="parent_id" class="select2 form-control"></select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label" for="menu_item_icon">Icon</label>
                        <input type="text" class="form-control maxlength" id="menu_item_icon" name="menu_item_icon" maxlength="50" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label class="form-label" for="menu_item_url">URL</label>
                        <input type="text" class="form-control maxlength" id="menu_item_url" name="menu_item_url" maxlength="50" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>