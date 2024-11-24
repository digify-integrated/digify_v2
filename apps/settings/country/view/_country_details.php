<div class="card mb-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Country Details</h3>
        </div>
        <?php
            if ($deleteAccess['total'] > 0) {
                $action = '<a href="#" class="btn btn-light-primary btn-flex btn-center btn-active-light-primary show menu-dropdown align-self-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        Actions
                                        <i class="ki-outline ki-down fs-5 ms-1"></i>
                                    </a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true" style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate(-60px, 539px);" data-popper-placement="bottom-end">';
                    
                if ($deleteAccess['total'] > 0) {
                    $action .= '<div class="menu-item px-3">
                                    <a href="javascript:void(0);" class="menu-link px-3" id="delete-country">
                                        Delete
                                    </a>
                                </div>';
                }
                        
                $action .= '</div>';
                        
                echo $action;
            }
        ?>
    </div>
    <div class="card-body">
        <form id="country-form" method="post" action="#">
            <div class="fv-row mb-4">
                <label class="fs-6 fw-semibold form-label mt-3" for="country_name">
                    <span class="required">Display Name</span>
                </label>

                <input type="text" class="form-control" id="country_name" name="country_name" maxlength="100" autocomplete="off" <?php echo $disabled; ?>>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="country_code">
                            <span class="required">Country Code</span>
                        </label>

                        <input type="text" class="form-control" id="country_code" name="country_code" maxlength="10" autocomplete="off" <?php echo $disabled; ?>>
                    </div>

                </div>

                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="phone_code">
                            <span class="required">Phone Code</span>
                        </label>

                        <input type="text" class="form-control" id="phone_code" name="phone_code" maxlength="10" autocomplete="off" <?php echo $disabled; ?>>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
        echo ($writeAccess['total'] > 0) ? ' <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" form="country-form" class="btn btn-primary" id="submit-data">Save</button>
                                            </div>' : '';
    ?>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>