<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ajax/process-excel-import.php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

// Include PhpSpreadsheet library (you'll need to install via Composer)
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

header('Content-Type: application/json');

try {
    if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred.');
    }

    $uploadedFile = $_FILES['excelFile']['tmp_name'];
    $fileName = $_FILES['excelFile']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file extension
    if (!in_array($fileExtension, ['xlsx', 'xls'])) {
        throw new Exception('Invalid file format. Please upload Excel files only (.xlsx or .xls).');
    }

    // Load the spreadsheet
    $spreadsheet = IOFactory::load($uploadedFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();

    if (empty($rows)) {
        throw new Exception('The Excel file appears to be empty.');
    }

    // Get headers from first row and normalize them - FIX APPLIED HERE
    $headers = array_map(function($header) {
        return trim($header ?? '');
    }, $rows[0]);
    $normalizedHeaders = array_map('normalizeHeader', $headers);
    
    // Create column mapping
    $columnMapping = createColumnMapping($normalizedHeaders);
    
    // DEBUG: Add column mapping info to response for troubleshooting
    $mappingDebug = [];
    foreach ($columnMapping as $field => $columnIndex) {
        $mappingDebug[$field] = [
            'column_index' => $columnIndex,
            'original_header' => $headers[$columnIndex] ?? 'Unknown',
            'normalized_header' => $normalizedHeaders[$columnIndex] ?? 'Unknown'
        ];
    }
    
    if (empty($columnMapping)) {
        throw new Exception('No recognizable columns found. Please ensure your Excel file contains columns like Name, Email, Phone, etc. Available headers: ' . implode(', ', $headers));
    }

    $members = [];
    $errors = [];
    
    // Process data rows (skip header row)
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        
        // Skip completely empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        
        try {
            $member = processRowData($row, $columnMapping, $headers);
            
            // Modified validation - email is no longer required if not present in sheet
            if (!empty($member['email'])) {
                // Only validate email if it's provided
                if (!filter_var($member['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row " . ($i + 1) . ": Invalid email format: {$member['email']}";
                    continue;
                }
                
                // Check for duplicate email only if email exists
                $stmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
                $stmt->bind_param("s", $member['email']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Row " . ($i + 1) . ": Email '{$member['email']}' already exists";
                    continue;
                }
            } else {
                // Generate a placeholder email if none provided
                $member['email'] = generatePlaceholderEmail($member, $i);
            }
            
            if (empty($member['first_name']) && empty($member['last_name'])) {
                $errors[] = "Row " . ($i + 1) . ": At least first name or last name is required";
                continue;
            }
            
            $members[] = $member;
            
        } catch (Exception $e) {
            $errors[] = "Row " . ($i + 1) . ": " . $e->getMessage();
        }
    }

    if (empty($members)) {
        throw new Exception('No valid member data found. Errors: ' . implode(', ', $errors));
    }

    // If preview is requested, return data for preview
    if (isset($_POST['preview']) || !isset($_POST['confirm'])) {
        // Store the processed members data in session for later use
        $_SESSION['member_import_preview'] = $members;
        
        echo json_encode([
            'status' => 'success',
            'preview_data' => $members,
            'total_rows' => count($members),
            'errors' => $errors,
            'column_mapping' => $mappingDebug,
            'message' => 'Data processed successfully. ' . count($members) . ' members ready for import.'
        ]);
    } else {
        // Direct import without preview
        $importedCount = importMembersToDatabase($members, $conn);
        
        echo json_encode([
            'status' => 'success',
            'imported_count' => $importedCount,
            'errors' => $errors,
            'column_mapping' => $mappingDebug, // Include mapping debug info
            'message' => "$importedCount members imported successfully."
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

function normalizeHeader($header) {
    // Convert to lowercase and remove special characters - NULL SAFETY ADDED
    $normalized = strtolower(trim($header ?? ''));
    $normalized = preg_replace('/[^a-z0-9\s]/', '', $normalized);
    $normalized = preg_replace('/\s+/', ' ', $normalized);
    return $normalized;
}

function createColumnMapping($normalizedHeaders) {
    $mapping = [];
    
    // Define possible column variations for each field - COMPREHENSIVE MAPPING
    $fieldMappings = [
        // Basic Info
        'sr_no' => ['sr no', 'serial number', 'sno', 's no', 'sno', 'sl no', 'serial no'],
        'member_id' => ['member id', 'memberid', 'id', 'member no', 'membership id', 'membership number', 'member number'],
        'first_name' => ['first fame', 'firstname', 'fname', 'given name', 'forename', 'name first'],
        'last_name' => ['last name', 'lastname', 'lname', 'surname', 'family name', 'name last'],
        'email' => ['email', 'email address', 'mail', 'e mail', 'email id', 'e-mail', 'electronic mail'],
        'phone' => ['phone', 'phone number', 'mobile', 'contact', 'tel', 'telephone', 'Mobile No.', 'mobile number', 'contact number', 'cell', 'cellphone'],
        
        // Demographics
        'age' => ['age', 'years', 'age in years', 'current age'],
        'date_of_birth' => ['date of birth', 'dob', 'birth date', 'birthday', 'date birth', 'birthdate'],
        'gender' => ['gender', 'sex', 'male female'],
        
        // Location
        'address' => ['address', 'full address', 'street address', 'location', 'home address', 'residence'],
        'city' => ['city', 'town', 'district'],
        'state' => ['state', 'province', 'region'],
        'zip_code' => ['zip', 'zip code', 'postal code', 'pincode', 'pin', 'postal', 'zipcode'],
        'country' => ['country', 'nation'],
        
        // Professional Info
        'profession' => ['profession', 'occupation', 'job', 'work', 'job title', 'designation', 'position', 'career', 'profession please list qualification'],
        'highest_qualification' => ['qualification', 'education', 'degree', 'highest qualification', 'academic qualification', 'educational qualification', 'please list qualification'],
        'area_of_expertise' => ['expertise', 'specialization', 'area of expertise', 'skills', 'speciality', 'domain', 'field'],
        'company' => ['company', 'organization', 'employer', 'workplace', 'office'],
        'experience' => ['experience', 'work experience', 'years of experience', 'exp'],
        
        // Social/Professional Links
        'linkedin_url' => ['linkedin', 'linkedin url', 'linkedin profile', 'linkedin link', 'linkedin id'],
        'website' => ['website', 'personal website', 'web site', 'homepage', 'url'],
        
        // Membership Info
        'introducer' => ['introducer', 'referred by', 'reference', 'sponsor', 'referrer', 'introduced by'],
        'introducer_contact' => ['introducer contact', 'reference contact', 'sponsor contact', 'referrer contact'],
        'join_date' => ['join date', 'joining date', 'date joined', 'membership date', 'registration date'],
        'member_type' => ['member type', 'membership type', 'category', 'type'],
        'status' => ['status', 'member status', 'membership status', 'active inactive'],
        
        // Additional Info
        'journey' => ['journey', 'bio', 'biography', 'about', 'description', 'story', 'background', 'profile'],
        'interests' => ['interests', 'hobbies', 'passion', 'likes'],
        'remarks' => ['remarks', 'notes', 'comments', 'additional info', 'other details'],
        'emergency_contact' => ['emergency contact', 'emergency number', 'emergency phone'],
        'blood_group' => ['blood group', 'blood type', 'blood'],
        'anniversary' => ['anniversary', 'wedding anniversary', 'marriage anniversary']
    ];
    
    // Special handling for name fields
    $nameVariations = ['name', 'full name', 'member name', 'person name'];
    
    foreach ($normalizedHeaders as $index => $header) {
        // Check for exact matches first
        foreach ($fieldMappings as $field => $variations) {
            if (in_array($header, $variations)) {
                $mapping[$field] = $index;
                break;
            }
        }
        
        // Handle combined name field
        if (in_array($header, $nameVariations)) {
            $mapping['full_name'] = $index;
        }
        
        // More conservative fuzzy matching to prevent wrong mappings
        foreach ($fieldMappings as $field => $variations) {
            if (!isset($mapping[$field])) {
                foreach ($variations as $variation) {
                    // Only check if header exactly contains variation (not the other way around)
                    // This prevents partial matches from causing wrong mappings
                    if (strlen($header) >= strlen($variation) && strpos($header, $variation) !== false) {
                        $mapping[$field] = $index;
                        break 2;
                    }
                }
            }
        }
    }
    
    return $mapping;
}

function processRowData($row, $columnMapping, $headers) {
    $member = [
        'member_id' => '',
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'age' => '',
        'date_of_birth' => null,
        'gender' => '',
        'profession' => '',
        'highest_qualification' => '',
        'area_of_expertise' => '',
        'company' => '',
        'experience' => '',
        'address' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => '',
        'linkedin_url' => '',
        'website' => '',
        'journey' => '',
        'interests' => '',
        'remarks' => '',
        'introducer' => '',
        'introducer_contact' => '',
        'join_date' => null,
        'member_type' => 'general',
        'status' => 'active',
        'profile_image' => 'default-avatar.png'
    ];
    
    // Process mapped columns
    foreach ($columnMapping as $field => $columnIndex) {
        if (isset($row[$columnIndex]) && !empty(trim($row[$columnIndex] ?? ''))) {
            $value = trim($row[$columnIndex] ?? '');
            
            switch ($field) {
                case 'full_name':
                    // Split full name into first and last name
                    $nameParts = explode(' ', $value, 2);
                    $member['first_name'] = $nameParts[0];
                    $member['last_name'] = isset($nameParts[1]) ? $nameParts[1] : '';
                    break;
                    
                case 'date_of_birth':
                    // Handle different date formats
                    $member[$field] = parseDate($value);
                    break;
                    
                case 'profession':
                    // Map profession to both profession and area_of_expertise
                    $member['profession'] = $value;
                    $member['area_of_expertise'] = $value;
                    break;
                    
                case 'age':
                    // Convert age to approximate date of birth
                    if (is_numeric($value) && $value > 0 && $value < 120) {
                        $birthYear = date('Y') - intval($value);
                        $member['date_of_birth'] = $birthYear . '-01-01'; // Approximate birth date
                        $member['age'] = $value;
                    }
                    break;
                    
                case 'email':
                    // Don't validate here, just store the value
                    // Validation will happen in the main processing loop
                    $member[$field] = strtolower($value);
                    break;
                    
                case 'phone':
                    // Clean phone number - improved regex to handle mobile numbers
                    $phone = preg_replace('/[^0-9+\-\s]/', '', $value);
                    $member[$field] = $phone;
                    break;
                    
                case 'linkedin_url':
                    // Validate LinkedIn URL
                    if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                        // Try to construct LinkedIn URL if it's just a username
                        if (!strpos($value, 'http')) {
                            $value = 'https://www.linkedin.com/in/' . trim($value, '/');
                        }
                    }
                    $member[$field] = $value;
                    break;
                    
                default:
                    $member[$field] = $value;
                    break;
            }
        }
    }
    
    // Generate username and password only if we have email or can create one
    if (!empty($member['email'])) {
        $member['username'] = generateUsername($member['email']);
        $member['password'] = password_hash('defaultpass123', PASSWORD_DEFAULT); // Default password
    }
    
    return $member;
}

function generatePlaceholderEmail($member, $rowIndex) {
    // Generate a placeholder email based on name and row number
    $emailBase = '';
    
    if (!empty($member['first_name'])) {
        $emailBase .= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $member['first_name']));
    }
    
    if (!empty($member['last_name'])) {
        $emailBase .= strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $member['last_name']));
    }
    
    if (empty($emailBase)) {
        $emailBase = 'member';
    }
    
    return $emailBase . $rowIndex . '@placeholder.local';
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

function importMembersToDatabase($members, $conn) {
    $importedCount = 0;
    
    $stmt = $conn->prepare("INSERT INTO members (
        first_name, last_name, email, phone, highest_qualification, 
        area_of_expertise, address, city, state, zip_code, date_of_birth,
        linkedin_url, journey, introducer, introducer_contact, 
        status, member_type, profile_image, username, password, joined_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    foreach ($members as $member) {
        try {
            // Generate username if not already set
            if (empty($member['username']) && !empty($member['email'])) {
                $member['username'] = generateUsername($member['email']);
                $member['password'] = password_hash('defaultpass123', PASSWORD_DEFAULT);
            }
            
            $stmt->bind_param("ssssssssssssssssssss",
                $member['first_name'],
                $member['last_name'],
                $member['email'],
                $member['phone'],
                $member['highest_qualification'],
                $member['area_of_expertise'],
                $member['address'],
                $member['city'],
                $member['state'],
                $member['zip_code'],
                $member['date_of_birth'],
                $member['linkedin_url'],
                $member['journey'],
                $member['introducer'],
                $member['introducer_contact'],
                $member['status'],
                $member['member_type'],
                $member['profile_image'],
                $member['username'],
                $member['password']
            );
            
            if ($stmt->execute()) {
                $importedCount++;
            }
        } catch (Exception $e) {
            error_log("Failed to import member: " . $e->getMessage());
        }
    }
    
    return $importedCount;
}
?>