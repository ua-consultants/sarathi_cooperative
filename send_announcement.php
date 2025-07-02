<?php
// send_announcement.php - Send announcements to all subscribers
$conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function sendAnnouncementToSubscribers($title, $content, $announcement_type = 'general') {
    global $conn;
    
    // Get all active subscribers
    $sql = "SELECT email FROM announcement_subscribers WHERE status = 'active'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 0) {
        return ['success' => false, 'message' => 'No active subscribers found'];
    }
    
    $sent_count = 0;
    $failed_count = 0;
    
    $subject = "Sarathi Cooperative - " . $title;
    $headers = "From: sarathi@sarathicooperative.org\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Create HTML email template
    $html_message = "
    <html>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;'>
                <h2 style='color: #2c3e50; margin-top: 0;'>$title</h2>
            </div>
            <div style='background-color: #ffffff; padding: 20px; border: 1px solid #e9ecef; border-radius: 5px;'>
                $content
            </div>
            <div style='margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px; font-size: 12px; color: #6c757d;'>
                <p>This email was sent to you because you subscribed to Sarathi Cooperative announcements.</p>
                <p>If you no longer wish to receive these emails, please contact us at sarathi@sarathicooperative.org</p>
            </div>
        </div>
    </body>
    </html>";
    
    // Send to all subscribers
    while ($row = $result->fetch_assoc()) {
        if (mail($row['email'], $subject, $html_message, $headers)) {
            $sent_count++;
        } else {
            $failed_count++;
        }
    }
    
    // Log the announcement
    $log_sql = "INSERT INTO announcement_log (title, content, type, sent_count, sent_at) VALUES (?, ?, ?, ?, NOW())";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("sssi", $title, $content, $announcement_type, $sent_count);
    $log_stmt->execute();
    
    return [
        'success' => true, 
        'message' => "Announcement sent successfully to $sent_count subscribers" . 
                    ($failed_count > 0 ? " ($failed_count failed)" : "")
    ];
}

// Handle manual announcement creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $type = $_POST['type'] ?? 'general';
    
    if (empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Title and content are required']);
        exit;
    }
    
    $result = sendAnnouncementToSubscribers($title, $content, $type);
    echo json_encode($result);
}
?>
