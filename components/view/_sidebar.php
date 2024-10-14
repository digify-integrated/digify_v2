<aside class="side-mini-panel with-vertical">
  <div class="iconbar">
    <div>
      <div class="mini-nav">
        <div class="brand-logo d-flex align-items-center justify-content-center">
          <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
            <iconify-icon icon="solar:hamburger-menu-line-duotone" class="fs-7"></iconify-icon>
          </a>
        </div>
        <ul class="mini-nav-ul" data-simplebar>
          <li class="mini-nav-item" id="mini-apps">
            <a href="apps.php" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="Apps">
              <img src="./assets/images/default/apps.png" width="35" height="35" alt="app-logo">
            </a>
          </li>
          <?php
            $apps = '';

            $sql = $databaseModel->getConnection()->prepare('CALL buildAppModuleStack(:userID)');
            $sql->bindValue(':userID', $userID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();
            
            foreach ($options as $row) {
              $appModuleSidebarID = $row['app_module_id'];
              $appModuleName = $row['app_module_name'];
              $menuItemID = $row['menu_item_id'];
              $appLogo = $systemModel->checkImage($row['app_logo'], 'app module logo');

              $menuItemDetails = $menuItemModel->getMenuItem($menuItemID);
              $menuItemURL = $menuItemDetails['menu_item_url'];
              $menuItemPageID = $menuItemDetails['menu_item_id'] ?? null;
              $menuItemAppModuleID = $menuItemDetails['app_module_id'] ?? null;
              $menuItemPageURL = $menuItemDetails['menu_item_url'] ?? null;
              $menuItemPageLink = $menuItemPageURL . '?app_module_id=' . $securityModel->encryptData($menuItemAppModuleID) . '&page_id=' . $securityModel->encryptData($menuItemPageID);

              $tooltipAttributes = 'data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="right" data-bs-title="' . htmlspecialchars($appModuleName, ENT_QUOTES, 'UTF-8') . '"';
              $logoImage = '<img src="' . htmlspecialchars($appLogo, ENT_QUOTES, 'UTF-8') . '" width="40" height="40" alt="app-logo">';

              $apps .= '<li class="mini-nav-item" id="mini-1">
                          <a href="' . ($appModuleSidebarID == $appModuleID ? 'javascript:void(0)' : htmlspecialchars($menuItemPageLink, ENT_QUOTES, 'UTF-8')) . '" ' . $tooltipAttributes . '>
                            ' . $logoImage . '
                          </a>
                        </li>';

            }

            echo $apps;
          ?>
        </ul>
      </div>
      <div class="sidebarmenu">
        <div class="brand-logo d-flex align-items-center nav-logo">
          <a href="apps.php" class="text-nowrap logo-img">
            <img src="./assets/images/logos/logo-dark.svg" alt="digify-img" />
            <img src="./assets/images/logos/logo-light.svg" alt="digify-img" class="d-none">
          </a>
        </div>
        <nav class="sidebar-nav" id="menu-right-mini-1" data-simplebar>
          <ul class="sidebar-menu" id="sidebarnav">
            <?php
              $menu = '';

              $sql = $databaseModel->getConnection()->prepare('CALL buildMenuGroup(:userID, :appModuleID)');
              $sql->bindValue(':userID', $userID, PDO::PARAM_INT);
              $sql->bindValue(':appModuleID', $appModuleID, PDO::PARAM_INT);
              $sql->execute();
              $options = $sql->fetchAll(PDO::FETCH_ASSOC);
              $sql->closeCursor();

              $menuGroups = [];

              foreach ($options as $row) {
                  $menuGroups[$row['menu_group_id']] = $row['menu_group_name'];
              }

              foreach ($menuGroups as $menuGroupID => $menuGroupName) {
                  $menu .= '<li class="nav-small-cap">
                                <span class="hide-menu">' . $menuGroupName . '</span>
                              </li>';

                  $menu .= $authenticationModel->buildMenuItem($userID, $menuGroupID);
              }

              echo $menu;
            ?>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</aside>
