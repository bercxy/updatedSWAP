<?php
// booking_success.php
// Purpose: Landing / confirmation page after booking submission

require_once "db.php"; // DB connection

// Get booking_id from URL
$booking_id = $_GET["booking_id"] ?? "";

// Basic validation
if ($booking_id === "" || !ctype_digit($booking_id)) {
    die("Invalid booking reference.");
}

// Fetch booking details
$stmt = $conn->prepare("
    SELECT 
        b.booking_id,
        f.facility_name,
        b.booking_date,
        b.start_time,
        b.end_time,
        b.status
    FROM bookings b
    JOIN facilities f ON b.facility_id = f.facility_id
    WHERE b.booking_id = ?
");

$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Submitted</title>

    <link rel="stylesheet" href="../styles.css">
</head>

<body>

    <nav>
        <div class="title">Advanced Manufacturing Centre</div>
        <ul class="nav-links">
            <li><a href="homepage.php">Facilities</a></li>
            <li><a href="user_bookings.php">My Bookings</a></li>
            <li><a href="#">Profile</a></li>
        </ul>
    </nav>

    <section class="hero">
        <div class="success-container">
            <div class="success-card">
                <h1>✅ Booking Submitted</h1>

                <div class="row">
                    <div class="label">Booking ID</div>
                    <div class="value"><?php echo htmlspecialchars($booking["booking_id"]); ?></div>
                </div>

                <div class="row">
                    <div class="label">Facility</div>
                    <div class="value"><?php echo htmlspecialchars($booking["facility_name"]); ?></div>
                </div>

                <div class="row">
                    <div class="label">Date</div>
                    <div class="value"><?php echo htmlspecialchars($booking["booking_date"]); ?></div>
                </div>

                <div class="row">
                    <div class="label">Time</div>
                    <div class="value">
                        <?php echo htmlspecialchars($booking["start_time"]); ?>
                        –
                        <?php echo htmlspecialchars($booking["end_time"]); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="label">Status</div>
                    <div class="value">
                        <span class="badge pending"><?php echo htmlspecialchars($booking["status"]); ?></span>
                    </div>
                </div>

                <a href="homepage.php" class="btn">Back to Facilities</a>
            </div>
        </div>
    </section>

</body>
</html>
