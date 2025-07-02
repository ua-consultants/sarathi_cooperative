<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../vendor/autoload.php';
// require_once '../includes/mailer/php';

use PhpOffice/PHPSpreadSheet/IOFactory

use Smalot\PdfParser\Parser;
use PHPExcel_IOFactory;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['fileUpload']) || $_FILES['fileUpload']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'File upload error']);
    exit;
}

$upload = $_FILES['fileUpload'];
$tmpName = $upload['tmp_name'];
$fileName = $upload['name'];
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Check if file type is supported
if (!in_array($extension, ['xls', 'xlsx', 'pdf'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unsupported file format. Only Excel (.xls, .xlsx) and PDF (.pdf) are allowed.']);
    exit;
}

$members = [];

// Parse Excel File
if ($extension === 'xls' || $extension === 'xlsx') {
    try {
        $objPHPExcel = PHPExcel_IOFactory::load($tmpName);
        $sheet = $objPHPExcel->getActiveSheet();
        $rows = $sheet->toArray();

        // Skip header row
        array_shift($rows);

        foreach ($rows as $row) {
            $firstName = trim($row[0]);
            $lastName = !empty($row[1]) ? trim($row[1]) : '';
            $email = filter_var(trim($row[2]), FILTER_VALIDATE_EMAIL);
            $phone = trim($row[3]);
            $highestQualification = trim($row[4]);
            $areaOfExpertise = trim($row[5]);
            $zipCode = trim($row[6]);
            $city = trim($row[7]);
            $state = trim($row[8]);

            if (!$email || !$firstName) continue;

            $members[] = compact(
                'firstName', 'lastName', 'email', 'phone',
                'highestQualification', 'areaOfExpertise',
                'zipCode', 'city', 'state'
            );
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error reading Excel file: ' . $e->getMessage()]);
        exit;
    }
}

// Parse PDF File
if ($extension === 'pdf') {
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($tmpName);
        $text = $pdf->getText();
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            $data = str_getcsv(trim($line));
            if (count($data) < 9) continue;

            $firstName = trim($data[0]);
            $lastName = trim($data[1]);
            $email = filter_var(trim($data[2]), FILTER_VALIDATE_EMAIL);
            $phone = trim($data[3]);
            $highestQualification = trim($data[4]);
            $areaOfExpertise = trim($data[5]);
            $zipCode = trim($data[6]);
            $city = trim($data[7]);
            $state = trim($data[8]);

            if (!$email || !$firstName) continue;

            $members[] = compact(
                'firstName', 'lastName', 'email', 'phone',
                'highestQualification', 'areaOfExpertise',
                'zipCode', 'city', 'state'
            );
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error reading PDF file: ' . $e->getMessage()]);
        exit;
    }
}

if (empty($members)) {
    echo json_encode(['status' => 'error', 'message' => 'No valid member data found in the file.']);
    exit;
}

$inserted = 0;
$errors = [];

foreach ($members as $member) {
    // Generate Username: MMYY + 3-digit auto-increment (starts after 120)
    $monthYear = date('my'); // e.g., 0625
    $stmt = $conn->query("SELECT MAX(id) as last_id FROM members");
    $row = $stmt->fetch_assoc();
    $lastID = $row['last_id'] ?? 120;
    $nextID = $lastID + 1;
    $username = $monthYear . str_pad($nextID, 3, '0', STR_PAD_LEFT);

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
    $stmt->bind_param("s", $member['email']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Duplicate email: {$member['email']}";
        continue;
    }

    // Insert Member
    $stmt = $conn->prepare("
        INSERT INTO members (
            first_name, last_name, email, phone,
            highest_qualification, area_of_expertise,
            zip_code, city, state, status, member_type,
            joined_date, updated_at, username
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', 'general', NOW(), NOW(), ?)
    ");

    $stmt->bind_param(
        "ssssssssss",
        $member['firstName'],
        $member['lastName'],
        $member['email'],
        $member['phone'],
        $member['highestQualification'],
        $member['areaOfExpertise'],
        $member['zipCode'],
        $member['city'],
        $member['state'],
        $username
    );

    if ($stmt->execute()) {
        $inserted++;

        // Send Email Notification
        echo('Successfully Added Members');
    } else {
        $errors[] = "Failed to insert: {$member['email']} - " . $stmt->error;
    }
}

$response = [
    'status' => $inserted > 0 ? 'success' : 'partial_success',
    'message' => "$inserted member(s) imported successfully.",
];

if (!empty($errors)) {
    $response['errors'] = $errors;
}

echo json_encode($response);
?>