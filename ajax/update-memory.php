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

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/memories/';
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            throw new Exception('Failed to upload image');
        }

        $imagePath = 'memories/' . $fileName;

        // Delete old image
        $stmt = $conn->prepare("SELECT image_path FROM memories WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        $oldImage = $stmt->get_result()->fetch_assoc()['image_path'];
        if ($oldImage && file_exists('../../uploads/' . $oldImage)) {
            unlink('../../uploads/' . $oldImage);
        }
    }

    // Update memory
    $query = "UPDATE memories SET 
              title = ?, description = ?, event_date = ?, 
              venue = ?, status = ?, updated_at = NOW()";
    $params = [$_POST['title'], $_POST['description'], $_POST['event_date'], 
               $_POST['venue'], $_POST['status']];
    $types = 'sssss';

    if ($imagePath) {
        $query .= ", image_path = ?";
        $params[] = $imagePath;
        $types .= 's';
    }

    $query .= " WHERE id = ?";
    $params[] = $_POST['id'];
    $types .= 'i';

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update memory');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}