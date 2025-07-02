<?php
// Include your database connection file
// include 'config.php'; // Uncomment and adjust as needed
$conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
/**
 * Function to send announcement emails to all subscribers
 * Call this function after creating a new announcement
 * 
 * @param int $announcement_id - The ID of the announcement
 * @return array - Result array with success status and message count
 */
function sendAnnouncementEmails($announcement_id) {
    global $conn;
    
    try {
        // Get announcement details
        $announcement_sql = "SELECT title, content, media_type, media_url, created_at 
                           FROM announcements 
                           WHERE id = ? AND status = 'active'";
        $announcement_stmt = $conn->prepare($announcement_sql);
        $announcement_stmt->bind_param("i", $announcement_id);
        $announcement_stmt->execute();
        $announcement_result = $announcement_stmt->get_result();
        
        if ($announcement_result->num_rows === 0) {
            return ['success' => false, 'message' => 'Announcement not found'];
        }
        
        $announcement = $announcement_result->fetch_assoc();
        $announcement_stmt->close();
        
        // Get all active subscribers
        $subscribers_sql = "SELECT email FROM announcement_subscribers WHERE status = 'active'";
        $subscribers_result = $conn->query($subscribers_sql);
        
        if ($subscribers_result->num_rows === 0) {
            return ['success' => true, 'message' => 'No subscribers found', 'sent_count' => 0];
        }
        
        $sent_count = 0;
        $failed_count = 0;
        
        // Prepare email content
        $subject = "New Announcement: " . $announcement['title'];
        $email_content = generateEmailContent($announcement);
        
        // Send emails to all subscribers
        while ($subscriber = $subscribers_result->fetch_assoc()) {
            $email = $subscriber['email'];
            
            // Send email
            if (sendEmail($email, $subject, $email_content)) {
                $sent_count++;
                // Log successful send
                logNotification($announcement_id, $email, 'sent');
            } else {
                $failed_count++;
                // Log failed send
                logNotification($announcement_id, $email, 'failed');
            }
        }
        
        return [
            'success' => true, 
            'sent_count' => $sent_count, 
            'failed_count' => $failed_count,
            'message' => "Sent $sent_count emails successfully, $failed_count failed"
        ];
        
    } catch (Exception $e) {
        error_log("Error sending announcement emails: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

/**
 * Generate HTML email content for announcement
 */
function generateEmailContent($announcement) {
    $formatted_date = date('F j, Y \a\t g:i A', strtotime($announcement['created_at']));
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>New Announcement from Sarathi</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2800bb; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background-color: #f8f9fa; padding: 20px; border-radius: 0 0 10px 10px; }
            .announcement-title { color: #2800bb; font-size: 1.3em; margin-bottom: 15px; }
            .announcement-content { background-color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
            .announcement-image { max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 15px; }
            .footer { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 0.9em; }
            .btn { display: inline-block; background-color: #2800bb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🔔 New Announcement from Sarathi</h1>
            <p>सारथी of Viksit Bharat</p>
        </div>
        
        <div class='content'>
            <h2 class='announcement-title'>" . htmlspecialchars($announcement['title']) . "</h2>
            
            <div class='announcement-content'>";
    
    // Add image if available
    if ($announcement['media_type'] == 'image' && !empty($announcement['media_url'])) {
        $html .= "<img src='" . htmlspecialchars($announcement['media_url']) . "' alt='Announcement Image' class='announcement-image'>";
    }
    
    $html .= "
                <p>" . nl2br(htmlspecialchars($announcement['content'])) . "</p>
                
                <p><strong>Published:</strong> " . $formatted_date . "</p>
            </div>
            
            <div style='text-align: center;'>
                <a href='#' class='btn'>Visit Our Website</a>
            </div>
        </div>
        
        <div class='footer'>
            <p>You received this email because you subscribed to Sarathi announcements.</p>
            <p><a href='#'>Unsubscribe</a> | <a href='#'>Contact Us</a></p>
            <p>&copy; " . date('Y') . " Sarathi Cooperative. All rights reserved.</p>
        </div>
    </body>
    </html>";
    
    return $html;
}

/**
 * Send email using PHP mail function
 * You can replace this with your preferred email service (PHPMailer, SendGrid, etc.)
 */
function sendEmail($to, $subject, $html_content) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Sarathi Announcements <noreply@sarathi.com>' . "\r\n"; // Change to your email
    $headers .= 'Reply-To: info@sarathi.com' . "\r\n"; // Change to your email
    $headers .= 'X-Mailer: PHP/' . phpversion();
    
    return mail($to, $subject, $html_content, $headers);
}

/**
 * Log email notification attempt
 */
function logNotification($announcement_id, $email, $status) {
    global $conn;
    
    try {
        $log_sql = "INSERT INTO announcement_notifications (announcement_id, subscriber_email, status, sent_at) 
                   VALUES (?, ?, ?, NOW())";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iss", $announcement_id, $email, $status);
        $log_stmt->execute();
        $log_stmt->close();
    } catch (Exception $e) {
        error_log("Error logging notification: " . $e->getMessage());
    }
}

// Example usage - call this after creating a new announcement
/*
if (isset($_POST['new_announcement_id'])) {
    $announcement_id = (int)$_POST['new_announcement_id'];
    $result = sendAnnouncementEmails($announcement_id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
}
*/

// Alternative: If you want to send emails for a specific announcement ID via GET/POST
if (isset($_GET['send_emails']) && isset($_GET['announcement_id'])) {
    $announcement_id = (int)$_GET['announcement_id'];
    $result = sendAnnouncementEmails($announcement_id);
    
    if ($result['success']) {
        echo "Success: " . $result['message'];
    } else {
        echo "Error: " . $result['message'];
    }
}
?>