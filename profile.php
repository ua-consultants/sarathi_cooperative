<?php
session_start();
// Check login
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /');
    exit();
}

// Database config
$host = 'localhost';
$dbname = 'u828878874_sarathi_db';
$username = 'u828878874_sarathi_new';
$password = '#Sarathi@2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed");
}

$user_id = $_SESSION['member_id'];
$message = '';
$messageType = '';

// FETCH USER DATA FIRST - This was missing!
try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("User not found");
    }
} catch(PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Helper function to handle file uploads
function handleFileUpload($fieldName, $currentPath, $uploadDir) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES[$fieldName]['name']);
        $targetFile = $uploadDir . uniqid() . '-' . $fileName;

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$fieldName]['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (!in_array($mime, $allowedMimeTypes)) {
            throw new Exception("Invalid file type for $fieldName");
        }

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFile)) {
            return $targetFile;
        } else {
            throw new Exception("Failed to move uploaded file for $fieldName");
        }
    }

    return $currentPath; // Keep existing file if no upload
}

function handleImageUpload($fieldName, $currentPath, $uploadDir) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($_FILES[$fieldName]['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $targetFile = $uploadDir . uniqid() . '-' . $fileName;

        // Validate file type for images
        $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedImageTypes)) {
            throw new Exception("Invalid image type. Only JPG, JPEG, PNG, and GIF are allowed.");
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$fieldName]['tmp_name']);
        finfo_close($finfo);

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($mime, $allowedMimeTypes)) {
            throw new Exception("Invalid image format for $fieldName");
        }

        // Check file size (max 5MB)
        if ($_FILES[$fieldName]['size'] > 5 * 1024 * 1024) {
            throw new Exception("Image file is too large. Maximum size is 5MB.");
        }

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFile)) {
            // Delete old profile image if it exists and is not the default
            if ($currentPath && $currentPath !== 'img/default-avatar.png' && file_exists($currentPath)) {
                unlink($currentPath);
            }
            return $targetFile;
        } else {
            throw new Exception("Failed to upload image for $fieldName");
        }
    }

    return $currentPath; // Keep existing image if no upload
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $uploadDir = 'admin/uploads/documents/';
        $imageUploadDir = 'admin/uploads/members/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (!is_dir($imageUploadDir)) {
            mkdir($imageUploadDir, 0755, true);
        }

        // Handle profile image upload - Pass current profile_image path
        $profile_image = handleImageUpload('profile_image', $user['profile_image'], $imageUploadDir);

        // Handle document uploads
        $id_proof1 = handleFileUpload('id_proof1', $user['id_proof1'], $uploadDir);
        $id_proof2 = handleFileUpload('id_proof2', $user['id_proof2'], $uploadDir);
        $profile_doc = handleFileUpload('profile_doc', $user['profile_doc'], $uploadDir);
        $achievements_doc = handleFileUpload('achievements_doc', $user['achievements_doc'], $uploadDir);

        // Update database with all fields including profile image
        $stmt = $pdo->prepare("UPDATE members SET 
            first_name = ?, last_name = ?, email = ?, phone = ?, 
            highest_qualification = ?, area_of_expertise = ?, zip_code = ?, 
            city = ?, state = ?, journey = ?, date_of_birth = ?, address = ?, 
            profile_link = ?, linkedin_url = ?, introducer = ?, introducer_contact = ?,
            id_type1 = ?, id_proof1 = ?, id_type2 = ?, id_proof2 = ?,
            profile_doc = ?, achievements_doc = ?, profile_image = ?
            WHERE id = ?");

        $result = $stmt->execute([
            $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'],
            $_POST['highest_qualification'], $_POST['area_of_expertise'], $_POST['zip_code'],
            $_POST['city'], $_POST['state'], $_POST['journey'], $_POST['date_of_birth'],
            $_POST['address'], $_POST['profile_link'], $_POST['linkedin_url'],
            $_POST['introducer'], $_POST['introducer_contact'],
            $_POST['id_type1'], $id_proof1, $_POST['id_type2'], $id_proof2,
            $profile_doc, $achievements_doc, $profile_image, $user_id
        ]);

        if ($result) {
            // Update session data
            $_SESSION['first_name'] = $_POST['first_name'];
            $_SESSION['last_name'] = $_POST['last_name'];
            $_SESSION['email'] = $_POST['email'];

            $message = 'Profile and documents updated successfully!';
            $messageType = 'success';
            
            // Refresh user data to show updated information
            $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = 'Error updating profile. Please try again.';
            $messageType = 'error';
        }
        
    } catch(Exception | PDOException $e) {
        $message = 'Error updating profile: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Sarathi Cooperative</title>
    <style>
        body {
            margin-top: 100px;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #0a2b4f, #1e4d72);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .profile-info h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 600;
        }
        
        .profile-info p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .profile-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 1rem;
            display: inline-block;
        }
        
        .profile-content {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }
        
        .tab-button {
            flex: 1;
            padding: 1rem 2rem;
            background: none;
            border: none;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #666;
        }
        
        .tab-button.active {
            color: #0a2b4f;
            border-bottom: 3px solid #0a2b4f;
            background: #f8f9fa;
        }
        
        .tab-button:hover {
            background: #f8f9fa;
            color: #0a2b4f;
        }
        
        .tab-content {
            display: none;
            padding: 2rem;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            color: #0a2b4f;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
            font-size: 1.3rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0a2b4f;
            box-shadow: 0 0 0 3px rgba(10, 43, 79, 0.1);
        }
        
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0a2b4f, #1e4d72);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 43, 79, 0.3);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid #0a2b4f;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #0a2b4f;
            flex: 0 0 40%;
        }
        
        .info-value {
            color: #666;
            flex: 1;
            text-align: right;
        }
        
        .file-upload {
            position: relative;
            display: inline-block;
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #e9ecef;
            border: 2px dashed #adb5bd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .file-upload-label:hover {
            background: #dee2e6;
            border-color: #0a2b4f;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .profile-info h1 {
                font-size: 1.5rem;
            }
            
            .profile-tabs {
                flex-direction: column;
            }
            
            .tab-button {
                text-align: left;
                border-bottom: 1px solid #eee;
            }
            
            .tab-button.active {
                border-bottom: 1px solid #0a2b4f;
                border-left: 3px solid #0a2b4f;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .tab-content {
                padding: 1rem;
            }
        }
        .image-upload-section {
    text-align: center;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
}

