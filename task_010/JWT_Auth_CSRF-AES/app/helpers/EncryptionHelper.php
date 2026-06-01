<?php

class EncryptionHelper
{
    private static function getKey()
    {
        $key = $_ENV['ENCRYPTION_KEY'] ?? '';
        if (empty($key)) {
            throw new Exception('Encryption key is not set in environment variables.');
        }
        return $key;
    }

    public static function encrypt($data)
    {
        if (empty($data)) return $data;
        
        $key = self::getKey();
        $cipher = 'aes-256-cbc';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data)
    {
        if (empty($data)) return $data;
        
        $key = self::getKey();
        $cipher = 'aes-256-cbc';
        
        $decoded = base64_decode($data);
        if ($decoded === false) return $data;
        
        $ivLength = openssl_cipher_iv_length($cipher);
        if (strlen($decoded) <= $ivLength) return $data;
        
        $iv = substr($decoded, 0, $ivLength);
        $encrypted = substr($decoded, $ivLength);
        
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);
        
        return $decrypted !== false ? $decrypted : $data;
    }
}
