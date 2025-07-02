<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

try {
    if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }

    $inputFileName = $_FILES['excelFile']['tmp_name'];
    $spreadsheet = IOFactory::load($inputFileName);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    // Remove header row
    array_shift($rows);

    $successCount = 0;
    $failureCount = 0;
    $errors = [];

    foreach ($rows as $row) {
        $name = trim($row[0]);
        $email = trim($row[1]);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email: $email";
            $failureCount++;
            continue;
        }

        // Send email notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sarathicooperative@outlook.com'; // Update with your email
            $mail->Password = 'Business777!'; // Update with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-email@gmail.com', 'Sarathi Cooperative');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Complete Your Sarathi Membership Form';
            $mail->Body = <<<HTML
                <html>
                <body>
                    <h2>Welcome to Sarathi Cooperative!</h2>
                    <p>Dear $name,</p>
                    <p>Please complete your membership registration by following these steps:</p>
                    <ol>
                        <li>Visit <a href="https://sarathicooperative.org/sarathi-new">our registration page</a></li>
                        <li>Click on the "Interested? Let's get started" button</li>
                        <li>Fill out the complete membership form</li>
                    </ol>
                    <p><strong>Important:</strong> Please ensure you complete the entire form once you start.</p>
                    <p>If you have any questions, feel free to contact us.</p>
                    <br>
                    <p>Best regards,<br>Sarathi Cooperative Team</p>
                </body>
                </html>
HTML;

            $mail->send();
            $successCount++;
        } catch (Exception $e) {
            $errors[] = "Failed to send email to $email: " . $mail->ErrorInfo;
            $failureCount++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => "Processed $successCount members successfully. Failed: $failureCount",
        'errors' => $errors
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}