<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'biodata_system');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Function to create database and tables if they don't exist
function initializeDatabase($showMessages = false) {
    // First connect without database name to create database
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
    if ($conn->query($sql) === TRUE) {
        if ($showMessages) echo "Database created successfully or already exists<br>";
    } else {
        if ($showMessages) echo "Error creating database: " . $conn->error . "<br>";
    }
    
    // Close connection and reconnect with database
    $conn->close();
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        if ($showMessages) echo "Users table created successfully or already exists<br>";
    } else {
        if ($showMessages) echo "Error creating users table: " . $conn->error . "<br>";
    }
    
    // Create biodata table
    $sql = "CREATE TABLE IF NOT EXISTS biodata (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) UNSIGNED NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        father_name VARCHAR(100),
        mother_name VARCHAR(100),
        date_of_birth DATE,
        gender ENUM('Male', 'Female', 'Other'),
        address TEXT,
        phone VARCHAR(20),
        email VARCHAR(100),
        linkedin VARCHAR(255),
        github VARCHAR(255),
        education TEXT,
        skills TEXT,
        languages VARCHAR(255),
        marital_status VARCHAR(20),
        hobbies TEXT,
        blood_group VARCHAR(5),
        website VARCHAR(255),
        profile_picture VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        if ($showMessages) echo "Biodata table created successfully or already exists<br>";
    } else {
        if ($showMessages) echo "Error creating biodata table: " . $conn->error . "<br>";
    }
    
    $conn->close();
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Validate password strength
function validatePassword($password) {
    // Check minimum length (8 characters)
    if (strlen($password) < 8) {
        return false;
    }
    
    // Check for uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    
    // Check for lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    
    // Check for number
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    // Check for special character
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        return false;
    }
    
    return true;
}
?>
