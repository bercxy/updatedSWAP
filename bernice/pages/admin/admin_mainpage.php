<?php
session_start();

if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /booking_system/mengyao/pages/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Main Page</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

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
    <div class="admin-main">

        <div class="admin-card">
            <h2>Manage All Users</h2>
            <p>Approve account requests, assign roles, and edit users.</p>
            <a href="/booking_system/mengyao/pages/adminDashboard.php">
                Go to User Management
            </a>
        </div>

        <div class="admin-card">
            <h2>Manage All Bookings</h2>
            <p>View, approve, and reject all facility bookings.</p>
            <a href="/booking_system/bernice/pages/admin/view_all_bookings.php">
                Go to Booking Management
            </a>
        </div>

    </div>
</div>

</body>
</html>
