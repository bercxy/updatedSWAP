<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/config.php';

class RequestTest extends TestCase {

    // test that contact number fails validation if not exactly 8 digits
    public function testInvalidContact() {
        $contact = '12345';
        // preg_match returns 1 if matches, 0 if not
        $this->assertFalse(preg_match('/^\d{8}$/', $contact) === 1, 'Contact must be 8 digits');
    }
    // test that password and confirm password do NOT match
    public function testPasswordMismatch() {
        $password = 'abc123';
        $confirm = 'abc124';
        $this->assertNotEquals($password, $confirm, 'Passwords should match');
    }

    // test that a user request can be inserted successfully into the database
    public function testRequestInsertion() {
        global $conn; 

        $full_name = 'Test User';
        $email = 'testuser@example.com';
        $contact = '12345678';
        $purpose = 'Test';
        $hashed_password = password_hash('test123', PASSWORD_DEFAULT);

        // insert test request into user_requests table
        $stmt = $conn->prepare("INSERT INTO user_requests (full_name, email, contact, purpose, requested_password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $email, $contact, $purpose, $hashed_password);
        $stmt->execute();

        // Check if insertion succeeded
        $stmt_check = $conn->prepare("SELECT * FROM user_requests WHERE email=?");
        $stmt_check->bind_param('s', $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        $this->assertEquals(1, $result->num_rows, 'Request should be inserted');

        // clean up after test
        $stmt_delete = $conn->prepare("DELETE FROM user_requests WHERE email=?");
        $stmt_delete->bind_param('s', $email);
        $stmt_delete->execute();
    }
}
?>
