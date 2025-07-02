<?php
// ajax/process-pdf-import.php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

// Include PDF parser library (you'll need to install Smalot/PdfParser via Composer)
require_once '../vendor/autoload.php';

use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

try {
    if (!isset($_FILES['pdfFile']) || $_FILES['pdfFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No PDF file uploaded or upload error occurred.');
    }

    $uploadedFile = $_FILES['pdfFile']['tmp_name'];
    $fileName = $_FILES['pdfFile']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file extension
    if ($fileExtension !== 'pdf') {
        throw new Exception('Invalid file format. Please upload PDF files only.');
    }

    // Parse PDF
    $parser = new Parser();
    $pdf = $parser->parseFile($uploadedFile);
    $text = $pdf->getText();

    if (empty(trim($text))) {
        throw new Exception('Could not extract text from PDF. The PDF might be image-based or encrypted.');
    }

    // Extract member data from text
    $memberData = extractMemberDataFromText($text);
    
    // Validate required fields
    if (empty($memberData['email'])) {
        throw new Exception('Could not find a valid email address in the PDF.');
    }
    
    if (empty($memberData['first_name']) && empty($memberData['last_name'])) {
        throw new Exception('Could not find a name in the PDF.');
    }

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
    $stmt->bind_param("s", $memberData['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        throw new Exception("Email '{$memberData['email']}' already exists in the system.");
    }

    // Return data for preview
    echo json_encode([
        'status' => 'success',
        'preview_data' => $memberData,
        'extracted_text' => substr($text, 0, 500) . '...', // First 500 characters for reference
        'message' => 'PDF processed successfully. Member data extracted.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function extractMemberDataFromText($text) {
    $member = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'highest_qualification' => '',
        'area_of_expertise' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'date_of_birth' => null,
        'linkedin_url' => '',
        'journey' => '',
        'introducer' => '',
        'introducer_contact' => '',
        'status' => 'active',
        'member_type' => 'general',
        'profile_image' => 'default-avatar.png'
    ];

    // Clean text
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    
    // Extract email
    $emailPattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
    if (preg_match($emailPattern, $text, $matches)) {
        $member['email'] = strtolower($matches[0]);
        $member['username'] = generateUsername($member['email']);
        $member['password'] = password_hash('defaultpass123', PASSWORD_DEFAULT);
    }

    // Extract phone numbers
    $phonePatterns = [
        '/(?:\+91|91)?[\s-]?[6-9]\d{9}/', // Indian mobile numbers
        '/(?:\+1)?[\s-]?\(?\d{3}\)?[\s-]?\d{3}[\s-]?\d{4}/', // US numbers
        '/(?:\+\d{1,3})?[\s-]?\d{10,14}/' // General international
    ];
    
    foreach ($phonePatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $member['phone'] = preg_replace('/[^0-9+]/', '', $matches[0]);
            break;
        }
    }

    // Extract name patterns
    $namePatterns = [
        '/Name\s*:?\s*([A-Za-z\s]+)/i',
        '/(?:Mr\.?|Ms\.?|Mrs\.?|Dr\.?)\s+([A-Za-z\s]+)/i',
        '/([A-Z][a-z]+\s+[A-Z][a-z]+)/' // Capitalized first and last name
    ];
    
    foreach ($namePatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $fullName = trim($matches[1]);
            $nameParts = explode(' ', $fullName, 2);
            $member['first_name'] = $nameParts[0];
            $member['last_name'] = isset($nameParts[1]) ? $nameParts[1] : '';
            break;
        }
    }

    // Extract date of birth
    $dobPatterns = [
        '/(?:Date of Birth|DOB|Born)\s*:?\s*(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/i',
        '/(?:Date of Birth|DOB|Born)\s*:?\s*(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})/i',
        '/(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})/', // Generic date pattern
    ];
    
    foreach ($dobPatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $member['date_of_birth'] = parseDate($matches[1]);
            if ($member['date_of_birth']) break;
        }
    }

    // Extract qualification/education
    $qualificationKeywords = ['qualification', 'education', 'degree', 'graduate', 'diploma', 'certificate'];
    foreach ($qualificationKeywords as $keyword) {
        $pattern = '/' . $keyword . '\s*:?\s*([A-Za-z\s,\.]+)/i';
        if (preg_match($pattern, $text, $matches)) {
            $qualification = trim($matches[1]);
            if (strlen($qualification) > 5 && strlen($qualification) < 100) {
                $member['highest_qualification'] = $qualification;
                break;
            }
        }
    }

    // Extract expertise/profession
    $expertiseKeywords = ['expertise', 'profession', 'occupation', 'specialization', 'skills', 'experience'];
    foreach ($expertiseKeywords as $keyword) {
        $pattern = '/' . $keyword . '\s*:?\s*([A-Za-z\s,\.]+)/i';
        if (preg_match($pattern, $text, $matches)) {
            $expertise = trim($matches[1]);
            if (strlen($expertise) > 5 && strlen($expertise) < 100) {
                $member['area_of_expertise'] = $expertise;
                break;
            }
        }
    }

    // Extract address components
    $addressPatterns = [
        '/(?:Address|Location)\s*:?\s*([A-Za-z0-9\s,.-]+)/i'
    ];
    
    foreach ($addressPatterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $address = trim($matches[1]);
            $member['address'] = $address;
            
            // Try to extract city and state from address
            $addressParts = explode(',', $address);
            if (count($addressParts) >= 2) {
                $member['city'] = trim($addressParts[count($addressParts) - 2]);
                $member['state'] = trim($addressParts[count($addressParts) - 1]);
            }
            break;
        }
    }

    // Extract PIN/ZIP code
    $zipPattern = '/(?:PIN|ZIP|Postal Code)\s*:?\s*(\d{5,6})/i';
    if (preg_match($zipPattern, $text, $matches)) {
        $member['zip_code'] = $matches[1];
    }

    // Extract LinkedIn URL
    $linkedinPattern = '/(?:linkedin\.com\/in\/|linkedin\.com\/profile\/)[A-Za-z0-9\-]+/i';
    if (preg_match($linkedinPattern, $text, $matches)) {
        $member['linkedin_url'] = 'https://' . $matches[0];
    }

    // Extract journey/bio (look for longer text sections)
    $bioKeywords = ['about', 'biography', 'journey', 'background', 'story', 'profile'];
    foreach ($bioKeywords as $keyword) {
        $pattern = '/' . $keyword . '\s*:?\s*([A-Za-z0-9\s,.\-]{50,500})/i';
        if (preg_match($pattern, $text, $matches)) {
            $member['journey'] = trim($matches[1]);
            break;
        }
    }

    // Extract introducer information
    $introducerKeywords = ['introduced by', 'referred by', 'sponsor', 'reference'];
    foreach ($introducerKeywords as $keyword) {
        $pattern = '/' . $keyword . '\s*:?\s*([A-Za-z\s]+)/i';
        if (preg_match($pattern, $text, $matches)) {
            $member['introducer'] = trim($matches[1]);
            break;
        }
    }

    return $member;
}

function parseDate($dateString) {
    if (empty($dateString)) {
        return null;
    }
    
    // Try different date formats
    $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'm-d-Y', 'Y/m/d'];
    
    foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $dateString);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
    }
    
    // Try to parse with strtotime
    $timestamp = strtotime($dateString);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    
    return null;
}

function generateUsername($email) {
    $username = explode('@', $email)[0];
    $username = preg_replace('/[^a-zA-Z0-9]/', '', $username);
    return strtolower($username);
}
?>