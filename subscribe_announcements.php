<?php
$conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');

if ($connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['email'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
    exit;
}

// Check if email already exists
$check_sql = "SELECT id FROM announcement_subscribers WHERE email = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This email is already subscribed to our announcements']);
    exit;
}

// Insert new subscriber
$insert_sql = "INSERT INTO announcement_subscribers (email, subscribed_at, status) VALUES (?, NOW(), 'active')";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("s", $email);

if ($insert_stmt->execute()) {
    // Send welcome email
    $subject = "Welcome to Sarathi Cooperative Announcements";
    $message = "
    <html>
    <body>
        <h2>Welcome to Sarathi Cooperative!</h2>
        <p>Thank you for subscribing to our announcements. You will now receive updates about:</p>
        <ul>
            <li>Board of Directors meetings (2nd Saturday of every month)</li>
            <li>Online webinars (Last Saturday of every month)</li>
            <li>Quarterly General Body meetings</li>
            <li>Special announcements and news</li>
        </ul>
        <p>Stay connected with Sarathi Cooperative!</p>
    </body>
    </html>";
    $headers = "From: sarathi@sarathicooperative.org\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    mail($email, $subject, $message, $headers);
    
    echo json_encode(['success' => true, 'message' => 'Successfully subscribed! You will receive email notifications for new announcements.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to subscribe. Please try again later.']);
}

$conn->close();
?>
