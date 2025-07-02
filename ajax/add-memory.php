<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Debug logging (remove in production)
    error_log("POST data: " . print_r($_POST, true));
    error_log("FILES data: " . print_r($_FILES, true));
    
    // Input validation
    $requiredFields = ['title', 'event_date', 'venue', 'memory_type'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("The {$field} field is required");
        }
    }
    
    $memoryType = $_POST['memory_type']; // 'single' or 'book'
    
    // Validate date format
    $eventDate = $_POST['event_date'];
    if (!DateTime::createFromFormat('Y-m-d', $eventDate)) {
        throw new Exception('Invalid date format');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = dirname(__FILE__) . '/../uploads/memories/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Check if directory is writable
    if (!is_writable($uploadDir)) {
        throw new Exception('Upload directory is not writable');
    }
    
    // Database connection check
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        if ($memoryType === 'single') {
            // Handle single image upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('No image file uploaded or upload error occurred');
            }
            
            $uploadedFile = processImageUpload($_FILES['image'], $uploadDir);
            
            // Insert single memory
            $stmt = $conn->prepare("
                INSERT INTO memories (
                    title, description, event_date, venue, image_path, cover_image,
                    type, total_pages, created_by, status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, 'single', 1, ?, 1, NOW())
            ");
            
            if (!$stmt) {
                throw new Exception('Database prepare failed: ' . $conn->error);
            }
            
            $description = ''; // Single memories don't have descriptions
            $stmt->bind_param(
                'ssssssi',
                $_POST['title'],
                $description,
                $eventDate,
                $_POST['venue'],
                $uploadedFile,
                $uploadedFile,
                $_SESSION['user_id']
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Database execution failed: ' . $stmt->error);
            }
            
            $memoryId = $conn->insert_id;
            $stmt->close();
            
        } else if ($memoryType === 'book') {
            // Handle multiple images upload for book
            if (!isset($_FILES['images']) || !is_array($_FILES['images']['name'])) {
                throw new Exception('No images uploaded for memory book');
            }
            
            $imageCount = count($_FILES['images']['name']);
            if ($imageCount === 0) {
                throw new Exception('No images selected for memory book');
            }
            
            if ($imageCount > 100) {
                throw new Exception('Maximum 100 images allowed per memory book');
            }
            
            $uploadedImages = [];
            $coverImage = '';
            
            // Process each uploaded image
            for ($i = 0; $i < $imageCount; $i++) {
                // Check if this file has an error
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue; // Skip files with errors
                }
                
                // Create a temporary $_FILES structure for single file processing
                $singleFile = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i]
                ];
                
                $uploadedFile = processImageUpload($singleFile, $uploadDir);
                $uploadedImages[] = $uploadedFile;
                
                // First image becomes the cover
                if (empty($coverImage)) {
                    $coverImage = $uploadedFile;
                }
            }
            
            if (empty($uploadedImages)) {
                throw new Exception('No valid images were uploaded');
            }
            
           // Insert memory book
$stmt = $conn->prepare("
    INSERT INTO memories (
        title, description, event_date, venue, image_path, cover_image,
        type, total_pages, created_by, status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, 'book', ?, ?, 1, NOW())
");

if (!$stmt) {
    throw new Exception('Database prepare failed: ' . $conn->error);
}

$description = ''; // Books don't have descriptions in this implementation
$totalPages = count($uploadedImages);

$stmt->bind_param(
    'ssssssii',  // Fixed: changed 'ssssssiі' to 'ssssssii'
    $_POST['title'],
    $description,
    $eventDate,
    $_POST['venue'],
    $coverImage, // image_path points to cover for books
    $coverImage,
    $totalPages,
    $_SESSION['user_id']
);

if (!$stmt->execute()) {
    throw new Exception('Database execution failed: ' . $stmt->error);
}

$memoryId = $conn->insert_id;
$stmt->close();

// Insert individual pages
$pageStmt = $conn->prepare("
    INSERT INTO memory_pages (memory_id, page_number, image_path, created_at)
    VALUES (?, ?, ?, NOW())
");

if (!$pageStmt) {
    throw new Exception('Failed to prepare page insert statement: ' . $conn->error);
}

foreach ($uploadedImages as $pageNumber => $imagePath) {
    $pageNum = $pageNumber + 1; // Pages start from 1
    $pageStmt->bind_param('iis', $memoryId, $pageNum, $imagePath); // Fixed: changed 'iіs' to 'iis'
    
    if (!$pageStmt->execute()) {
        throw new Exception('Failed to insert page ' . $pageNum . ': ' . $pageStmt->error);
    }
}

$pageStmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => $memoryType === 'book' ? 'Memory book created successfully' : 'Memory added successfully',
            'id' => $memoryId,
            'type' => $memoryType,
            'pages' => $memoryType === 'book' ? count($uploadedImages) : 1
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        
        // Clean up uploaded files if database insertion fails
        if (isset($uploadedImages)) {
            foreach ($uploadedImages as $imagePath) {
                $fullPath = $uploadDir . ltrim($imagePath, '/');
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        } else if (isset($uploadedFile)) {
            $fullPath = $uploadDir . ltrim($uploadedFile, '/');
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        throw $e;
    }
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Add Memory Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'file' => __FILE__,
            'line' => $e->getLine()
        ]
    ]);
} catch (Error $e) {
    // Catch fatal errors
    error_log("Add Memory Fatal Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'A fatal error occurred: ' . $e->getMessage()
    ]);
}

/**
 * Process and validate image upload
 */
function processImageUpload($file, $uploadDir) {
    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('Image file is too large');
            case UPLOAD_ERR_PARTIAL:
                throw new Exception('Image upload was interrupted');
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No image file selected');
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new Exception('Server configuration error: no temp directory');
            case UPLOAD_ERR_CANT_WRITE:
                throw new Exception('Server configuration error: cannot write file');
            default:
                throw new Exception('Image upload failed with error code: ' . $file['error']);
        }
    }
    
    // Image type validation
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
    $fileType = $file['type'];
    $fileName = $file['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileType, $allowedTypes) || !in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Invalid image type. Only JPG, JPEG, and PNG are allowed.');
    }
    
    // File size validation (e.g., max 10MB per image)
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        throw new Exception('Image file is too large. Maximum size is 10MB per image.');
    }
    
    // Generate unique filename with proper sanitization
    $sanitizedFileName = preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
    $uniqueFileName = uniqid() . '_' . time() . '_' . $sanitizedFileName;
    $uploadFile = $uploadDir . $uniqueFileName;
    
    // Upload image
    if (!move_uploaded_file($file['tmp_name'], $uploadFile)) {
        throw new Exception('Failed to upload image to server');
    }
    
    // Return relative path for database storage
    return '/' . $uniqueFileName;
}
?>