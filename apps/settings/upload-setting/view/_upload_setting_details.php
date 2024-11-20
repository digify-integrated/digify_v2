
<div class="d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 mb-7 me-lg-10">
        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Upload Setting Details</h3>
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-upload-setting">
                                                Delete
                                            </a>
                                        </div>';
                        }
                    
                        $action .= '</div>';
                    
                        echo $action;
                    }
                ?>
            </div>

            <form id="upload-setting-form" class="form" method="post" action="#">
                <div class="card-body border-top p-9">
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="upload_setting_name">
                            <span class="required">Display Name</span>
                        </label>

                        <input type="text" class="form-control" id="upload_setting_name" name="upload_setting_name" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="menu_item_id">
                                    <span class="required">Description</span>
                                </label>

                                <input type="text" class="form-control" id="upload_setting_description" name="upload_setting_description" maxlength="200" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>

                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="max_file_size">
                                    <span class="required">Max File Size</span>
                                </label>
                                
                                <div class="input-group mb-5">
                                    <input type="number" class="form-control" id="max_file_size" name="max_file_size" min="1" step="1" <?php echo $disabled ?>>
                                    <span class="input-group-text">kb</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                            <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                            <button type="submit" form="upload-setting-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                        </div>' : '';
                ?>
            </form>
        </div>
    </div>
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-500px">
        <div class="card card-flush py-4">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Allowed File Extensions</h3>
                </div>
            </div>

            <div class="card-body border-top">
                <select id="allowed_file_extension" name="allowed_file_extension" multiple="multiple" class="form-select form-select-solid" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>></select>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>