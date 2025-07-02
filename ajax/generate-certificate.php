<?php
// Add this at the very beginning to catch any early errors
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set content type for JSON response
header('Content-Type: application/json');

// Function to clean output and send JSON response
function sendJsonResponse($data) {
    // Clean any output that might have been generated
    ob_clean();
    echo json_encode($data);
    exit;
}

// Function to handle errors and send JSON response
function handleError($message, $debug_info = null) {
    $error_data = ['success' => false, 'error' => $message];
    if ($debug_info) {
        $error_data['debug'] = $debug_info;
    }
    sendJsonResponse($error_data);
}

session_start();

// Check if config and functions files exist
if (!file_exists('../includes/config.php')) {
    handleError('Config file not found', 'config.php missing');
}
if (!file_exists('../includes/functions.php')) {
    handleError('Functions file not found', 'functions.php missing');
}

require_once '../includes/config.php';
require_once '../includes/functions.php';

// Try to include TCPDF - check multiple possible locations
$tcpdf_loaded = false;
$possible_paths = [
    '../vendor/autoload.php',
    '../../vendor/autoload.php',
    '../../../vendor/autoload.php',
    '../tcpdf/tcpdf.php',
    '../../tcpdf/tcpdf.php'
];

$tried_paths = [];
foreach ($possible_paths as $path) {
    $tried_paths[] = $path . ' - ' . (file_exists($path) ? 'EXISTS' : 'NOT FOUND');
    if (file_exists($path)) {
        require_once $path;
        $tcpdf_loaded = true;
        break;
    }
}

if (!$tcpdf_loaded) {
    handleError('TCPDF library not found', $tried_paths);
}

// Check if user is logged in (comment out for testing)
// if (!isset($_SESSION['user_id'])) {
//     handleError('Unauthorized access');
// }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    handleError('Invalid request method - only POST allowed');
}

