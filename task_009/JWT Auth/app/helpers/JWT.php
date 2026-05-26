<?php

class JWT
{
    private static function base64url_encode($data)
    {
        $b64 = base64_encode($data);
        if ($b64 === false) {
            return false;
        }
        $url = strtr($b64, '+/', '-_');
        return rtrim($url, '=');
    }

    private static function base64url_decode($data)
    {
        $b64 = strtr($data, '-_', '+/');
        return base64_decode($b64, true);
    }

    public static function encode($payload)
    {
        $secret = $_ENV['JWT_SECRET'] ?? 'secret';
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $base64UrlHeader = self::base64url_encode($header);
        
        $base64UrlPayload = self::base64url_encode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = self::base64url_encode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function decode($token)
    {
        $secret = $_ENV['JWT_SECRET'] ?? 'secret';
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header64, $payload64, $signature64) = $parts;

        $header = json_decode(self::base64url_decode($header64), true);
        if ($header === null || !isset($header['alg']) || $header['alg'] !== 'HS256') {
            return false;
        }

        $payload = json_decode(self::base64url_decode($payload64), true);
        if ($payload === null) {
            return false;
        }

        $validSignature = hash_hmac('sha256', $header64 . "." . $payload64, $secret, true);
        $validSignature64 = self::base64url_encode($validSignature);

        if (!hash_equals($validSignature64, $signature64)) {
            return false;
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        return $payload;
    }
}
