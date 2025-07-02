<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    if (empty($_POST['id'])) {
        throw new Exception('E-book ID is required');
    }

    // Input validation
    $requiredFields = ['title', 'author'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            throw new Exception("The {$field} field is required");
        }
    }

    // Handle book_date with proper validation
    $book_date = null;
    if (!empty($_POST['book_date'])) {
        $date = DateTime::createFromFormat('Y-m-d', $_POST['book_date']);
        if ($date) {
            $book_date = $date->format('Y-m-d');
        } else {
            throw new Exception("Invalid date format. Please use YYYY-MM-DD.");
        }
    }

    $coverImagePath = null;
    $pdfFilePath = null;
    
    // Handle cover image upload if new image is provided
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($_FILES['cover_image']['type'], $allowedTypes)) {
            throw new Exception('Invalid cover image type. Only JPG, JPEG, and PNG are allowed.');
        }

        $uploadDir = '../uploads/ebooks/covers/';
        $fileName = uniqid() . '_' . basename($_FILES['cover_image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadFile)) {
            throw new Exception('Failed to upload cover image');
        }

        $coverImagePath = $fileName; // Store just the filename, not the full path

        // Delete old cover image
        $stmt = $conn->prepare("SELECT cover_image FROM ebooks WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        $oldImage = $stmt->get_result()->fetch_assoc();
        
        if ($oldImage && $oldImage['cover_image']) {
            $oldImagePath = '../uploads/ebooks/covers/' . $oldImage['cover_image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }

    // Handle PDF file upload if new file is provided
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['pdf_file']['type'] !== 'application/pdf') {
            throw new Exception('Invalid file type. Only PDF files are allowed.');
        }

        $uploadDir = '../uploads/ebooks/files/';
        $fileName = uniqid() . '_' . basename($_FILES['pdf_file']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadFile)) {
            throw new Exception('Failed to upload PDF file');
        }

        $pdfFilePath = $fileName; // Store just the filename, not the full path

        // Delete old PDF file
        $stmt = $conn->prepare("SELECT file_path FROM ebooks WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);
        $stmt->execute();
        $oldFile = $stmt->get_result()->fetch_assoc();
        
        if ($oldFile && $oldFile['file_path']) {
            $oldFilePath = '../uploads/ebooks/files/' . $oldFile['file_path'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }
    }

    // Build dynamic UPDATE query based on what needs to be updated
    $updateFields = [];
    $params = [];
    $types = '';

    // Always update these basic fields
    $updateFields[] = "title = ?";
    $params[] = $_POST['title'];
    $types .= 's';

    $updateFields[] = "author = ?";
    $params[] = $_POST['author'];
    $types .= 's';

    // Add optional fields if they exist in POST data
    if (isset($_POST['description'])) {
        $updateFields[] = "description = ?";
        $params[] = $_POST['description'];
        $types .= 's';
    }

    if (isset($_POST['category'])) {
        $updateFields[] = "category = ?";
        $params[] = $_POST['category'];
        $types .= 's';
    }

    if (isset($_POST['language'])) {
        $updateFields[] = "language = ?";
        $params[] = $_POST['language'];
        $types .= 's';
    }

    if (isset($_POST['status'])) {
        $updateFields[] = "status = ?";
        $params[] = $_POST['status'];
        $types .= 's';
    }

    if (isset($_POST['visibility'])) {
        $updateFields[] = "visibility = ?";
        $params[] = $_POST['visibility'];
        $types .= 's';
    }

    // Handle book_date - only update if provided
    if ($book_date !== null) {
        $updateFields[] = "book_date = ?";
        $params[] = $book_date;
        $types .= 's';
    }

    // Add file fields if new files were uploaded
    if ($coverImagePath !== null) {
        $updateFields[] = "cover_image = ?";
        $params[] = $coverImagePath;
        $types .= 's';
    }

    if ($pdfFilePath !== null) {
        $updateFields[] = "file_path = ?";
        $params[] = $pdfFilePath;
        $types .= 's';
    }

    // Always update the updated_at timestamp
    $updateFields[] = "updated_at = NOW()";

    // Add the ID parameter for WHERE clause
    $params[] = $_POST['id'];
    $types .= 'i';

    // Build and execute the query
    $sql = "UPDATE ebooks SET " . implode(', ', $updateFields) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update e-book: ' . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception('No changes were made or e-book not found');
    }

    echo json_encode([
        'success' => true,
        'message' => 'E-book updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>