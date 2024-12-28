<?php
// Include the logging helper to log messages for different events
require_once '../helpers/log_helper.php';

// Secret key for signing the token
$jwt_secret = '638103891';  // Replace with a strong secret key to protect JWT integrity

/**
 * Creates a JWT for user authentication.
 *
 * This function takes the user's ID and username to generate a secure JSON Web Token (JWT) 
 * that contains essential user information for authentication. The token is then signed using 
 * a secret key for integrity and validity.
 *
 * @param int $user_id The user ID.
 * @param string $username The username.
 * @return string|false The generated JWT or false if the creation failed.
 */
function createJWT($user_id, $username) {
    global $jwt_secret;  // Access the global secret key for signing the JWT

    // Check if user ID and username are provided, return false if either is missing
    if (empty($user_id) || empty($username)) {
        log_message("ERROR", "JWT Creation Failed: Missing user ID or username.");  // Log the error when inputs are invalid
        return false;  // Return false if the required parameters are missing
    }

    // Header section for the JWT - specifies algorithm and type of token
    $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));  // Base64 encode the header after converting it to JSON

    // Payload section for the JWT - contains the user's ID, username, and expiration time
    $payload = base64_encode(json_encode([
        'user_id' => $user_id,
        'username' => $username,
        'iat' => time(),  // Issued at time (current time)
        'exp' => time() + 3600 // Expiration time set to 1 hour after the issuance
    ]));

    // Signature calculation: Creates a signature using the header, payload, and secret key
    $signature = hash_hmac('sha256', "$header.$payload", $jwt_secret, true);  // Sign the header and payload with HMAC-SHA256
    $signature = base64_encode($signature);  // Base64 encode the signature for the final token

    log_message("INFO", "JWT created successfully for user ID: {$user_id}");  // Log the successful JWT creation
    // Return the complete JWT as a combination of header, payload, and signature
    return "$header.$payload.$signature";
}

/**
 * Validates a given JWT.
 *
 * This function is responsible for verifying the validity of a JWT by checking its signature 
 * and ensuring that its payload is not corrupted or expired.
 *
 * @param string $token The JWT token to be validated.
 * @return array|false The decoded payload if valid, or false on failure (invalid or expired).
 */
function validateJWT($token) {
    global $jwt_secret;  // Access the global secret key for signature verification

    // Split the JWT into three parts: header, payload, and signature
    $parts = explode('.', $token);

    // Ensure the JWT has exactly three parts (header, payload, and signature)
    if (count($parts) !== 3) {
        log_message("ERROR", "Invalid JWT: Incorrect format.");  // Log the error if the token format is incorrect
        return false;  // Return false if the JWT does not consist of exactly three parts
    }

    // Assign each part to its respective variable
    [$header, $payload, $signature] = $parts;

    // Recalculate the signature based on the header and payload and compare with the provided signature
    $valid_signature = base64_encode(hash_hmac('sha256', "$header.$payload", $jwt_secret, true));  // Generate a valid signature from header and payload

    // Check if the calculated signature matches the one in the token
    if ($signature !== $valid_signature) {
        log_message("ERROR", "Invalid JWT: Signature mismatch.");  // Log the error if the signature is invalid
        return false;  // Return false if the signature is invalid
    }

    // Decode the payload from Base64 to JSON and decode to PHP array
    $payload = json_decode(base64_decode($payload), true);  // Decode the Base64-encoded payload into an associative array

    // Ensure the payload is valid and not expired
    if (!$payload || $payload['exp'] < time()) {  // Check if the payload is empty or expired
        log_message("ERROR", "Invalid JWT: Expired or corrupted payload.");  // Log the error if the payload is invalid or expired
        return false;  // Return false if the payload is invalid or expired
    }

    log_message("INFO", "JWT validated successfully for user ID: {$payload['user_id']}");  // Log the success of the JWT validation
    return $payload;  // Return the decoded payload, which contains user ID and other information
}
?>
