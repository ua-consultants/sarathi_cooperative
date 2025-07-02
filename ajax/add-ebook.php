<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Set JSON response header
header('Content-Type: application/json');

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Initialize variables with default values
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author = isset($_POST['author']) ? trim($_POST['author']) : '';
    $book_date = null; // Changed from empty string to null
    
    // Fix: Proper date handling
    if (!empty($_POST['book_date'])) {
        $date = DateTime::createFromFormat('Y-m-d', $_POST['book_date']);
        if ($date) {
            $book_date = $date->format('Y-m-d');
        } else {
            throw new Exception("Invalid date format. Please use YYYY-MM-DD.");
        }
    }
    
    $visibility = isset($_POST['visibility']) ? $_POST['visibility'] : 'public';
    $status = 1; // Active by default

    // Validate required fields
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    
    if (empty($author)) {
        throw new Exception('Author is required');
    }

    // Validate visibility
    if (!in_array($visibility, ['public', 'members'])) {
        $visibility = 'public'; // Default to public if invalid
    }

    // Handle file uploads
    $cover_image = '';
    $file_path = '';
    $upload_dir = '../uploads/ebooks/';
    $covers_dir = $upload_dir . 'covers/';
    $files_dir = $upload_dir . 'files/';

    // Create upload directories if they don't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    if (!file_exists($covers_dir)) {
        mkdir($covers_dir, 0755, true);
    }
    if (!file_exists($files_dir)) {
        mkdir($files_dir, 0755, true);
    }

    // Handle cover image upload
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $cover_tmp = $_FILES['cover_image']['tmp_name'];
        $cover_name = $_FILES['cover_image']['name'];
        $cover_ext = strtolower(pathinfo($cover_name, PATHINFO_EXTENSION));
        
        // Validate image file
        $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($cover_ext, $allowed_image_types)) {
            throw new Exception('Invalid cover image format. Please use JPG, PNG, GIF, or WebP');
        }
        
        // Check file size (5MB limit)
        if ($_FILES['cover_image']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Cover image size must be less than 5MB');
        }
        
        // Generate unique filename
        $cover_image = 'cover_' . time() . '_' . uniqid() . '.' . $cover_ext;
        $cover_path = $covers_dir . $cover_image;
        
        if (!move_uploaded_file($cover_tmp, $cover_path)) {
            throw new Exception('Failed to upload cover image');
        }
    }

    // Handle PDF file upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $pdf_tmp = $_FILES['pdf_file']['tmp_name'];
        $pdf_name = $_FILES['pdf_file']['name'];
        $pdf_ext = strtolower(pathinfo($pdf_name, PATHINFO_EXTENSION));
        
        // Validate PDF file
        if ($pdf_ext !== 'pdf') {
            throw new Exception('Only PDF files are allowed');
        }
        
        // Check file size (50MB limit)
        if ($_FILES['pdf_file']['size'] > 50 * 1024 * 1024) {
            throw new Exception('PDF file size must be less than 50MB');
        }
        
        // Generate unique filename
        $file_path = 'ebook_' . time() . '_' . uniqid() . '.pdf';
        $pdf_path = $files_dir . $file_path;
        
        if (!move_uploaded_file($pdf_tmp, $pdf_path)) {
            throw new Exception('Failed to upload PDF file');
        }
    }

    // Fix: Prepare SQL statement with proper NULL handling for book_date
    $sql = "INSERT INTO ebooks (title, author, visibility, status, created_at";
    $values = "VALUES (?, ?, ?, ?, NOW()";
    $params = [$title, $author, $visibility, $status];
    $types = "sssi";

    // Add book_date only if it has a value
    if ($book_date !== null) {
        $sql .= ", book_date";
        $values .= ", ?";
        $params[] = $book_date;
        $types .= "s";
    }

    // Add optional fields if they have values
    if (!empty($cover_image)) {
        $sql .= ", cover_image";
        $values .= ", ?";
        $params[] = $cover_image;
        $types .= "s";
    }

    if (!empty($file_path)) {
        $sql .= ", file_path";
        $values .= ", ?";
        $params[] = $file_path;
        $types .= "s";
    }

    // Complete the SQL statement
    $sql .= ") " . $values . ")";

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    // Bind parameters dynamically
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        // If database insert fails, clean up uploaded files
        if (!empty($cover_image) && file_exists($cover_path)) {
            unlink($cover_path);
        }
        if (!empty($file_path) && file_exists($pdf_path)) {
            unlink($pdf_path);
        }
        throw new Exception('Database execution error: ' . $stmt->error);
    }

    $ebook_id = $conn->insert_id;
    $stmt->close();

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'E-book added successfully',
        'ebook_id' => $ebook_id,
        'data' => [
            'title' => $title,
            'author' => $author,
            'book_date' => $book_date,
            'visibility' => $visibility,
            'cover_image' => $cover_image,
            'file_path' => $file_path
        ]
    ]);

} catch (Exception $e) {
    // Error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    
    // Log the error (optional)
    error_log("Add E-book Error: " . $e->getMessage() . " - User: " . ($_SESSION['user_id'] ?? 'Unknown'));
}

// Close database connection
if (isset($conn)) {
    $conn->close();
}
?>