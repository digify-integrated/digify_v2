<div class="card mb-5 mb-xl-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">App Module Details</h3>
        </div>
    </div>

    <div>
        <form id="app-module-form" class="form" method="post" action="#">
            <div class="card-body border-top p-9" data-select2-id="select2-data-124-vlzc">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6">Logo</label>   
                    
                    <div class="col-lg-8">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-125px h-125px" style="background-image: url(/metronic8/demo34/assets/media/avatars/300-1.jpg)"></div>

                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change logo" data-bs-original-title="Change logo" data-kt-initialized="1">
                                <i class="ki-outline ki-pencil fs-7"></i>
                                <input type="file" name="avatar" accept=".png, .jpg, .jpeg">
                                <input type="hidden" name="avatar_remove">
                            </label>

                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" aria-label="Cancel avatar" data-bs-original-title="Cancel avatar" data-kt-initialized="1">
                                <i class="ki-outline ki-cross fs-2"></i>
                            </span>
                        </div>
                        
                        <div class="form-text">Set the app module image. Only *.png, *.jpg and *.jpeg image files are accepted.</div>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6" for="app_module_name"><span class="required">Display Name</span></label>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                <input type="text" class="form-control form-control-solid maxlength" id="app_module_name" name="app_module_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6" for="app_module_description"><span class="required">Description</span></label>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                <textarea class="form-control form-control-solid maxlength" id="app_module_description" name="app_module_description" maxlength="500" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6" for="menu_item_id"><span class="required">Default Page</span></label>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                <select id="menu_item_id" name="menu_item_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="true"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-semibold fs-6" for="order_sequence"><span class="required">Order Sequence</span></label>

                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                <input type="number" class="form-control form-control-solid" id="order_sequence" name="order_sequence" min="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>
<?php require_once('components/view/_internal_notes.php'); ?>