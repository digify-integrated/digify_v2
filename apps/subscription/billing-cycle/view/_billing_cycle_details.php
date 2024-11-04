<div class="card mb-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Billing Cycle Details</h3>
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-billing-cycle">
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
        <form id="billing-cycle-form" method="post" action="#">
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="billing_cycle_name">
                    <span class="required">Display Name</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="billing_cycle_name" name="billing_cycle_name" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-0">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="billing_cycle_description">
                    <span class="required">Description</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <textarea class="form-control form-control-solid maxlength" id="billing_cycle_description" name="billing_cycle_description" maxlength="200" rows="3" <?php echo $disabled ?>></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-0">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="order_sequence">
                    <span class="required">Order Sequence</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="number" class="form-control form-control-solid" id="order_sequence" name="order_sequence" min="0" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
        echo ($writeAccess['total'] > 0) ? ' <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" form="billing-cycle-form" class="btn btn-primary" id="submit-data">Save</button>
                                            </div>' : '';
    ?>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>