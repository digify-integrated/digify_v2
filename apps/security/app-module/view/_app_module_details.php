
<div class="d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <div class="card card-flush">
            <div class="card-body text-center">
                <div class="image-input image-input-outline" data-kt-image-input="true">
                    <div class="image-input-wrapper w-125px h-125px" id="app_thumbnail" style="background-image: url(./assets/images/default/app-module-logo.png)"></div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change logo" data-bs-original-title="Change logo" data-kt-initialized="1">
                                                                <i class="ki-outline ki-pencil fs-7"></i>
                                                                <input type="file" id="app_logo" name="app_logo" accept=".png, .jpg, .jpeg">
                                                            </label>' : '';
                    ?>
                </div>
                        
                <div class="form-text mt-5">Set the app module image. Only *.png, *.jpg and *.jpeg image files are accepted.</div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">App Module Details</h3>
                </div>
                <?php
                    if ($deleteAccess['total'] > 0 || $exportAccess['total'] > 0) {
                        $action = '<a href="#" class="btn btn-light-primary btn-flex btn-center btn-active-light-primary show menu-dropdown align-self-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true" style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate(-60px, 539px);" data-popper-placement="bottom-end">';
                    
                        if ($deleteAccess['total'] > 0) {
                            $action .= '<div class="menu-item px-3">
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-app-module">
                                                Delete
                                            </a>
                                        </div>';
                        }
                    
                        $action .= '</div>';
                    
                        echo $action;
                    }
                ?>
            </div>

            <form id="app-module-form" class="form" method="post" action="#">
                <div class="card-body border-top p-9" data-select2-id="select2-data-124-vlzc">
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="app_module_name">
                            <span class="required">Display Name</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="text" class="form-control form-control-solid maxlength" id="app_module_name" name="app_module_name" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="app_module_description">
                            <span class="required">Description</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <textarea class="form-control form-control-solid maxlength" id="app_module_description" name="app_module_description" maxlength="500" rows="3" <?php echo $disabled ?>></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="menu_item_id">
                            <span class="required">Default Page</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <select id="menu_item_id" name="menu_item_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>></select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="order_sequence">
                            <span class="required">Order Sequence</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12 fv-row fv-plugins-icon-container">
                                    <input type="number" class="form-control form-control-solid" id="order_sequence" name="order_sequence" min="0" <?php echo $disabled ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                            <button type="submit" form="app-module-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                        </div>' : '';
                ?>
            </form>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>