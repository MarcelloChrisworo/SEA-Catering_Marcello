<?php
/**
 * SEA Catering Testimonials Handler
 * Process testimonial submissions and retrieve testimonials
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'SeaCatering_Database.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'POST') {
        // Handle testimonial submission
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Alternative: Handle form data
        if (!$input) {
            $input = $_POST;
        }
        
        // Validate required fields
        $required_fields = ['customerName', 'reviewMessage', 'rating'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Sanitize data
        $customerName = trim($input['customerName']);
        $reviewMessage = trim($input['reviewMessage']);
        $rating = intval($input['rating']);
        
        // Validate data
        if (strlen($customerName) < 2 || strlen($customerName) > 100) {
            throw new Exception("Name must be between 2-100 characters");
        }
        
        if (strlen($reviewMessage) < 10 || strlen($reviewMessage) > 1000) {
            throw new Exception("Review message must be between 10-1000 characters");
        }
        
        if ($rating < 1 || $rating > 5) {
            throw new Exception("Rating must be between 1-5");
        }
        
        // Insert testimonial into database
        $sql = "INSERT INTO testimonials (customer_name, message, rating, status) VALUES (?, ?, ?, 'approved')";
        $params = [$customerName, $reviewMessage, $rating];
        
        $stmt = executeQuery($pdo, $sql, $params);
        $testimonialId = getLastInsertId($pdo);
        
        // Success response
        $response = [
            'success' => true,
            'message' => 'Thank you for your testimonial! Your review has been added.',
            'testimonial_id' => $testimonialId,
            'data' => [
                'id' => $testimonialId,
                'name' => $customerName,
                'message' => $reviewMessage,
                'rating' => $rating,
                'status' => 'approved'
            ]
        ];
        
        http_response_code(201);
        echo json_encode($response);
        
    } elseif ($method === 'GET') {
        // Retrieve approved testimonials
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        
        // Validate limit and offset
        $limit = max(1, min($limit, 50)); // Between 1-50
        $offset = max(0, $offset);
        
        $sql = "SELECT id, customer_name, message, rating, created_at 
                FROM testimonials 
                WHERE status = 'approved' 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = executeQuery($pdo, $sql, [$limit, $offset]);
        $testimonials = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM testimonials WHERE status = 'approved'";
        $countStmt = executeQuery($pdo, $countSql);
        $totalCount = $countStmt->fetch()['total'];
        
        // Format testimonials for frontend
        $formattedTestimonials = array_map(function($testimonial) {
            return [
                'id' => $testimonial['id'],
                'name' => $testimonial['customer_name'],
                'message' => $testimonial['message'],
                'rating' => intval($testimonial['rating']),
                'date' => date('M d, Y', strtotime($testimonial['created_at']))
            ];
        }, $testimonials);
        
        $response = [
            'success' => true,
            'data' => $formattedTestimonials,
            'pagination' => [
                'total' => intval($totalCount),
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $totalCount
            ]
        ];
        
        echo json_encode($response);
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    error_log("Testimonial Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
} catch (PDOException $e) {
    error_log("Database Error in Testimonials: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred. Please try again later.'
    ]);
}

closeConnection($pdo);
?>
