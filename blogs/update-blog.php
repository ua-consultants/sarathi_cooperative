<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blog_id = (int)$_POST['blog_id'];
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $author_name = $conn->real_escape_string($_POST['author_name']);
    $excerpt = $conn->real_escape_string($_POST['excerpt']);
    $category_id = (int)$_POST['category_id'];
    $status = $_POST['status'];
    
    // SEO metadata
    $meta_title = $conn->real_escape_string($_POST['meta_title'] ?: $title);
    $meta_description = $conn->real_escape_string($_POST['meta_description'] ?: $excerpt);
    $meta_keywords = $conn->real_escape_string($_POST['meta_keywords']);

    // Handle featured image upload if new image is provided
    if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $upload = uploadImage($_FILES['featured_image'], '../uploads/blogs/');
        if(!$upload['success']) {
            $response['message'] = $upload['message'];
            echo json_encode($response);
            exit;
        }
        $featured_image_sql = ", featured_image = '" . $conn->real_escape_string($upload['path']) . "'";
    } else {
        $featured_image_sql = "";
    }
    
    $sql = "UPDATE blogs SET 
            title = ?, 
            content = ?, 
            author_name = ?,
            excerpt = ?, 
            category_id = ?, 
            status = ?,
            meta_title = ?,
            meta_description = ?,
            meta_keywords = ?,
            updated_at = NOW()
            $featured_image_sql
            WHERE id = ? AND author_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissssis", 
        $title, 
        $content, 
        $author_name,
        $excerpt, 
        $category_id, 
        $status,
        $meta_title,
        $meta_description,
        $meta_keywords,
        $blog_id,
        $_SESSION['user_id']
    );
    
    if($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Blog updated successfully';
    } else {
        $response['message'] = "Error updating blog: " . $conn->error;
    }
}

header('Content-Type: application/json');
echo json_encode($response);