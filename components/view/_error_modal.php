<div id="system-error-modal" class="modal fade" tabindex="-1" aria-labelledby="system-error-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger text-white">
                <h5 class="modal-title text-white" id="system-error-modalLabel">System Error</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col">
                        <h5>An error occured</h5>
                        <p>Please use the copy button to report the error to your support service.</p>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-danger" id="copy-error-message">Copy the full error to clipboard</button>
                    </div>
                </div>
                <div class="row align-items-center mb-3">
                    <div class="col">
                        <div class="alert alert-danger text-danger" role="alert" id="error-dialog"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>