<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $location = trim($_POST['location']);
    $job_type = $_POST['job_type'];
    $salary_range = trim($_POST['salary_range']);
    $experience_required = trim($_POST['experience_required']);
    $department = trim($_POST['department']);
    $positions_available = intval($_POST['positions_available']) ?: 1;
    $application_deadline = !empty($_POST['application_deadline']) ? $_POST['application_deadline'] : null;
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($location)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }
    
    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO opportunities (title, description, requirements, location, job_type, salary_range, experience_required, department, positions_available, application_deadline, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sssssssssssi", $title, $description, $requirements, $location, $job_type, $salary_range, $experience_required, $department, $positions_available, $application_deadline, $status, $created_by);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Job opportunity posted successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        
        $stmt->close();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>