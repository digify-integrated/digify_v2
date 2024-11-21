<?php
/**
 * Configuration File
 * 
 * This file contains configuration constants for the application, including database settings,
 * encryption keys, email server configuration, default images, security settings, and more.
 */

// -------------------------------------------------------------
// Timezone Configuration
// -------------------------------------------------------------
date_default_timezone_set('Asia/Manila'); // Set default timezone to Philippines

// -------------------------------------------------------------
// Database Configuration
// -------------------------------------------------------------
define('DB_HOST', 'localhost'); // Database host (e.g., '127.0.0.1' or 'localhost')
define('DB_NAME', 'digifydb'); // Name of the database
define('DB_USER', 'digify'); // Database user with necessary permissions
define('DB_PASS', 'qKHJpbkgC6t93nQr'); // Password for the database user

// -------------------------------------------------------------
// Encryption Configuration
// -------------------------------------------------------------
define('ENCRYPTION_KEY', '4b$Gy#89%q*aX@^p&cT!sPv6(5w)zSd+R'); // Key for data encryption and decryption
define('SECRET_KEY', '9n6ui[N];T\?{Wju[@zq^7)y>gsz2ltMT'); // Key for data encryption and decryption

// -------------------------------------------------------------
// Email Configuration
// -------------------------------------------------------------
define('MAIL_HOST', 'smtp.hostinger.com'); // SMTP server host
define('MAIL_SMTP_AUTH', true); // Enable SMTP authentication
define('MAIL_USERNAME', 'cgmi-noreply@christianmotors.ph'); // SMTP username
define('MAIL_PASSWORD', 'P@ssw0rd'); // SMTP password
define('MAIL_SMTP_SECURE', 'ssl'); // Encryption method (ssl or tls)
define('MAIL_PORT', 465); // SMTP port

// -------------------------------------------------------------
// Default User Interface Images
// -------------------------------------------------------------
define('DEFAULT_AVATAR_IMAGE', './assets/images/default/default-avatar.jpg'); // Default avatar image
define('DEFAULT_BG_IMAGE', './assets/images/default/default-bg.jpg'); // Default background image
define('DEFAULT_LOGIN_LOGO_IMAGE', './assets/images/default/default-logo-placeholder.png'); // Login logo
define('DEFAULT_MENU_LOGO_IMAGE', './assets/images/default/default-menu-logo.png'); // Menu logo
define('DEFAULT_MODULE_ICON_IMAGE', './assets/images/default/default-module-icon.svg'); // Module icon
define('DEFAULT_FAVICON_IMAGE', './assets/images/default/default-favicon.svg'); // Favicon
define('DEFAULT_COMPANY_LOGO', './assets/images/default/default-company-logo.png'); // Company logo
define('DEFAULT_APP_MODULE_LOGO', './assets/images/default/app-module-logo.png'); // App module logo
define('DEFAULT_PLACEHOLDER_IMAGE', './assets/images/default/default-image-placeholder.png'); // Placeholder image
define('DEFAULT_ID_PLACEHOLDER_FRONT', './assets/images/default/id-placeholder-front.jpg'); // ID placeholder front
define('DEFAULT_UPLOAD_PLACEHOLDER', './assets/images/default/upload-placeholder.png'); // Upload placeholder

// -------------------------------------------------------------
// Security Configuration
// -------------------------------------------------------------
define('DEFAULT_PASSWORD', 'P@ssw0rd'); // Default user password
define('MAX_FAILED_LOGIN_ATTEMPTS', 5); // Max failed login attempts before lockout
define('RESET_PASSWORD_TOKEN_DURATION', 10); // Duration for password reset token (in minutes)
define('REGISTRATION_VERIFICATION_TOKEN_DURATION', 180); // Duration for registration verification token (in minutes)
define('DEFAULT_PASSWORD_DURATION', 180); // Duration for password validity (in days)
define('MAX_FAILED_OTP_ATTEMPTS', 5); // Max failed OTP attempts
define('DEFAULT_OTP_DURATION', 5); // Duration for OTP validity (in minutes)
define('DEFAULT_SESSION_INACTIVITY', 30); // Session inactivity timeout (in minutes)
define('DEFAULT_PASSWORD_RECOVERY_LINK', 'http://localhost/digify/password-reset.php?id='); // Link for password recovery
?>
