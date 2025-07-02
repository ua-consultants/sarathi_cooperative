<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();
header('Content-Type: application/json');

// Add debugging
error_log("Delete E-book Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method. POST required.');
    }
    
    // Validate ID parameter
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('E-book ID is required');
    }
    
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id === false || $id === null) {
        throw new Exception('Invalid e-book ID provided');
    }
    
    // Get file paths before deletion
    $stmt = $conn->prepare("SELECT cover_image, file_path FROM ebooks WHERE id = ?");
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to fetch e-book data: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $ebook = $result->fetch_assoc();
    
    if (!$ebook) {
        throw new Exception('E-book not found');
    }
    
    // Delete from database first
    $deleteStmt = $conn->prepare("DELETE FROM ebooks WHERE id = ?");
    if (!$deleteStmt) {
        throw new Exception('Database prepare failed: ' . $conn->error);
    }
    
    $deleteStmt->bind_param('i', $id);
    
    if (!$deleteStmt->execute()) {
        throw new Exception('Failed to delete e-book: ' . $deleteStmt->error);
    }
    
    // Check if any rows were affected
    if ($deleteStmt->affected_rows === 0) {
        throw new Exception('No e-book found with the given ID');
    }
    
    // Delete files if they exist (fixed file paths)
    if ($ebook) {
        $files = [
            '../uploads/ebooks/covers/' . $ebook['cover_image'],  // Added missing slash
            '../uploads/ebooks/files/' . $ebook['file_path']       // Added missing slash
        ];
        
        foreach ($files as $file) {
            if (!empty($ebook[basename(dirname($file))]) && file_exists($file)) {
                if (!unlink($file)) {
                    error_log("Failed to delete file: $file");
                }
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'E-book deleted successfully']);
    
} catch (Exception $e) {
    error_log("Delete E-book Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}