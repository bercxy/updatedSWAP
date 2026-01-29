<?php
require '../config/config.php'; // connect to db

$error = '';   
$success = '';

// ==============================
// initialize variables
// ==============================
$full_name = $email = $contact = $purpose = $password = $confirm_password = $student_id = $staff_id = $company_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ==============================
    // collect and sanitize user input
    // ==============================
    $full_name = trim($_POST['full_name']);      // removes leading and trailing whitespace from user inputs
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $purpose = trim($_POST['purpose']);
    $student_id = trim($_POST['student_id']);
    $staff_id = trim($_POST['staff_id']);
    $company_name = trim($_POST['company_name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // ==============================
    // validate input
    // ==============================
    if (!preg_match('/^\d{8}$/', $contact)) {
        $error = "Contact number must be exactly 8 digits.";   // validates that contact number contains exactly 8 digits using regex
    }

    
    if (empty($error) && strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";   //error when password is less than 6 chars
    } elseif (empty($error) && $password !== $confirm_password) {   
        $error = "Passwords do not match.";                        // error when password and confirm password do not match
    }

    // ensures that user puts in at least one ID/company when registering for account so admin can verify identity
    if (empty($error) && empty($student_id) && empty($staff_id) && empty($company_name)) {
        $error = "You must provide a Student ID, Staff ID, or Company name.";
    }

    // ==============================
    // insert into user_requests table in amc_db if no errors occur
    // ==============================
    if (empty($error)) {
        // hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // uses prepared statement to insert request into user_requests table, preventing sql injection
        $stmt = $conn->prepare("
            INSERT INTO booking_system.user_requests
            (full_name, email, contact, purpose, student_id, staff_id, company_name, requested_password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssssssss",
            $full_name,
            $email,
            $contact,
            $purpose,
            $student_id,
            $staff_id,
            $company_name,
            $hashed_password
        );

        if ($stmt->execute()) {
            $success = "Your registration request has been submitted. The admin will review it soon.";
            $full_name = $email = $contact = $purpose = $password = $confirm_password = $student_id = $staff_id = $company_name = '';
            // clear form fields
        } else {
            $error = "There was an error submitting your registration. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Account</title>
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

    <!-- registration form -->
    <div class="page-container">
        <div class="request-card">
            <h2>Register Account</h2>

            <?php if (!empty($error)): ?>
                <p style="color:red; text-align:left;"><?php echo $error; ?></p>  <!-- display error message right before the form -->
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <p style="color:green; text-align:left;"><?php echo $success; ?></p> <!-- display success message right before the form -->
            <?php endif; ?>

            <form method="POST" action="">   <!-- action is empty so that form submits to same page -->

                <!--htmlspecialchars is used to convert special characters into literal text-->
                <label>Full Name:</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>

                <label>Email:</label>
                <input type="email" name="email" placeholder="e.g. ChrisLan@.tp.edu.sg" value="<?php echo htmlspecialchars($email); ?>" required>

                <label>Contact number:</label>                              <!-- Client-side regex validation -->
                <input type="text" name="contact" placeholder="8-digit number" pattern="\d{8}" title="Please enter an 8-digit number" value="<?php echo htmlspecialchars($contact); ?>" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <label>Confirm Password:</label>
                <input type="password" name="confirm_password" required>

                <label>Purpose of access:</label>
                <input type="text" name="purpose" placeholder="Briefly state the purpose" value="<?php echo htmlspecialchars($purpose); ?>" required>

                <label>Student ID:</label>
                <input type="text" name="student_id" placeholder="Fill this in if you are a student" value="<?php echo htmlspecialchars($student_id); ?>">

                <label>Staff ID:</label>
                <input type="text" name="staff_id" placeholder="Fill this in if you are staff" value="<?php echo htmlspecialchars($staff_id); ?>">

                <label>Company name:</label>
                <input type="text" name="company_name" placeholder="Fill this in if you are an industry partner" value="<?php echo htmlspecialchars($company_name); ?>">

                <button type="submit" class="request-btn">Register</button>
            </form>
        </div>
    </div>
</body>
</html>


