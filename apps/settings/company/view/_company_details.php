
<div class="d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        <div class="card card-flush">
            <div class="card-body text-center">
                <div class="image-input image-input-outline" data-kt-image-input="true">
                    <div class="image-input-wrapper w-125px h-125px" id="compay_logo_thumbnail" style="background-image: url(./assets/images/default/default-company-logo.png); background-size: cover; background-repeat: no-repeat; background-position: center;"></div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" aria-label="Change logo" data-bs-original-title="Change logo" data-kt-initialized="1">
                                                                <i class="ki-outline ki-pencil fs-7"></i>
                                                                <input type="file" id="company_logo" name="company_logo" accept=".png, .jpg, .jpeg">
                                                            </label>' : '';
                    ?>
                </div>
                        
                <div class="form-text mt-5">Set the company logo image. Only *.png, *.jpg and *.jpeg image files are accepted.</div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Company Details</h3>
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-company">
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
                <form id="company-form" class="form" method="post" action="#">
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="company_name">
                            <span class="required">Display Name</span>
                        </label>

                        <input type="text" class="form-control" id="company_name" name="company_name" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                    
                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="address">
                                    <span class="required">Address</span>
                                </label>

                                <input type="text" class="form-control" id="address" name="address" maxlength="1000" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>

                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="city_id">
                                    <span class="required">City</span>
                                </label>

                                <select id="city_id" name="city_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>></select>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="tax_id">
                                    Tax Identification Number
                                </label>

                                <input type="text" class="form-control" id="tax_id" name="tax_id" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>

                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="currency_id">
                                Currency
                                </label>

                                <select id="currency_id" name="currency_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>></select>
                            </div>
                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="phone">
                                    Phone
                                </label>

                                <input type="text" class="form-control" id="phone" name="phone" maxlength="20" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>
                        
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="telephone">
                                    Telephone
                                </label>

                                <input type="text" class="form-control" id="telephone" name="telephone" maxlength="20" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>
                    </div>

                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="email">
                                    Email
                                </label>

                                <input type="email" class="form-control" id="email" name="email" maxlength="250" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>

                        <div class="col">
                            <div class="fv-row mb-7">
                                <label class="fs-6 fw-semibold form-label mt-3" for="website">
                                    Website
                                </label>

                                <input type="text" class="form-control" id="website" name="website" maxlength="250" autocomplete="off" <?php echo $disabled ?>>
                            </div>

                        </div>
                    </div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                                <button type="submit" form="company-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                            </div>' : '';
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>