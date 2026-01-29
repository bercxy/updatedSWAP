<?php
// booking_form.php
// Purpose: Create a NEW booking
// Facility ID and Facility Name are passed from the homepage (index.php) via URL

$facility_id   = $_GET['facility_id'] ?? '';
$facility_name = $_GET['facility_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Create Booking</title>
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

    <div class="form-container">
        <h1>Facility Booking</h1>

<form method="POST" action="/booking_system/hazel/booking_action.php">

            <!-- User ID  -->
            <label for="user_id">User ID</label>
            <input type="text"
                   id="user_id"
                   name="user_id"
                   placeholder="e.g. 2404363J"
                   required>

            <!-- Facility Name shown to user (READ-ONLY for usability) -->
            <label for="facility_display">Facility</label>
            <input type="text"
                   id="facility_display"
                   value="<?php echo htmlspecialchars($facility_name); ?>"
                   readonly>

            <!-- Facility ID is hidden and used internally by the system -->
            <input type="hidden"
                   name="facility_id"
                   value="<?php echo htmlspecialchars($facility_id); ?>">

            <div class="helper-text">
                Facility is selected from the homepage and cannot be changed here.
            </div>

            <!-- Booking date -->
            <label for="booking_date">Booking Date</label>
            <input type="date"
                   id="booking_date"
                   name="booking_date"
                   required>

            <!-- Start time -->
            <label for="start_time">Start Time</label>
            <input type="time"
                   id="start_time"
                   name="start_time"
                   min="09:00"
                   max="18:00"
                   required>

            <!-- End time -->
            <label for="end_time">End Time</label>
            <input type="time"
                   id="end_time"
                   name="end_time"
                   min="09:00"
                   max="18:00"
                   required>

            <!-- Purpose of booking -->
            <label for="purpose">Purpose</label>
            <input type="text"
                   id="purpose"
                   name="purpose"
                   placeholder="e.g. Project work / Training session"
                   required>

            <!-- Submit booking -->
            <input type="submit" value="Submit Booking">

        </form>
    </div>

</body>
</html>
