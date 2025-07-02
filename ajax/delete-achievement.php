<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('Achievement ID is required');
    }

    // Get image path before deletion
    $stmt = $conn->prepare("SELECT image_path FROM achievements WHERE id = ?");
    $stmt->bind_param('i', $_POST['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $achievement = $result->fetch_assoc();

    // Delete from database
    $stmt = $conn->prepare("DELETE FROM achievements WHERE id = ?");
    $stmt->bind_param('i', $_POST['id']);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete achievement');
    }

    // Delete image file if exists
    if ($achievement && $achievement['image_path']) {
        $imagePath = '../../uploads/' . $achievement['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}