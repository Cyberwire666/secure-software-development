<?php
// db.php
require_once '../helpers/log_helper.php';  // Include the helper functions to log database actions

// Database configuration parameters
$host = 'localhost';  // The host where the database is located, commonly 'localhost' on local setups
$user = 'root';       // Database username, commonly 'root' in XAMPP (default MySQL setup)
$pass = '';           // Database password, usually empty for the default MySQL setup on XAMPP
$dbname = 'webapp';   // The name of the database, should be created beforehand in MySQL

// Establish a connection to the MySQL database using the parameters provided
$db = new mysqli($host, $user, $pass, $dbname);

// Check if the connection was successful
if ($db->connect_error) {
    // Log the error if the connection fails
    log_message("ERROR", "Database connection failed: " . $db->connect_error);
    // Exit the script and display the connection error message
    die("Connection failed: " . $db->connect_error);
} else {
    // Log the successful connection to the database
    log_message("INFO", "Database connection successful.");
}
?>
