<?php
/**
 * User Login Endpoint
 */

require_once __DIR__ . '/../SeaCatering_Database.php';

// Start a secure session
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['email'], $data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input.']);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
    exit;
}

try {
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Password is correct, regenerate session ID for security
        session_regenerate_id(true);

        // Store user info in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['logged_in'] = true;

        // The "token" sent to the client is the session ID
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful!',
            'token' => session_id(), // Send session ID as token
            'user' => [
                'name' => $user['full_name']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid email or password.']);
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error. Please try again later.']);
}
?>
