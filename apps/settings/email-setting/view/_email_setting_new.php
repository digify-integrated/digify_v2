<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Email Setting Form</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="submit" form="email-setting-form" class="btn btn-success mb-0" id="submit-data">Save</button>
                    <button type="button" id="discard-create" class="btn btn-outline-danger mb-0">Discard</button>
                </div>
            </div>
            <div class="card-body">
                <form id="email-setting-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="email_setting_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="email_setting_name" name="email_setting_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="email_setting_description">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="email_setting_description" name="email_setting_description" maxlength="200" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="mail_host">Host <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="mail_host" name="mail_host" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="port">Port <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="port" name="port" maxlength="10" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="mail_username">Email Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="mail_username" name="mail_username" maxlength="200" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="mail_password">Email Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="mail_password" name="mail_password">
                                    <button class="btn btn-dark rounded-end d-flex align-items-center password-addon" type="button">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="mail_from_name">Mail From Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="mail_from_name" name="mail_from_name" maxlength="200" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="mail_from_email">Mail From Email <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="mail_from_email" name="mail_from_email" maxlength="200" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="mail_encryption">Mail Encryption <span class="text-danger">*</span></label>
                                <select class="form-control" id="mail_encryption" name="mail_encryption">
                                    <option value="none">None</option>
                                    <option value="ssl">SSL</option>
                                    <option value="starttls">Start TLS</option>
                                    <option value="tls">TLS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="smtp_auth">SMTP Authentication <span class="text-danger">*</span></label>
                                <select class="form-control" id="smtp_auth" name="smtp_auth">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="smtp_auto_tls">SMTP Auto TLS <span class="text-danger">*</span></label>
                                <select class="form-control" id="smtp_auto_tls" name="smtp_auto_tls">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>