<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include("db.php");

// Set content type to JSON
header('Content-Type: application/json');

// Debugging के लिए POST डेटा को प्रिंट करें (फाइनल वर्जन में हटा दें)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    file_put_contents("debug_log.txt", print_r($_POST, true)); // POST डेटा को एक फाइल में सेव करें
}

// Check if POST request is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        if (!isset($pdo)) {
            throw new Exception("Database connection error.");
        }

        // Get form data safely
        $userID = $_POST['userID'] ?? null;
        $name = $_POST['name'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $address = $_POST['address'] ?? null;
        $qualification = $_POST['qualification'] ?? null;
        $otherQualification = $_POST['otherQualification'] ?? null;
        $dob = $_POST['dob'] ?? null;
        $email = $_POST['email'] ?? null;
        $zipCode = $_POST['zipCode'] ?? null;
        $state = $_POST['state'] ?? null;
        $city = $_POST['city'] ?? null;
        $category = $_POST['category'] ?? null;
        $expertise = $_POST['expertise'] ?? null;
        $otherExpertise = $_POST['otherExpertise'] ?? null;

        if (!$userID) {
            throw new Exception("User ID is missing.");
        }

        // Image handling
        $imageURL = null;
        if (!empty($_POST['useExistingImage']) && $_POST['useExistingImage'] === 'yes') {
            $stmt = $pdo->prepare("SELECT imageURL FROM user_profiles WHERE userID = :userID");
            $stmt->execute([':userID' => $userID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $imageURL = $result['imageURL'];
        } else if (!empty($_FILES['image']['name'])) {
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            if ($_FILES['image']['size'] > $maxFileSize) {
                throw new Exception("File size should not exceed 2MB.");
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG, and GIF are allowed.");
            }

            $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
            $imagePath = "uploads/" . $imageName;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                throw new Exception("Error uploading image.");
            }
            $imageURL = 'https://sarathicooperative.org/uploads/' . $imageName;
        }

        // Update the database using prepared statement
        $pdo->beginTransaction();
        $sql = "UPDATE user_profiles SET 
                name = :name, 
                phone = :phone, 
                address = :address, 
                qualification = :qualification, 
                otherQualification = :otherQualification, 
                dob = :dob, 
                email = :email, 
                zipCode = :zipCode, 
                state = :state, 
                city = :city, 
                category = :category, 
                expertise = :expertise, 
                otherExpertise = :otherExpertise";
        
        if ($imageURL) {
            $sql .= ", imageURL = :imageURL";
        }
        
        $sql .= " WHERE userID = :userID";
        $stmt = $pdo->prepare($sql);

        $params = [
            ':userID' => $userID,
            ':name' => $name,
            ':phone' => $phone,
            ':address' => $address,
            ':qualification' => $qualification,
            ':otherQualification' => $otherQualification,
            ':dob' => $dob,
            ':email' => $email,
            ':zipCode' => $zipCode,
            ':state' => $state,
            ':city' => $city,
            ':category' => $category,
            ':expertise' => $expertise,
            ':otherExpertise' => $otherExpertise
        ];

        if ($imageURL) {
            $params[':imageURL'] = $imageURL;
        }

        $stmt->execute($params);
        $pdo->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Profile successfully updated!',
            'imageURL' => $imageURL ?? null
        ]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request',
        'debug' => $_POST
    ]);
}
?>
