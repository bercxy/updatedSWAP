<?php
use PHPUnit\Framework\TestCase;

// include config
require_once __DIR__ . '/../config/config.php';

class LoginTest extends TestCase {

    protected $testEmail = 'admin@example.com';
    protected $testPassword = 'admin123';

    protected function setUp(): void {
        global $conn;

        $hashed = password_hash($this->testPassword, PASSWORD_DEFAULT);

        // insert this record into users table
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE password=VALUES(password)");
        $stmt->bind_param("ssss", $fullName, $email, $hashed, $role);

        // set user data
        $fullName = 'Admin User';
        $email = $this->testEmail;
        $role = 'admin';

        $stmt->execute();
    }

    // test for valid user login
    public function testLoginValidUser() {
        global $conn;

        // prepared statement to fetch password and role for the test email
        $stmt = $conn->prepare("SELECT password, role FROM users WHERE email=?");
        $stmt->bind_param('s', $this->testEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $this->assertNotNull($user, 'User should exist'); // check whether user exists
        $this->assertTrue(password_verify($this->testPassword, $user['password']), 'Password should match'); //verify password
        $this->assertEquals('admin', $user['role'], 'Role should be admin'); //check role
    }
    
    // test for invalid user login
    public function testLoginInvalidUser() {
        global $conn;

        $email = 'nonexistent@example.com';
        
        // prepared statement to fetch user according to a non-existing email
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        $this->assertEquals(0, $result->num_rows, 'User should not exist');
    }

    // clean up test data after each test
    protected function tearDown(): void {
        global $conn;

        // delete the test admin user to clean up after the test
        $stmt = $conn->prepare("DELETE FROM users WHERE email=?");
        $stmt->bind_param('s', $this->testEmail);
        $stmt->execute();
    }
}
?>

