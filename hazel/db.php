<?php
// db.php
// Reusable database connection file (so you don't repeat code)

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "booking_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection FAILED: " . $conn->connect_error);
}
?>
