<?php
require_once '../helpers/log_helper.php';

// Secret key for signing the token
$jwt_secret = 'your_secret_key';  // Replace with a strong secret key

/**
 * Creates a JWT for user authentication.
 *
 * @param int $user_id The user ID.
 * @param string $username The username.
 * @return string|false The generated JWT or false on failure.
 */
function createJWT($user_id, $username) {
    global $jwt_secret;

    if (empty($user_id) || empty($username)) {
        log_message("ERROR", "JWT Creation Failed: Missing user ID or username.");
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

    log_message("INFO", "JWT created successfully for user ID: {$user_id}");
    return "$header.$payload.$signature";
}

/**
 * Validates a given JWT.
 *
 * @param string $token The JWT token.
 * @return array|false The decoded payload if valid, or false on failure.
 */
function validateJWT($token) {
    global $jwt_secret;

    $parts = explode('.', $token);

    if (count($parts) !== 3) {
        log_message("ERROR", "Invalid JWT: Incorrect format.");
        return false;
    }

    [$header, $payload, $signature] = $parts;

    // Recalculate signature
    $valid_signature = base64_encode(hash_hmac('sha256', "$header.$payload", $jwt_secret, true));

    if ($signature !== $valid_signature) {
        log_message("ERROR", "Invalid JWT: Signature mismatch.");
        return false;
    }

    // Decode payload
    $payload = json_decode(base64_decode($payload), true);

    if (!$payload || $payload['exp'] < time()) {
        log_message("ERROR", "Invalid JWT: Expired or corrupted payload.");
        return false;
    }

    log_message("INFO", "JWT validated successfully for user ID: {$payload['user_id']}");
    return $payload; // Return user data
}
?>
