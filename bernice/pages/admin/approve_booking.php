<?php
require_once "../../config/config.php";
require_once "../../includes/admin_guard.php";
require_once "../../includes/booking_rules.php";

$booking_id = intval($_GET['id'] ?? 0);
if ($booking_id <= 0) {
    die("Invalid booking ID");
}

$conn->begin_transaction();

/* Get pending booking */
$stmt = $conn->prepare(
    "SELECT facility_id, booking_date, start_time, end_time
     FROM bookings
     WHERE booking_id = ? AND status = 'pending'"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();

$booking = $stmt->get_result()->fetch_assoc();
if (!$booking) {
    $conn->rollback();
    die("Booking not found or already processed");
}

/* Check for time conflict */
if (hasConflict(
    $conn,
    $booking['facility_id'],
    $booking['booking_date'],
    $booking['start_time'],
    $booking['end_time'],
    $booking_id
)) {
    $conn->rollback();
    die("Time slot conflict detected");
}

/* Approve booking */
$stmt = $conn->prepare(
    "UPDATE bookings SET status = 'approved' WHERE booking_id = ?"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();

$conn->commit();

header("Location: view_all_bookings.php");
exit;
