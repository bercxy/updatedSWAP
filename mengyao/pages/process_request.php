<?php
session_start();

// only admin can access
if (!isset($_SESSION['user_role']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: /booking_system/mengyao/pages/login.php");
    exit;
}

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $request_id = intval($_POST['request_id']);
    $role = $_POST['role'];

    $allowed_roles = ['student', 'staff', 'industry'];
    if (!in_array($role, $allowed_roles)) $role = 'student';

    // APPROVE request
    if (isset($_POST['approve'])) {

        $stmt = $conn->prepare("SELECT * FROM booking_system.user_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $request = $result->fetch_assoc();

            // Hash the requested password before inserting
            $hashed_password = password_hash($request['requested_password'], PASSWORD_DEFAULT);

            $stmt_insert = $conn->prepare(
                "INSERT INTO booking_system.users (full_name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt_insert->bind_param(
                "ssss",
                $request['full_name'],
                $request['email'],
                $hashed_password,
                $role
            );

            if (!$stmt_insert->execute()) {
                die("Insert failed: " . $stmt_insert->error);
            }

            // Delete the request after approval
            $stmt_delete = $conn->prepare("DELETE FROM booking_system.user_requests WHERE id = ?");
            $stmt_delete->bind_param("i", $request_id);
            if (!$stmt_delete->execute()) {
                die("Delete failed: " . $stmt_delete->error);
            }
        }

    } elseif (isset($_POST['reject'])) {
        $stmt_delete = $conn->prepare("DELETE FROM booking_system.user_requests WHERE id = ?");
        $stmt_delete->bind_param("i", $request_id);
        if (!$stmt_delete->execute()) {
            die("Delete failed: " . $stmt_delete->error);
        }
    }

    // Redirect back to dashboard
    header("Location: /booking_system/mengyao/pages/adminDashboard.php");
    exit;
}

