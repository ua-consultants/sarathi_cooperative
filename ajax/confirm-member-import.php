<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    // Verify that we have preview data in session
    if (!isset($_SESSION['member_import_preview']) || empty($_SESSION['member_import_preview'])) {
        throw new Exception('No preview data found. Please upload the Excel file again.');
    }

    $members = $_SESSION['member_import_preview'];
    $importedCount = 0;
    $errors = [];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO members (
        first_name, last_name, email, phone, highest_qualification, 
        area_of_expertise, address, city, state, zip_code, date_of_birth,
        linkedin_url, journey, introducer, introducer_contact, 
        status, member_type, profile_image, username, password, joined_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    foreach ($members as $member) {
        try {
            // Ensure status is set to active
            $member['status'] = 'active';
            $member['member_type'] = 'general';

            // Generate Member ID if not present (format: MMYYNNN)
            if (empty($member['member_id'])) {
                $prefix = date('my');
                $result = $conn->query("SELECT member_id FROM members WHERE member_id LIKE '$prefix%' ORDER BY member_id DESC LIMIT 1");
                $lastId = $result->fetch_assoc();
                $sequence = $lastId ? intval(substr($lastId['member_id'], -3)) + 1 : 121;
                $member['member_id'] = $prefix . str_pad($sequence, 3, '0', STR_PAD_LEFT);
            }

            // Generate username and password if not set
            if (empty($member['username'])) {
                $member['username'] = $member['member_id'];
                $member['password'] = password_hash('Sarathi' . $member['member_id'], PASSWORD_DEFAULT);
            }

            $stmt->bind_param("ssssssssssssssssssss",
                $member['first_name'],
                $member['last_name'],
                $member['email'],
                $member['phone'],
                $member['highest_qualification'],
                $member['area_of_expertise'],
                $member['address'],
                $member['city'],
                $member['state'],
                $member['zip_code'],
                $member['date_of_birth'],
                $member['linkedin_url'],
                $member['journey'],
                $member['introducer'],
                $member['introducer_contact'],
                $member['status'],
                $member['member_type'],
                $member['profile_image'],
                $member['username'],
                $member['password']
            );

            if ($stmt->execute()) {
                $importedCount++;

                // Send welcome email with credentials
                $to = $member['email'];
                $subject = 'Welcome to Sarathi - Your Account Details';
                $message = "Dear {$member['first_name']},\n\n";
                $message .= "Welcome to Sarathi! Your account has been created successfully.\n\n";
                $message .= "Your login credentials:\n";
                $message .= "Username: {$member['username']}\n";
                $message .= "Password: Sarathi{$member['member_id']}\n\n";
                $message .= "Please login and change your password at your earliest convenience.\n\n";
                $message .= "Best regards,\nSarathi Team";

                mail($to, $subject, $message);
            }
        } catch (Exception $e) {
            $errors[] = "Failed to import member {$member['email']}: " . $e->getMessage();
        }
    }

    // Clear the preview data from session
    unset($_SESSION['member_import_preview']);

    echo json_encode([
        'status' => 'success',
        'imported_count' => $importedCount,
        'errors' => $errors,
        'message' => "Successfully imported $importedCount members."
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>