<?php
# -------------------------------------------------------------
# Timezone Configuration
# -------------------------------------------------------------
date_default_timezone_set('Asia/Manila');

# -------------------------------------------------------------
# Database Configuration
# -------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'digifydb');
define('DB_USER', 'digify');
define('DB_PASS', 'qKHJpbkgC6t93nQr');

# -------------------------------------------------------------
# Encryption Configuration
# -------------------------------------------------------------
define('ENCRYPTION_KEY', '4b$Gy#89%q*aX@^p&cT!sPv6(5w)zSd+R');
define('SECRET_KEY', '9n6ui[N];T\?{Wju[@zq^7)y>gsz2ltMT');

# -------------------------------------------------------------
# Email Configuration
# -------------------------------------------------------------
define('MAIL_HOST', 'smtp.hostinger.com');
define('MAIL_SMTP_AUTH', true);
define('MAIL_USERNAME', 'cgmi-noreply@christianmotors.ph');
define('MAIL_PASSWORD', 'P@ssw0rd');
define('MAIL_SMTP_SECURE', 'ssl');
define('MAIL_PORT', 465);

# -------------------------------------------------------------
# Default User Interface Images
# -------------------------------------------------------------
define('DEFAULT_AVATAR_IMAGE', './assets/images/default/default-avatar.jpg');
define('DEFAULT_BG_IMAGE', './assets/images/default/default-bg.jpg');
define('DEFAULT_LOGIN_LOGO_IMAGE', './assets/images/default/default-logo-placeholder.png');
define('DEFAULT_MENU_LOGO_IMAGE', './assets/images/default/default-menu-logo.png');
define('DEFAULT_MODULE_ICON_IMAGE', './assets/images/default/default-module-icon.svg');
define('DEFAULT_FAVICON_IMAGE', './assets/images/default/default-favicon.svg');
define('DEFAULT_COMPANY_LOGO', './assets/images/default/default-company-logo.png');
define('DEFAULT_APP_MODULE_LOGO', './assets/images/default/app-module-logo.png');
define('DEFAULT_PLACEHOLDER_IMAGE', './assets/images/default/default-image-placeholder.png');
define('DEFAULT_ID_PLACEHOLDER_FRONT', './assets/images/default/id-placeholder-front.jpg');
define('DEFAULT_UPLOAD_PLACEHOLDER', './assets/images/default/upload-placeholder.png');

# -------------------------------------------------------------
# Security Configuration
# -------------------------------------------------------------
define('DEFAULT_PASSWORD', 'P@ssw0rd');
define('MAX_FAILED_LOGIN_ATTEMPTS', 5);
define('RESET_PASSWORD_TOKEN_DURATION', 10);
define('REGISTRATION_VERIFICATION_TOKEN_DURATION', 180);
define('DEFAULT_PASSWORD_DURATION', 180);
define('MAX_FAILED_OTP_ATTEMPTS', 5);
define('DEFAULT_OTP_DURATION', 5);
define('DEFAULT_SESSION_INACTIVITY', 30);
define('DEFAULT_PASSWORD_RECOVERY_LINK', 'http:#localhost/digify_v2/password-reset.php?id=');
?>
