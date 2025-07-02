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
    
    if (empty($id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid opportunity ID']);
        exit;
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // First, delete all applications for this job (if any)
        $stmt1 = $conn->prepare("DELETE FROM job_applications WHERE job_id = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        $stmt1->close();
        
        // Then delete the opportunity
        $stmt2 = $conn->prepare("DELETE FROM opportunities WHERE id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        
        if ($stmt2->affected_rows > 0) {
            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Job opportunity and all associated applications deleted successfully']);
        } else {
            $conn->rollback();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Opportunity not found']);
        }
        
        $stmt2->close();
        
    } catch (Exception $e) {
        $conn->rollback();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>