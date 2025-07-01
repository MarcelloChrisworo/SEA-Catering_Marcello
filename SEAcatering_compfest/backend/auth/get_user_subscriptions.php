<?php
/**
 * Get User Subscriptions Endpoint
 * Fetches all subscriptions for the currently logged-in user.
 */

session_start();
require_once __DIR__ . '/../SeaCatering_Database.php';

header('Content-Type: application/json');

// 1. Check for authentication
if (!isset($_SESSION['user_id'])) {
    // Not logged in
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];

try {
    // 2. Get database connection
    $pdo = getDbConnection();

    // 3. Prepare and execute the query
    $stmt = $pdo->prepare("
        SELECT 
            s.id, 
            s.plan_type, 
            s.meal_types, 
            s.delivery_days, 
            s.total_price, 
            s.status,
            DATE_FORMAT(s.pause_start_date, '%Y-%m-%d') as pause_start_date,
            DATE_FORMAT(s.pause_end_date, '%Y-%m-%d') as pause_end_date,
            DATE_FORMAT(s.created_at, '%d %b %Y') as subscription_date
        FROM 
            subscriptions s
        WHERE 
            s.user_id = :user_id 
        ORDER BY 
            s.subscription_date DESC
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    // 4. Fetch results
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Return the data
    echo json_encode(['success' => true, 'subscriptions' => $subscriptions]);

} catch (PDOException $e) {
    // Log the real error for debugging
    error_log("Database Error in get_user_subscriptions.php: " . $e->getMessage());
    
    // Send a generic error to the client
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'A server error occurred while fetching your subscriptions.']);
}
?>
