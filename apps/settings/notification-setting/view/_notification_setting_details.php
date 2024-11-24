<div class="d-flex flex-column flex-lg-row">
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 mb-7 me-lg-10">
        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Notification Setting Details</h3>
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
                                            <a href="javascript:void(0);" class="menu-link px-3" id="delete-notification-setting">
                                                Delete
                                            </a>
                                        </div>';
                        }
                    
                        $action .= '</div>';
                    
                        echo $action;
                    }
                ?>
            </div>

            <form id="notification-setting-form" class="form" method="post" action="#">
                <div class="card-body border-top p-9">
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="notification_setting_name">
                            <span class="required">Display Name</span>
                        </label>

                        <input type="text" class="form-control" id="notification_setting_name" name="notification_setting_name" maxlength="100" autocomplete="off">
                    </div>
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="notification_setting_description">
                            <span class="required">Description</span>
                        </label>

                        <input type="text" class="form-control" id="notification_setting_description" name="notification_setting_description" maxlength="200" autocomplete="off">
                    </div>
                </div>

                <?php
                    echo ($writeAccess['total'] > 0) ? '<div class="card-footer d-flex justify-content-end py-6 px-9">
                                                            <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                            <button type="submit" form="notification-setting-form" class="btn btn-primary" id="submit-data">Save Changes</button>
                                                        </div>' : '';
                ?>
            </form>
        </div>

        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">System Notification Template</h3>
                </div>
            </div>

            <div class="card-body border-top p-9">
                <form id="update-system-notification-template-form">
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="system_notification_title">
                            <span class="required">Title</span>
                        </label>

                        <input type="text" class="form-control" id="system_notification_title" name="system_notification_title" maxlength="200" autocomplete="off" <?php echo $disabled; ?>>
                    </div>
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="system_notification_message">
                            <span class="required">Message</span>
                        </label>

                        <textarea class="form-control" id="system_notification_message" name="system_notification_message" maxlength="500" rows="3" <?php echo $disabled; ?>></textarea>
                    </div>
                                        
                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                <button id="submit-system-notification-template" form="update-system-notification-template-form" type="submit" class="btn btn-primary me-2 px-6">Update System Notification Template</button>
                                                            </div>' : '';
                    ?>
                </form>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Email Notification Template</h3>
                </div>
            </div>

            <div class="card-body border-top p-9">
                <form id="update-email-notification-template-form">
                    <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                        <div class="col">
                            <div class="fv-row mb-4">
                                <label class="fs-6 fw-semibold form-label mt-3" for="email_notification_subject">
                                    <span class="required">Subject</span>
                                </label>

                                <input type="text" class="form-control" id="email_notification_subject" name="email_notification_subject" maxlength="200" autocomplete="off" <?php echo $disabled; ?>>
                            </div>

                        </div>

                        <div class="col">
                            <div class="fv-row mb-4">
                                <label class="fs-6 fw-semibold form-label mt-3" for="email_setting_id">
                                    <span class="required">Email Sender</span>
                                </label>

                                <select id="email_setting_id" name="email_setting_id" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled; ?>></select>
                            </div>
                        </div>
                    </div>
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="email_notification_body">
                            <span class="required">Body</span>
                        </label>

                        <textarea id="email_notification_body" class="tox-target" <?php echo $disabled; ?>></textarea>
                    </div>
                                        
                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                <button id="submit-email-notification-template" form="update-email-notification-template-form" type="submit" class="btn btn-primary me-2 px-6">Update Email Notification Template</button>
                                                            </div>' : '';
                    ?>
                </form>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">SMS Notification Template</h3>
                </div>
            </div>

            <div class="card-body border-top p-9">
                <form id="update-sms-notification-template-form">
                    <div class="fv-row mb-4">
                        <label class="fs-6 fw-semibold form-label mt-3" for="sms_notification_message">
                            <span class="required">Message</span>
                        </label>

                        <textarea class="form-control" id="sms_notification_message" name="sms_notification_message" maxlength="500" rows="3" <?php echo $disabled; ?>></textarea>
                    </div>
                                        
                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                <button id="submit-sms-notification-template" form="update-sms-notification-template-form" type="submit" class="btn btn-primary me-2 px-6">Update SMS Notification Template</button>
                                                            </div>' : '';
                    ?>
                </form>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-500px">
        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Notification Channel</h3>
                </div>
            </div>
            <div>
                <div class="card-body border-top p-9">
                    <div class="py-2">
                        <div class="d-flex flex-stack">
                            <div class="d-flex">
                                <div class="d-flex flex-column">
                                    <label class="fs-5 text-gray-900 fw-bold" for="system-notification">System Notification</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-check-custom form-switch">
                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="system-notification" <?php echo $disabled; ?>>
                                </div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-5"></div>

                        <div class="d-flex flex-stack">
                            <div class="d-flex">
                                <div class="d-flex flex-column">
                                    <label class="fs-5 text-gray-900 fw-bold" for="email-notification">Email Notification</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-check-custom form-switch">
                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="email-notification" <?php echo $disabled; ?>>
                                </div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-5"></div>

                        <div class="d-flex flex-stack">
                            <div class="d-flex">
                                <div class="d-flex flex-column">
                                    <label class="fs-5 text-gray-900 fw-bold" for="sms-notification">SMS Notification</label>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <div class="form-check form-check-solid form-check-custom form-switch">
                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="sms-notification" <?php echo $disabled; ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>