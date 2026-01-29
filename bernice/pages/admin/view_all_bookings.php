<?php
require_once "../../config/config.php";
require_once "../../includes/admin_guard.php";

$result = $conn->query("
    SELECT booking_id, user_id, facility_id,
           booking_date, start_time, end_time, status
    FROM bookings
    ORDER BY booking_date ASC, start_time ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - All Bookings</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

<!-- NAV BAR (same structure & classes) -->
<div class="nav-bar">
    <div class="nav-bar-left">
        <h1>TP AMC Booking System - Admin Dashboard</h1>
    </div>

    <div class="nav-bar-right">
        <a class="nav-btn" href="/booking_system/bernice/pages/admin/admin_mainpage.php">Manage</a>
        <a class="nav-btn logout" href="/booking_system/mengyao/pages/logout.php">Logout</a>
    </div>
</div>

<div class="page-container">

    <!-- BOOKINGS TABLE CARD -->
    <div class="table-card">
        <h2>All Facility Bookings</h2>
        <p>Admin View: All bookings are validated before approval.</p>
        <br>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Facility ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['facility_id']) ?></td>
                        <td><?= htmlspecialchars($row['booking_date']) ?></td>
                        <td>
                            <?= substr($row['start_time'], 0, 5) ?>
                            -
                            <?= substr($row['end_time'], 0, 5) ?>
                        </td>
                        <td><?= ucfirst($row['status']) ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending'): ?>
                                <a href="approve_booking.php?id=<?= $row['booking_id'] ?>"
                                   class="btn btn-primary">
                                    Approve
                                </a>
                                <a href="reject_booking.php?id=<?= $row['booking_id'] ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Reject this booking?')">
                                    Reject
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">No bookings found</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
