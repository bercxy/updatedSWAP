<?php
// booking_action.php
// Handles CREATE booking with validation + conflict checking

require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Error: Invalid request method.");
}

// Get inputs
$user_id      = $_POST["user_id"] ?? "";
$facility_id  = $_POST["facility_id"] ?? "";
$booking_date = $_POST["booking_date"] ?? "";
$start_time   = $_POST["start_time"] ?? "";
$end_time     = $_POST["end_time"] ?? "";

// 1) Required fields
if ($user_id === "" || $facility_id === "" || $booking_date === "" || $start_time === "" || $end_time === "") {
    die("Error: All fields are required.");
}

// 2) Validate numeric IDs
if (!ctype_digit((string)$user_id) || !ctype_digit((string)$facility_id)) {
    die("Error: Invalid user or facility ID.");
}

$user_id = (int)$user_id;
$facility_id = (int)$facility_id;

// 3) Date cannot be in the past
if ($booking_date < date("Y-m-d")) {
    die("Error: Booking date cannot be in the past.");
}

// 4) Time rules (09:00–18:00)
$start = strtotime($start_time);
$end   = strtotime($end_time);

if ($start < strtotime("09:00") || $end > strtotime("18:00")) {
    die("Error: Bookings allowed only between 09:00 and 18:00.");
}

if ($start >= $end) {
    die("Error: End time must be later than start time.");
}

// 5) Conflict check
$conflictStmt = $conn->prepare("
    SELECT booking_id
    FROM bookings
    WHERE facility_id = ?
      AND booking_date = ?
      AND status IN ('pending','approved')
      AND (? < end_time AND ? > start_time)
    LIMIT 1
");

$conflictStmt->bind_param(
    "isss",
    $facility_id,
    $booking_date,
    $start_time,
    $end_time
);

$conflictStmt->execute();
$conflict = $conflictStmt->get_result();

if ($conflict->num_rows > 0) {
    die("Error: Time slot already booked.");
}

$conflictStmt->close();

// 6) Insert booking
$stmt = $conn->prepare("
    INSERT INTO bookings
    (user_id, facility_id, booking_date, start_time, end_time, status)
    VALUES (?, ?, ?, ?, ?, 'pending')
");

$stmt->bind_param(
    "iisss",
    $user_id,
    $facility_id,
    $booking_date,
    $start_time,
    $end_time
);

$stmt->execute();
$newId = $stmt->insert_id;

$stmt->close();
$conn->close();

// Redirect
header("Location: /booking_system/hazel/pages/booking_success.php?booking_id=" . $newId);
exit;
