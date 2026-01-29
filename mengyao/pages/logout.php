<?php
session_start();

// unset all session variables
$_SESSION = [];

// destroy the session
session_destroy();

// redirect to login page using absolute path
header("Location: /booking_system/mengyao/pages/login.php");
exit;
?>
