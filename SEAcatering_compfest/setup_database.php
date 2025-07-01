<?php
/**
 * SEA Catering Database Setup Script
 * Run this once to create the database and tables
 */

echo "<h1>🔥 SEA Catering Database Setup</h1>";

try {
    // Connect to MySQL server (without specific database)
    $pdo = new PDO("mysql:host=localhost;port=3306;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to MySQL server<br>";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sea_catering_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'sea_catering_db' created<br>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sea_catering_db;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connected to sea_catering_db<br>";
    
    // Create subscriptions table
    $subscriptionsSQL = "
    CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        plan_type ENUM('diet', 'protein', 'royal') NOT NULL,
        meal_types JSON NOT NULL COMMENT 'Array of selected meal types',
        delivery_days JSON NOT NULL COMMENT 'Array of selected delivery days',
        allergies TEXT,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('active', 'pending', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($subscriptionsSQL);
    echo "✅ Subscriptions table created<br>";
    
    // Create testimonials table
    $testimonialsSQL = "
    CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        status ENUM('approved', 'pending', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($testimonialsSQL);
    echo "✅ Testimonials table created<br>";

    // Create users table for authentication
    $usersSQL = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($usersSQL);
    echo "✅ Users table created<br>";

    // Insert sample testimonials
    $sampleTestimonials = [
        ["Sarah Chen", "Amazing healthy meals! The protein plan really helped me reach my fitness goals. Fresh ingredients and great taste!", 5],
        ["Ahmad Rahman", "SEA Catering has been a game-changer for my busy lifestyle. The royal plan is absolutely delicious and convenient!", 5],
        ["Maria Santos", "Love the variety and the nutritional information provided with each meal. The diet plan is perfect for my weight loss journey.", 4],
        ["David Kim", "Excellent service and quality food. The delivery is always on time and the packaging keeps everything fresh.", 5],
        ["Priya Sharma", "The vegetarian options are fantastic! Great portion sizes and the flavors are authentic and delicious.", 4]
    ];
    
    $insertTestimonial = "INSERT INTO testimonials (customer_name, message, rating, status) VALUES (?, ?, ?, 'approved')";
    $stmt = $pdo->prepare($insertTestimonial);
    
    foreach ($sampleTestimonials as $testimonial) {
        $stmt->execute($testimonial);
    }
    
    echo "✅ Sample testimonials inserted<br>";
    
    echo "<br><h2>🎉 SUCCESS! Database setup complete!</h2>";
    echo "<p><strong>Your SEA Catering database is ready to use!</strong></p>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li>Test the subscription form</li>";
    echo "<li>Submit testimonials</li>";
    echo "<li>View testimonials from database</li>";
    echo "</ul>";
    echo "<br><a href='SeaCateringSubscription.html'>🔗 Test Subscription Form</a><br>";
    echo "<a href='SeaCateringMenu.html'>🔗 Test Menu & Testimonials</a><br>";
    echo "<a href='http://localhost/phpmyadmin' target='_blank'>🔗 View Database in phpMyAdmin</a>";
    
} catch(PDOException $e) {
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p><strong>Make sure XAMPP MySQL is running!</strong></p>";
    echo "<p>1. Open XAMPP Control Panel</p>";
    echo "<p>2. Start Apache and MySQL services</p>";
    echo "<p>3. Refresh this page</p>";
}
?>
