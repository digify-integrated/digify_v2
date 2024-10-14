<div class="card card-body py-3">
    <div class="row align-items-center">
        <div class="col-12">
            <div class="d-sm-flex align-items-center justify-space-between">
                <h4 class="mb-4 mb-sm-0 card-title"><?php echo $pageTitle; ?></h4>
                <nav aria-label="breadcrumb" class="ms-auto">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item d-flex align-items-center">
                            <a class="text-muted text-decoration-none d-flex" href="apps.php">
                                <iconify-icon icon="solar:home-2-line-duotone" class="fs-6"></iconify-icon>
                            </a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a class="text-decoration-none" href="<?php echo $pageLink; ?>" id="page-link"><span class="badge fw-medium fs-2 bg-primary-subtle text-primary"><?php echo $pageTitle; ?></span></a>
                        </li>
                        <?php
                            if(!$newRecord && !empty($detailID)){
                                echo '<li class="breadcrumb-item" id="details-id">'. $detailID .'</li>';
                            }

                            if($newRecord){
                                echo '<li class="breadcrumb-item">New</li>';
                            }
                        ?>
                    </ol>
                </nav>
                <input type="hidden" id="page-id" value="<?php echo $pageID; ?>">
            </div>
        </div>
    </div>
</div>