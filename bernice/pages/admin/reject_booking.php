<?php
require_once "../../config/config.php";
require_once "../../includes/admin_guard.php";

$booking_id = intval($_GET['id'] ?? 0);
if ($booking_id <= 0) {
    die("Invalid booking ID");
}

/* Reject booking if still pending */
$stmt = $conn->prepare(
    "UPDATE bookings 
     SET status = 'rejected' 
     WHERE booking_id = ? AND status = 'pending'"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();

header("Location: view_all_bookings.php");
exit;
