<div id="export-modal" class="modal fade" tabindex="-1" aria-labelledby="export-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Export Data</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
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
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="button" form="export-form" class="btn btn-success" id="submit-export">Export</button>
            </div>
        </div>
    </div>
</div>