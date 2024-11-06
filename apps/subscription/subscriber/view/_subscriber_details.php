<?php
    $addSubscription = $authenticationModel->checkSystemActionAccessRights($userID, 15);
?>
<div class="card mb-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Subscriber Details</h3>
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-subscriber">
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
        <form id="subscriber-form" method="post" action="#">
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="subscriber_name">
                    <span class="required">Subscriber Name</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="subscriber_name" name="subscriber_name" maxlength="500" autocomplete="off" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="company_name">
                    <span class="required">Company</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="company_name" name="company_name" maxlength="200" autocomplete="off" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="phone">
                    <span class="required">Phone</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="text" class="form-control form-control-solid maxlength" id="phone" name="phone" maxlength="50" autocomplete="off" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="email">
                    <span class="required">Email</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <input type="email" class="form-control form-control-solid maxlength" id="email" name="email" maxlength="50" autocomplete="off" <?php echo $disabled ?>>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="subscription_tier_id">
                    <span class="required">Subscription Tier</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <select id="subscription_tier_id" name="subscription_tier_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="billing_cycle_id">
                    <span class="required">Billing Cycle</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <select id="billing_cycle_id" name="billing_cycle_id" class="form-select form-select-solid" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6" for="subscriber_status">
                    <span class="required">Status</span>
                </label>

                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <select id="subscriber_status" name="subscriber_status" class="form-select form-select-solid" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php
        echo ($writeAccess['total'] > 0) ? ' <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" form="subscriber-form" class="btn btn-primary" id="submit-data">Save</button>
                                            </div>' : '';
    ?>
</div>

<div class="card">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1 me-3">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i> <input type="text" class="form-control form-control-solid w-250px ps-12" id="subscription-datatable-search" placeholder="Search..." autocomplete="off" />
            </div>
            <select id="subscription-datatable-length" class="form-select form-select-solid w-auto">
                <option value="-1">All</option>
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                <?php
                    echo $addSubscription['total'] > 0 ? '<button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#subscription-modal" id="add-subscription"><i class="ki-outline ki-plus fs-2"></i> Add Subscription</button>' : '';
                ?>
            </div>
        </div>
    </div>
    <div class="card-body pt-9">
        <table class="table align-middle cursor-pointer table-row-dashed fs-6 gy-5 gs-7" id="subscription-table">
            <thead>
                <tr class="fw-semibold fs-6 text-gray-800">
                    <th>Subscription Date</th>
                    <th>Deactivation Date</th>
                    <th>Number of Users</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="fw-semibold text-gray-600"></tbody>
        </table>
    </div>
</div>

<div id="subscription-modal" class="modal fade" tabindex="-1" aria-labelledby="subscription-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Subscription</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <form id="subscription-form" method="post" action="#">
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="subscription_date">
                            <span class="required">Subscription Date</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                <input type="text" class="form-control form-control-solid daterange-picker" id="subscription_date" name="subscription_date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="deactivation_date">
                            <span class="required">Deactivation Date</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                <input type="text" class="form-control form-control-solid single-date-picker" id="deactivation_date" name="deactivation_date" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="no_users">
                            <span class="required">Number of Users</span>
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                    <input type="number" class="form-control form-control-solid" id="no_users" name="no_users" min="-1" step="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-6">
                        <label class="col-lg-4 col-form-label fw-semibold fs-6" for="remarks">
                            Remarks
                        </label>

                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                    <textarea class="form-control form-control-solid maxlength" id="remarks" name="remarks" maxlength="1000" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="subscription-form" class="btn btn-primary" id="submit-subsription">Submit</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>