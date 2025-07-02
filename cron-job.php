<?php
/**
 * Cron Job - Profile Completion Reminder & Announcements
 * Sends reminder emails to 30 active members with incomplete profiles daily
 * Also sends announcement emails to all active members
 * 
 * To set up this cron job, add this line to your crontab:
 * 0 9 * * * /usr/bin/php /path/to/your/cron-job.php >/dev/null 2>&1
 * 
 * This runs daily at 9 AM
 */

// Prevent direct browser access for security
if (php_sapi_name() !== 'cli' && !isset($_GET['run_cron'])) {
    die('This script can only be run from command line or with ?run_cron parameter');
}

// Database configuration
$host = 'localhost';
$dbname = 'u828878874_sarathi_db';
$username = 'u828878874_sarathi_new';
$password = '#Sarathi@2025';

// Email configuration
$smtp_from_email = 'sarathi@sarathicooperative.org';
$smtp_from_name = 'Sarathi Cooperative';
$admin_email = 'sarathi@sarathicooperative.org';
$outlook_copy_email = 'sarathicooperative@outlook.com'; // Copy emails to Outlook
$daily_reminder_limit = 30; // Maximum reminders per day

// Log file path
$log_file = __DIR__ . '/cron_logs/profile_reminder_log.txt';

// Create log directory if it doesn't exist
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

/**
 * Log function
 */
function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    echo $log_entry; // Also output to console if running via CLI
}

/**
 * Check if profile is complete
 */
function isProfileComplete($member) {
    $required_fields = [
        'first_name',
        'last_name',
        'email',
        'area_of_expertise',
        'city',
        'state',
        'highest_qualification'
    ];
    
    $optional_but_important = [
        'profile_image',
        'journey'
    ];
    
    // Check required fields
    foreach ($required_fields as $field) {
        if (!isset($member[$field]) || empty($member[$field]) || trim($member[$field]) === '') {
            return false;
        }
    }
    
    // Check at least one optional field
    $has_optional = false;
    foreach ($optional_but_important as $field) {
        if (isset($member[$field]) && !empty($member[$field]) && trim($member[$field]) !== '') {
            $has_optional = true;
            break;
        }
    }
    
    return $has_optional;
}

/**
 * Get missing fields for a member
 */
function getMissingFields($member) {
    $missing = [];
    
    $all_fields = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'area_of_expertise' => 'Area of Expertise',
        'city' => 'City',
        'state' => 'State',
        'highest_qualification' => 'Highest Qualification',
        'profile_image' => 'Profile Image',
        'journey' => 'Professional Journey'
    ];
    
    foreach ($all_fields as $field => $label) {
        if (!isset($member[$field]) || empty($member[$field]) || trim($member[$field]) === '') {
            $missing[] = $label;
        }
    }
    
    return $missing;
}

/**
 * Enhanced email validation
 */
function isValidEmail($email) {
    if (empty($email)) {
        return false;
    }
    
    // Basic filter validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Additional checks for common issues
    if (strpos($email, '..') !== false) {
        return false; // Double dots not allowed
    }
    
    if (strlen($email) > 254) {
        return false; // Email too long
    }
    
    return true;
}

/**
 * Enhanced send email function with better error handling
 */
