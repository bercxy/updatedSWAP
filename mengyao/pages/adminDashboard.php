<?php
session_start();

// only admin can access
if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /booking_system/mengyao/pages/login.php");
    exit;
}


require_once '../config/config.php';

// fetch pending account requests
$requests = $conn->query("SELECT * FROM booking_system.user_requests ORDER BY created_at ASC");

// fetch existing users (with search)
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("SELECT * FROM booking_system.users WHERE full_name LIKE ? OR email LIKE ? OR role LIKE ?");
    $like = "%" . $search . "%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $users = $stmt->get_result();
} else {
    $users = $conn->query("SELECT * FROM booking_system.users");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="nav-bar">
        <div class="nav-bar-left">
            <h1>TP AMC Booking System - Admin Dashboard</h1>
        </div>
        <div class="nav-bar-right">
            <a href="/booking_system/mengyao/pages/logout.php">Logout</a>
        </div>
    </div>

    <div class="page-container">
        <!-- pending requests table -->
        <div class="table-card">
            <h2>Pending Account Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Purpose</th>
                        <th>Assign Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($requests && $requests->num_rows > 0): ?>
                    <?php while ($row = $requests->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['purpose']) ?></td>
                            <td>
                                <form method="POST" action="process_request.php">
                                    <select name="role" required>
                                        <option value="student">Student</option>
                                        <option value="staff">Academic Staff</option>
                                        <option value="industry">Industry Partner</option>
                                    </select>
                            </td>
                            <td>
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="approve" class="request-btn">Approve</button>
                                    <button type="submit" name="reject" class="request-btn delete-btn">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No pending requests</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- existing users table -->
        <div class="table-card">
            <h2>Existing Users</h2>
            <form method="GET" style="margin-bottom: 15px; display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Search by name, email, or role" value="<?= htmlspecialchars($search) ?>" style="padding: 8px; width: 250px;">
                <button type="submit" class="request-btn">Search</button>
                <a href="adminDashboard.php" class="request-btn delete-btn">Reset</a>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users && $users->num_rows > 0): ?>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td>
                                <a href="editUsers.php?id=<?= $user['id'] ?>" class="request-btn">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No users found</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
