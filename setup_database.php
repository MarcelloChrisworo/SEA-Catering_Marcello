<?php
/**
 * SEA Catering Database Setup Script
 * Run this once to create/update the database and tables for all features, including Level 5.
 */

echo "<h1>🔥 SEA Catering Database Setup</h1>";

try {
    // --- 1. CONNECT TO MYSQL SERVER ---
    $pdo = new PDO("mysql:host=localhost;port=3306;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected to MySQL server<br>";

    // --- 2. CREATE DATABASE ---
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sea_catering_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'sea_catering_db' created or already exists<br>";

    // --- 3. CONNECT TO THE SPECIFIC DATABASE ---
    $pdo->exec("USE sea_catering_db");
    echo "✅ Connected to 'sea_catering_db'<br>";

    // --- 4. CREATE USERS TABLE (with role for admin) ---
    $usersSQL = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($usersSQL);
    echo "✅ 'users' table created or already exists<br>";

    // --- 5. CREATE SUBSCRIPTIONS TABLE (with all new fields) ---
    $subscriptionsSQL = "
    CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        plan_type ENUM('diet', 'protein', 'royal') NOT NULL,
        meal_types JSON NOT NULL,
        delivery_days JSON NOT NULL,
        allergies TEXT,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('active', 'pending', 'paused', 'cancelled') DEFAULT 'pending',
        pause_start_date DATE NULL,
        pause_end_date DATE NULL,
        reactivated_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($subscriptionsSQL);
    echo "✅ 'subscriptions' table created or already exists<br>";

    // --- 6. CREATE TESTIMONIALS TABLE ---
    $testimonialsSQL = "
    CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        status ENUM('approved', 'pending', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($testimonialsSQL);
    echo "✅ 'testimonials' table created or already exists<br>";

    // --- 7. INSERT SAMPLE TESTIMONIALS (if table is empty) ---
    $stmt = $pdo->query("SELECT COUNT(*) FROM testimonials");
    if ($stmt->fetchColumn() == 0) {
        $sampleTestimonials = [
            ["Sarah Chen", "Amazing healthy meals! The protein plan really helped me reach my fitness goals.", 5],
            ["Ahmad Rahman", "SEA Catering has been a game-changer for my busy lifestyle. The royal plan is absolutely delicious!", 5],
            ["Maria Santos", "Love the variety and the nutritional information provided. The diet plan is perfect.", 4]
        ];
        $insertTestimonial = "INSERT INTO testimonials (customer_name, message, rating, status) VALUES (?, ?, ?, 'approved')";
        $stmt = $pdo->prepare($insertTestimonial);
        foreach ($sampleTestimonials as $testimonial) {
            $stmt->execute($testimonial);
        }
        echo "✅ Sample testimonials inserted<br>";
    } else {
        echo "ℹ️ Testimonials table already has data, skipping sample insertion.<br>";
    }
    
    // --- 8. CREATE A DEFAULT ADMIN USER (if it doesn't exist) ---
    $adminEmail = 'admin@seacatering.com';
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    if (!$stmt->fetch()) {
        $adminPassword = 'adminpassword'; // Use a strong password in a real project!
        $adminPasswordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
        $adminSQL = "INSERT INTO users (full_name, email, password_hash, role) VALUES ('Admin User', ?, ?, 'admin')";
        $pdo->prepare($adminSQL)->execute([$adminEmail, $adminPasswordHash]);
        echo "✅ Default admin user created. Email: <strong>$adminEmail</strong>, Password: <strong>$adminPassword</strong><br>";
    } else {
        echo "ℹ️ Admin user already exists, skipping creation.<br>";
    }

    echo "<br><h2>🎉 SUCCESS! Database setup complete!</h2>";
    echo "<p><strong>Your SEA Catering database is now fully configured for Level 5.</strong></p>";
    echo "<a href='http://localhost/phpmyadmin' target='_blank'>🔗 View Database in phpMyAdmin</a>";

} catch(PDOException $e) {
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p><strong>Please ensure your XAMPP MySQL server is running and try again.</strong></p>";
}
?>
