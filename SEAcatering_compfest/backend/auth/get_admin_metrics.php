<?php
session_start();
include 'db_connection.php';

// Role check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit();
}

// Metrics calculation
$total_subscriptions = $conn->query("SELECT COUNT(*) as count FROM subscriptions")->fetch_assoc()['count'];
$mrr = $conn->query("SELECT SUM(price) as total FROM subscriptions WHERE status = 'active'")->fetch_assoc()['total'];
$reactivations = $conn->query("SELECT COUNT(*) as count FROM subscriptions WHERE status = 'active' AND updated_at > created_at")->fetch_assoc()['count'];
$paused = $conn->query("SELECT COUNT(*) as count FROM subscriptions WHERE status = 'paused'")->fetch_assoc()['count'];
$cancelled = $conn->query("SELECT COUNT(*) as count FROM subscriptions WHERE status = 'cancelled'")->fetch_assoc()['count'];

echo json_encode([
    'success' => true,
    'metrics' => [
        'total_subscriptions' => $total_subscriptions,
        'mrr' => $mrr,
        'reactivations' => $reactivations,
        'paused' => $paused,
        'cancelled' => $cancelled
    ]
]);
?>