function sendEmailWithCopy($to_email, $subject, $message, $is_html = true) {
    global $smtp_from_email, $smtp_from_name, $outlook_copy_email;
    
    // Validate email first
    if (!isValidEmail($to_email)) {
        logMessage("Invalid email format: $to_email");
        return false;
    }
    
    // Clean and prepare email content
    $subject = trim($subject);
    if (empty($subject)) {
        logMessage("Empty subject line for email to: $to_email");
        return false;
    }
    
    // Try PHPMailer first if available
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return sendWithPHPMailer($to_email, $subject, $message, $is_html);
    }
    
    // Enhanced PHP mail() function with better headers
    $headers = [];
    
    // From header
    $headers[] = 'From: ' . $smtp_from_name . ' <' . $smtp_from_email . '>';
    $headers[] = 'Reply-To: ' . $smtp_from_email;
    
    // Copy to Outlook (only if different from main recipient)
    if ($outlook_copy_email !== $to_email && isValidEmail($outlook_copy_email)) {
        $headers[] = 'Cc: ' . $outlook_copy_email;
    }
    
    // Technical headers
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    $headers[] = 'X-Priority: 3'; // Normal priority
    $headers[] = 'Message-ID: <' . time() . '-' . md5($to_email) . '@sarathicooperative.org>';
    
    if ($is_html) {
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
    }
    
    // Additional headers to improve deliverability
    $headers[] = 'Return-Path: ' . $smtp_from_email;
    $headers[] = 'Sender: ' . $smtp_from_email;
    
    // Try to send email
    $result = mail($to_email, $subject, $message, implode("\r\n", $headers));
    
    // Enhanced logging
    if ($result) {
        logMessage("Email sent successfully to: $to_email");
    } else {
        $error = error_get_last();
        logMessage("Mail function failed for: $to_email. PHP Error: " . ($error['message'] ?? 'Unknown error'));
    }
    
    return $result;
}

/**
 * Send email using PHPMailer (if available) with enhanced error handling
 */
function sendWithPHPMailer($to_email, $subject, $message, $is_html = true) {
    global $smtp_from_email, $smtp_from_name, $outlook_copy_email;
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isMail(); // Use PHP's mail() function
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = '8bit';
        
        // Recipients
        $mail->setFrom($smtp_from_email, $smtp_from_name);
        $mail->addAddress($to_email);
        
        // Add CC only if different from main recipient
        if ($outlook_copy_email !== $to_email && isValidEmail($outlook_copy_email)) {
            $mail->addCC($outlook_copy_email);
        }
        
        // Content
        $mail->isHTML($is_html);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        // Additional settings for better deliverability
        $mail->XMailer = 'Sarathi Cooperative Mailer';
        
        $result = $mail->send();
        
        if ($result) {
            logMessage("PHPMailer: Email sent successfully to: $to_email");
        }
        
        return $result;
        
    } catch (Exception $e) {
        logMessage("PHPMailer Error for $to_email: " . $e->getMessage());
        return false;
    }
}

/**
 * Send reminder email with enhanced content
 */
