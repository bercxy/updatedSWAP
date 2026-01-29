<?php
session_start();

// only admin can access this page, preventing unauthorized access and privilege escalation
if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /booking_system/mengyao/pages/login.php");
    exit;
}

require_once '../config/config.php'; // connect to db

// initialize variables
$message = "";
$user = [
    'id' => '',
    'email' => '',
    'full_name' => '',
    'role' => ''
];

// check if id is provided in URL
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']); // convert to integer to prevent SQL injection
    
    // uses prepared statement to fetch user data from users table
    $stmt = $conn->prepare("SELECT * FROM booking_system.users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc(); // retrieve user data if user is found
    } else {
        die("User not found");  // terminate if user not found
    }
}

// handle form submission for updating or deleting user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = intval($_POST['id']);
    $email = trim($_POST['email']);  // remove leading and trailing whitespace
    $full_name = trim($_POST['full_name']);
    $role = trim($_POST['role']);
    $password = trim($_POST['password']); // only used when admin changes the password
    $operation = $_POST['operation']; // update or delete
    
    // prevent assigning admin role to other users
    if ($role === 'admin') {
    $message = "You are not allowed to assign admin role.";
    $role = $user['role']; // changes back to the original role of the user
    }

    // UPDATE user in users table if operation is update
    if ($operation === 'update') {
        if (!empty($password)) {
            // if password field is not empty, hash the new password for secure storage
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // updates the user record in users table including changing password
            $stmt = $conn->prepare("UPDATE booking_system.users SET email=?, full_name=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $email, $full_name, $role, $hashed, $id);
        } else {
            // updates the user record in users table without changing password
            $stmt = $conn->prepare("UPDATE booking_system.users SET email=?, full_name=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $email, $full_name, $role, $id);
        }

        // message printed when update is successful or error occurs
        if ($stmt->execute()) {
            $message = "User updated successfully.";
            $user['email'] = $email;
            $user['full_name'] = $full_name;
            $user['role'] = $role;
        } else {
            $message = "Error updating user: " . $conn->error;
        }
    }
    // DELETE user from users table if operation is delete
    elseif ($operation === 'delete') {
        if ($id == $_SESSION['user_id']) {
            // prevent admin from deleting their own account
            $message = "You cannot delete your own account.";
        } else {
            // deletes the user record from users table
            $stmt = $conn->prepare("DELETE FROM booking_system.users WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "User deleted successfully.";
                // Clear form fields
                $user = ['id'=>'', 'email'=>'', 'full_name'=>'', 'role'=>''];
            } else {
                $message = "Error deleting user: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="nav-bar">
        <div class="nav-bar-left">
            <h1>TP AMC Booking System - Admin</h1>
        </div>
        <div class="nav-bar-right">
            <a href="adminDashboard.php">Back to Dashboard</a>
            <a href="/booking_system/mengyao/pages/logout.php">Logout</a>
        </div>
    </div>

    <div class="page-container">
        <div class="request-card">
            <h2>Edit User</h2>

            <?php if (!empty($message)): ?>
                <p style="color: green; text-align: left; margin-bottom:10px;">
                    <?= htmlspecialchars($message) ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <!-- convert into an integer before using it for query, preventing sql injection -->
                <!-- escapes special characters to prevent XSS attacks -->
                <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                <label>Full Name:</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

                <label>Role:</label>
                <select name="role" required>
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="supervisor" <?= $user['role'] === 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                </select>

                <label>Password (leave blank if not changing):</label>
                <input type="password" name="password" placeholder="Enter new password">

                <!-- Dropdown for operation -->
                <label>Operation:</label>
                <select name="operation" required>
                    <option value="">Select Operation</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                </select>

                <button type="submit" class="request-btn">Submit</button>
            </form>
        </div>
    </div>
</body>
</html>


