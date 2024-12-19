<?php
// db.php

$host = 'localhost';  // Database host (usually localhost)
$user = 'root';       // Database username (root for XAMPP by default)
$pass = '';           // Database password (empty for XAMPP by default)
$dbname = 'webapp';   // The database name ('webapp' should be created in MySQL)

$db = new mysqli($host, $user, $pass, $dbname);

// Check if connection was successful
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);  // Displays an error if the connection fails
}
?>
