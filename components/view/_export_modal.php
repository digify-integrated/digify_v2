<div id="export-modal" class="modal fade" tabindex="-1" aria-labelledby="export-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modal title</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12 mb-2">
                        <label class="form-label" for="export_to">Export To <span class="text-danger">*</span></label>
                        <select class="form-control" id="export_to" name="export_to">
                            <option value="csv">CSV</option>
                            <option value="xlsx">Excel</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <select multiple="multiple" size="20" id="table_column" name="table_column[]"></select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit-export">Export</button>
            </div>
        </div>
    </div>
</div>