<?php
/**
 * SEA Catering Subscription Handler
 * Process subscription form submissions
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once 'SeaCatering_Database.php';

try {
    // Get and validate input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Alternative: Handle form data
    if (!$input) {
        $input = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['fullName', 'phoneNumber', 'plan', 'mealTypes', 'deliveryDays', 'totalPrice'];
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }
    
    // Sanitize and validate data
    $fullName = trim($input['fullName']);
    $phoneNumber = trim($input['phoneNumber']);
    $plan = strtolower(trim($input['plan']));
    $allergies = isset($input['allergies']) ? trim($input['allergies']) : null;
    $totalPrice = floatval($input['totalPrice']);
    
    // Validate plan type
    $validPlans = ['diet', 'protein', 'royal'];
    if (!in_array($plan, $validPlans)) {
        throw new Exception("Invalid plan type");
    }
    
    // Validate and process meal types
    $mealTypes = $input['mealTypes'];
    if (!is_array($mealTypes) || empty($mealTypes)) {
        throw new Exception("At least one meal type must be selected");
    }
    $validMealTypes = ['breakfast', 'lunch', 'dinner'];
    foreach ($mealTypes as $mealType) {
        if (!in_array($mealType, $validMealTypes)) {
            throw new Exception("Invalid meal type: $mealType");
        }
    }
    
    // Validate and process delivery days
    $deliveryDays = $input['deliveryDays'];
    if (!is_array($deliveryDays) || empty($deliveryDays)) {
        throw new Exception("At least one delivery day must be selected");
    }
    $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    foreach ($deliveryDays as $day) {
        if (!in_array($day, $validDays)) {
            throw new Exception("Invalid delivery day: $day");
        }
    }
    
    // Validate phone number format (Indonesian format)
    if (!preg_match('/^[0-9]{10,13}$/', $phoneNumber)) {
        throw new Exception("Invalid phone number format");
    }
    
    // Get plan price for validation
    $planPrices = [
        'diet' => 30000,
        'protein' => 40000,
        'royal' => 60000
    ];
    
    $planPrice = $planPrices[$plan];
    
    // Verify price calculation
    $calculatedPrice = $planPrice * count($mealTypes) * count($deliveryDays) * 4.3;
    if (abs($totalPrice - $calculatedPrice) > 1) { // Allow small rounding differences
        throw new Exception("Price calculation mismatch");
    }
    
    // Insert subscription into database
    $sql = "INSERT INTO subscriptions (full_name, phone_number, plan_type, meal_types, delivery_days, allergies, plan_price, total_price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $params = [
        $fullName,
        $phoneNumber,
        $plan,
        json_encode($mealTypes),
        json_encode($deliveryDays),
        $allergies,
        $planPrice,
        round($totalPrice, 2)
    ];
    
    $stmt = executeQuery($pdo, $sql, $params);
    $subscriptionId = getLastInsertId($pdo);
    
    // Success response
    $response = [
        'success' => true,
        'message' => 'Subscription submitted successfully!',
        'subscription_id' => $subscriptionId,
        'data' => [
            'id' => $subscriptionId,
            'name' => $fullName,
            'phone' => $phoneNumber,
            'plan' => ucfirst($plan) . ' Plan',
            'meal_types' => array_map('ucfirst', $mealTypes),
            'delivery_days' => array_map('ucfirst', $deliveryDays),
            'total_price' => round($totalPrice, 2),
            'status' => 'pending'
        ]
    ];
    
    http_response_code(201);
    echo json_encode($response);
    
} catch (Exception $e) {
    // Error response
    error_log("Subscription Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    
} catch (PDOException $e) {
    // Database error
    error_log("Database Error in Subscription: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred. Please try again later.'
    ]);
}

closeConnection($pdo);
?>
