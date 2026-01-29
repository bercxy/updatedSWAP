<?php
// user_bookings.php
// Purpose: Show bookings in a centralised view (READ-only)
// No login required for now (demo-friendly)

require_once "../db.php";

/* ------------------------------------
   Get all bookings + facility name
------------------------------------- */
$bookings = [];

$sql = "
  SELECT
    b.booking_id,
    f.facility_name,
    b.booking_date,
    b.start_time,
    b.end_time,
    b.purpose,
    b.status,
    b.created_at
  FROM bookings b
  JOIN facilities f ON b.facility_id = f.facility_id
  ORDER BY b.booking_date DESC, b.start_time DESC
";

$result = $conn->query($sql);

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
  }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Bookings</title>
  <link rel="stylesheet" href="../styles.css">
</head>
</head>

<body>

<!-- Navigation -->
<nav>
  <div class="title">Advanced Manufacturing</div>
  <ul class="nav-links">
    <li><a href="homepage.php">Facilities</a></li>
    <li><a href="user_bookings.php" class="active">My Bookings</a></li>
    <li><a href="#">Profile</a></li>
  </ul>
</nav>

<!-- Main content -->
<section class="hero bookings-hero">
<div class="container">

  <h2 class="bookings-title">My Bookings</h2>

  <?php if (count($bookings) === 0): ?>
    <div class="empty">No bookings found.</div>
  <?php else: ?>
    <div class="panel">
      <table>
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Facility</th>
            <th>Date</th>
            <th>Time</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?php echo htmlspecialchars($b["booking_id"]); ?></td>
              <td><?php echo htmlspecialchars($b["facility_name"]); ?></td>
              <td><?php echo htmlspecialchars($b["booking_date"]); ?></td>
              <td>
                <?php echo htmlspecialchars($b["start_time"]); ?>
                -
                <?php echo htmlspecialchars($b["end_time"]); ?>
              </td>
              <td><?php echo htmlspecialchars($b["purpose"]); ?></td>
              <td>
                <?php
                  $status = strtolower($b["status"]);
                  $class = "badge ";
                  if ($status === "pending") $class .= "pending";
                  elseif ($status === "approved") $class .= "approved";
                  elseif ($status === "rejected") $class .= "rejected";
                  elseif ($status === "cancelled") $class .= "cancelled";
                  else $class .= "cancelled";
                ?>
                <span class="<?php echo $class; ?>">
                  <?php echo htmlspecialchars($b["status"]); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($b["created_at"]); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>
</section>

</body>
</html>
