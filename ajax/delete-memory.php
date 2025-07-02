<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        throw new Exception('Invalid request');
    }

    // Get image path before deletion
    $stmt = $conn->prepare("SELECT image_path FROM memories WHERE id = ?");
    $stmt->bind_param('i', $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($image = $result->fetch_assoc()) {
        // Delete the image file
        $imagePath = '../../uploads/' . $image['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete the record
    $stmt = $conn->prepare("DELETE FROM memories WHERE id = ?");
    $stmt->bind_param('i', $_POST['id']);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete memory');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}