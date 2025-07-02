<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    try {
        $id = (int)$_POST['id'];
        
        $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Testimonial deleted successfully';
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);