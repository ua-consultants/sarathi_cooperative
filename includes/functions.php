<?php
function validateLogin($username, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? AND status = 1 LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($user = $result->fetch_assoc()) {
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if(!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    requireLogin();
    if(!isAdmin()) {
        header("Location: dashboard.php");
        exit();
    }
}

function logout() {
    session_destroy();
    header("Location: login.php");
    exit();
}


function createSlug($string) {
    // Convert to lowercase
    $string = strtolower($string);
    
    // Replace non-alphanumeric characters with hyphens
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    
    // Remove multiple consecutive hyphens
    $string = preg_replace('/-+/', '-', $string);
    
    // Remove leading and trailing hyphens
    $string = trim($string, '-');
    
    return $string;
}

function uploadImage($file, $directory) {
    $response = [
        'success' => false,
        'path' => '',
        'url' => '',
        'message' => ''
    ];
    
    // Create directory if it doesn't exist
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }
    
    // Validate file type
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        $response['message'] = 'Invalid file type. Allowed types: ' . implode(', ', $allowed);
        return $response;
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $response['message'] = 'File size too large. Maximum size: 5MB';
        return $response;
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . createSlug(pathinfo($file['name'], PATHINFO_FILENAME)) . '.' . $ext;
    $filepath = $directory . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $response['success'] = true;
        $response['path'] = str_replace('../../', '/', $filepath); // Convert to web path
        $response['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $response['path'];
        return $response;
    }
    
    $response['message'] = 'Error uploading file';
    return $response;
}