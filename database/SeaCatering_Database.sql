-- SEA Catering Database Schema
-- Subscription System

-- Create database
CREATE DATABASE IF NOT EXISTS sea_catering_db;
USE sea_catering_db;

-- Table for storing subscription plans and pricing
CREATE TABLE IF NOT EXISTS plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(50) NOT NULL,
    price_per_meal DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for storing customer subscriptions
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    plan_type ENUM('diet', 'protein', 'royal') NOT NULL,
    meal_types JSON NOT NULL COMMENT 'Array of selected meal types (breakfast, lunch, dinner)',
    delivery_days JSON NOT NULL COMMENT 'Array of selected delivery days',
    allergies TEXT,
    plan_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'pending', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for storing customer testimonials
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    status ENUM('approved', 'pending', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for tracking orders (future feature)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT,
    order_date DATE NOT NULL,
    delivery_date DATE NOT NULL,
    status ENUM('pending', 'preparing', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
);

-- Insert default plan data
INSERT INTO plans (plan_name, price_per_meal, description) VALUES
('diet', 30000.00, 'Perfect for weight management with balanced nutrition and portion control'),
('protein', 40000.00, 'High-protein meals ideal for fitness enthusiasts and muscle building'),
('royal', 60000.00, 'Premium meals with the finest ingredients and gourmet preparation');

-- Insert sample testimonials for initial display
INSERT INTO testimonials (customer_name, message, rating, status) VALUES
('Sarah Jakarta', 'Amazing food quality and the delivery is always on time! The Diet Plan helped me lose 5kg in 2 months.', 5, 'approved'),
('Budi Surabaya', 'The Protein Plan is perfect for my workout routine. Fresh ingredients and great taste!', 5, 'approved'),
('Maya Bandung', 'Royal Plan exceeded my expectations. Restaurant-quality meals delivered to my door!', 4, 'approved'),
('Ahmad Medan', 'Excellent service and the meals are always fresh. The customization options are great!', 5, 'approved'),
('Lisa Yogya', 'Been using SEA Catering for 6 months now. Consistent quality and amazing customer service.', 4, 'approved');

-- Create indexes for better performance
CREATE INDEX idx_subscriptions_phone ON subscriptions(phone_number);
CREATE INDEX idx_subscriptions_status ON subscriptions(status);
CREATE INDEX idx_testimonials_status ON testimonials(status);
CREATE INDEX idx_orders_subscription ON orders(subscription_id);
CREATE INDEX idx_orders_date ON orders(order_date);
