<?php
class Secure{
    
private static $encryptionKey = '#447_88+%000%7--?--?--444-';  

    public static function encryption($name) {
        $iv = substr(hash('sha256', self::$encryptionKey), 0, 16); // 16-byte IV
        return openssl_encrypt($name, 'aes-256-cbc', self::$encryptionKey, 0, $iv);
    }

    public static function decryption($name) {
         $iv = substr(hash('sha256', self::$encryptionKey), 0, 16); // 16-byte IV
        return openssl_decrypt($name, 'aes-256-cbc', self::$encryptionKey, 0, $iv);
    }
}