function sendReminderEmail($email, $first_name, $missing_fields) {
    $subject = "Complete Your Sarathi Profile - Don't Miss Out!";
    
    // Ensure we have at least a name to address
    $display_name = !empty($first_name) ? htmlspecialchars(trim($first_name)) : 'Member';
    $missing_list = implode(', ', array_map('htmlspecialchars', $missing_fields));
    
    $message = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Complete Your Sarathi Profile</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background-color: #002147; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .missing-fields { background-color: #f8f9fa; padding: 15px; border-left: 4px solid #ffd700; margin: 15px 0; }
            .button { display: inline-block; background-color: #002147; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 15px 0; }
            .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; }
            @media only screen and (max-width: 600px) {
                .content { padding: 15px; }
                .button { display: block; text-align: center; }
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Sarathi Cooperative</h1>
                <h2>Profile Completion Reminder</h2>
            </div>
            
            <div class='content'>
                <p>Dear $display_name,</p>
                
                <p>We hope this message finds you well! We noticed that your Sarathi Cooperative profile is not yet complete.</p>
                
                <p>A complete profile helps you:</p>
                <ul>
                    <li>Connect better with fellow Sarathians</li>
                    <li>Showcase your expertise and experience</li>
                    <li>Get discovered by members looking for your skills</li>
                    <li>Build meaningful professional relationships</li>
                </ul>
                
                <div class='missing-fields'>
                    <h3>📝 Missing Information:</h3>
                    <p><strong>$missing_list</strong></p>
                </div>
                
                <p>It only takes a few minutes to complete your profile and unlock the full potential of our Sarathi community!</p>
                
                <p style='text-align: center;'>
                    <a href='https://sarathicooperative.org/become-a-sarathian.php' class='button'>Complete My Profile Now</a>
                </p>
                
                <p>If you have any questions or need assistance, please don't hesitate to reach out to us.</p>
                
                <p>Best regards,<br>
                The Sarathi Cooperative Team</p>
            </div>
            
            <div class='footer'>
                <p>This is an automated reminder. If you've recently updated your profile, please ignore this message.</p>
                <p>&copy; " . date('Y') . " Sarathi Cooperative. All rights reserved.</p>
                <p>Visit us at <a href='https://sarathicooperative.org'>sarathicooperative.org</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmailWithCopy($email, $subject, $message, true);
}

/**
 * Send announcement email
 */
function sendAnnouncementEmail($email, $first_name, $announcement) {
    $subject = "Sarathi Cooperative - " . $announcement['title'];
    
    $display_name = !empty($first_name) ? htmlspecialchars(trim($first_name)) : 'Member';
    
    $media_content = '';
    if (!empty($announcement['media_url'])) {
        if ($announcement['media_type'] === 'image') {
            $media_content = "<div style='text-align: center; margin: 20px 0;'>
                <img src='" . htmlspecialchars($announcement['media_url']) . "' 
                     alt='Announcement Image' style='max-width: 100%; height: auto; border-radius: 8px;'>
            </div>";
        } elseif ($announcement['media_type'] === 'video') {
            $media_content = "<div style='text-align: center; margin: 20px 0;'>
                <video controls style='max-width: 100%; height: auto; border-radius: 8px;'>
                    <source src='" . htmlspecialchars($announcement['media_url']) . "' type='video/mp4'>
                    Your browser does not support the video tag.
                </video>
            </div>";
        } elseif ($announcement['media_type'] === 'document') {
            $media_content = "<div style='text-align: center; margin: 20px 0;'>
                <a href='" . htmlspecialchars($announcement['media_url']) . "' 
                   style='display: inline-block; background-color: #28a745; color: white; padding: 10px 20px; 
                          text-decoration: none; border-radius: 5px;'>📄 Download Document</a>
            </div>";
        }
    }
    
    $message = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>" . htmlspecialchars($announcement['title']) . "</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; }
            .header { background-color: #002147; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .announcement-content { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Sarathi Cooperative</h1>
                <h2>" . htmlspecialchars($announcement['title']) . "</h2>
            </div>
            
            <div class='content'>
                <p>Dear $display_name,</p>
                
                <div class='announcement-content'>
                    " . nl2br(htmlspecialchars($announcement['content'])) . "
                </div>
                
                $media_content
                
                <p>For more updates and information, visit our website at 
                   <a href='https://sarathicooperative.org'>sarathicooperative.org</a></p>
                
                <p>Best regards,<br>
                The Sarathi Cooperative Team</p>
            </div>
            
            <div class='footer'>
                <p>You are receiving this because you are a member of Sarathi Cooperative.</p>
                <p>&copy; " . date('Y') . " Sarathi Cooperative. All rights reserved.</p>
                <p>Visit us at <a href='https://sarathicooperative.org'>sarathicooperative.org</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmailWithCopy($email, $subject, $message, true);
}

/**
 * Get members who haven't received reminders recently - ENHANCED
 */
function getMembersForReminder($pdo, $limit) {
    try {
        // Enhanced query with better email validation and debugging
        $query = "SELECT m.*, mrl.last_reminder_sent,
                         CASE WHEN m.email IS NULL THEN 'NULL_EMAIL'
                              WHEN m.email = '' THEN 'EMPTY_EMAIL'
                              WHEN m.email NOT REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$' THEN 'INVALID_FORMAT'
                              ELSE 'VALID_EMAIL'
                         END as email_status
                  FROM members m 
                  LEFT JOIN member_reminder_log mrl ON m.id = mrl.member_id 
                  WHERE m.status = 'active'
                  ORDER BY COALESCE(mrl.last_reminder_sent, '1970-01-01') ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        
        $all_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        logMessage("Total active members found: " . count($all_members));
        
        // Filter and categorize members
        $valid_members = [];
        $email_issues = [];
        $recent_reminders = 0;
        
        foreach ($all_members as $member) {
            // Check email validity
            if (!isValidEmail($member['email'])) {
                $email_issues[] = "ID: {$member['id']}, Email: '{$member['email']}', Status: {$member['email_status']}";
                continue;
            }
            
            // Check if reminder was sent recently (within 7 days)
            if (!empty($member['last_reminder_sent'])) {
                $last_sent = new DateTime($member['last_reminder_sent']);
                $now = new DateTime();
                $diff = $now->diff($last_sent);
                
                if ($diff->days < 7) {
                    $recent_reminders++;
                    logMessage("Recent reminder sent to ID: {$member['id']}, Email: {$member['email']} on {$member['last_reminder_sent']}");
                    continue;
                }
            }
            
            $valid_members[] = $member;
            
            // Stop when we reach the limit
            if (count($valid_members) >= $limit) {
                break;
            }
        }
        
        // Log email issues
        if (!empty($email_issues)) {
            logMessage("Members with email issues (" . count($email_issues) . "):");
            foreach ($email_issues as $issue) {
                logMessage("  - $issue");
            }
        }
        
        logMessage("Members with recent reminders (< 7 days): $recent_reminders");
        logMessage("Valid members eligible for reminders: " . count($valid_members));
        
        return $valid_members;
        
    } catch (PDOException $e) {
        logMessage("Error in getMembersForReminder: " . $e->getMessage());
        return [];
    }
}

/**
 * Update reminder log for a member
 */
function updateReminderLog($pdo, $member_id) {
    try {
        $query = "INSERT INTO member_reminder_log (member_id, last_reminder_sent) 
                  VALUES (:member_id, NOW()) 
                  ON DUPLICATE KEY UPDATE last_reminder_sent = NOW()";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':member_id', $member_id, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        if ($result) {
            logMessage("Updated reminder log for member ID: $member_id");
        } else {
            logMessage("Failed to update reminder log for member ID: $member_id");
        }
        
        return $result;
        
    } catch (PDOException $e) {
        logMessage("Error updating reminder log for member $member_id: " . $e->getMessage());
        return false;
    }
}

