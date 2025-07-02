<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$response = ['uploaded' => false];

if (isset($_FILES['upload']) && $_FILES['upload']['error'] == 0) {
    $upload_dir = '../uploads/blogs/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $filename = time() . '_' . $_FILES['upload']['name'];
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $filepath)) {
        $response = [
            'uploaded' => true,
            'url' => 'https://sarathicooperative.org/admin/uploads/blogs/' . $filename
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);