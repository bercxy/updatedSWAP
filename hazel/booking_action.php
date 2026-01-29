<?php
// booking_action.php
// Purpose: Handle CREATE booking securely using prepared statements

require_once "db.php"; // use shared DB connection

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Error: Invalid request method.");
}

// Get inputs safely
$user_id      = trim($_POST["user_id"] ?? "");
$facility_id  = trim($_POST["facility_id"] ?? "");
$booking_date = trim($_POST["booking_date"] ?? "");
$start_time   = trim($_POST["start_time"] ?? "");
$end_time     = trim($_POST["end_time"] ?? "");
$purpose      = trim($_POST["purpose"] ?? "");

// 1) Required fields check
if ($user_id === "" || $facility_id === "" || $booking_date === "" || $start_time === "" || $end_time === "" || $purpose === "") {
    die("Error: All fields are required.");
}

// 2) Validate facility_id is numeric
if (!is_numeric($facility_id)) {
    die("Error: Invalid facility ID.");
}

// 3) Validate user_id format (alphanumeric, 7-10 characters)
if (!preg_match('/^[a-zA-Z0-9]{7,10}$/', $user_id)) {
    die("Error: Invalid user ID format.");
}

// 3) Cast to integers
$user_id = (int)$user_id;
$facility_id = (int)$facility_id;

// 4) Validate booking_date is not in the past
$today = date("Y-m-d");
if ($booking_date < $today) {
    die("Error: Booking date cannot be in the past.");
}

// 5) Validate times are between 9:00 and 18:00
$startTimestamp = strtotime($start_time);
$endTimestamp = strtotime($end_time);
$minTime = strtotime("09:00");
$maxTime = strtotime("18:00");

if ($startTimestamp < $minTime || $startTimestamp > $maxTime || $endTimestamp < $minTime || $endTimestamp > $maxTime) {
    die("Error: Booking times must be between 9:00 AM and 6:00 PM.");
}

// 6) Time order validation (start must be earlier than end)
if ($startTimestamp >= $endTimestamp) {
    die("Error: End time must be later than start time.");
}

// 7) Conflict check (prevents overlapping bookings for same facility/date)
// Overlap rule: new_start < existing_end AND new_end > existing_start
$conflictStmt = $conn->prepare("
    SELECT booking_id
    FROM bookings
    WHERE facility_id = ?
      AND booking_date = ?
      AND status IN ('Pending','Approved')
      AND (? < end_time AND ? > start_time)
    LIMIT 1
");

if (!$conflictStmt) {
    die("Error preparing conflict check: " . $conn->error);
}

$conflictStmt->bind_param("isss", $facility_id, $booking_date, $start_time, $end_time);
$conflictStmt->execute();
$res = $conflictStmt->get_result();

if ($res && $res->num_rows > 0) {
    $conflictStmt->close();
    die("Error: Slot unavailable (conflicting booking exists).");
}
$conflictStmt->close();

// 8) Insert booking (Prepared statement = SQLi protection)
$stmt = $conn->prepare("
    INSERT INTO bookings (user_id, facility_id, booking_date, start_time, end_time, purpose, status)
    VALUES (?, ?, ?, ?, ?, ?, 'Pending')
");

if (!$stmt) {
    die("Error preparing insert: " . $conn->error);
}

// user_id is varchar, facility_id is integer
$stmt->bind_param("sissss", $user_id, $facility_id, $booking_date, $start_time, $end_time, $purpose);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    // ✅ Redirect to landing page after successful submission
    header("Location: booking_success.php?booking_id=" . urlencode($newId));
    exit;

} else {
    echo "Error inserting booking: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
