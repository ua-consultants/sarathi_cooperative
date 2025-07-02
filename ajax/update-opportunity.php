<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle POST request - update opportunity details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get input data - check both JSON and form data
        $input = null;
        
        // Try to get JSON input first
        $json_input = file_get_contents('php://input');
        if (!empty($json_input)) {
            $input = json_decode($json_input, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON data: ' . json_last_error_msg());
            }
        } else {
            // Fallback to POST data
            $input = $_POST;
        }
        
        if (empty($input)) {
            throw new Exception('No input data received');
        }
        
        // Build dynamic update query based on provided fields
        $updateFields = [];
        $params = [];
        $types = '';
        
        // Common fields that might be updated
        $allowedFields = [
            'title', 'description', 'company', 'location', 'salary', 
            'job_type', 'requirements', 'benefits', 'application_deadline',
            'status', 'contact_email', 'contact_phone', 'experience_level',
            'category', 'skills_required', 'posted_date', 'is_active'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field]) && $input[$field] !== '') {
                $updateFields[] = "`$field` = ?";
                $params[] = $input[$field];
                $types .= 's'; // assuming most fields are strings
            }
        }
        
        // Handle numeric fields specifically
        if (isset($input['salary_min']) && $input['salary_min'] !== '') {
            $updateFields[] = "`salary_min` = ?";
            $params[] = floatval($input['salary_min']);
            $types .= 'd';
        }
        
        if (isset($input['salary_max']) && $input['salary_max'] !== '') {
            $updateFields[] = "`salary_max` = ?";
            $params[] = floatval($input['salary_max']);
            $types .= 'd';
        }
        
        if (empty($updateFields)) {
            throw new Exception('No valid fields to update. Received fields: ' . implode(', ', array_keys($input)));
        }
        
        // Add updated_at timestamp
        $updateFields[] = "`updated_at` = NOW()";
        
        // Create the SQL query
        $sql = "UPDATE `opportunities` SET " . implode(', ', $updateFields);
        
        // Log the query for debugging
        error_log("SQL Query: " . $sql);
        error_log("Parameters: " . print_r($params, true));
        error_log("Types: " . $types);
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $conn->error);
        }
        
        // Bind parameters if any
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute query: ' . $stmt->error);
        }
        
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Opportunity updated successfully',
            'affected_rows' => $affected_rows,
            'updated_fields' => array_keys($input)
        ]);
        
    } catch (Exception $e) {
        error_log("Update opportunity error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Error updating opportunity: ' . $e->getMessage(),
            'debug_info' => [
                'received_data' => $input ?? 'No data',
                'server_method' => $_SERVER['REQUEST_METHOD']
            ]
        ]);
    }
}

// Handle GET request for testing
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Simple test endpoint
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => 'Update opportunity endpoint is working',
        'method' => 'GET'
    ]);
}

// Handle unsupported methods
else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed: ' . $_SERVER['REQUEST_METHOD']
    ]);
}

$conn->close();
?>