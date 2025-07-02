<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
requireLogin();

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid member ID',
        'debug' => $_POST
    ]);
    exit;
}

$memberId = (int)$_POST['id'];

try {
    // Debug database connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get member and application details
    $stmt = $conn->prepare("SELECT m.*, ma.status as application_status 
        FROM members m 
        JOIN membership_applications ma ON m.id = ma.member_id 
        WHERE m.id = ? AND ma.status = 'pending'");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $memberId);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    
    if (!$member) {
        throw new Exception("Member not found or already processed");
    }

    // Start transaction
    $conn->begin_transaction();

    // Update member status to active
    $updateMemberStmt = $conn->prepare("UPDATE members SET status = 'active', updated_at = NOW() WHERE id = ?");
    if (!$updateMemberStmt->bind_param("i", $memberId) || !$updateMemberStmt->execute()) {
        throw new Exception("Failed to update member status");
    }

    // Update application status
    $updateAppStmt = $conn->prepare("UPDATE membership_applications SET status = 'approved', updated_at = NOW() WHERE member_id = ?");
    if (!$updateAppStmt->bind_param("i", $memberId) || !$updateAppStmt->execute()) {
        throw new Exception("Failed to update application status");
    }

    // Send approval email
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'anuragchahar870@gmail.com';
    $mail->Password = 'pwjswzyulmolckka';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('anuragchahar870@gmail.com', 'Sarathi Cooperative');
    $mail->addAddress($member['email']);
    $mail->isHTML(true);
    $mail->Subject = 'Membership Application Approved - Welcome to Sarathi Cooperative!';
    
    // Email template
    $emailBody = "<html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0a2b4f; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .footer { text-align: center; padding: 20px; font-size: 0.8em; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Welcome to Sarathi Cooperative!</h2>
                </div>
                <div class='content'>
                    <p>Dear " . htmlspecialchars($member['first_name']) . ",</p>
                    <p>Congratulations! Your membership application has been approved. You are now an active member of the Sarathi Cooperative community.</p>
                    <p>You can now access all member benefits and participate in our community activities.</p>
                    <p>If you have any questions, please don't hesitate to contact us.</p>
                    <p>Best regards,<br>The Sarathi Cooperative Team</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message, please do not reply directly to this email.</p>
                </div>
            </div>
        </body>
    </html>";
    
    $mail->Body = $emailBody;
    $mail->send();

    // Commit transaction
    $conn->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Application approved successfully'
    ]);

} catch (Exception $e) {
    if ($conn->connect_error === false) {
        $conn->rollback();
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}