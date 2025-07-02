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
    $id = intval($_POST['id']);
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    $category = trim($_POST['category']);
    $sort_order = intval($_POST['sort_order']);
    $status = $_POST['status'];
    
    // Validate required fields
    if ($id <= 0 || empty($question) || empty($answer) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
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
        // First check if FAQ exists
        $check_stmt = $conn->prepare("SELECT id FROM faqs WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'FAQ not found']);
            exit;
        }
        
        $check_stmt->close();
        
        // Update FAQ
        $stmt = $conn->prepare("UPDATE faqs SET question = ?, answer = ?, category = ?, sort_order = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssisi", $question, $answer, $category, $sort_order, $status, $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'FAQ updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update FAQ']);
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