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
    
    // Validate ID
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid FAQ ID']);
        exit;
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
        
        // Delete FAQ
        $stmt = $conn->prepare("DELETE FROM faqs WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'FAQ deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete FAQ']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete FAQ']);
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