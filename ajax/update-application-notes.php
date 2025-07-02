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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id']);
    $notes = trim($input['notes']);
    
    // Validate input
    if (empty($id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
        exit;
    }
    
    // Update application notes
    $stmt = $conn->prepare("UPDATE job_applications SET notes = ?, reviewed_by = ?, reviewed_date = CURRENT_TIMESTAMP WHERE id = ?");
    
    if ($stmt) {
        $reviewed_by = $_SESSION['user_id'];
        $stmt->bind_param("sii", $notes, $reviewed_by, $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Notes updated successfully']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Application not found or no changes made']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>