<?php
class AuthenticationModel {
    public $db;
    public $securityModel;

    public function __construct(DatabaseModel $db, SecurityModel $securityModel) {
        $this->db = $db;
        $this->securityModel = $securityModel;
    }

    # -------------------------------------------------------------
    #   Get methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getLoginCredentials($p_user_account_id, $p_credentials) {
        $stmt = $this->db->getConnection()->prepare('CALL getLoginCredentials(:p_user_account_id, :p_credentials)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_credentials', $p_credentials, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getPasswordHistory($p_user_account_id) {
        $stmt = $this->db->getConnection()->prepare('CALL getPasswordHistory(:p_user_account_id)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check exist methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkLoginCredentialsExist($p_user_account_id, $p_credentials) {
        $stmt = $this->db->getConnection()->prepare('CALL checkLoginCredentialsExist(:p_user_account_id, :p_credentials)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_credentials', $p_credentials, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkSignUpEmailExist($p_email) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSignUpEmailExist(:p_email)');
        $stmt->bindValue(':p_credentials', $p_credentials, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkSignUpUsernameExist($p_username) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSignUpUsernameExist(:p_username)');
        $stmt->bindValue(':p_username', $p_username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Check methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkAccessRights($p_user_account_id, $p_menu_item_id, $p_access_type) {
        $stmt = $this->db->getConnection()->prepare('CALL checkAccessRights(:p_user_account_id, :p_menu_item_id, :p_access_type)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_item_id', $p_menu_item_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_access_type', $p_access_type, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkSystemActionAccessRights($p_user_account_id, $p_system_action_id) {
        $stmt = $this->db->getConnection()->prepare('CALL checkSystemActionAccessRights(:p_user_account_id, :p_system_action_id)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_system_action_id', $p_system_action_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    }
    # -------------------------------------------------------------


    # -------------------------------------------------------------
    #   Update methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateLoginAttempt($p_user_account_id, $p_failed_login_attempts, $p_last_failed_login_attempt) {
        $stmt = $this->db->getConnection()->prepare('CALL updateLoginAttempt(:p_user_account_id, :p_failed_login_attempts, :p_last_failed_login_attempt)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_failed_login_attempts', $p_failed_login_attempts, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_failed_login_attempt', $p_last_failed_login_attempt, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateAccountLock($p_user_account_id, $p_locked, $p_lock_duration) {
        $stmt = $this->db->getConnection()->prepare('CALL updateAccountLock(:p_user_account_id, :p_locked, :p_lock_duration)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_locked', $p_locked, PDO::PARAM_STR);
        $stmt->bindValue(':p_lock_duration', $p_lock_duration, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateOTP($p_user_account_id, $p_otp, $p_otp_expiry_date, $p_failed_otp_attempts) {
        $stmt = $this->db->getConnection()->prepare('CALL updateOTP(:p_user_account_id, :p_otp, :p_otp_expiry_date, :p_failed_otp_attempts)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_otp', $p_otp, PDO::PARAM_STR);
        $stmt->bindValue(':p_otp_expiry_date', $p_otp_expiry_date, PDO::PARAM_STR);
        $stmt->bindValue(':p_failed_otp_attempts', $p_failed_otp_attempts, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateLastConnection($p_user_account_id, $p_session_token, $p_last_connection_date) {
        $stmt = $this->db->getConnection()->prepare('CALL updateLastConnection(:p_user_account_id, :p_session_token, :p_last_connection_date)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_session_token', $p_session_token, PDO::PARAM_STR);
        $stmt->bindValue(':p_last_connection_date', $p_last_connection_date, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateOTPAsExpired($p_user_account_id, $p_otp_expiry_date) {
        $stmt = $this->db->getConnection()->prepare('CALL updateOTPAsExpired(:p_user_account_id, :p_otp_expiry_date)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_otp_expiry_date', $p_otp_expiry_date, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateFailedOTPAttempts($p_user_account_id, $p_failed_otp_attempts) {
        $stmt = $this->db->getConnection()->prepare('CALL updateFailedOTPAttempts(:p_user_account_id, :p_failed_otp_attempts)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_failed_otp_attempts', $p_failed_otp_attempts, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateResetToken($p_user_account_id, $p_resetToken, $p_resetToken_expiry_date) {
        $stmt = $this->db->getConnection()->prepare('CALL updateResetToken(:p_user_account_id, :p_resetToken, :p_resetToken_expiry_date)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_resetToken', $p_resetToken, PDO::PARAM_STR);
        $stmt->bindValue(':p_resetToken_expiry_date', $p_resetToken_expiry_date, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateUserPassword($p_user_account_id, $p_password, $p_password_expiry_date, $p_locked, $p_failed_login_attempts, $p_account_lock_duration) {
        $stmt = $this->db->getConnection()->prepare('CALL updateUserPassword(:p_user_account_id, :p_password, :p_password_expiry_date, :p_locked, :p_failed_login_attempts, :p_account_lock_duration)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_password', $p_password, PDO::PARAM_STR);
        $stmt->bindValue(':p_password_expiry_date', $p_password_expiry_date, PDO::PARAM_STR);
        $stmt->bindValue(':p_locked', $p_locked, PDO::PARAM_STR);
        $stmt->bindValue(':p_failed_login_attempts', $p_failed_login_attempts, PDO::PARAM_STR);
        $stmt->bindValue(':p_account_lock_duration', $p_account_lock_duration, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function updateResetTokenAsExpired($p_user_account_id, $p_reset_token_expiry_date) {
        $stmt = $this->db->getConnection()->prepare('CALL updateResetTokenAsExpired(:p_user_account_id, :p_reset_token_expiry_date)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_reset_token_expiry_date', $p_reset_token_expiry_date, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    #   Build methods
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function buildMenuItem($p_user_account_id, $p_menu_group_id) {
        $menuItems = [];
    
        $stmt = $this->db->getConnection()->prepare('CALL buildMenuItem(:p_user_account_id, :p_menu_group_id)');
        $stmt->bindValue(':p_user_account_id', $p_user_account_id, PDO::PARAM_INT);
        $stmt->bindValue(':p_menu_group_id', $p_menu_group_id, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->rowCount();
    
        if ($count > 0) {
            $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
    
            foreach ($options as $row) {
                $menuItemID = $row['menu_item_id'];
                $menuItemName = $row['menu_item_name'];
                $menuItemURL = $row['menu_item_url'] ?? null;
                $parentID = $row['parent_id'];
                $appModuleID = $row['app_module_id'];
                $menuItemIcon = !empty($row['menu_item_icon']) ? $row['menu_item_icon'] : null;
    
                $menuItem = [
                    'MENU_ITEM_ID' => $menuItemID,
                    'MENU_ITEM_NAME' => $menuItemName,
                    'MENU_ITEM_URL' => $menuItemURL,
                    'PARENT_ID' => $parentID,
                    'MENU_ITEM_ICON' => $menuItemIcon,
                    'APP_MODULE_ID' => $appModuleID,
                    'CHILDREN' => []
                ];
    
                $menuItems[$menuItemID] = $menuItem;
            }
    
            foreach ($menuItems as $menuItem) {
                if (!empty($menuItem['PARENT_ID'])) {
                    if ($this->checkAccessRights($p_user_account_id, $menuItem['PARENT_ID'], 'read')['total'] > 0) {
                        $menuItems[$menuItem['PARENT_ID']]['CHILDREN'][] = &$menuItems[$menuItem['MENU_ITEM_ID']];
                    }
                }
            }
    
            $rootMenuItems = array_filter($menuItems, function ($item) {
                return empty($item['PARENT_ID']);
            });
    
            $html = '';
    
            foreach ($rootMenuItems as $rootMenuItem) {
                $html .= $this->buildMenuItemHTML($rootMenuItem);
            }
    
            return $html;
        }
    
        return ''; // Return empty if no items are found
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function buildMenuItemHTML($menuItemDetails, $level = 1) {
        $html = '';
        $menuItemID = $this->securityModel->encryptData($menuItemDetails['MENU_ITEM_ID'] ?? null);
        $appModuleID = $this->securityModel->encryptData($menuItemDetails['APP_MODULE_ID'] ?? null);
        $menuItemName = $menuItemDetails['MENU_ITEM_NAME'] ?? null;
        $menuItemIcon = $menuItemDetails['MENU_ITEM_ICON'] ?? null;
        $menuItemURL = $menuItemDetails['MENU_ITEM_URL'] ?? null;
        $children = $menuItemDetails['CHILDREN'] ?? null;
    
        $menuItemURL = !empty($menuItemURL) ? (strpos($menuItemURL, '?page_id=') !== false ? $menuItemURL : $menuItemURL . '?app_module_id=' . $appModuleID . '&page_id=' . $menuItemID) : 'javascript:void(0)';
    
        if ($level === 1) {
            if (empty($children)) {
                $html .= '<li class="sidebar-item">
                                <a class="sidebar-link" href="'. $menuItemURL .'" aria-expanded="false">
                                    <span>
                                        <i class="'. $menuItemIcon .'"></i>
                                    </span>
                                    <span class="hide-menu">'. $menuItemName .'</span>
                                </a>
                            </li>';
            }
            else {
                $html .= '<li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                    <i class="'. $menuItemIcon .'"></i>
                                    <span class="hide-menu">'. $menuItemName .'</span>
                                </a>
                                <ul aria-expanded="false" class="collapse first-level">';
    
                foreach ($children as $child) {
                    $html .= $this->buildMenuItemHTML($child, $level + 1);
                }
    
                $html .= '</ul>
                        </li>';
            }
        }
        else {
            if (empty($children)) {
                $html .= '<li class="sidebar-item">
                                <a class="sidebar-link" href="'. $menuItemURL .'">
                                    <span class="icon-small"></span>
                                    '. $menuItemName .'
                                </a>
                            </li>';
            }
            else {
                $html .= ' <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="javascript:void(0)" aria-expanded="false">
                                    <span class="icon-small"></span>
                                    <span class="hide-menu">'. $menuItemName .'</span>
                                </a>
                                <ul aria-expanded="false" class="collapse two-level">';
    
                foreach ($children as $child) {
                    $html .= $this->buildMenuItemHTML($child, $level + 1);
                }
    
                $html .= '</ul>
                        </li>';
            }
        }
    
        return $html;
    }    
    # -------------------------------------------------------------
}
?>