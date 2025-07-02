<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    // Input validation
    $requiredFields = ['title', 'description', 'achievement_date'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("The {$field} field is required");
        }
    }

    // Image validation
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Please select an image');
    }

    // Image type validation
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileType = $_FILES['image']['type'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid image type. Only JPG, JPEG, and PNG are allowed.');
    }

    // Create upload directory if it doesn't exist
    $uploadDir = '../../uploads/achievements/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
    $uploadFile = $uploadDir . $fileName;

    // Upload image
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        throw new Exception('Failed to upload image');
    }

    // Database insertion
    $stmt = $conn->prepare("
        INSERT INTO achievements (
            title, 
            description, 
            achievement_date, 
            image_path, 
            created_by, 
            status, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, 1, NOW())
    ");

    $imagePath = 'https://sarathicooperative.org/admin/uploads/achievements/' . $fileName;
    
    $stmt->bind_param(
        'ssssi',
        $_POST['title'],
        $_POST['description'],
        $_POST['achievement_date'],
        $imagePath,
        $_SESSION['user_id']
    );

    if (!$stmt->execute()) {
        // Clean up uploaded file if database insertion fails
        if (file_exists($uploadFile)) {
            unlink($uploadFile);
        }
        throw new Exception('Database error: ' . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Achievement added successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}