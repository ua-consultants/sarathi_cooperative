<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();
header('Content-Type: application/json');

// Add debugging information
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("Raw input: " . file_get_contents('php://input'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if id exists in POST
    if (!isset($_POST['id'])) {
        error_log("ID not found in POST data");
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID parameter missing']);
        exit;
    }
    
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    
    if ($id === false || $id === null) {
        error_log("Invalid ID provided: " . $_POST['id']);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid ID provided']);
        exit;
    }
    
    try {
        // First get the media info
        $stmt = $conn->prepare("SELECT media_url FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $announcement = $result->fetch_assoc();
        
        // Delete the media file if exists
        if ($announcement && $announcement['media_url']) {
            $file_path = '../../' . ltrim($announcement['media_url'], '/sarathi/');
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Delete the database record
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Failed to delete announcement');
        }
    } catch (Exception $e) {
        error_log("Delete error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request method. POST required.']);
}