<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $conn->prepare("INSERT INTO testimonials (name, designation, company_name, description, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", 
            $_POST['name'],
            $_POST['designation'],
            $_POST['company_name'],
            $_POST['description'],
            $_POST['status']
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Testimonial added successfully';
        } else {
            throw new Exception($conn->error);
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);