/**
 * Get active announcements that haven't been sent yet
 */
function getPendingAnnouncements($pdo) {
    try {
        $query = "SELECT * FROM announcements 
                  WHERE status = 'active' 
                  AND (end_date IS NULL OR end_date >= CURDATE())
                  AND id NOT IN (SELECT announcement_id FROM announcement_send_log WHERE DATE(sent_at) = CURDATE())
                  ORDER BY created_at ASC";
        
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        logMessage("Error getting pending announcements: " . $e->getMessage());
        return [];
    }
}

/**
 * Log announcement send
 */
function logAnnouncementSend($pdo, $announcement_id, $total_sent) {
    try {
        $query = "INSERT INTO announcement_send_log (announcement_id, sent_at, total_recipients) 
                  VALUES (:announcement_id, NOW(), :total_sent)";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':announcement_id', $announcement_id, PDO::PARAM_INT);
        $stmt->bindValue(':total_sent', $total_sent, PDO::PARAM_INT);
        return $stmt->execute();
        
    } catch (PDOException $e) {
        logMessage("Error logging announcement send: " . $e->getMessage());
        return false;
    }
}

/**
 * Create required tables if they don't exist
 */
function createRequiredTables($pdo) {
    try {
        // Create member_reminder_log table
        $sql1 = "CREATE TABLE IF NOT EXISTS member_reminder_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            member_id INT NOT NULL,
            last_reminder_sent DATETIME NOT NULL,
            UNIQUE KEY unique_member (member_id),
            INDEX idx_last_reminder (last_reminder_sent)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql1);
        
        // Create announcement_send_log table
        $sql2 = "CREATE TABLE IF NOT EXISTS announcement_send_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            announcement_id INT NOT NULL,
            sent_at DATETIME NOT NULL,
            total_recipients INT NOT NULL,
            INDEX idx_announcement_date (announcement_id, sent_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql2);
        
        logMessage("Required tables created/verified successfully");
        
    } catch (PDOException $e) {
        logMessage("Error creating tables: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Test email configuration
 */
function testEmailConfiguration() {
    global $smtp_from_email, $admin_email;
    
    logMessage("Testing email configuration...");
    
    // Test basic email validation
    if (!isValidEmail($smtp_from_email)) {
        logMessage("ERROR: Invalid FROM email address: $smtp_from_email");
        return false;
    }
    
    if (!isValidEmail($admin_email)) {
        logMessage("ERROR: Invalid admin email address: $admin_email");
        return false;
    }
    
    // Check if mail function is available
    if (!function_exists('mail')) {
        logMessage("ERROR: PHP mail() function is not available");
        return false;
    }
    
    logMessage("Email configuration test: PASSED");
    return true;
}

/**
 * Test database connection and basic functionality
 */
function testDatabaseConnection($pdo) {
    try {
        // Test basic connection
        $stmt = $pdo->query("SELECT 1");
        logMessage("Database connection test: SUCCESS");
        
        // Check if members table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'members'");
        if ($stmt->rowCount() > 0) {
            logMessage("Members table exists: YES");
            
            // Check member count with detailed breakdown
            $stmt = $pdo->query("SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                SUM(CASE WHEN status = 'active' AND email IS NOT NULL AND email != '' THEN 1 ELSE 0 END) as active_with_email
                FROM members");
            $counts = $stmt->fetch(PDO::FETCH_ASSOC);
            
            logMessage("Total members: " . $counts['total_count']);
            logMessage("Active members: " . $counts['active_count']);
            logMessage("Active members with email: " . $counts['active_with_email']);
            
        } else {
            logMessage("Members table exists: NO - THIS IS A PROBLEM!");
            return false;
        }
        
        return true;
        
    } catch (PDOException $e) {
        logMessage("Database test failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Main execution
 */
try {
    logMessage("=== Starting daily cron job - Profile reminders & Announcements ===");
    logMessage("PHP Version: " . phpversion());
    logMessage("Memory Limit: " . ini_get('memory_limit'));
    logMessage("Max Execution Time: " . ini_get('max_execution_time'));
    
    // Test email configuration first
    if (!testEmailConfiguration()) {
        throw new Exception("Email configuration test failed");
    }
    
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    
    logMessage("Database connection established");
    
    // Test database connection
    if (!testDatabaseConnection($pdo)) {
        throw new Exception("Database connection test failed");
    }
    
    // Create required tables
    createRequiredTables($pdo);
    
    $total_emails_sent = 0;
    $total_emails_failed = 0;
    
    // ===== PROFILE REMINDERS =====
    logMessage("=== Processing profile completion reminders ===");
    
    // Get members for reminder (max 30 per day)
    $members_for_reminder = getMembersForReminder($pdo, $daily_reminder_limit);
    
    logMessage("Found " . count($members_for_reminder) . " members eligible for profile reminders");
    
    $reminder_sent = 0;
    $reminder_failed = 0;
    $complete_profiles = 0;
    
    foreach ($members_for_reminder as $member) {
        $member_info = "ID: {$member['id']}, Email: {$member['email']}, Name: {$member['first_name']} {$member['last_name']}";
        logMessage("Processing member: $member_info");
        
        if (!isProfileComplete($member)) {
            // Get missing fields
            $missing_fields = getMissingFields($member);
            logMessage("Profile incomplete - Missing: " . implode(', ', $missing_fields));
            
            // Send reminder email
            $first_name = !empty($member['first_name']) ? $member['first_name'] : 'Member';
            $email = $member['email'];
            
            logMessage("Attempting to send reminder email to: $email");
            
            if (sendReminderEmail($email, $first_name, $missing_fields)) {
                $reminder_sent++;
                updateReminderLog($pdo, $member['id']);
                logMessage("✓ Profile reminder sent successfully to: $email ($first_name)");
            } else {
                $reminder_failed++;
                logMessage("✗ Failed to send profile reminder to: $email ($first_name)");
            }
            
            // Increased delay to prevent overwhelming the mail server
            sleep(1); // 1 second delay between emails
        } else {
            $complete_profiles++;
            logMessage("✓ Profile complete for: " . $member['email'] . " (" . $member['first_name'] . ")");
        }
    }
    
    $total_emails_sent += $reminder_sent;
    $total_emails_failed += $reminder_failed;
    
    logMessage("=== Profile reminders completed ===");
    logMessage("Reminders sent: $reminder_sent");
    logMessage("Reminders failed: $reminder_failed");
    logMessage("Complete profiles (skipped): $complete_profiles");
    
    // ===== ANNOUNCEMENTS =====
    logMessage("=== Processing announcements ===");
    
    $pending_announcements = getPendingAnnouncements($pdo);
    
    if (!empty($pending_announcements)) {
        logMessage("Found " . count($pending_announcements) . " pending announcements");
        
        // Get all active members for announcements with valid emails
        $query = "SELECT * FROM members 
                  WHERE status = 'active' 
                  AND email IS NOT NULL 
                  AND email != '' 
                  AND email REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+
                  ORDER BY id";
        $stmt = $pdo->query($query);
        $all_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter members with valid emails
        $valid_members_for_announcement = [];
        foreach ($all_members as $member) {
            if (isValidEmail($member['email'])) {
                $valid_members_for_announcement[] = $member;
            }
        }
        
        logMessage("Sending announcements to " . count($valid_members_for_announcement) . " active members with valid emails");
        
        foreach ($pending_announcements as $announcement) {
            $announcement_sent = 0;
            $announcement_failed = 0;
            
            logMessage("Sending announcement: " . $announcement['title']);
            
            foreach ($valid_members_for_announcement as $member) {
                $first_name = !empty($member['first_name']) ? $member['first_name'] : 'Member';
                $email = $member['email'];
                
                if (sendAnnouncementEmail($email, $first_name, $announcement)) {
                    $announcement_sent++;
                } else {
                    $announcement_failed++;
                    logMessage("Failed to send announcement to: $email ($first_name)");
                }
                
                // Delay between announcement emails
                usleep(500000); // 0.5 second delay
            }
            
            // Log the announcement send
            logAnnouncementSend($pdo, $announcement['id'], $announcement_sent);
            
            $total_emails_sent += $announcement_sent;
            $total_emails_failed += $announcement_failed;
            
            logMessage("Announcement '{$announcement['title']}' completed. Sent: $announcement_sent, Failed: $announcement_failed");
        }
    } else {
        logMessage("No pending announcements found");
    }
    
    // ===== SUMMARY =====
    $summary = "=== DAILY CRON JOB SUMMARY ===\n" .
               "Profile reminders sent: $reminder_sent\n" .
               "Profile reminders failed: $reminder_failed\n" .
               "Complete profiles found: $complete_profiles\n" .
               "Total emails sent: $total_emails_sent\n" .
               "Total emails failed: $total_emails_failed\n" .
               "=== END SUMMARY ===";
    
    logMessage($summary);
    
    // Send admin notification if there were failures or summary
    $admin_subject = $total_emails_failed > 0 ? 
        "Daily Cron Job - Some Failures Detected" : 
        "Daily Cron Job - Completed Successfully";
        
    $admin_message = "The daily cron job has completed.\n\n" .
                    str_replace('===', '', $summary) . "\n\n";
    
    if ($total_emails_failed > 0) {
        $admin_message .= "Please check the log file for details: $log_file\n\n";
    }
    
    $admin_message .= "Log file location: $log_file\n" .
                     "Execution time: " . date('Y-m-d H:i:s');
    
    // Send admin notification (but don't fail the whole job if this fails)
    $admin_sent = sendEmailWithCopy($admin_email, $admin_subject, $admin_message, false);
    if (!$admin_sent) {
        logMessage("Warning: Failed to send admin notification email");
    }
    
} catch (PDOException $e) {
    $error_message = "Database error in cron job: " . $e->getMessage();
    logMessage($error_message);
    
    // Send admin notification
    if (function_exists('sendEmailWithCopy')) {
        sendEmailWithCopy($admin_email, "Daily Cron Job - Database Error", $error_message, false);
    }
    
} catch (Exception $e) {
    $error_message = "General error in cron job: " . $e->getMessage();
    logMessage($error_message);
    
    // Send admin notification
    if (function_exists('sendEmailWithCopy')) {
        sendEmailWithCopy($admin_email, "Daily Cron Job - General Error", $error_message, false);
    }
}

logMessage("=== Daily cron job execution finished ===");
?>