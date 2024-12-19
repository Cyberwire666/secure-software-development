<?php
// jwt.php

// Secret key for signing the token
$jwt_secret = 'your_secret_key';

// Function to create JWT
function createJWT($payload) {
    global $jwt_secret;
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload = json_encode($payload);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $jwt_secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
}

// Function to validate JWT
function validateJWT($token) {
    global $jwt_secret;
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }

    list($header, $payload, $signature) = $parts;

    $validSignature = hash_hmac('sha256', "$header.$payload", $jwt_secret, true);
    $validBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

    if ($signature === $validBase64UrlSignature) {
        return json_decode(base64_decode($payload), true);
    }

    return false;
}
?>
