<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// PHPMailer includes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php'; // Adjust path as needed

// Check if user is logged in and has admin privileges
requireLogin();

// Set content type to JSON
header('Content-Type: application/json');

// Email configuration for record keeping
define('RECORD_EMAIL', 'sarathicooperative@outlook.com');
define('SUPPORT_EMAIL', 'sarathi@sarathicooperative.org');
define('SUPPORT_PHONE', '+91-9667153393');

// SMTP Configuration for Outlook
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'sarathicooperative@outlook.com'); // Your Outlook email
define('SMTP_PASSWORD', 'Business777!'); // Replace with your email password or app password
define('SMTP_FROM_EMAIL', 'sarathicooperative@outlook.com');
define('SMTP_FROM_NAME', 'Sarathi Cooperative');

// ENHANCED CONFIGURATION - Optimized for high volume
define('MAX_EXECUTION_TIME', 900); // 15 minutes for large batches
define('EMAIL_RATE_LIMIT_DELAY', 1.2); // 1.2 seconds = 50 emails per hour max
define('MAX_EMAIL_RETRIES', 2); // Reduced retries for faster processing
define('BATCH_SIZE', 50); // Process 50 at once
define('HOURLY_EMAIL_LIMIT', 50); // Max 50 emails per hour
define('DAILY_EMAIL_LIMIT', 1000); // Max 1000 emails per day

// Increase execution time and memory limit
set_time_limit(MAX_EXECUTION_TIME);
ini_set('memory_limit', '512M');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // Get member ID or batch processing flag from POST data
    $member_id = isset($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
    $batch_process = isset($_POST['batch_process']) ? (bool)$_POST['batch_process'] : false;
    $batch_size = isset($_POST['batch_size']) ? min((int)$_POST['batch_size'], BATCH_SIZE) : BATCH_SIZE;
    
    // Create email records table if it doesn't exist
    createEmailRecordsTable();
    
    if ($batch_process) {
        // Process multiple members
        processMembersBatch($batch_size);
    } elseif ($member_id > 0) {
        // Process single member
        processSingleMember($member_id);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Valid member ID or batch process flag is required'
        ]);
        exit;
    }
    
} catch (Exception $e) {
    error_log("Critical error in member login update process: " . $e->getMessage());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'System error occurred',
        'error' => $e->getMessage()
    ]);
}

/**
 * PROCESS SINGLE MEMBER
 */
