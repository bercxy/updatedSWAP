<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/config.php';

class EditUserTest extends TestCase {

    public function testUpdateUser() {
        global $conn;

        // create dummy user
        $name = 'EditTest';
        $email = 'edit@example.com';
        $role = 'user';
        $hashed = password_hash('test123', PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);
        $stmt->execute();

        $user_id = $conn->insert_id;

        // update user by changing full name
        $new_name = 'UpdatedTest';
        $stmt_update = $conn->prepare("UPDATE users SET full_name=? WHERE id=?");
        $stmt_update->bind_param('si', $new_name, $user_id);
        $stmt_update->execute();

        // verify update in users table
        $stmt_check = $conn->prepare("SELECT full_name FROM users WHERE id=?");
        $stmt_check->bind_param('i', $user_id);
        $stmt_check->execute();
        $user = $stmt_check->get_result()->fetch_assoc();

        $this->assertEquals($new_name, $user['full_name'], 'Full name should be updated');

        // cleanup
        $stmt_delete = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt_delete->bind_param('i', $user_id);
        $stmt_delete->execute();
    }
}

