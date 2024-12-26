<?php
// Include the log helper to enable logging functionality
require_once '../helpers/log_helper.php';

// Secret key for signing the token
$jwt_secret = 'your_secret_key';

// Function to create JWT
function createJWT($payload) {
    global $jwt_secret;
    $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
    $payload = json_encode($payload);

    // Create base64Url encoded header and payload
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

    // Create the signature
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $jwt_secret, true);
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

    // Construct the JWT token
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    // Log JWT creation action
    write_log("JWT Created: Payload - " . json_encode($payload) . ", Token: $jwt");

    return $jwt;
}

// Function to validate JWT
function validateJWT($token) {
    global $jwt_secret;

    // Split the token into parts
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        // Log failed validation attempt
        write_log("JWT Validation Failed: Invalid Token Structure - $token");
        return false;
    }

    list($header, $payload, $signature) = $parts;

    // Validate the signature
    $validSignature = hash_hmac('sha256', "$header.$payload", $jwt_secret, true);
    $validBase64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

    if ($signature === $validBase64UrlSignature) {
        // Log successful validation
        write_log("JWT Validated: Payload - " . base64_decode($payload));
        return json_decode(base64_decode($payload), true);
    }

    // Log failed validation
    write_log("JWT Validation Failed: Invalid Signature - $token");
    return false;
}
?>
