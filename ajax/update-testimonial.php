<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id = (int)$_POST['id'];
        
        $stmt = $conn->prepare("UPDATE testimonials SET name=?, designation=?, company_name=?, description=?, status=? WHERE id=?");
        $stmt->bind_param("sssssi", 
            $_POST['name'],
            $_POST['designation'],
            $_POST['company_name'],
            $_POST['description'],
            $_POST['status'],
            $id
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Testimonial updated successfully';
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);