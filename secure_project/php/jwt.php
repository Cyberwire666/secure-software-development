<?php
require_once '../helpers/log_helper.php';

// Secret key for signing the token
$jwt_secret = 'your_secret_key';  // Replace with a strong secret key

// Function to create JWT
function createJWT($user_id, $username) {
    global $jwt_secret;

    if (empty($user_id) || empty($username)) {
        write_log("JWT Creation Failed: Missing user ID or username.");
        return false;
    }

    // Header and payload
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payload = base64_encode(json_encode([
        'user_id' => $user_id,
        'username' => $username,
        'iat' => time(),
        'exp' => time() + 3600 // Token expires in 1 hour
    ]));

    // Signature
    $signature = hash_hmac('sha256', "$header.$payload", $jwt_secret, true);
    $signature = base64_encode($signature);

    return "$header.$payload.$signature";
}

// Function to validate JWT
function validateJWT($token) {
    global $jwt_secret;

    $parts = explode('.', $token);

    if (count($parts) !== 3) {
        write_log("Invalid JWT: Incorrect format.");
        return false;
    }

    [$header, $payload, $signature] = $parts;

    // Recalculate signature
    $valid_signature = base64_encode(hash_hmac('sha256', "$header.$payload", $jwt_secret, true));

    if ($signature !== $valid_signature) {
        write_log("Invalid JWT: Signature mismatch.");
        return false;
    }

    // Decode payload
    $payload = json_decode(base64_decode($payload), true);

    if (!$payload || $payload['exp'] < time()) {
        write_log("Invalid JWT: Expired or corrupted payload.");
        return false;
    }

    return $payload; // Return user data
}
?>
