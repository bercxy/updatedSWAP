<?php
// starts a secure session
session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Strict'
]);
session_start(); // starts a new session OR resumes an existing session if one already exists
require '../config/config.php'; // db connection

$error = ''; // initialize error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);            // removes leading and trailing whitespace from email and password value from login form
    $password = trim($_POST['password']);

    // uses prepared statement to prevent SQL injection so that concatenation of user input won't happen
    $stmt = $conn->prepare("SELECT id, email, password, role FROM booking_system.users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {  // check whether there is a user with the email exists in db (database)
        $user = $result->fetch_assoc();  // retrieve user data when email exists in db

        // compares user input password against the hashed password in db
        if (password_verify($password, $user['password'])) {
            // regenerate session ID to prevent fixation
            session_regenerate_id(true);

            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // converts role value to lowercase to prevent case sensitivity issues like 'Admin' gets redirected to admin dashboard but 'admin' doesn't
            $role = strtolower($user['role']);
            
            // redirect to the right pages based on role. e.g. admin gets redirected to admin dashboard page
            switch ($role) {
                case 'admin':
                    header('Location: /booking_system/bernice/pages/admin/admin_mainpage.php'); //absolute path used for redirection
                    exit;
                case 'supervisor':
                    header('Location: /booking_system/mengyao/pages/supervisor_dashboard.php'); //page to be coded by another teammate 
                    exit;
                default:
                    header('Location: /booking_system/hazel/index.php');  //page to be coded by another teammate 
                    exit;
                }
            } else {
                $error = "Invalid email or password."; // generic error is printed when user enters wrong password to prevent revealing which part was incorrect
            }
        } else {
            $error = "Invalid email or password."; // generic error printed when email not found to prevent revealing which part was incorrect
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | TP AMC Booking System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- navigation bar -->
    <div class="nav-bar">
        <div class="nav-bar-left">
            <h1>TP AMC Booking System</h1>
        </div>
        <div class="nav-bar-right">
            <a href="/booking_system/mengyao/pages/login.php">Home</a>
            <a href="/booking_system/mengyao/pages/about.php">About</a>
        </div>
    </div>

    <div class="page-container">
        <div class="login-card">
            
            <!-- left side -->
            <div class="login-form">
                <h2>Welcome to TP AMC</h2>
                <p class="words">Enter your email and password to access your account</p>

                <!-- display error when user enters wrong email or password -->
                <?php if (!empty($error)): ?>
                    <p style="color:red;"><?php echo $error; ?></p>
                <?php endif; ?>

                <!-- login form -->
                <form method="POST" action="/booking_system/mengyao/pages/login.php">
                    <label>Email:</label>
                    <input type="email" name="email" required>

                    <label>Password:</label>
                    <input type="password" name="password" required>

                    <button type="submit" class="login-btn">Login</button>
                    <p>Don't have an account? <a href="/booking_system/mengyao/pages/requestAccount.php">Register here</a></p>
                </form>
            </div>

            <!-- right side -->
            <div class="login-image">
                <img src="../images/amc.png" alt="AMC Logo">
            </div>
        </div>
    </div>
</body>
</html>
