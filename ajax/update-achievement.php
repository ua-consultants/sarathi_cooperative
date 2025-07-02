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

    // Input validation
    $requiredFields = ['title', 'description', 'achievement_date', 'category', 'status'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            throw new Exception("The {$field} field is required");
        }
    }

    $imagePath = null;
    
    // Handle image upload if new image is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            throw new Exception('Invalid image type. Only JPG, JPEG, and PNG are allowed.');
        }

        $uploadDir = '../uploads/achievements/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            throw new Exception('Failed to upload image');
        }

        $imagePath = 'achievements/' . $fileName;

        // Delete old image
        $stmt = $conn->prepare("SELECT image_path FROM achievements WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        $oldImage = $stmt->get_result()->fetch_assoc();
        
        if ($oldImage && $oldImage['image_path']) {
            $oldImagePath = '../uploads/' . $oldImage['image_path'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }

    // Update database
    if ($imagePath) {
        $stmt = $conn->prepare("
            UPDATE achievements 
            SET title = ?, description = ?, achievement_date = ?, 
                category = ?, status = ?, image_path = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param(
            'ssssisi',
            $_POST['title'],
            $_POST['description'],
            $_POST['achievement_date'],
            $_POST['category'],
            $_POST['status'],
            $imagePath,
            $_POST['id']
        );
    } else {
        $stmt = $conn->prepare("
            UPDATE achievements 
            SET title = ?, description = ?, achievement_date = ?, 
                category = ?, status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param(
            'ssssii',
            $_POST['title'],
            $_POST['description'],
            $_POST['achievement_date'],
            $_POST['category'],
            $_POST['status'],
            $_POST['id']
        );
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to update achievement');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Achievement updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}