<div class="card mb-10">
    <div class="card-header border-0">
        <div class="card-title m-0">
            <h3 class="fw-bold m-0">Email Setting Details</h3>
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
                                    <a href="javascript:void(0);" class="menu-link px-3" id="delete-email-setting">
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
        <form id="email-setting-form" method="post" action="#">
            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="email_setting_name">
                            <span class="required">Display Name</span>
                        </label>

                        <input type="text" class="form-control" id="email_setting_name" name="email_setting_name" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="menu_item_id">
                            <span class="required">Description</span>
                        </label>

                        <input type="text" class="form-control" id="email_setting_description" name="email_setting_description" maxlength="200" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="mail_host">
                            <span class="required">Host</span>
                        </label>
                        
                        <input type="text" class="form-control" id="mail_host" name="mail_host" maxlength="100" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="port">
                            <span class="required">Port</span>
                        </label>
                        
                        <input type="text" class="form-control" id="port" name="port" maxlength="10" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="mail_username">
                            <span class="required">Email Username</span>
                        </label>
                        
                        <input type="text" class="form-control" id="mail_username" name="mail_username" maxlength="200" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="mail_password">
                            <span class="required">Email Password</span>
                        </label>
                        
                        <div class="input-group">
                            <input type="password" class="form-control" name="mail_password" id="mail_password" <?php echo $disabled ?>>
                            <button class="btn btn-light bg-transparent password-addon" type="button">
                                <i class="ki-outline ki-eye-slash fs-2 p-0"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="mail_from_name">
                            <span class="required">Mail From Name</span>
                        </label>
                        
                        <input type="text" class="form-control" id="mail_from_name" name="mail_from_name" maxlength="200" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="mail_from_email">
                            <span class="required">Mail From Email</span>
                        </label>
                        
                        <input type="text" class="form-control" id="mail_from_email" name="mail_from_email" maxlength="200" autocomplete="off" <?php echo $disabled ?>>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="mail_encryption">
                            <span class="required">Mail Encryption</span>
                        </label>
                        
                        <select id="mail_encryption" name="mail_encryption" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>>
                            <option value="none">None</option>
                            <option value="ssl">SSL</option>
                            <option value="starttls">Start TLS</option>
                            <option value="tls">TLS</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="smtp_auth">
                            <span class="required">SMTP Authentication</span>
                        </label>
                        
                        <select id="smtp_auth" name="smtp_auth" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 rol-cols-md-1 row-cols-lg-2">
                <div class="col">
                    <div class="fv-row mb-7">
                        <label class="fs-6 fw-semibold form-label mt-3" for="smtp_auto_tls">
                            <span class="required">SMTP Auto TLS</span>
                        </label>
                        
                        <select id="smtp_auto_tls" name="smtp_auto_tls" class="form-select" data-control="select2" data-allow-clear="false" <?php echo $disabled ?>>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php
        echo ($writeAccess['total'] > 0) ? ' <div class="card-footer d-flex justify-content-end py-6 px-9">
                                                <button type="button" id="discard-create" class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                                <button type="submit" form="email-setting-form" class="btn btn-primary" id="submit-data">Save</button>
                                            </div>' : '';
    ?>
</div>

<?php require_once('components/view/_log_notes_modal.php'); ?>