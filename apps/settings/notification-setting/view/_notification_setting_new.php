<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0">Notification Setting Form</h5>
                <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                    <button type="submit" form="notification-setting-form" class="btn btn-success mb-0" id="submit-data">Save</button>
                    <button type="button" id="discard-create" class="btn btn-outline-danger mb-0">Discard</button>
                </div>
            </div>
            <div class="card-body">
                <form id="notification-setting-form" method="post" action="#">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="notification_setting_name">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="notification_setting_name" name="notification_setting_name" maxlength="100" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label" for="notification_setting_description">Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control maxlength" id="notification_setting_description" name="notification_setting_description" maxlength="200" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>