<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blog_id = (int)$_POST['blog_id'];
    
    // Get blog info for image deletion
    $blog = $conn->query("SELECT featured_image FROM blogs WHERE id = $blog_id AND author_id = {$_SESSION['user_id']}")->fetch_assoc();
    
    if ($blog) {
        // Delete the featured image
        if ($blog['featured_image'] && file_exists('../../' . ltrim($blog['featured_image'], '/'))) {
            unlink('../../' . ltrim($blog['featured_image'], '/'));
        }
        
        // Delete the blog
        if ($conn->query("DELETE FROM blogs WHERE id = $blog_id")) {
            $response['success'] = true;
            $response['message'] = 'Blog deleted successfully';
        } else {
            $response['message'] = 'Error deleting blog';
        }
    } else {
        $response['message'] = 'Blog not found or unauthorized';
    }
}

header('Content-Type: application/json');
echo json_encode($response);