<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'u828878874_sarathi_new');
define('DB_PASS', '#Sarathi@2025');
define('DB_NAME', 'u828878874_sarathi_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Set timezone
date_default_timezone_set('Asia/Kolkata');