try {
    // Debug: Log all POST data
    error_log("POST data received: " . print_r($_POST, true));
    
    // Get form data with error checking
    $required_fields = ['member_id', 'nominee_name', 'shares_alloted', 'share_sequence', 'aadhar_number', 'pan_number'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        handleError('Missing required form fields', $missing_fields);
    }
    
    $member_id = intval($_POST['member_id']);
    $nominee_name = trim($_POST['nominee_name']);
    $shares_alloted = intval($_POST['shares_alloted']);
    $share_sequence = trim($_POST['share_sequence']);
    $aadhar_number = trim($_POST['aadhar_number']);
    $pan_number = trim($_POST['pan_number']);
    
    // Validate required fields
    $empty_fields = [];
    if (empty($member_id)) $empty_fields[] = 'member_id';
    if (empty($nominee_name)) $empty_fields[] = 'nominee_name';
    if (empty($shares_alloted)) $empty_fields[] = 'shares_alloted';
    if (empty($share_sequence)) $empty_fields[] = 'share_sequence';
    if (empty($aadhar_number)) $empty_fields[] = 'aadhar_number';
    if (empty($pan_number)) $empty_fields[] = 'pan_number';
    
    if (!empty($empty_fields)) {
        handleError('Empty required fields', $empty_fields);
    }
    
    // Check database connection
    if (!isset($conn)) {
        handleError('Database connection not available', 'Check config.php');
    }
    
    if (!$conn) {
        handleError('Database connection failed', mysqli_connect_error());
    }
    
    // Get member details from database (including username)
    $stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
    if (!$stmt) {
        handleError('Database prepare failed', $conn->error);
    }
    
    $stmt->bind_param("i", $member_id);
    if (!$stmt->execute()) {
        handleError('Database execute failed', $stmt->error);
    }
    
    $result = $stmt->get_result();
    $member = $result->fetch_assoc();
    
    if (!$member) {
        handleError('Member not found', 'Member ID: ' . $member_id);
    }
    
    // Check if TCPDF class exists
    if (!class_exists('TCPDF')) {
        handleError('TCPDF class not found after inclusion');
    }
    
    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Sarathi Cooperative');
    $pdf->SetAuthor('Sarathi Research Consulting and Management Services Cooperative Limited');
    $pdf->SetTitle('Share Certificate - ' . $member['first_name'] . ' ' . $member['last_name']);
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 10);
    
    // Add decorative border - simple double border
    $pdf->SetLineWidth(2);
    $pdf->Rect(10, 10, 190, 277, 'D');
    $pdf->SetLineWidth(1);
    $pdf->Rect(12, 12, 186, 273, 'D');
    
    // Add corner decorations (simple rectangles as decoration)
    $pdf->SetFillColor(0, 0, 0);
    $pdf->Rect(10, 10, 15, 15, 'F');
    $pdf->Rect(185, 10, 15, 15, 'F');
    $pdf->Rect(10, 275, 15, 15, 'F');
    $pdf->Rect(185, 275, 15, 15, 'F');
    
    // Reset fill color
    $pdf->SetFillColor(255, 255, 255);
    
    // Add logo area (placeholder - you can add actual logo)
    $pdf->SetXY(80, 25);
    $pdf->SetFont('helvetica', 'B', 24);
    $pdf->SetTextColor(150, 50, 150);
    $pdf->Cell(50, 15, 'sarathi', 0, 1, 'C');
    
    $pdf->SetXY(75, 40);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(60, 8, 'Cooperative', 0, 1, 'C');
    
    $pdf->SetXY(70, 50);
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->SetTextColor(0, 150, 0);
    $pdf->Cell(70, 6, 'a Coop of Consultants', 0, 1, 'C');
    
    // Company details
    $pdf->SetXY(20, 65);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(170, 8, 'Sarathi Research Consulting and Management Services Cooperative Limited', 0, 1, 'C');
    
    $pdf->SetXY(20, 75);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(170, 5, 'Regd. Office: B-21, Lajpat Nagar - 1, New Delhi - 110024', 0, 1, 'C');
    
    $pdf->SetXY(20, 82);
    $pdf->Cell(170, 5, 'Registered with the Central Registrar of Societies:', 0, 1, 'C');
    
    $pdf->SetXY(20, 89);
    $pdf->Cell(170, 5, 'Registration Nbr. MSCS/CR/1610/2025', 0, 1, 'C');
    
    // Certification text
    $pdf->SetXY(20, 105);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell(170, 4, 'THIS IS TO CERTIFY that the person(s) named in this Warrant is/are the Registered Holder(s) of the within mentioned Warrant(s) bearing the distinctive number(s) herein specified, in terms of and subject to the conditions printed overleaf, and as stipulated in the Sarathi Rule Book/ Byelaws dated 2nd April, 2026', 0, 'C');
    
    // Main title
    $pdf->SetXY(20, 125);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(170, 10, 'NON-TRADEABLE NON-TRANSFERABLE WARRANT', 0, 1, 'C');
    
    $pdf->SetXY(20, 135);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(170, 5, 'FOR ALLOTMENT OF ONE SHARE OF THE FACE VALUE OF Rs. 100/- OF THE COOPERATIVE', 0, 1, 'C');
    
    $pdf->SetXY(20, 142);
    $pdf->Cell(170, 5, 'FOR EACH WARRANT HOLDING', 0, 1, 'C');
    
    // Certificate details table - exactly matching screenshot layout
    $pdf->SetXY(25, 155);
    $pdf->SetFont('helvetica', '', 10);
    
    // Table structure - using username instead of subscriber registration number
    $table_data = [
        ['Subscriber Registered Nbr:', $member['username'] ?? '', 'Date:', date('d/m/Y')],
        ['Name of the Holder:', ($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''), 'Date of Birth:', !empty($member['date_of_birth']) ? date('d/m/Y', strtotime($member['date_of_birth'])) : ''],
        ['Address:', ($member['address'] ?? '') . ', ' . ($member['city'] ?? '') . ', ' . ($member['state'] ?? ''), '', ''],
        ['Contact Number:', $member['phone'] ?? '', 'Email Id:', $member['email'] ?? ''],
        ['Aadhar Card:', $aadhar_number, 'PAN Card:', $pan_number],
        ['Nominee Name:', $nominee_name, 'Contact Number:', ''],
        ['No of Shares Allotted:', $shares_alloted, '', ''],
        ['Share Sequential Number:', $share_sequence, 'To:', '']
    ];
    
    $y_pos = 155;
    foreach ($table_data as $row) {
        $pdf->SetXY(25, $y_pos);
        
        // First column
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(45, 7, $row[0], 1, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(45, 7, $row[1], 1, 0, 'L');
        
        // Second column (if exists)
        if (!empty($row[2])) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(35, 7, $row[2], 1, 0, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->Cell(35, 7, $row[3], 1, 0, 'L');
        } else {
            $pdf->Cell(70, 7, '', 1, 0, 'L');
        }
        
        $y_pos += 7;
    }
    
    // Company signature section - positioned right after table
    $pdf->SetXY(20, $y_pos + 8);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(170, 6, 'For Sarathi Research Consulting and', 0, 1, 'C');
    $pdf->SetXY(20, $y_pos + 14);
    $pdf->Cell(170, 6, 'Management Services Cooperative Limited', 0, 1, 'C');
    
    // Seal and signature area - positioned side by side
    $pdf->SetXY(40, $y_pos + 25);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Rect(40, $y_pos + 25, 25, 15, 'D'); // Seal box
    $pdf->SetXY(42, $y_pos + 32);
    $pdf->Cell(21, 6, 'SEAL', 0, 1, 'C');
    
    // Signature line
    $pdf->SetXY(110, $y_pos + 35);
    $pdf->Line(110, $y_pos + 35, 170, $y_pos + 35); // Signature line
    $pdf->SetXY(110, $y_pos + 38);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(60, 4, 'Chairman / Chief Executive Officer', 0, 1, 'C');
    
    // Notes section - positioned to fit remaining space
    $notes_y = $y_pos + 48;
    $pdf->SetXY(20, $notes_y);
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(20, 4, 'Notes:', 0, 1, 'L');
    
    $notes = [
        '(1) Share Transfer is Not Allowed',
        '(2) Nominee role is limited to the extent of returning the Shares in case of the Shareholder\'s unavoidable',
        '     unavailability. Such cases will require additional documentary proof to be included and available',
        '     when requested.',
        '(3) Board of Directors decisions in all cases will be considered final and irrefutable'
    ];
    
    $notes_y += 5;
    foreach ($notes as $note) {
        $pdf->SetXY(25, $notes_y);
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(160, 3.5, $note, 0, 1, 'L');
        $notes_y += 3.5;
    }
    
    // Generate absolute file path
    $filename = 'certificate_' . $member['id'] . '_' . time() . '.pdf';
    
    // Get the absolute path to the certificates directory
    $certificates_dir = realpath(dirname(__FILE__) . '/../certificates');
    if (!$certificates_dir) {
        // If certificates directory doesn't exist, create it first
        $certificates_relative = dirname(__FILE__) . '/../certificates';
        if (!file_exists($certificates_relative)) {
            if (!mkdir($certificates_relative, 0755, true)) {
                handleError('Failed to create certificates directory');
            }
        }
        $certificates_dir = realpath($certificates_relative);
    }
    
    if (!$certificates_dir) {
        handleError('Could not resolve certificates directory path');
    }
    
    // Use absolute path for file output
    $filepath = $certificates_dir . '/' . $filename;
    
    // Check if directory is writable
    if (!is_writable($certificates_dir)) {
        handleError('Certificates directory is not writable', 'Directory: ' . $certificates_dir);
    }
    
    // Save PDF file using absolute path
    try {
        $pdf->Output($filepath, 'F');
        
        // Verify file was created
        if (!file_exists($filepath)) {
            handleError('PDF file was not created successfully', 'Path: ' . $filepath);
        }
        
        // Get file size for verification
        $filesize = filesize($filepath);
        if ($filesize === false || $filesize < 1000) {
            handleError('PDF file appears to be corrupted or empty', 'Size: ' . $filesize . ' bytes');
        }
        
    } catch (Exception $pdf_error) {
        handleError('PDF generation failed', $pdf_error->getMessage());
    }
    
    // Store certificate record in database (optional - comment out if table doesn't exist)
    /*
    try {
        $stmt = $conn->prepare("INSERT INTO certificates (member_id, nominee_name, shares_alloted, share_sequence, aadhar_number, pan_number, certificate_path, generated_by, generated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ississsi", $member_id, $nominee_name, $shares_alloted, $share_sequence, $aadhar_number, $pan_number, $filepath, $_SESSION['user_id']);
        $stmt->execute();
    } catch (Exception $db_error) {
        // Log database error but don't fail certificate generation
        error_log("Certificate DB logging error: " . $db_error->getMessage());
    }
    */
    
    // Return success response with PDF URL (relative URL for web access)
    sendJsonResponse([
        'success' => true,
        'pdf_url' => 'certificates/' . $filename,
        'message' => 'Certificate generated successfully',
        'member_name' => ($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''),
        'file_size' => filesize($filepath),
        'file_path' => $filepath // For debugging
    ]);
    
} catch (Exception $e) {
    error_log("Certificate generation error: " . $e->getMessage());
    handleError('Certificate generation failed', $e->getMessage());
} catch (Error $e) {
    error_log("Certificate generation fatal error: " . $e->getMessage());
    handleError('Fatal error during certificate generation', $e->getMessage());
}
?>