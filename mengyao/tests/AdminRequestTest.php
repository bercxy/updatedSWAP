<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/config.php';

class AdminRequestTest extends TestCase {

    public function testApproveRequest() {
        global $conn;

        // insert dummy request into user_requests
        $name = 'Dummy';
        $email = 'dummy@example.com';
        $hashed = password_hash('dummy123', PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO user_requests (full_name, email, requested_password) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $hashed);
        $stmt->execute();

        $request_id = $conn->insert_id;

        // fetch request
        $stmt_select = $conn->prepare("SELECT * FROM user_requests WHERE id=?");
        $stmt_select->bind_param('i', $request_id);
        $stmt_select->execute();
        $req = $stmt_select->get_result()->fetch_assoc();

        // approve request → insert into users
        $role = 'user';
        $stmt_insert = $conn->prepare(
            "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt_insert->bind_param(
            "ssss",
            $req['full_name'],
            $req['email'],
            $req['requested_password'],
            $role
        );
        $stmt_insert->execute();

        // delete request from user_requests
        $stmt_delete = $conn->prepare("DELETE FROM user_requests WHERE id=?");
        $stmt_delete->bind_param('i', $request_id);
        $stmt_delete->execute();

        // verify user exists
        $stmt_check = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt_check->bind_param('s', $email);
        $stmt_check->execute();
        $res = $stmt_check->get_result();

        $this->assertEquals(1, $res->num_rows, 'User should be approved and inserted');

        // cleanup
        $stmt_cleanup = $conn->prepare("DELETE FROM users WHERE email=?");
        $stmt_cleanup->bind_param('s', $email);
        $stmt_cleanup->execute();
    }
}

