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
                <div class="d-flex flex-wrap align-items-center">
                    <div id="change_system_notification_template">
                        <div class="fw-semibold text-gray-600" id="system_notification_template_summary"></div>
                    </div>
                                        
                    <div id="change_system_notification_template_edit" class="flex-row-fluid d-none">
                        <form id="update-system-notification-template-form">
                            <div class="row mb-6">
                                <div class="col-lg-12 mb-4 mb-lg-0">
                                    <div class="fv-row mb-0 fv-plugins-icon-container">
                                        <textarea class="form-control" id="system_notification_template" name="system_notification_template" maxlength="500" rows="3" <?php echo $disabled ?>></textarea>
                                    </div>
                                </div>
                            </div>
                                        
                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                        <button id="update_full_name_submit" form="update-system-notification-template-form" type="submit" class="btn btn-primary me-2 px-6">Update System Notification Template</button>
                                                                        <button id="update_full_name_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_system_notification_template">Cancel</button>
                                                                    </div>' : '';
                            ?>
                        </form>
                    </div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div id="change_system_notification_template_button" class="ms-auto" data-toggle-section="change_system_notification_template">
                                                                <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                            </div>' : '';
                    ?>
                </div>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Email Notification Template</h3>
                </div>
            </div>

            <div class="card-body border-top p-9">
                <div class="d-flex flex-wrap align-items-center">
                    <div id="change_email_notification_template">
                        <div class="fw-semibold text-gray-600" id="email_notification_template_summary"></div>
                    </div>
                                        
                    <div id="change_email_notification_template_edit" class="flex-row-fluid d-none">
                        <form id="update-email-notification-template-form">
                            <div class="row mb-6">
                                <div class="col-lg-12 mb-4 mb-lg-0">
                                    <div class="fv-row mb-0 fv-plugins-icon-container">
                                        <textarea id="email_notification_template_tinymce" name="email_notification_template_tinymce" class="tox-target"></textarea>
                                    </div>
                                </div>
                            </div>
                                        
                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                        <button id="update_full_name_submit" form="update-email-notification-template-form" type="submit" class="btn btn-primary me-2 px-6">Update Email Notification Template</button>
                                                                        <button id="update_full_name_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_email_notification_template">Cancel</button>
                                                                    </div>' : '';
                            ?>
                        </form>
                    </div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div id="change_email_notification_template_button" class="ms-auto" data-toggle-section="change_email_notification_template">
                                                                <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                            </div>' : '';
                    ?>
                </div>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">SMS Notification Template</h3>
                </div>
            </div>

            <div class="card-body border-top p-9">
                <div class="d-flex flex-wrap align-items-center">
                    <div id="change_sms_notification_template">
                        <div class="fw-semibold text-gray-600" id="sms_notification_template_summary"></div>
                    </div>
                                        
                    <div id="change_sms_notification_template_edit" class="flex-row-fluid d-none">
                        <form id="update-sms-notification-template-form">
                            <div class="row mb-6">
                                <div class="col-lg-12 mb-4 mb-lg-0">
                                    <div class="fv-row mb-0 fv-plugins-icon-container">
                                        <textarea class="form-control" id="sms_notification_template" name="sms_notification_template" maxlength="500" rows="3" <?php echo $disabled ?>></textarea>
                                    </div>
                                </div>
                            </div>
                                        
                            <?php
                                echo ($writeAccess['total'] > 0) ? '<div class="d-flex">
                                                                        <button id="update_full_name_submit" form="update-sms-notification-template-form" type="submit" class="btn btn-primary me-2 px-6">Update SMS Notification Template</button>
                                                                        <button id="update_full_name_cancel" type="button" class="btn btn-color-gray-500 btn-active-light-primary  px-6" data-toggle-section="change_sms_notification_template">Cancel</button>
                                                                    </div>' : '';
                            ?>
                        </form>
                    </div>

                    <?php
                        echo ($writeAccess['total'] > 0) ? '<div id="change_sms_notification_template_button" class="ms-auto" data-toggle-section="change_sms_notification_template">
                                                                <button class="btn btn-icon btn-light btn-active-light-primary"><i class="ki-outline ki-pencil fs-3"></i></button>
                                                            </div>' : '';
                    ?>
                </div>
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
                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="system-notification">
                                    <label class="form-check-label" for="system-notification"></label>
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
                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="email-notification">
                                    <label class="form-check-label" for="email-notification"></label>
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
                                    <input class="form-check-input w-45px h-30px" type="checkbox" id="sms-notification">
                                    <label class="form-check-label" for="sms-notification"></label>
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