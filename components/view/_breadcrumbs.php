<div id="kt_app_toolbar" class="app-toolbar py-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex align-items-start">
        <div class="d-flex flex-column flex-row-fluid">
            <div class="d-flex align-items-center pt-1">
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold">
                    <li class="breadcrumb-item text-white fw-bold lh-1">
                        <a href="apps.php" class="text-white text-hover-primary">
                            <i class="ki-outline ki-abstract-26 text-gray-700 fs-6"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i>
                    </li>
                    <li class="breadcrumb-item text-white fw-bold lh-1">
                        <a class="text-decoration-none text-white fw-bold fs-7" href="<?php echo $pageLink; ?>" id="page-link">
                            <?php echo $pageTitle; ?>
                        </a>
                    </li>
                    <?php
                        if(!$newRecord && !empty($detailID)){
                            echo '<li class="breadcrumb-item">
                                    <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i>
                                </li>
                                <li class="breadcrumb-item text-white fw-bold lh-1 fs-7" id="details-id">'. $detailID .'</li>';
                        }

                        if($newRecord){
                            echo '<li class="breadcrumb-item">
                                     <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i>
                                </li>
                                <li class="breadcrumb-item text-white fw-bold lh-1 text-white fw-bold fs-7">New</li>';
                        }

                        if($importRecord){
                            echo '<li class="breadcrumb-item">
                                     <i class="ki-outline ki-right fs-7 text-gray-700 mx-n1"></i>
                                </li>
                                <li class="breadcrumb-item text-white fw-bold lh-1 text-white fw-bold fs-7">Import</li>';
                        }
                    ?>
                </ul>
            </div>
           <div class="d-flex flex-stack flex-wrap flex-lg-nowrap gap-4 gap-lg-10 pt-13 pb-6 mb-lg-0 mb-8">
                <div class="page-title me-5">
                    <h1 class="page-heading d-flex text-white fw-bold fs-2 flex-column justify-content-center my-0">
                        <?php echo $pageTitle; ?>
                    </h1>
                </div>
                <div class="d-flex align-self-center flex-center flex-shrink-1">
                    <?php
                        echo $createAccess['total'] > 0 && !isset($_GET['new']) ? '<a href="' . $pageLink . '&new" class="btn btn-flex btn-sm btn-outline btn-active-color-primary btn-custom px-4"><i class="ki-outline ki-plus-square fs-4 me-2"></i> New</a>' : '';

                        echo $importAccess['total'] > 0 && !isset($_GET['import']) ? '<a href="' . $pageLink . '&import='. $securityModel->encryptData($tableName) .'" class="btn btn-flex btn-sm btn-outline btn-active-color-primary btn-custom ms-3 px-4"><i class="ki-outline ki-exit-down fs-4 me-2"></i> Import</a>' : '';

                        echo $logNotesAccess['total'] > 0 && isset($_GET['id']) ? '<button id="log-notes-main" class="btn btn-flex btn-sm btn-outline btn-active-color-primary btn-custom ms-3 px-4" data-bs-toggle="modal" data-bs-target="#log-notes-modal"><i class="ki-outline ki-shield-search fs-4 me-2"></i> Log Notes</button>' : '';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="page-id" value="<?php echo $pageID; ?>">