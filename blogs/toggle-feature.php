<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blog_id = (int)$_POST['blog_id'];
    $featured = (int)$_POST['featured'];
    
    $stmt = $conn->prepare("UPDATE blogs SET is_featured = ? WHERE id = ?");
    $stmt->bind_param("ii", $featured, $blog_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update featured status']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}