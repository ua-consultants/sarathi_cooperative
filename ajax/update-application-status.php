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
    $status = $input['status'];
    
    // Validate input
    if (empty($id) || empty($status)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }
    
    // Validate status value
    $valid_statuses = ['pending', 'reviewed', 'shortlisted', 'rejected', 'hired'];
    if (!in_array($status, $valid_statuses)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid status value']);
        exit;
    }
    
    // Update application status
    $stmt = $conn->prepare("UPDATE job_applications SET application_status = ?, reviewed_date = CURRENT_TIMESTAMP, reviewed_by = ? WHERE id = ?");
    
    if ($stmt) {
        $reviewed_by = $_SESSION['user_id'];
        $stmt->bind_param("sii", $status, $reviewed_by, $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Application status updated successfully']);
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