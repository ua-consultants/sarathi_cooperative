<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/mailer.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id']) || !isset($_POST['remarks'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$remarks = filter_input(INPUT_POST, 'remarks', FILTER_SANITIZE_STRING);

try {
    $conn->begin_transaction();

    // Get application details with member info
    $stmt = $conn->prepare("
        SELECT ma.*, m.first_name, m.last_name, m.email 
        FROM membership_applications ma
        JOIN members m ON ma.member_id = m.id 
        WHERE ma.id = ? AND ma.status = 'pending'
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $application = $stmt->get_result()->fetch_assoc();

    if (!$application) {
        throw new Exception('Application not found or already processed');
    }

    // Update application status, remarks and timestamp
    $stmt = $conn->prepare("
        UPDATE membership_applications 
        SET status = 'rejected', 
            admin_remarks = ?,
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("si", $remarks, $id);
    $stmt->execute();

    // Update member status and timestamp
    $stmt = $conn->prepare("
        UPDATE members 
        SET status = 'inactive',
            updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("i", $application['member_id']);
    $stmt->execute();

    // Send rejection email
    $to = $application['email'];
    $subject = "Sarathi Cooperative - Application Status Update";
    
    $message = "Dear " . htmlspecialchars($application['first_name']) . ",\n\n";
    $message .= "Thank you for your interest in joining Sarathi Cooperative.\n\n";
    $message .= "After careful review of your application, we regret to inform you that we are unable to approve your membership at this time.\n\n";
    
    if (!empty($remarks)) {
        $message .= "Feedback from our review team:\n";
        $message .= $remarks . "\n\n";
    }
    
    $message .= "You may reapply after a period of one year with updated qualifications and achievements.\n\n";
    $message .= "If you have any questions about this decision or would like additional feedback, please feel free to contact our support team.\n\n";
    
    $message .= "Best regards,\n";
    $message .= "Sarathi Cooperative Team";

    $headers = "From: Sarathi Cooperative <sarathi@sarathicooperative.org>\r\n";
    $headers .= "Reply-To: support@sarathicooperative.org\r\n";

    if(!mail($to, $subject, $message, $headers)) {
        throw new Exception('Failed to send notification email');
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}