.current-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #0a2b4f;
    margin-bottom: 1rem;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.image-upload-input {
    display: none;
}

.image-upload-btn {
    background: linear-gradient(135deg, #0a2b4f, #1e4d72);
    color: white;
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.image-upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(10, 43, 79, 0.3);
}

.image-preview {
    margin-top: 1rem;
    display: none;
}

.image-preview img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #28a745;
}

.remove-image-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    cursor: pointer;
    margin-left: 0.5rem;
}

.file-info {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.5rem;
}
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <img src="<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'img/default-avatar.png'; ?>" 
                 alt="Profile Picture" class="profile-avatar">
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                <p><?php echo htmlspecialchars($user['area_of_expertise']); ?></p>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                <div class="profile-badge">
                    <i class="fas fa-star"></i> <?php echo ucfirst($user['member_type']); ?> Member
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Message Display -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="profile-tabs">
                <button class="tab-button active" onclick="openTab(event, 'overview')">
                    <i class="fas fa-user"></i> Overview
                </button>
                <button class="tab-button" onclick="openTab(event, 'edit')">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button class="tab-button" onclick="openTab(event, 'documents')">
                    <i class="fas fa-file-alt"></i> Documents
                </button>
            </div>

            <!-- Overview Tab -->
            <div id="overview" class="tab-content active">
                <div class="form-section">
                    <h3><i class="fas fa-user"></i> Personal Information</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">Full Name:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date of Birth:</span>
                            <span class="info-value"><?php echo $user['date_of_birth'] ? date('F j, Y', strtotime($user['date_of_birth'])) : 'Not provided'; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['address']) ?: 'Not provided'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-map-marker-alt"></i> Location Details</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">City:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['city']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">State:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['state']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Zip Code:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['zip_code']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-graduation-cap"></i> Professional Information</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">Highest Qualification:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['highest_qualification']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Area of Expertise:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['area_of_expertise']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">LinkedIn:</span>
                            <span class="info-value">
                                <?php if (!empty($user['linkedin_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['linkedin_url']); ?>" target="_blank" style="color: #0a2b4f;">
                                        <i class="fab fa-linkedin"></i> View Profile
                                    </a>
                                <?php else: ?>
                                    Not provided
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($user['journey'])): ?>
                <div class="form-section">
                    <h3><i class="fas fa-road"></i> My Journey</h3>
                    <div class="info-card">
                        <p><?php echo nl2br(htmlspecialchars($user['journey'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-section">
                    <h3><i class="fas fa-info-circle"></i> Membership Details</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">Member Type:</span>
                            <span class="info-value"><?php echo ucfirst($user['member_type']); ?> Member</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value"><?php echo ucfirst($user['status']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Joined Date:</span>
                            <span class="info-value"><?php echo date('F j, Y', strtotime($user['joined_date'])); ?></span>
                        </div>
                        <?php if (!empty($user['introducer'])): ?>
                        <div class="info-row">
                            <span class="info-label">Introducer:</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['introducer']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Tab -->
           <div id="edit" class="tab-content">
    <form method="POST" enctype="multipart/form-data">
        <!-- Profile Image Upload Section -->
        <div class="form-section">
            <h3><i class="fas fa-camera"></i> Profile Picture</h3>
            <div class="image-upload-section">
                <img src="<?php echo !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'img/default-avatar.png'; ?>" 
                     alt="Current Profile Picture" class="current-image" id="currentProfileImage">
                
                <div>
                    <input type="file" id="profile_image" name="profile_image" class="image-upload-input" 
                           accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewImage(this)">
                    <button type="button" class="image-upload-btn" onclick="document.getElementById('profile_image').click()">
                        <i class="fas fa-camera"></i> Change Photo
                    </button>
                </div>
                
                <div class="image-preview" id="imagePreview">
                    <p>New Image Preview:</p>
                    <img id="previewImg" src="" alt="Preview">
                    <button type="button" class="remove-image-btn" onclick="removeImagePreview()">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                
                <div class="file-info">
                    <small><i class="fas fa-info-circle"></i> Accepted formats: JPG, JPEG, PNG, GIF | Maximum size: 5MB</small>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-user-edit"></i> Edit Personal Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" 
                           value="<?php echo $user['date_of_birth']; ?>">
                </div>
                <div class="form-group full-width">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-map-marker-alt"></i> Location Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city" 
                           value="<?php echo htmlspecialchars($user['city']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="state">State *</label>
                    <input type="text" id="state" name="state" 
                           value="<?php echo htmlspecialchars($user['state']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="zip_code">Zip Code *</label>
                    <input type="text" id="zip_code" name="zip_code" 
                           value="<?php echo htmlspecialchars($user['zip_code']); ?>" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-graduation-cap"></i> Professional Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="highest_qualification">Highest Qualification *</label>
                    <input type="text" id="highest_qualification" name="highest_qualification" 
                           value="<?php echo htmlspecialchars($user['highest_qualification']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="area_of_expertise">Area of Expertise *</label>
                    <input type="text" id="area_of_expertise" name="area_of_expertise" 
                           value="<?php echo htmlspecialchars($user['area_of_expertise']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profile_link">Profile Link</label>
                    <input type="url" id="profile_link" name="profile_link" 
                           value="<?php echo htmlspecialchars($user['profile_link']); ?>" 
                           placeholder="https://example.com">
                </div>
                <div class="form-group">
                    <label for="linkedin_url">LinkedIn URL</label>
                    <input type="url" id="linkedin_url" name="linkedin_url" 
                           value="<?php echo htmlspecialchars($user['linkedin_url']); ?>" 
                           placeholder="https://linkedin.com/in/username">
                </div>
                <div class="form-group full-width">
                    <label for="journey">My Journey</label>
                    <textarea id="journey" name="journey" rows="5" 
                              placeholder="Tell us about your professional journey..."><?php echo htmlspecialchars($user['journey']); ?></textarea>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-users"></i> Reference Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="introducer">Introducer Name</label>
                    <input type="text" id="introducer" name="introducer" 
                           value="<?php echo htmlspecialchars($user['introducer']); ?>">
                </div>
                <div class="form-group">
                    <label for="introducer_contact">Introducer Contact</label>
                    <input type="text" id="introducer_contact" name="introducer_contact" 
                           value="<?php echo htmlspecialchars($user['introducer_contact']); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3><i class="fas fa-file-upload"></i> Upload Documents</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="id_type1">ID Type 1</label>
                    <input type="text" id="id_type1" name="id_type1" value="<?php echo htmlspecialchars($user['id_type1']); ?>">
                </div>
                <div class="form-group">
                    <label for="id_proof1">Upload ID Proof 1</label>
                    <div class="file-upload">
                        <input type="file" id="id_proof1" name="id_proof1" accept=".pdf,.jpg,.jpeg,.png">
                        <label class="file-upload-label">Choose File</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="id_type2">ID Type 2</label>
                    <input type="text" id="id_type2" name="id_type2" value="<?php echo htmlspecialchars($user['id_type2']); ?>">
                </div>
                <div class="form-group">
                    <label for="id_proof2">Upload ID Proof 2</label>
                    <div class="file-upload">
                        <input type="file" id="id_proof2" name="id_proof2" accept=".pdf,.jpg,.jpeg,.png">
                        <label class="file-upload-label">Choose File</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="profile_doc">Profile Document</label>
                    <div class="file-upload">
                        <input type="file" id="profile_doc" name="profile_doc" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <label class="file-upload-label">Choose File</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="achievements_doc">Achievements Document</label>
                    <div class="file-upload">
                        <input type="file" id="achievements_doc" name="achievements_doc" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <label class="file-upload-label">Choose File</label>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <button type="submit" name="update_profile" class="btn-primary">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </div>
    </form>
</div>
            <!-- Documents Tab -->
            <div id="documents" class="tab-content">
                <div class="form-section">
                    <h3><i class="fas fa-file-alt"></i> Identity Documents</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">ID Type 1:</span>
                            <span class="info-value"><?php echo $user['id_type1'] ? ucfirst($user['id_type1']) : 'Not provided'; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">ID Proof 1:</span>
                            <span class="info-value">
                                <?php if (!empty($user['id_proof1'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['id_proof1']); ?>" target="_blank" style="color: #0a2b4f;">
                                        <i class="fas fa-download"></i> View Document
                                    </a>
                                <?php else: ?>
                                    Not uploaded
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">ID Type 2:</span>
                            <span class="info-value"><?php echo $user['id_type2'] ? ucfirst($user['id_type2']) : 'Not provided'; ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">ID Proof 2:</span>
                            <span class="info-value">
                                <?php if (!empty($user['id_proof2'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['id_proof2']); ?>" target="_blank" style="color: #0a2b4f;">
                                        <i class="fas fa-download"></i> View Document
                                    </a>
                                <?php else: ?>
                                    Not uploaded
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3><i class="fas fa-folder"></i> Other Documents</h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="info-label">Profile Document:</span>
                            <span class="info-value">
                                <?php if (!empty($user['profile_doc'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['profile_doc']); ?>" target="_blank" style="color: #0a2b4f;">
                                        <i class="fas fa-download"></i> View Document
                                    </a>
                                <?php else: ?>
                                    Not uploaded
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Achievements Document:</span>
                            <span class="info-value">
                                <?php if (!empty($user['achievements_doc'])): ?>
                                    <a href="<?php echo htmlspecialchars($user['achievements_doc']); ?>" target="_blank" style="color: #0a2b4f;">
                                        <i class="fas fa-download"></i> View Document
                                    </a>
                                <?php else: ?>
                                    Not uploaded
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            // Hide all tab content
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            
            // Remove active class from all tab buttons
            tablinks = document.getElementsByClassName("tab-button");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            
            // Show the selected tab content and mark button as active
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>
    <script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImagePreview() {
    const preview = document.getElementById('imagePreview');
    const input = document.getElementById('profile_image');
    const previewImg = document.getElementById('previewImg');
    
    input.value = '';
    previewImg.src = '';
    preview.style.display = 'none';
}

function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    // Remove active class from all tab buttons
    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    
    // Show the selected tab content and mark button as active
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}
</script>
</body>
</html>