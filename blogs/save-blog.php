<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Handle file upload
        $featured_image = '';
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
            $upload_dir = '../uploads/blogs/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $filename = time() . '_' . basename($_FILES['featured_image']['name']);
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $filepath)) {
                // Use relative path or ensure public access
                $featured_image = '/admin/uploads/blogs/' . $filename;
            } else {
                throw new Exception('Failed to move uploaded file');
            }
        } elseif (isset($_FILES['featured_image'])) {
            throw new Exception('Upload error: ' . $_FILES['featured_image']['error']);
        }

        // Prepare blog data
        $title = $conn->real_escape_string($_POST['title']);
        $slug = createSlug($title);
        $content = $conn->real_escape_string($_POST['content']);
        $excerpt = $conn->real_escape_string($_POST['excerpt']);
        $category_id = (int)$_POST['category_id'];
        $status = $conn->real_escape_string($_POST['status']);
        $author_id = $_SESSION['user_id'];

        // ✅ Fixed: Corrected author_name input
        $author_name = $conn->real_escape_string($_POST['author_name']);

        // SEO metadata
        $meta_title = $conn->real_escape_string($_POST['meta_title'] ?: $_POST['title']);
        $meta_description = $conn->real_escape_string($_POST['meta_description']);
        $meta_keywords = $conn->real_escape_string($_POST['meta_keywords']);

        // Insert blog - ✅ Fixed missing quotes around author_name
        $sql = "INSERT INTO blogs (
            title, slug, content, excerpt, featured_image, 
            category_id, author_id, status, 
            meta_title, meta_description, meta_keywords,
            created_at, updated_at, author_name
        ) VALUES (
            '$title', '$slug', '$content', '$excerpt', '$featured_image',
            $category_id, $author_id, '$status',
            '$meta_title', '$meta_description', '$meta_keywords',
            NOW(), NOW(), '$author_name'
        )";

        if ($conn->query($sql)) {
            $response['success'] = true;
            $response['message'] = 'Blog created successfully';
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);