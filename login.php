<?php
session_start();
header('Content-Type: application/json');

// Database configuration - Update these with your actual database credentials
$host = 'localhost';
$dbname = 'u828878874_sarathi_db';
$username = 'u828878874_sarathi_new';
$password = '#Sarathi@2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);
    
    if (empty($input_username) || empty($input_password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        exit();
    }
    
    try {
        // Fetch user from database
        $stmt = $pdo->prepare("SELECT * FROM members WHERE username = ? AND status = 'active'");
        $stmt->execute([$input_username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Verify password
            // If passwords are stored as plain text (not recommended in production)
            if ($user['password'] === $input_password) {
                // If passwords are hashed, use: password_verify($input_password, $user['password'])
                
                // Set session variables
                $_SESSION['member_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['profile_image'] = $user['profile_image'];
                $_SESSION['member_type'] = $user['member_type'];
                $_SESSION['logged_in'] = true;
                
                echo json_encode(['success' => true, 'message' => 'Login successful']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password, Please contact the Admin, in case you have forgot your password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password, Please contact the Admin, in case you have forgot your password']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>