function processSingleMember($member_id) {
    global $conn;
    
    try {
        // Check rate limits before processing
        if (!checkRateLimits()) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Rate limit exceeded. Please try again later.',
                'rate_limit_info' => getRateLimitInfo()
            ]);
            return;
        }
        
        // Get specific member details
        $stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM members WHERE id = ? AND status = 'active' AND email IS NOT NULL AND email != ''");
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            throw new Exception("Database query failed: " . $conn->error);
        }
        
        $member = $result->fetch_assoc();
        $stmt->close();
        
        if (!$member) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Active member with valid email address not found'
            ]);
            return;
        }
        
        // Process this member
        $result = processMemberCredentials($member);
        
        echo json_encode($result);
        
    } catch (Exception $e) {
        error_log("Error processing single member {$member_id}: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Error processing member',
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * PROCESS MEMBERS IN BATCH
 */
function processMembersBatch($batch_size) {
    global $conn;
    
    try {
        // Check rate limits
        $rate_limit_info = getRateLimitInfo();
        $available_slots = min(
            $rate_limit_info['hourly_remaining'],
            $rate_limit_info['daily_remaining'],
            $batch_size
        );
        
        if ($available_slots <= 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Rate limit exceeded. No emails can be sent at this time.',
                'rate_limit_info' => $rate_limit_info
            ]);
            return;
        }
        
        // Get pending members
        $members = getPendingMembers($available_slots);
        
        if (empty($members)) {
            echo json_encode([
                'status' => 'success',
                'message' => 'No pending members found',
                'processed' => 0,
                'rate_limit_info' => $rate_limit_info
            ]);
            return;
        }
        
        $results = [
            'total_processed' => 0,
            'successful_emails' => 0,
            'failed_emails' => 0,
            'members' => [],
            'rate_limit_info' => $rate_limit_info
        ];
        
        $start_time = time();
        
        foreach ($members as $member) {
            // Check if we're running out of time
            if ((time() - $start_time) > (MAX_EXECUTION_TIME - 60)) {
                error_log("Stopping batch processing due to time limit");
                break;
            }
            
            // Check rate limits before each email
            if (!checkRateLimits()) {
                error_log("Rate limit hit during batch processing");
                break;
            }
            
            $member_result = processMemberCredentials($member);
            
            $results['total_processed']++;
            $results['members'][] = $member_result;
            
            if ($member_result['status'] === 'success' || 
                ($member_result['status'] === 'warning' && isset($member_result['user_id']))) {
                $results['successful_emails']++;
            } else {
                $results['failed_emails']++;
            }
            
            // Rate limiting delay
            sleep(EMAIL_RATE_LIMIT_DELAY);
        }
        
        // Send batch summary report
        sendBatchSummaryReport($results);
        
        echo json_encode([
            'status' => 'success',
            'message' => "Batch processing completed. Processed {$results['total_processed']} members.",
            'results' => $results
        ]);
        
    } catch (Exception $e) {
        error_log("Error in batch processing: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Batch processing error',
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * PROCESS INDIVIDUAL MEMBER CREDENTIALS
 */
function processMemberCredentials($member) {
    global $conn;
    
    try {
        // Clean and validate email
        $cleaned_email = cleanAndValidateEmail($member['email']);
        if (!$cleaned_email['valid']) {
            return [
                'status' => 'error',
                'message' => 'Invalid email format',
                'member_id' => $member['id'],
                'member_name' => $member['first_name'] . ' ' . $member['last_name'],
                'error' => $cleaned_email['error']
            ];
        }
        
        $email = $cleaned_email['email'];
        
        // Start transaction
        $conn->autocommit(false);
        
        try {
            // Generate credentials
            $next_number = getNextUserIdNumber();
            if ($next_number === false) {
                throw new Exception("Failed to generate next user ID number");
            }
            
            $user_id = generateUserId($next_number);
            $password = generatePassword($user_id);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Ensure username uniqueness
            $user_id = ensureUniqueUsername($user_id, $member['id']);
            
            // Update member credentials
            $update_stmt = $conn->prepare("UPDATE members SET username = ?, password = ? WHERE id = ?");
            if (!$update_stmt) {
                throw new Exception("Failed to prepare update statement: " . $conn->error);
            }
            
            $update_stmt->bind_param("ssi", $user_id, $password_hash, $member['id']);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Failed to update credentials: " . $update_stmt->error);
            }
            
            $update_stmt->close();
            $conn->commit();
            
            // Send email using PHPMailer
            $email_result = sendLoginCredentialsEmail($email, $member['first_name'] . ' ' . $member['last_name'], $user_id, $password, $member['id']);
            
            // Record result
            $status = $email_result['success'] ? 'sent' : 'failed';
            $error_msg = $email_result['error'] ?? null;
            recordEmailSent($member['id'], $email, $user_id, $status, $error_msg);
            
            if ($email_result['success']) {
                return [
                    'status' => 'success',
                    'message' => 'Credentials updated and email sent successfully',
                    'member_id' => $member['id'],
                    'member_name' => $member['first_name'] . ' ' . $member['last_name'],
                    'member_email' => $email,
                    'user_id' => $user_id
                ];
            } else {
                return [
                    'status' => 'warning',
                    'message' => 'Credentials updated but email failed',
                    'member_id' => $member['id'],
                    'member_name' => $member['first_name'] . ' ' . $member['last_name'],
                    'member_email' => $email,
                    'user_id' => $user_id,
                    'error' => $error_msg
                ];
            }
            
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        } finally {
            $conn->autocommit(true);
        }
        
    } catch (Exception $e) {
        error_log("Error processing member {$member['id']}: " . $e->getMessage());
        return [
            'status' => 'error',
            'message' => 'Error processing member',
            'member_id' => $member['id'],
            'member_name' => $member['first_name'] . ' ' . $member['last_name'],
            'error' => $e->getMessage()
        ];
    }
}

/**
 * SEND EMAIL USING PHPMAILER WITH OUTLOOK
 */
function sendLoginCredentialsEmail($email, $memberName, $userId, $password, $memberId) {
    try {
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($email, $memberName);
        $mail->addReplyTo(SUPPORT_EMAIL, 'Sarathi Support');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Sarathi Cooperative - Login Credentials';
        
        // HTML email body
        $mail->Body = getEmailHTMLBody($memberName, $userId, $password);
        
        // Plain text alternative
        $mail->AltBody = getEmailTextBody($memberName, $userId, $password);

        // Additional settings
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        
        // Send the email
        $mail->send();
        
        error_log("Email sent successfully to {$email} for member {$memberId}");
        return ['success' => true];
        
    } catch (Exception $e) {
        $error_msg = "PHPMailer Error: {$mail->ErrorInfo}";
        error_log("Failed to send email to {$email} for member {$memberId}: {$error_msg}");
        return ['success' => false, 'error' => $error_msg];
    }
}

/**
 * GET HTML EMAIL BODY
 */
function getEmailHTMLBody($memberName, $userId, $password) {
    $cleanName = htmlspecialchars(trim($memberName), ENT_QUOTES, 'UTF-8') ?: "Member";
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Sarathi Cooperative Login Credentials</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2c5282; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; }
            .credentials { background-color: white; padding: 20px; margin: 20px 0; border-left: 4px solid #2c5282; border-radius: 3px; }
            .button { display: inline-block; background-color: #2c5282; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            .footer { background-color: #e9ecef; padding: 20px; text-align: center; font-size: 12px; color: #6c757d; border-radius: 0 0 5px 5px; }
            .important { color: #dc3545; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Sarathi Cooperative</h1>
                <p>Login Credentials</p>
            </div>
            
            <div class='content'>
                <h2>Dear {$cleanName},</h2>
                
                <p>Welcome to Sarathi Cooperative! Your login credentials have been generated successfully.</p>
                
                <div class='credentials'>
                    <h3>Your Login Details:</h3>
                    <p><strong>User ID:</strong> <code>{$userId}</code></p>
                    <p><strong>Password:</strong> <code>{$password}</code></p>
                </div>
                
                <p class='important'>Please keep these credentials secure and do not share them with anyone.</p>
                
                <p>You can now access your account using the button below:</p>
                
                <a href='https://sarathicooperative.org' class='button'>Login to Your Account</a>
                
                <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
                
                <p>Best regards,<br>
                <strong>Sarathi Cooperative Team</strong></p>
            </div>
            
            <div class='footer'>
                <p><strong>Support Contact:</strong></p>
                <p>Email: " . SUPPORT_EMAIL . " | Phone: " . SUPPORT_PHONE . "</p>
                <p>© " . date('Y') . " Sarathi Cooperative. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * GET PLAIN TEXT EMAIL BODY
 */
function getEmailTextBody($memberName, $userId, $password) {
    $cleanName = trim($memberName) ?: "Member";
    
    return "Dear {$cleanName},

Welcome to Sarathi Cooperative! Your login credentials have been generated successfully.

YOUR LOGIN DETAILS:
User ID: {$userId}
Password: {$password}

Login URL: https://sarathicooperative.org

IMPORTANT: Please keep these credentials secure and do not share them with anyone.

If you have any questions or need assistance, please contact our support team:
Email: " . SUPPORT_EMAIL . "
Phone: " . SUPPORT_PHONE . "

Best regards,
Sarathi Cooperative Team

© " . date('Y') . " Sarathi Cooperative. All rights reserved.";
}

/**
 * RATE LIMITING FUNCTIONS
 */
function checkRateLimits() {
    $info = getRateLimitInfo();
    return ($info['hourly_remaining'] > 0 && $info['daily_remaining'] > 0);
}

function getRateLimitInfo() {
    global $conn;
    
    try {
        // Check hourly limit
        $hourly_stmt = $conn->prepare("
            SELECT COUNT(*) as hourly_count 
            FROM email_records 
            WHERE sent_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            AND status = 'sent'
        ");
        
        $hourly_count = 0;
        if ($hourly_stmt) {
            $hourly_stmt->execute();
            $result = $hourly_stmt->get_result();
            $row = $result->fetch_assoc();
            $hourly_count = (int)$row['hourly_count'];
            $hourly_stmt->close();
        }
        
        // Check daily limit
        $daily_stmt = $conn->prepare("
            SELECT COUNT(*) as daily_count 
            FROM email_records 
            WHERE DATE(sent_at) = CURDATE()
            AND status = 'sent'
        ");
        
        $daily_count = 0;
        if ($daily_stmt) {
            $daily_stmt->execute();
            $result = $daily_stmt->get_result();
            $row = $result->fetch_assoc();
            $daily_count = (int)$row['daily_count'];
            $daily_stmt->close();
        }
        
        return [
            'hourly_limit' => HOURLY_EMAIL_LIMIT,
            'hourly_sent' => $hourly_count,
            'hourly_remaining' => max(0, HOURLY_EMAIL_LIMIT - $hourly_count),
            'daily_limit' => DAILY_EMAIL_LIMIT,
            'daily_sent' => $daily_count,
            'daily_remaining' => max(0, DAILY_EMAIL_LIMIT - $daily_count)
        ];
        
    } catch (Exception $e) {
        error_log("Error checking rate limits: " . $e->getMessage());
        return [
            'hourly_limit' => HOURLY_EMAIL_LIMIT,
            'hourly_sent' => 0,
            'hourly_remaining' => HOURLY_EMAIL_LIMIT,
            'daily_limit' => DAILY_EMAIL_LIMIT,
            'daily_sent' => 0,
            'daily_remaining' => DAILY_EMAIL_LIMIT
        ];
    }
}

/**
 * UTILITY FUNCTIONS
 */
function cleanAndValidateEmail($email) {
    $cleaned = trim(strtolower($email));
    $cleaned = preg_replace('/[\x00-\x1F\x7F]/', '', $cleaned);
    $cleaned = preg_replace('/\s+/', '', $cleaned);
    
    if (!filter_var($cleaned, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Invalid email format'];
    }
    
    if (strlen($cleaned) < 5 || strlen($cleaned) > 254) {
        return ['valid' => false, 'error' => 'Email length invalid'];
    }
    
    return ['valid' => true, 'email' => $cleaned];
}

function ensureUniqueUsername($user_id, $member_id) {
    global $conn;
    
    $attempt = 0;
    $max_attempts = 10;
    $original_user_id = $user_id;
    
    while ($attempt < $max_attempts) {
        $check_stmt = $conn->prepare("SELECT id FROM members WHERE username = ? AND id != ?");
        $check_stmt->bind_param("si", $user_id, $member_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            $check_stmt->close();
            return $user_id;
        }
        
        $check_stmt->close();
        $attempt++;
        $user_id = $original_user_id . $attempt;
    }
    
    throw new Exception("Could not generate unique username after $max_attempts attempts");
}

function createEmailRecordsTable() {
    global $conn;
    
    $sql = "CREATE TABLE IF NOT EXISTS email_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        member_id INT NOT NULL,
        recipient_email VARCHAR(255) NOT NULL,
        user_id VARCHAR(50) NOT NULL,
        status ENUM('sent', 'failed') NOT NULL,
        sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        error_message TEXT NULL,
        INDEX idx_member_id (member_id),
        INDEX idx_sent_at (sent_at),
        INDEX idx_status (status)
    )";
    
    if (!$conn->query($sql)) {
        throw new Exception("Failed to create email_records table: " . $conn->error);
    }
}

function recordEmailSent($member_id, $email, $user_id, $status, $error_message = null) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("INSERT INTO email_records (member_id, recipient_email, user_id, status, error_message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("issss", $member_id, $email, $user_id, $status, $error_message);
            $stmt->execute();
            $stmt->close();
        }
    } catch (Exception $e) {
        error_log("Failed to record email: " . $e->getMessage());
    }
}

function getNextUserIdNumber() {
    global $conn;
    
    try {
        $current_month = date('m');
        $current_year = date('y');
        $prefix = $current_month . $current_year;
        
        $stmt = $conn->prepare("
            SELECT username FROM members 
            WHERE username LIKE ? AND username REGEXP '^[0-9]{4}[0-9]+$'
            ORDER BY CAST(SUBSTRING(username, 5) AS UNSIGNED) DESC 
            LIMIT 1
        ");
        
        if (!$stmt) {
            return false;
        }
        
        $like_pattern = $prefix . '%';
        $stmt->bind_param("s", $like_pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $last_number = (int)substr($row['username'], 4);
            $next_number = $last_number + 1;
            $stmt->close();
            return $next_number;
        } else {
            $stmt->close();
            return 1;
        }
        
    } catch (Exception $e) {
        error_log("Error getting next user ID number: " . $e->getMessage());
        return false;
    }
}

function generateUserId($sequential_number) {
    $month = date('m');
    $year = date('y');
    return $month . $year . str_pad($sequential_number, 2, '0', STR_PAD_LEFT);
}

function generatePassword($user_id) {
    return 'Sarathi' . $user_id;
}

function getPendingMembers($limit = 50) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT m.id, m.first_name, m.last_name, m.email 
            FROM members m 
            LEFT JOIN email_records er ON m.id = er.member_id AND er.status = 'sent'
            WHERE m.status = 'active' 
            AND m.email IS NOT NULL 
            AND m.email != '' 
            AND (m.username IS NULL OR m.username = '' OR er.member_id IS NULL)
            ORDER BY m.id ASC
            LIMIT ?
        ");
        
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }
        
        $stmt->close();
        return $members;
        
    } catch (Exception $e) {
        error_log("Error getting pending members: " . $e->getMessage());
        return [];
    }
}

function sendBatchSummaryReport($results) {
    try {
        // Use PHPMailer for batch summary report as well
        $mail = new PHPMailer(true);

        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress(RECORD_EMAIL, 'Sarathi Admin');

        // Content
        $mail->isHTML(false); // Plain text for reports
        $mail->Subject = 'Batch Email Processing Report - ' . date('Y-m-d H:i:s');
        
        $message = "Batch Processing Summary:\n\n";
        $message .= "Total Processed: " . $results['total_processed'] . "\n";
        $message .= "Successful Emails: " . $results['successful_emails'] . "\n";
        $message .= "Failed Emails: " . $results['failed_emails'] . "\n";
        $message .= "Processing Date: " . date('Y-m-d H:i:s') . "\n\n";
        
        $message .= "Rate Limit Status:\n";
        $message .= "Hourly: " . $results['rate_limit_info']['hourly_sent'] . "/" . $results['rate_limit_info']['hourly_limit'] . "\n";
        $message .= "Daily: " . $results['rate_limit_info']['daily_sent'] . "/" . $results['rate_limit_info']['daily_limit'] . "\n\n";
        
        if (!empty($results['members'])) {
            $message .= "Member Details:\n";
            foreach ($results['members'] as $member) {
                $status = $member['status'] === 'success' ? 'SUCCESS' : ($member['status'] === 'warning' ? 'WARNING' : 'FAILED');
                $message .= "- ID: " . $member['member_id'] . " | " . $member['member_name'] . " | " . $status . "\n";
            }
        }
        
        $mail->Body = $message;
        $mail->send();
        
    } catch (Exception $e) {
        error_log("Failed to send batch summary report: " . $e->getMessage());
    }
}
?>