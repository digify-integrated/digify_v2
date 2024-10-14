<div class="form-with-tabs">
    <div class="card mb-0">
        <ul class="nav nav-pills user-profile-tab border-bottom" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link position-relative rounded-0 active d-flex align-items-center justify-content-center bg-transparent fs-3 py-6 fw-bold" id="pills-internal-notes-tab" data-bs-toggle="pill" data-bs-target="#pills-internal-notes" type="button" role="tab" aria-controls="pills-internal-notes" aria-selected="true">Internal Notes</button>
            </li>
            <?php
                echo $logNotesAccess['total'] > 0 ? '<li class="nav-item" role="presentation">
                                                    <button class="nav-link position-relative rounded-0 d-flex align-items-center justify-content-center bg-transparent fs-3 py-6 fw-bold" id="pills-log-notes-tab" data-bs-toggle="pill" data-bs-target="#pills-log-notes" type="button" role="tab" aria-controls="pills-log-notes" aria-selected="false" tabindex="-1">Log Notes</button>
                                                </li>' : '';
            ?>
        </ul>
        <div class="card-body">
            <div class="tab-content" id="logs-tabContent">
                <div class="tab-pane fade show active" id="pills-internal-notes" role="tabpanel" aria-labelledby="pills-internal-notes-tab" tabindex="0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-floating">
                                <form id="internal-notes-form" method="post" action="#">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <input type="file" class="form-control d-none" id="internal_notes_files" name="internal_notes_files[]" multiple>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-floating mb-3">
                                                <textarea class="form-control maxlength" id="internal_note" name="internal_note" placeholder="Internal notes" rows="10" maxlength="5000"></textarea>
                                                <label for="internal_note">Internal Notes</label>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="d-flex align-items-center gap-6 flex-wrap">
                                <label class="d-flex nav-icon-hover-bg rounded cursor-pointer" for="internal_notes_files" data-bs-toggle="tooltip" data-bs-placement="top" title="Attach files">
                                    <iconify-icon icon="solar:paperclip-outline" class="fs-7"></iconify-icon>
                                </label>
                                <label class="d-flex nav-icon-hover-bg rounded cursor-pointer" for="internal_notes_files" data-bs-toggle="tooltip" data-bs-placement="top" title="Insert photo">
                                    <iconify-icon icon="solar:gallery-add-linear" class="fs-7"></iconify-icon>
                                </label>
                                <button id="submit-internal-notes" type="submit" form="internal-notes-form" class="btn btn-success ms-auto">Post</button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-lg-12">
                            <div class="position-relative" style="max-height: 500px; overflow: auto;" id="internal-notes"></div>
                        </div>
                    </div>
                </div>
                <?php
                    echo $logNotesAccess['total'] > 0 ? '<div class="tab-pane fade" id="pills-log-notes" role="tabpanel" aria-labelledby="pills-log-notes-tab" tabindex="0">
                                                        <div class="row">
                                                            <div class="position-relative" style="max-height: 500px; overflow: auto;" id="log-notes-main"></div>
                                                        </div>
                                                    </div>' : '';
                ?>
            </div>
        </div>
    </div>
</div>