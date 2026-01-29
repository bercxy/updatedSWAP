<?php
require 'config/config.php'; // connect to DB

// Array of users to insert
$users = [
    [
        'email' => 'ChrisLan@tp.edu.sg',
        'full_name' => 'Chris Lan',
        'role' => 'User',
        'password' => 'password123'
    ],
    [
        'email' => 'JacobLee@tp.edu.sg',
        'full_name' => 'Jacob Lee',
        'role' => 'Supervisor',
        'password' => 'password123'
    ],
    [
        'email' => 'admin@tp.edu.sg',
        'full_name' => 'Admin User',
        'role' => 'Admin',
        'password' => 'password123'
    ]
];

foreach ($users as $user) {
    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param('s', $user['email']);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // Hash the password
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $user['email'], $hashedPassword, $user['full_name'], $user['role']);
        $stmt->execute();

        echo "User {$user['email']} created successfully!<br>";
    } else {
        echo "User {$user['email']} already exists.<br>";
    }
}

echo "<br>All done!";
?>
