<?php
/**
 * SEA Catering - Get Testimonials Handler
 * Backend Integration
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    // Include database connection
    require_once 'SeaCatering_Database.php';
    
    // Get query parameters
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    $minRating = isset($_GET['min_rating']) ? intval($_GET['min_rating']) : 1;
    
    // Validate parameters
    $limit = max(1, min(50, $limit)); // Between 1 and 50
    $offset = max(0, $offset); // Non-negative
    $minRating = max(1, min(5, $minRating)); // Between 1 and 5
    
    // Get approved testimonials
    $sql = "SELECT id, customer_name, message, rating, created_at 
            FROM testimonials 
            WHERE status = 'approved' AND rating >= :min_rating
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset";
    
    $params = [
        ':min_rating' => $minRating,
        ':limit' => $limit,
        ':offset' => $offset
    ];
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':min_rating', $minRating, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $testimonials = $stmt->fetchAll();
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM testimonials WHERE status = 'approved' AND rating >= :min_rating";
    $countStmt = executeQuery($pdo, $countSql, [':min_rating' => $minRating]);
    $total = $countStmt->fetch()['total'];
    
    // Format testimonials for frontend
    $formattedTestimonials = array_map(function($testimonial) {
        return [
            'id' => intval($testimonial['id']),
            'name' => $testimonial['customer_name'],
            'message' => $testimonial['message'],
            'rating' => intval($testimonial['rating']),
            'created_at' => $testimonial['created_at'],
            'formatted_date' => date('F j, Y', strtotime($testimonial['created_at']))
        ];
    }, $testimonials);
    
    // Success response
    $response = [
        'success' => true,
        'data' => $formattedTestimonials,
        'pagination' => [
            'total' => intval($total),
            'limit' => $limit,
            'offset' => $offset,
            'has_more' => ($offset + $limit) < $total
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error response
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => 'Failed to retrieve testimonials'
    ];
    
    // Log error for debugging
    error_log("Get Testimonials Error: " . $e->getMessage());
    
    echo json_encode($response);
}
?>
