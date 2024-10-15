<div class="card card-body">
    <div class="row">
        <?php require_once('components/view/_datatable_search.php'); ?>
        <div class="col-md-3 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
            <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                <?php
                    if ($deleteAccess['total'] > 0 || $exportAccess['total'] > 0) {
                        $action = '<button type="button" class="btn btn-dark dropdown-toggle action-dropdown mb-0 d-none" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">';
                    
                        if ($exportAccess['total'] > 0) {
                            $action .= '<li><button class="dropdown-item" type="button" data-bs-toggle="modal" id="export-data" data-bs-target="#export-modal">Export</button></li>';
                        }
                    
                        if ($deleteAccess['total'] > 0) {
                            $action .= '<li><button class="dropdown-item" type="button" id="delete-app-module">Delete</button></li>';
                        }
                    
                        $action .= '</ul>';
                    
                        echo $action;
                    }
                ?>
            </div>
        </div>
        <div class="col-md-4 text-end d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
            <div class="card-actions cursor-pointer ms-auto d-flex button-group">
                <a class="btn btn-warning mb-0 px-4" data-bs-toggle="modal" data-bs-target="#filter-modal"><i class="ti ti-filter me-1 fs-3"></i>Filter</a>
                <?php
                    echo $importAccess['total'] > 0 ? '<a href="' . $pageLink . '&import='. $securityModel->encryptData('menu_item') .'" class="btn btn-secondary d-flex align-items-center mb-0"><i class="ti ti-download me-1 fs-4"></i>Import</a>' : '';
                    echo $createAccess['total'] > 0 ? '<a href="' . $pageLink . '&new" class="btn btn-primary d-flex align-items-center mb-0 me-0"><i class="ti ti-circle-plus me-1 fs-4"></i>New</a>' : '';
                ?>
            </div>
        </div>
    </div>
</div>

<div class="datatables">
    <div class="row">
        <div class="col-12">
            <div class="card mb-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="menu-item-table" class="table w-100 table-hover display text-nowrap align-middle dataTable">
                            <thead class="text-dark">
                                <tr>
                                    <th class="all">
                                        <div class="form-check">
                                            <input class="form-check-input" id="datatable-checkbox" type="checkbox">
                                        </div>
                                    </th>
                                    <th>Menu Item</th>
                                    <th>Menu Group</th>
                                    <th>App Module</th>
                                    <th>Parent Menu Item</th>
                                    <th>Order Sequence</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="filter-modal" class="modal fade" tabindex="-1" aria-labelledby="filter-modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-r">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-8">Filter</h5>
                <button type="button" class="btn-close fs-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label" for="menu_item_name">By App Module</label>
                            <div class="mb-3">
                                <select id="app_module_filter" name="app_module_filter" multiple="multiple" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label" for="menu_item_name">By Menu Group</label>
                            <div class="mb-3">
                                <select id="menu_group_filter" name="menu_group_filter" multiple="multiple" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label" for="menu_item_name">By Parent Menu</label>
                            <div class="mb-3">
                                <select id="parent_id_filter" name="parent_id_filter" multiple="multiple" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" id="apply-filter">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

<?php
    $exportAccess['total'] > 0 ? require('./components/view/_export_modal.php') : '';
?>