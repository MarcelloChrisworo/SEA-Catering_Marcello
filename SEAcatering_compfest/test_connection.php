<?php
/**
 * Database Connection Test
 * Use this to verify XAMPP MySQL is working
 */

echo "<h2>SEA Catering - Database Connection Test</h2>";

try {
    // Test basic MySQL connection first
    $pdo = new PDO("mysql:host=localhost;port=3306", "root", "");
    echo "✅ MySQL Connection: SUCCESS<br>";
    
    // Try to create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sea_catering_db");
    echo "✅ Database Creation: SUCCESS<br>";
    
    // Connect to our specific database
    $pdo = new PDO("mysql:host=localhost;port=3306;dbname=sea_catering_db", "root", "");
    echo "✅ Database Connection: SUCCESS<br>";
    
    echo "<br><strong>🎉 Everything is working! You can now run the SQL file.</strong>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<br><strong>Make sure XAMPP MySQL is running!</strong>";
}
?>

<br><br>
<a href="http://localhost/phpmyadmin" target="_blank">🔗 Open phpMyAdmin</a>
