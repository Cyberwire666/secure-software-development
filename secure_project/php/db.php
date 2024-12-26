<?php
// db.php
require_once '../helpers/log_helper.php';

$host = 'localhost';  // Database host (usually localhost)
$user = 'root';       // Database username (root for XAMPP by default)
$pass = '';           // Database password (empty for XAMPP by default)
$dbname = 'webapp';   // The database name ('webapp' should be created in MySQL)

$db = new mysqli($host, $user, $pass, $dbname);

if ($db->connect_error) {
    write_log("Database connection failed: " . $db->connect_error);
    die("Connection failed: " . $db->connect_error);
} else {
    write_log("Database connection successful.");
}
?>
