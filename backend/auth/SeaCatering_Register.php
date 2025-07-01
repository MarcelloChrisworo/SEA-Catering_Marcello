<?php
/**
 * User Registration Endpoint
 */

// Include the database connection
require_once __DIR__ . '/../SeaCatering_Database.php';

header('Content-Type: application/json');

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);

// Basic validation
if (!isset($data['fullName'], $data['email'], $data['password'], $data['confirmPassword'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input. Please fill all fields.']);
    exit;
}

$fullName = trim($data['fullName']);
$email = trim($data['email']);
$password = $data['password'];
$confirmPassword = $data['confirmPassword'];

if (empty($fullName) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'error' => 'Password must be at least 8 characters long.']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'error' => 'Passwords do not match.']);
    exit;
}

try {
    $pdo = getDbConnection();

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'An account with this email already exists.']);
        exit;
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$fullName, $email, $passwordHash]);

    echo json_encode(['success' => true, 'message' => 'Registration successful! Redirecting to login...']);

} catch (PDOException $e) {
    // Log error to a file for debugging, don't show to user
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'A server error occurred. Please try again later.']);
}
?>
