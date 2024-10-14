<?php

class SecurityModel {
   
    # -------------------------------------------------------------
    public function encryptData($plainText) {
        $plainText = trim($plainText);
        if (empty($plainText)) return false;

        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $ciphertext = openssl_encrypt($plainText, 'aes-256-cbc', ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);
        
        return $ciphertext ? rawurlencode(base64_encode($iv . $ciphertext)) : false;
    }
    # -------------------------------------------------------------
    
    # -------------------------------------------------------------
    public function decryptData($ciphertext) {
        $decodedData = base64_decode(rawurldecode($ciphertext));
    
        if (!$decodedData) return false;
    
        $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    
        if (strlen($decodedData) < $iv_length) {
            return false;
        }
    
        $iv = substr($decodedData, 0, $iv_length);
    
        $ciphertext = substr($decodedData, $iv_length);
    
        $plainText = openssl_decrypt($ciphertext, 'aes-256-cbc', ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);
    
        return $plainText ? $plainText : false;
    }
    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function obscureEmail($email) {
        [$username, $domain] = explode('@', $email, 2);

        $firstChar = substr($username, 0, 1);
        $lastChar = substr($username, -1);
        $maskedUsername = $firstChar . str_repeat('*', max(0, strlen($username) - 2)) . $lastChar;

        return $maskedUsername . '@' . $domain;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function obscureCardNumber($cardNumber) {
        $last4Digits = substr($cardNumber, -4);
        $masked = str_repeat('*', max(0, strlen($cardNumber) - 4));

        return substr(implode(' ', str_split($masked, 4)), 0, -1) . ' ' . $last4Digits;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getErrorDetails($type) {
        $errorMessages = [
            'password reset token invalid' => [
                'TITLE' => 'Password Reset Token Invalid',
                'MESSAGE' => 'The password reset token is invalid. Please try again.'
            ],
            'password reset token expired' => [
                'TITLE' => 'Password Reset Token Expired',
                'MESSAGE' => 'The password reset token has expired. Please try again.'
            ],
            'email verification token expired' => [
                'TITLE' => 'Email Verification Token Expired',
                'MESSAGE' => 'The email verification token has expired. Please try again.'
            ],
            'invalid user' => [
                'TITLE' => 'Invalid User',
                'MESSAGE' => 'The user account is invalid. Please try again.'
            ],
            'otp expired' => [
                'TITLE' => 'OTP Expired',
                'MESSAGE' => 'The OTP has expired. Please try again.'
            ],
            'invalid otp' => [
                'TITLE' => 'Invalid OTP',
                'MESSAGE' => 'The OTP is invalid. Please try again.'
            ]
        ];

        return $errorMessages[$type] ?? [
            'TITLE' => 'Unknown Error',
            'MESSAGE' => 'An unknown error occurred.'
        ];
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function generateFileName($minLength = 4, $maxLength = 8) {
        $validCharacters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $filename = '';
        $length = random_int($minLength, $maxLength);

        for ($i = 0; $i < $length; $i++) {
            $filename .= $validCharacters[random_int(0, strlen($validCharacters) - 1)];
        }

        return $filename;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function directoryChecker($directory) {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                $error = error_get_last();
                return 'Error creating directory: ' . ($error['message'] ?? 'Unknown error');
            }
        } elseif (!is_writable($directory)) {
            return 'Directory exists but is not writable.';
        }

        return true;
    }
    # -------------------------------------------------------------
}

?>