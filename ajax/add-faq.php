<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    $status = $_POST['status'];
    
    // Validate required fields
    if (empty($question) || empty($answer) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'Question, answer, and category are required']);
        exit;
    }
    
    // Validate status
    if (!in_array($status, ['active', 'inactive'])) {
        $status = 'active';
    }
    
    // Validate sort order
    if ($sort_order < 1) {
        $sort_order = 1;
    }
    
    try {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO faqs (question, answer, category, status) VALUES (?, ?, ?)");
        $stmt->bind_param("sssis", $question, $answer, $status);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'FAQ added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add FAQ']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>