<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM opportunities WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $opportunity = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'opportunity' => $opportunity]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Opportunity not found']);
        }
        
        $stmt->close();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>