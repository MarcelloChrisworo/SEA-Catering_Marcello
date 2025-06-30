<?php
/**
 * SEA Catering Database Connection
 * Subscription System Backend
 */

// Database configuration
$servername = "localhost";
$username = "root";           // Default XAMPP MySQL username
$password = "";               // Default XAMPP MySQL password (empty)
$dbname = "sea_catering_db";
$port = 3306;                 // Default MySQL port

// Create connection
try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Uncomment for debugging
    // echo "Database connection successful!<br>";
    
} catch(PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    
    // For development, show detailed error. In production, show generic message.
    if (isset($_GET['debug']) || $_ENV['APP_ENV'] === 'development') {
        die("Database Connection Error: " . $e->getMessage());
    } else {
        die("Sorry, we're experiencing technical difficulties. Please try again later.");
    }
}

/**
 * Helper function to execute prepared statements safely
 */
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Query Error: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

/**
 * Helper function to get last insert ID
 */
function getLastInsertId($pdo) {
    return $pdo->lastInsertId();
}

/**
 * Helper function to close database connection
 */
function closeConnection($pdo) {
    $pdo = null;
}

// Set timezone for consistent datetime handling
date_default_timezone_set('Asia/Jakarta');
?>
