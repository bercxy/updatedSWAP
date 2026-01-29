<?php
/**
 * Application Configuration
 *
 * Defines database connection settings.
 */

/*Project Configuration
$host = $_SERVER['HTTP_HOST'];*/
// Detect if running from CLI or web
if (php_sapi_name() === 'cli') {
    // CLI mode (PHPUnit or command line) – use localhost as host
    $host = 'localhost';
} else {
    // Web mode
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
}
$base = '/booking_system';
define('BASE_URL', 'http://' . $host . $base);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'booking_system');

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8');
?>