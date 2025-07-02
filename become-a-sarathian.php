<?php
require_once 'admin/includes/config.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    try {
        $conn->begin_transaction();

        // Handle file uploads
        $uploadedFiles = [
            'profile_photo' => ['dir' => 'members', 'path' => ''],
            'profile_doc' => ['dir' => 'documents', 'path' => ''],
            'achievements_doc' => ['dir' => 'achievements', 'path' => ''],
            'id_proof1' => ['dir' => 'documents', 'path' => ''],
            'id_proof2' => ['dir' => 'documents', 'path' => ''],
            'payment_proof' => ['dir' => 'payments', 'path' => '']
        ];

        foreach ($uploadedFiles as $field => &$fileInfo) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$field];
                $fileName = time() . '_' . basename($file['name']);
                $uploadDir = "admin/uploads/{$fileInfo['dir']}/";
                
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $fileInfo['path'] = $fileName;
                } else {
                    throw new Exception("Failed to upload {$field}");
                }
            }
        }

        // Insert member data
        $memberSql = "INSERT INTO members (
            first_name, last_name, email, phone, date_of_birth,
            profile_image, highest_qualification, area_of_expertise,
            address, city, state, zip_code, journey, linkedin_url, introducer, 
            introducer_contact, profile_doc, achievements_doc,
            id_type1, id_proof1, id_type2, id_proof2,
            status, member_type, joined_date, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'probationary', NOW(), NOW())";

        $stmt = $conn->prepare($memberSql);
        $stmt->bind_param("ssssssssssssssssssssss",
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $_POST['phone'],
            $_POST['date_of_birth'],
            $uploadedFiles['profile_photo']['path'],
            $_POST['highest_qualification'],
            $_POST['area_of_expertise'],
            $_POST['address'],
            $_POST['city'],
            $_POST['state'],
            $_POST['zip_code'],
            $_POST['journey'],
            $_POST['linkedin_url'],
            $_POST['introducer'],
            $_POST['introducer_contact'],
            $uploadedFiles['profile_doc']['path'],
            $uploadedFiles['achievements_doc']['path'],
            $_POST['id_type1'],
            $uploadedFiles['id_proof1']['path'],
            $_POST['id_type2'],
            $uploadedFiles['id_proof2']['path']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to save member data");
        }

        $member_id = $stmt->insert_id;
        $paymentProofPath = $uploadedFiles['payment_proof']['path'];

        // Insert application
        $sql = "INSERT INTO membership_applications (member_id, payment_proof, status) VALUES (?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $member_id, $paymentProofPath);

        if (!$stmt->execute()) {
            throw new Exception("Failed to save application");
        }

        // Send confirmation emails
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'anuragchahar870@gmail.com'; // Update with your email
        $mail->Password = 'rjyddsmhavvewvap'; // Update with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Send applicant email
        $mail->setFrom('noreply@sarathicooperative.org', 'Sarathi Cooperative');
        $mail->addAddress($_POST['email']);
        $mail->isHTML(true);
        $mail->Subject = 'Membership Application Received';
        $mail->Body = "<p>Dear {$_POST['first_name']},</p>
                       <p>We have received your membership application. Our team will review it and get back to you soon.</p>
                       <p>Best regards,<br>Team Sarathi</p>";
        $mail->send();

        // Send admin notification
        $mail->clearAddresses();
        $mail->addAddress('sarathi@sarathicooperative.org');
        $mail->Subject = 'New Membership Application';
        $mail->Body = "<p>New membership application received from {$_POST['first_name']} {$_POST['last_name']}.</p>";
        $mail->send();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Application submitted successfully', 'member_id' => $member_id]);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <title>Become a Sarathian - Sarathi Cooperative</title>
    <link rel="icon" href="img/logo-favi-icon.png">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;  
            line-height: 1.6;
        }

        .header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
        }

        .logo-text h2 {
            color: #2c3e50;
            font-size: 1.5rem;
        }

        .logo-text p {
            color: #4ecdc4;
            font-size: 0.9rem;
            font-style: italic;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #4ecdc4;
        }

        .main-container {
            margin-top: 195px;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .content-section {
            margin-top: 25px;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .content-section h1 {
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .criteria-list {
            list-style: none;
        }

        .criteria-list li {
            gap: 0.2rem;
            margin-bottom: 1.5rem;
            padding-left: 2rem;
            position: relative;
            color: #555;
            line-height: 1.1;
        }

        .criteria-list li::before {
            content: counter(item);
            counter-increment: item;
            position: absolute;
            left: 0;
            top: 0;
            background: #4ecdc4;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .criteria-list {
            counter-reset: item;
        }

        .to-know-more {
            font-family: 'Dancing Script', cursive;
            border: none;
            background: none;
            margin-top: 3rem;
            color: black;
            font-style: italic;
            font-size: 1.1rem;
        }

        .sidebar {
            margin-top: 25px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 400px;
            height: 800px;
        }

        .sidebar h2 {
            color: #ffd700;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .benefits-list {
            list-style: none;
            font-size: 1.2rem;
            margin-bottom: 6rem;
        }

        .benefits-list li {
            margin-bottom: 0.8rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .benefits-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #ffd700;
            font-weight: bold;
        }

        .cta-section {
            text-align: center;
            margin-top: 2rem;
        }

        .cta-section h3 {
            font-size: 2rem;
            color: #ffd700;
            margin-bottom: 0.5rem;
        }

        .cta-section p {
            font-style: italic;
            font-size: 1.2rem;
            color: #ffd700;
            margin-bottom: 1.5rem;
        }

        .cta-button {
            background: #2a5298;
            color: gold;
            border: none;
            padding: 1rem 2rem;
            /* border-radius: 50px; */
            font-size: 1.8rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 750px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #000;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #ddd;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
        }

        .step.active {
            background: #ffd700;
            color: #1e3c72;
        }

        .step.completed {
            background: #4ecdc4;
            color: white;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-title {

            text-align: center;
            margin-bottom: 2rem;
            color: #2c3e50;
            font-family: 'Dancing Script', cursive;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            flex: 1;
        }

        .form-group.full-width {
            flex: none;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4ecdc4;
        }

        .form-group input.error {
            border-color: #e74c3c;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s, background-color 0.3s;
            margin-bottom: 1.5rem;
        }

        .upload-area:hover {
            border-color: #4ecdc4;
            background-color: #f8f9fa;
        }

        .upload-area p {
            margin: 0.5rem 0;
            color: #666;
        }

        .upload-area small {
            color: #999;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-primary {
            background: #4ecdc4;
            color: white;
        }

        .btn-primary:hover {
            background: #45b7d1;
        }

        .btn-warning {
            background: #ffd700;
            color: #1e3c72;
            font-weight: bold;
        }

        .btn-warning:hover {
            background: #ffed4e;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .hidden {
            display: none;
        }

        .word-count {
            text-align: right;
            font-size: 0.9rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .upload-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .upload-button {
            background: #4ecdc4;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .upload-button:hover {
            background: #45b7d1;
        }

        .preview-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .preview-section h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            border-bottom: 2px solid #4ecdc4;
            padding-bottom: 0.5rem;
        }

        .preview-item {
            margin-bottom: 0.8rem;
        }

        .preview-item strong {
            color: #555;
        }

        .bank-details {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .bank-details h3 {
            color: #ffd700;
            margin-bottom: 1rem;
        }

        .bank-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .bank-info-item {
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 8px;
        }

        .bank-info-item strong {
            display: block;
            color: #ffd700;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .cta-button span {
            font-family: 'Dancing Script', cursive;
            display: block;
        }

        .form-group select[multiple] {
    background-color: #fff;
    background-image: none;
    padding: 8px;
}

.form-group select[multiple] option {
    padding: 5px 8px;
    margin: 2px 0;
}

.form-group select[multiple] option:checked {
    background: #4ecdc4 !important;
    color: white !important;
}

.selected-expertise {
    display: none;
}

.selected-expertise.show {
    display: block;
}

.expertise-tag {
    display: inline-block;
    background: #4ecdc4;
    color: white;
    padding: 4px 8px;
    margin: 2px;
    border-radius: 15px;
    font-size: 0.8rem;
}
.additional-content {
        display: none;
        margin-top: 2rem;
    }

    .sections-wrapper {
            margin-left: 155px;
            margin-right: 155px;
            margin-bottom: 4rem;
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        .responsibilities-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
        }

        .responsibilities-section h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: #333;
            font-size: 1.5rem;
        }

        .responsibilities-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .responsibilities-list li {
            margin-bottom: 0.8rem;
            padding-left: 1.2rem;
            position: relative;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .responsibilities-list li::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #ffd700;
            font-weight: bold;
        }

        .levels-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
        }

        .levels-section h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            color: #333;
            font-size: 1.5rem;
        }

        .levels-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.85rem;
        }

        .levels-table th,
        .levels-table td {
            padding: 0.7rem;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        .levels-table th {
            background: #f8f9fa;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .levels-table td {
            line-height: 1.3;
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .container {
                margin-left: 50px;
                margin-right: 50px;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 20px;
                margin-right: 20px;
            }
            
            .sections-wrapper {
                flex-direction: column;
                gap: 1rem;
            }
            
            .levels-table {
                font-size: 0.75rem;
            }
            
            .levels-table th,
            .levels-table td {
                padding: 0.5rem;
            }
        }
        @media (max-width: 768px) {
            .main-container {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }

            .form-row {
                flex-direction: column;
            }

            .modal-content {
                width: 95%;
                margin: 2% auto;
            }

            .nav-container {
                padding: 0 1rem;
            }

            .nav-links {
                display: none;
            }
        }
        
        .page-heading {
            margin-top: 145px;
            text-align: center;
            font-size: 2rem;
        }
        
        .page-heading span {
            font-family: 'Dancing Script', cursive;
        }
    </style>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Sarathi Cooperative",
      "alternateName": [
       "Sarathi Research Consulting and Management Services",
        "Sarathi Research Services",
        "Sarathi Consulting Services",
        "Sarathi Marketing Services",
        "Sarathi Services",
        "Sarathi Research and Marketing Services",
        "Sarathi Research and Consulting Services",
        "Sarathi Consulting and Marketing Services",
        "Research and Consulting Services",
        "Marketing Services",
        "Marketing and Consulting Services",
        "Sarathi Consultants",
        "Sarathi Consultancy"
      ],
      "url": "https://www.sarathicooperative.com"
    }
</script>

</head>
<body>
    <!-- Header -->
<?php include 'header.php'; ?>
    <!-- Main Content -->
    <div class="page-heading">
        <h1 class="main-heading">Members <span>Rules & Regulations</span></h1>
    </div>
    <div class="main-container">
        <section class="content-section">
            <h1>Members Criteria</h1>
            <ol class="criteria-list">
                <li>Is Competent to Contract under section 11 of the Indian Contract Act 1872. And an adult of above 18 years of age.</li>
                <li>Is a Service Provider / Business Leader/ Professional including but not limited to being a: Expert or Excellence Practitioner or Researcher or Consultant or Management Proficient etc.</li>
                <li>Resides within the Area of Operation as Registered with the Registrar of the Society. (Refer Section 1e. for detailed explanation)</li>
                <li>Agrees to the Organizational Vision & Values of the Society and is capable & willing to genuinely contribute towards fulfilment of the vision and objectives of the society in any manner whatsoever.</li>
                <li>Is not a member of any other similar cooperative society of similar nature. Whose interest does not conflict with the interest of the Society.</li>
                <li>Has paid Admission Fee, Procured Minimum Shares of full value and Subscribed to the Quarterly Shares as prescribed in the Bye-laws.</li>
                <li>A mandatory step for all those desirous to be a part of the Society. This is an evaluation phase wherein desirous Member Application will be evaluated by the BOD.</li>
                <li>As per the evaluation, the Board may either award General Membership to the deserving ones who perform well & become eligible as per Byelaws or disallow the non-deserving ones & terminate their Probationary Membership or Extend/Continue the Probationary Membership till the next review.</li>
            </ol>
            <button class="to-know-more" onclick="toggleAdditionalContent()">
                <span>To Know More</span>
            </button>
        </section>

        <aside class="sidebar">
            <h2>Membership Benefits</h2>
            <ul class="benefits-list">
                <li>Access to exclusive cooperative resources</li>
                <li>Voting rights in society decisions</li>
                <li>Share in profit distributions</li>
                <li>Professional development opportunities</li>
                <li>Networking with industry experts</li>
                <li>Regular training programs</li>
                <li>Access to cooperative facilities</li>
                <li>Priority in new initiatives</li>
            </ul>
            <div class="cta-section">
                <button class="cta-button" onclick="openModal()">Interested? <span>Let's get started</span></button>
            </div>
        </aside>
    </div>
    <div id="additionalContent" class="additional-content">
    <div class="sections-wrapper">
            <div class="responsibilities-section">
                <h2>Members Roles & Responsibilities</h2>
                <ul class="responsibilities-list">
                    <li>A minimum 180 hours of time in a year as required by the board to be an active participant in the businesses of the society for three Consecutive Years.</li>
                    <li>It shall be a prime responsibility of every member to keep themselves deeply involved and connected with the business of Society.</li>
                    <li>The member activeness will be tracked and each Member will be given growth opportunities based on his/her level of performance.</li>
                    <li>To encourage more active members in the Society, Board may from time to time decide to reward the active ones with Performance Bonus L1 & L2 Opportunities OR, by any other suitable ways and similarly penalize the non-active members appropriately.</li>
                    <li>A member will be disqualified if She/he has not attended three consecutive general meetings of society and such absence has not been condoned by the members in the general meeting.</li>
                    <li>A member will be disqualified She/he has made any default in payment of all dues including contributions, subscriptions, etc. if any, as decided by the board of the society from time to time and has not made the payment within receiving the notice for payment.</li>
                    <li>A member will be disqualified she/he does not patronize, promote, and protect the interests and objects of the Society.</li>
                    <li>A member of the society may be expelled for laid rules provided that the member concerned shall be served Registered Notices, listing the cause of expulsion and the member concerned shall be given an opportunity to represent the case before the Executive Committee/Board.</li>
                    <li>On expulsion, the person will cease to be a member of the society. Such expulsion may involve forfeiture of shares at the sole discretion of the Society. Pending expulsion by the General Body, the Board may suspend the member till the next General Body meeting at its discretion.</li>
                    <li>No member who has been so expelled shall be allowed readmission as a member for a period of three years from the date of such expulsion.</li>
                    <li>Any member withdrawal from Membership of the society may withdraw from membership of the society only after 3 (Three) years from the date of obtaining membership by giving at least a three months notice and duly approved by the Board of the society.</li>
                    <li>A member who withdraws or resigns from the membership of the society shall not be eligible for re-admission as a member of the society, for a period of one year from the date of his withdrawal or resignation from membership.</li>
                </ul>
            </div>

            <div class="levels-section">
                <h2>Active Member <span style="font-family: 'Dancing Script', cursive;">Benefits</span></h2>
                <table class="levels-table">
                    <thead>
                        <tr>
                            <th>General Member Activeness</th>
                            <th>Active Participation Hours</th>
                            <th>Activity</th>
                            <th>Incentive</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>General Member (Level I)</td>
                            <td>208 hrs/year (4 hr/Wk OR 2 days/Month)</td>
                            <td>Activity as Assigned by Team Leader / BOD</td>
                            <td>Recognition / Appreciation Award</td>
                        </tr>
                        <tr>
                            <td>Team Leader (Level II)</td>
                            <td>260 hrs/year (5 hr/Wk OR 3 days/Month)</td>
                            <td>Manage 5 Members (L-1) + Target Management</td>
                            <td>L01 benefit = up to 3% of Sarathi Profits</td>
                        </tr>
                        <tr>
                            <td>Group Leader (Level III)</td>
                            <td>416 hrs/year (8 hr/Wk OR 4 days/Month)</td>
                            <td>Manage 5 Team Leaders (L02) + Performance Management</td>
                            <td>L02 benefit = up to 5% of Sarathi Profits + Closer to being part of BOD</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Multi-step Modal -->
    <div id="membershipModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="step-indicator">
                    <div class="step active" id="step1">1</div>
                    <div class="step" id="step2">2</div>
                    <div class="step" id="step3">3</div>
                    <div class="step" id="step4">4</div>
                </div>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>

            <div class="modal-body">
                <!-- Step 1: Personal Information -->
                <div id="step1Content" class="step-content">
                    <h2 class="form-title">Tell us about yourself</h2>
                    <form id="step1Form">
                        <div class="upload-area" onclick="document.getElementById('profile_photo').click()">
                            <div id="profilePreview"></div>
                            <p>Drag/Upload Profile Image</p>
                            <small>Size not more than 3 MB</small>
                            <input type="file" id="profile_photo" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name *</label>
                                <input type="text" id="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name *</label>
                                <input type="text" id="last_name" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Phone *</label>
                                <input type="tel" id="phone" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" id="email" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Date of Birth *</label>
                                <input type="date" id="date_of_birth" required>
                            </div>
                            <div class="form-group">
                                <label>Highest Qualification *</label>
                                <input type="text" id="highest_qualification" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Introducer Name</label>
                                <input type="text" id="introducer">
                            </div>
                            <div class="form-group">
                                <label>Introducer Mobile</label>
                                <input type="tel" id="introducer_contact">
                            </div>
                        </div>

                        <div class="form-group full-width">
    <label>Select Area of Expertise * <small>(Hold Ctrl/Cmd to select multiple)</small></label>
    <select id="area_of_expertise" multiple required style="height: 80px;">
        <option value="Marketing Consultant">Marketing Consultant</option>
        <option value="Recruitment Strategist">Recruitment Strategist</option>
        <option value="Environment Consultant">Environment Consultant</option>
        <option value="CSR Strategist">CSR Strategist</option>
        <option value="Market Entry Strategy Consultant">Market Entry Strategy Consultant</option>
        <option value="Leadership Consultant">Leadership Consultant</option>
        <option value="Project Management Consultant">Project Management Consultant</option>
        <option value="OKR (Objectives & Key Results) Consultants">OKR (Objectives & Key Results) Consultants</option>
        <option value="ROI Consultants">ROI Consultants</option>
        <option value="Risk and Compliance Consultants">Risk and Compliance Consultants</option>
        <option value="E-Commerce">E-Commerce</option>
        <option value="Marketing Compliance Consultants">Marketing Compliance Consultants</option>
        <option value="Human Resources Consulting">Human Resources Consulting</option>
        <option value="Cyber Security Consultants">Cyber Security Consultants</option>
        <option value="System Admin Consultants">System Admin Consultants</option>
        <option value="UX/UI Consultants">UX/UI Consultants</option>
        <option value="Investment Advisor">Investment Advisor</option>
        <option value="Insurance Consultants">Insurance Consultants</option>
        <option value="Accounting Advisory">Accounting Advisory</option>
        <option value="Taxation Experts">Taxation Experts</option>
        <option value="Corporate Finance Experts">Corporate Finance Experts</option>
        <option value="Brand Consultancy">Brand Consultancy</option>
        <option value="PR Consultancy">PR Consultancy</option>
        <option value="SEO Consultancy">SEO Consultancy</option>
        <option value="Conversion Funnel Consultancy">Conversion Funnel Consultancy</option>
        <option value="Multi-Channel Strategy">Multi-Channel Strategy</option>
        <option value="Sales Consultancy">Sales Consultancy</option>
        <option value="ROI Strategist">ROI Strategist</option>
        <option value="Social Media Consultancy">Social Media Consultancy</option>
        <option value="Cross-Functional Consulting">Cross-Functional Consulting</option>
        <option value="Outsourcing Expert">Outsourcing Expert</option>
        <option value="Employer Branding">Employer Branding</option>
        <option value="ROI Consultancy">ROI Consultancy</option>
        <option value="Leadership Consultancy">Leadership Consultancy</option>
        <option value="Strategy and Management Consulting">Strategy and Management Consulting</option>
        <option value="Marketing Strategist">Marketing Strategist</option>
        <option value="Legal Advisor">Legal Advisor</option>
        <option value="Financial Advisor">Financial Advisor</option>
        <option value="Strategy Consulting">Strategy Consulting</option>
        <option value="Operational Consulting">Operational Consulting</option>
        <option value="Supply Chain Management">Supply Chain Management</option>
        <option value="Logistics Consulting">Logistics Consulting</option>
        <option value="Lean Manufacturing Trainer">Lean Manufacturing Trainer</option>
        <option value="Financial Consulting">Financial Consulting</option>
        <option value="Risk Management">Risk Management</option>
        <option value="Investment Strategist">Investment Strategist</option>
        <option value="HR Consulting">HR Consulting</option>
        <option value="Organizational Effectiveness Planner">Organizational Effectiveness Planner</option>
        <option value="Talent Management">Talent Management</option>
        <option value="Employee Relationship Manager">Employee Relationship Manager</option>
        <option value="Industrial Design Consultant">Industrial Design Consultant</option>
        <option value="IT Consultant">IT Consultant</option>
        <option value="IT Infrastructure Designer">IT Infrastructure Designer</option>
        <option value="IT Infrastructure Manager">IT Infrastructure Manager</option>
        <option value="Cybersecurity Expert">Cybersecurity Expert</option>
        <option value="Digital Transformation Expert">Digital Transformation Expert</option>
        <option value="Compliance Consultant">Compliance Consultant</option>
        <option value="Environment Compliance Consultant">Environment Compliance Consultant</option>
        <option value="Industry-Specific Expertise">Industry-Specific Expertise</option>
        <option value="Branding Consultant">Branding Consultant</option>
        <option value="Digital Marketing">Digital Marketing</option>
        <option value="Legal Consulting">Legal Consulting</option>
        <option value="Intellectual Property and Regulatory Compliance Expert">Intellectual Property and Regulatory Compliance Expert</option>
        <option value="Social Media Consulting">Social Media Consulting</option>
        <option value="Branding Expert">Branding Expert</option>
        <option value="Sales Consulting">Sales Consulting</option>
        <option value="Sales Performance Expert">Sales Performance Expert</option>
        <option value="Training Experts">Training Experts</option>
        <option value="Sales Process Optimization">Sales Process Optimization</option>
        <option value="Wellness/Fitness Consulting">Wellness/Fitness Consulting</option>
        <option value="Growth Marketing Consultant">Growth Marketing Consultant</option>
        <option value="Career Coaching Consultancy">Career Coaching Consultancy</option>
        <option value="Product Development Consulting">Product Development Consulting</option>
        <option value="Design Consulting">Design Consulting</option>
        <option value="Brand Consulting">Brand Consulting</option>
        <option value="Branding Consultancy">Branding Consultancy</option>
    </select>
    <div class="selected-expertise" id="selectedExpertise" style="margin-top: 10px; font-size: 0.9rem; color: #666;"></div>
</div>

                        <div class="form-group full-width">
                            <label>Address *</label>
                            <textarea id="address" rows="3" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>City *</label>
                                <input type="text" id="city" required>
                            </div>
                            <div class="form-group">
                                <label>State *</label>
                                <input type="text" id="state" required>
                            </div>
                            <div class="form-group">
                                <label>ZIP Code *</label>
                                <input type="text" id="zip_code" required>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Journey -->
                <div id="step2Content" class="step-content hidden">
                    <h2 class="form-title">Your journey thus far....</h2>
                    <form id="step2Form">
                        <div class="form-group full-width">
                            <textarea id="journey" rows="10" placeholder="Tell us about your professional journey, achievements, and experiences..." maxlength="800"></textarea>
                            <div class="word-count" id="journey">0/800 words</div>
                        </div>

                        <div class="form-group full-width">
                            <label>LinkedIn Profile URL</label>
                            <input type="url" id="linkedin_url" placeholder="https://linkedin.com/in/yourprofile">
                        </div>
                    </form>
                </div>

                <!-- Step 3: Achievements -->
                <div id="step3Content" class="step-content hidden">
                    <h2 class="form-title">Achievements, Accolades if any</h2>
                    <form id="step3Form">
                        <div class="upload-area" onclick="document.getElementById('achievements_doc').click()">
                            <p>Drag your achievements document here or click to select</p>
                            <small>Size not more than 5 MB</small>
                            <input type="file" id="achievement_doc" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleFileUpload(this, 'achievementPreview')">
                        </div>
                        <div id="achievementPreview"></div>
                    </form>
                </div>

                <!-- Step 4: Documents -->
                <div id="step4Content" class="step-content hidden">
                    <h2 class="form-title">Identification Documents (any 2 are mandatory)</h2>
                    <form id="step4Form">
                        <div class="upload-item">
                            <span>Aadhar Card</span>
                            <button type="button" class="upload-button" onclick="document.getElementById('id_proof1').click()">Upload</button>
                            <input type="file" id="id_proof1" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleDocUpload(this, 'aadharStatus')">
                            <span id="aadharStatus"></span>
                        </div>

                        <div class="upload-item">
                            <span>PAN Card</span>
                            <button type="button" class="upload-button" onclick="document.getElementById('id_proof2').click()">Upload</button>
                            <input type="file" id="id_proof2" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleDocUpload(this, 'panStatus')">
                            <span id="panStatus"></span>
                        </div>

                        <div class="upload-item">
                            <span>Passport</span>
                            <button type="button" class="upload-button" onclick="document.getElementById('passport').click()">Upload</button>
                            <input type="file" id="passport" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleDocUpload(this, 'passportStatus')">
                            <span id="passportStatus"></span>
                        </div>

                        <div class="upload-item">
                            <span>Others</span>
                            <button type="button" class="upload-button" onclick="document.getElementById('otherDoc').click()">Upload</button>
                            <input type="file" id="otherDoc" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="handleDocUpload(this, 'otherStatus')">
                            <span id="otherStatus"></span>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" id="prevBtn" onclick="previousStep()" style="display: none;">Previous</button>
                <button class="btn btn-secondary" onclick="saveProgress()">Save</button>
                <button class="btn btn-primary" id="nextBtn" onclick="nextStep()">Next</button>
                <button class="btn btn-primary hidden" id="previewBtn" onclick="showPreview()">Preview</button>
                <button class="btn btn-warning hidden" id="paymentBtn" onclick="showPayment()">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Application Preview</h2>
                <span class="close" onclick="closePreview()">&times;</span>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be populated here -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closePreview()">Back to Edit</button>
                <button class="btn btn-warning" onclick="closePreview(); showPayment()">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Payment Details</h2>
                <span class="close" onclick="closePayment()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="bank-details">
                    <h3>Bank Details for Payment</h3>
                    <div class="bank-info">
                        <div class="bank-info-item">
                            <strong>Account Number</strong>
                            000434002100019
                        </div>
                        <div class="bank-info-item">
                            <strong>Account Name</strong>
                            Sarathi Research Consulting And Management Services
                        </div>
                        <div class="bank-info-item">
                            <strong>Bank Name</strong>
                            Faridabad Cooperative Bank
                        </div>
                        <div class="bank-info-item">
                            <strong>IFSC Code</strong>
                            UTIB0SFCB01
                        </div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Upload Payment Proof *</label>
                    <div class="upload-area" onclick="document.getElementById('payment_proof').click()">
                        <p>Upload payment receipt or screenshot</p>
                        <small>Accepted formats: JPG, PNG, PDF (Max 5MB)</small>
                        <input type="file" id="payment_proof" accept=".jpg,.jpeg,.png,.pdf" style="display: none;" onchange="handleFileUpload(this, 'paymentPreview')">
                    </div>
                    <div id="paymentPreview"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closePayment()">Cancel</button>
                <button class="btn btn-success" onclick="submitApplication()">Submit Application</button>
            </div>
        </div>
    </div>

    <script>
function toggleAdditionalContent() {
    const additionalContent = document.getElementById('additionalContent');
    if (additionalContent) {
        additionalContent.style.display = additionalContent.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
    <script>
        let currentStep = 1;
        let formData = {};
        let uploadedFiles = {};

        function openModal() {
            document.getElementById('membershipModal').style.display = 'block';
            updateStepIndicator();
        }

        function closeModal() {
            document.getElementById('membershipModal').style.display = 'none';
        }

        function closePreview() {
            document.getElementById('previewModal').style.display = 'none';
        }

        function closePayment() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        function updateStepIndicator() {
            for (let i = 1; i <= 4; i++) {
                const step = document.getElementById(`step${i}`);
                step.classList.remove('active', 'completed');
                
                if (i < currentStep) {
                    step.classList.add('completed');
                } else if (i === currentStep) {
                    step.classList.add('active');
                }
            }

            // Show/hide buttons
            document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
            document.getElementById('nextBtn').style.display = currentStep < 4 ? 'block' : 'none';
            document.getElementById('previewBtn').style.display = currentStep === 4 ? 'block' : 'none';
            document.getElementById('paymentBtn').style.display = currentStep === 4 ? 'block' : 'none';

            if (currentStep === 4) {
                document.getElementById('nextBtn').style.display = 'none';
            }
        }

        function showStep(step) {
            // Hide all step contents
            for (let i = 1; i <= 4; i++) {
                document.getElementById(`step${i}Content`).classList.add('hidden');
            }
            // Show current step content
            document.getElementById(`step${step}Content`).classList.remove('hidden');
        }

        function nextStep() {
            if (validateCurrentStep()) {
                saveStepData();
                if (currentStep < 4) {
                    currentStep++;
                    showStep(currentStep);
                    updateStepIndicator();
                }
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                updateStepIndicator();
            }
        }

        function validateCurrentStep() {
            let isValid = true;
            const currentForm = document.getElementById(`step${currentStep}Form`);
            
            if (currentStep === 1) {
                // Validate personal information
                const requiredFields = ['firstName', 'lastName', 'phone', 'email', 'dob', 'qualification', 'areaOfExpertise', 'address', 'city', 'state', 'zipCode'];
                
                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        field.classList.add('error');
                        isValid = false;
                    } else {
                        field.classList.remove('error');
                    }
                });

                // Validate email format
                const email = document.getElementById('email');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email.value && !emailPattern.test(email.value)) {
                    email.classList.add('error');
                    isValid = false;
                }

                // Validate age (must be 18+)
                const dob = new Date(document.getElementById('dob').value);
                const today = new Date();
                const age = today.getFullYear() - dob.getFullYear();
                if (age < 18) {
                    document.getElementById('dob').classList.add('error');
                    isValid = false;
                    alert('You must be at least 18 years old to apply.');
                }

            } else if (currentStep === 4) {
                // Validate that at least 2 documents are uploaded
                const uploadedDocs = Object.keys(uploadedFiles).filter(key => 
                    ['aadharCard', 'panCard', 'passport', 'otherDoc'].includes(key) && uploadedFiles[key]
                ).length;
                
                if (uploadedDocs < 2) {
                    alert('Please upload at least 2 identification documents.');
                    isValid = false;
                }
            }

            if (!isValid) {
                alert('Please fill in all required fields correctly.');
            }

            return isValid;
        }

        function saveStepData() {
            const currentForm = document.getElementById(`step${currentStep}Form`);
            const inputs = currentForm.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                if (input.type !== 'file') {
                    formData[input.id] = input.value;
                }
            });
        }

        function saveProgress() {
            saveStepData();
            alert('Progress saved! You can continue later.');
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; max-height: 100px; border-radius: 50%; object-fit: cover;">`;
                    uploadedFiles['profileImage'] = input.files[0];
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleFileUpload(input, previewId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    alert('File size should not exceed 5MB');
                    return;
                }

                const preview = document.getElementById(previewId);
                preview.innerHTML = `<p style="color: green;">✓ ${file.name} uploaded successfully</p>`;
                uploadedFiles[input.id] = file;
            }
        }

        function handleDocUpload(input, statusId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (file.size > maxSize) {
                    alert('File size should not exceed 5MB');
                    return;
                }

                const status = document.getElementById(statusId);
                status.innerHTML = '<span style="color: green;">✓ Uploaded</span>';
                status.style.color = 'green';
                uploadedFiles[input.id] = file;
            }
        }

        function showPreview() {
            saveStepData();
            
            const previewContent = document.getElementById('previewContent');
            previewContent.innerHTML = `
                <div class="preview-section">
                    <h3>Personal Information</h3>
                    <div class="preview-item"><strong>Name:</strong> ${formData.firstName || ''} ${formData.lastName || ''}</div>
                    <div class="preview-item"><strong>Email:</strong> ${formData.email || ''}</div>
                    <div class="preview-item"><strong>Phone:</strong> ${formData.phone || ''}</div>
                    <div class="preview-item"><strong>Date of Birth:</strong> ${formData.dob || ''}</div>
                    <div class="preview-item"><strong>Qualification:</strong> ${formData.qualification || ''}</div>
                    <div class="preview-item"><strong>Area of Expertise:</strong> ${formData.areaOfExpertise || ''}</div>
                    <div class="preview-item"><strong>Address:</strong> ${formData.address || ''}, ${formData.city || ''}, ${formData.state || ''} - ${formData.zipCode || ''}</div>
                    ${formData.introducerName ? `<div class="preview-item"><strong>Introducer:</strong> ${formData.introducerName} (${formData.introducerMobile || ''})</div>` : ''}
                </div>

                <div class="preview-section">
                    <h3>Professional Journey</h3>
                    <div class="preview-item">${formData.journey || 'Not provided'}</div>
                    ${formData.linkedinUrl ? `<div class="preview-item"><strong>LinkedIn:</strong> <a href="${formData.linkedinUrl}" target="_blank">${formData.linkedinUrl}</a></div>` : ''}
                </div>

                <div class="preview-section">
                    <h3>Uploaded Documents</h3>
                    ${Object.keys(uploadedFiles).map(key => {
                        if (uploadedFiles[key]) {
                            return `<div class="preview-item">✓ ${key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}</div>`;
                        }
                        return '';
                    }).join('')}
                </div>
            `;
            
            document.getElementById('previewModal').style.display = 'block';
        }

        function updateSelectedExpertise() {
    const select = document.getElementById('area_of_expertise');
    const selectedDiv = document.getElementById('selectedExpertise');
    const selectedOptions = Array.from(select.selectedOptions);
    
    if (selectedOptions.length > 0) {
        const tags = selectedOptions.map(option => 
            `<span class="expertise-tag">${option.text}</span>`
        ).join('');
        selectedDiv.innerHTML = '<strong>Selected:</strong> ' + tags;
        selectedDiv.classList.add('show');
    } else {
        selectedDiv.classList.remove('show');
    }
}

// Add event listener for the expertise select
document.addEventListener('DOMContentLoaded', function() {
    const expertiseSelect = document.getElementById('area_of_expertise');
    if (expertiseSelect) {
        expertiseSelect.addEventListener('change', updateSelectedExpertise);
    }
});

// Update the saveStepData function to handle multiple selections
function saveStepData() {
    const currentForm = document.getElementById(`step${currentStep}Form`);
    const inputs = currentForm.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input.type !== 'file') {
            if (input.multiple && input.tagName === 'SELECT') {
                // Handle multiple select
                const selectedValues = Array.from(input.selectedOptions).map(option => option.value);
                formData[input.id] = selectedValues;
            } else {
                formData[input.id] = input.value;
            }
        }
    });
}

// Update the validateCurrentStep function for multiple expertise
function validateCurrentStep() {
    let isValid = true;
    const currentForm = document.getElementById(`step${currentStep}Form`);
    
    if (currentStep === 1) {
        // Validate personal information
        const requiredFields = ['first_name', 'last_name', 'phone', 'email', 'date_of_birth', 'highest_qualification', 'address', 'city', 'state', 'zip_code'];
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && !field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else if (field) {
                field.classList.remove('error');
            }
        });

        // Validate area of expertise (at least one must be selected)
        const expertiseSelect = document.getElementById('area_of_expertise');
        if (!expertiseSelect.selectedOptions.length) {
            expertiseSelect.classList.add('error');
            isValid = false;
        } else {
            expertiseSelect.classList.remove('error');
        }

        // Validate email format
        const email = document.getElementById('email');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && email.value && !emailPattern.test(email.value)) {
            email.classList.add('error');
            isValid = false;
        }

        // Validate age (must be 18+)
        const dob = document.getElementById('date_of_birth');
        if (dob && dob.value) {
            const dobDate = new Date(dob.value);
            const today = new Date();
            const age = today.getFullYear() - dobDate.getFullYear();
            if (age < 18) {
                dob.classList.add('error');
                isValid = false;
                alert('You must be at least 18 years old to apply.');
            }
        }

    } else if (currentStep === 4) {
        // Validate that at least 2 documents are uploaded
        const uploadedDocs = Object.keys(uploadedFiles).filter(key => 
            ['id_proof1', 'id_proof2', 'passport', 'otherDoc'].includes(key) && uploadedFiles[key]
        ).length;
        
        if (uploadedDocs < 2) {
            alert('Please upload at least 2 identification documents.');
            isValid = false;
        }
    }

    if (!isValid) {
        alert('Please fill in all required fields correctly.');
    }

    return isValid;
}

// Update the showPreview function to display multiple expertise
function showPreview() {
    saveStepData();
    
    const expertiseArray = Array.isArray(formData.area_of_expertise) ? formData.area_of_expertise : [formData.area_of_expertise];
    const expertiseDisplay = expertiseArray.filter(Boolean).join(', ');
    
    const previewContent = document.getElementById('previewContent');
    previewContent.innerHTML = `
        <div class="preview-section">
            <h3>Personal Information</h3>
            <div class="preview-item"><strong>Name:</strong> ${formData.first_name || ''} ${formData.last_name || ''}</div>
            <div class="preview-item"><strong>Email:</strong> ${formData.email || ''}</div>
            <div class="preview-item"><strong>Phone:</strong> ${formData.phone || ''}</div>
            <div class="preview-item"><strong>Date of Birth:</strong> ${formData.date_of_birth || ''}</div>
            <div class="preview-item"><strong>Qualification:</strong> ${formData.highest_qualification || ''}</div>
            <div class="preview-item"><strong>Area of Expertise:</strong> ${expertiseDisplay || ''}</div>
            <div class="preview-item"><strong>Address:</strong> ${formData.address || ''}, ${formData.city || ''}, ${formData.state || ''} - ${formData.zip_code || ''}</div>
            ${formData.introducer ? `<div class="preview-item"><strong>Introducer:</strong> ${formData.introducer} (${formData.introducer_contact || ''})</div>` : ''}
        </div>

        <div class="preview-section">
            <h3>Professional Journey</h3>
            <div class="preview-item">${formData.journey || 'Not provided'}</div>
            ${formData.linkedin_url ? `<div class="preview-item"><strong>LinkedIn:</strong> <a href="${formData.linkedin_url}" target="_blank">${formData.linkedin_url}</a></div>` : ''}
        </div>

        <div class="preview-section">
            <h3>Uploaded Documents</h3>
            ${Object.keys(uploadedFiles).map(key => {
                if (uploadedFiles[key]) {
                    return `<div class="preview-item">✓ ${key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}</div>`;
                }
                return '';
            }).join('')}
        </div>
    `;
    
    document.getElementById('previewModal').style.display = 'block';
}

        function showPayment() {
            document.getElementById('membershipModal').style.display = 'none';
            document.getElementById('paymentModal').style.display = 'block';
        }

        function submitApplication() {
    const paymentProof = document.getElementById('payment_proof');
    
    if (!paymentProof.files || !paymentProof.files[0]) {
        alert('Please upload payment proof before submitting.');
        return;
    }
    
    // Save current step data before submission
    saveStepData();
    
    // Show loading state
    const submitBtn = document.querySelector('#paymentModal .btn-success');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    // Prepare FormData for API submission
    const formDataToSubmit = new FormData();
    
    // Add all text form fields to FormData
    Object.keys(formData).forEach(key => {
        if (formData[key] !== null && formData[key] !== undefined && formData[key] !== '') {
            formDataToSubmit.append(key, formData[key]);
        }
    });
    
    // Add uploaded files to FormData (these should be File objects)
    Object.keys(uploadedFiles).forEach(key => {
        if (uploadedFiles[key] && uploadedFiles[key] instanceof File) {
            formDataToSubmit.append(key, uploadedFiles[key]);
        }
    });
    
    // Add payment proof
    formDataToSubmit.append('payment_proof', paymentProof.files[0]);
    
    // Debug: Log form data keys (don't log files as they're large)
    console.log('Form data keys being sent:', Array.from(formDataToSubmit.keys()));
    
    // Submit to API endpoint
    fetch('become-a-sarathian.php', {
        method: 'POST',
        body: formDataToSubmit
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', [...response.headers.entries()]);
        
        // Get the response text first to see what we actually received
        const text = await response.text();
        console.log('Raw response:', text);
        
        // Check if response is empty
        if (!text.trim()) {
            throw new Error(`Server returned empty response. Status: ${response.status}`);
        }
        
        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('JSON parse error:', e);
            throw new Error(`Server returned invalid JSON. Status: ${response.status}. Response: ${text.substring(0, 500)}`);
        }
        
        return { ok: response.ok, status: response.status, data: data };
    })
    .then(result => {
        console.log('Parsed result:', result);
        
        if (result.ok && result.data.success) {
            alert('Application submitted successfully! You will receive a confirmation email shortly.');
            
            // Close all modals
            if (typeof closePayment === 'function') closePayment();
            if (typeof closeModal === 'function') closeModal();
            
            // Reset form
            if (typeof resetForm === 'function') resetForm();
        } else {
            throw new Error(result.data.message || `HTTP error! status: ${result.status}`);
        }
    })
    .catch(error => {
        console.error('Error submitting application:', error);
        alert('Error submitting application: ' + error.message + '. Please try again.');
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}
        function resetForm() {
            currentStep = 1;
            formData = {};
            uploadedFiles = {};
            
            // Clear all form fields
            document.querySelectorAll('input, select, textarea').forEach(element => {
                if (element.type !== 'file') {
                    element.value = '';
                }
                element.classList.remove('error');
            });
            
            // Clear file inputs
            document.querySelectorAll('input[type="file"]').forEach(element => {
                element.value = '';
            });
            
            // Clear preview areas
            document.getElementById('profilePreview').innerHTML = '';
            document.getElementById('achievementPreview').innerHTML = '';
            document.getElementById('paymentPreview').innerHTML = '';
            
            // Clear upload status
            ['aadharStatus', 'panStatus', 'passportStatus', 'otherStatus'].forEach(id => {
                document.getElementById(id).innerHTML = '';
            });
            
            showStep(1);
            updateStepIndicator();
        }

        // Word count for journey textarea
        document.addEventListener('DOMContentLoaded', function() {
            const journeyTextarea = document.getElementById('journey');
            const journeyCount = document.getElementById('journeyCount');
            
            if (journeyTextarea && journeyCount) {
                journeyTextarea.addEventListener('input', function() {
                    const words = this.value.trim().split(/\s+/).filter(word => word.length > 0).length;
                    const chars = this.value.length;
                    journeyCount.textContent = `${words} words / ${chars}/800 characters`;
                    
                    if (chars > 800) {
                        this.value = this.value.substring(0, 800);
                        journeyCount.textContent = `${this.value.trim().split(/\s+/).filter(word => word.length > 0).length} words / 800/800 characters`;
                    }
                });
            }
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const membershipModal = document.getElementById('membershipModal');
            const previewModal = document.getElementById('previewModal');
            const paymentModal = document.getElementById('paymentModal');
            
            if (event.target === membershipModal) {
                closeModal();
            } else if (event.target === previewModal) {
                closePreview();
            } else if (event.target === paymentModal) {
                closePayment();
            }
        }

        // Initialize form when page loads
        document.addEventListener('DOMContentLoaded', function() {
            showStep(1);
            updateStepIndicator();
        });

        // Form validation helpers
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function validatePhone(phone) {
            const re = /^[\+]?[1-9][\d]{0,15}$/;
            return re.test(phone);
        }

        function calculateAge(dob) {
            const today = new Date();
            const birthDate = new Date(dob);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            return age;
        }

        // Drag and drop functionality for file uploads
        function setupDragAndDrop() {
            const uploadAreas = document.querySelectorAll('.upload-area');
            
            uploadAreas.forEach(area => {
                area.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#4ecdc4';
                    this.style.backgroundColor = '#f8f9fa';
                });
                
                area.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#ddd';
                    this.style.backgroundColor = 'transparent';
                });
                
                area.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.style.borderColor = '#ddd';
                    this.style.backgroundColor = 'transparent';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        const fileInput = this.querySelector('input[type="file"]');
                        if (fileInput) {
                            fileInput.files = files;
                            fileInput.dispatchEvent(new Event('change'));
                        }
                    }
                });
            });
        }

        // Initialize drag and drop when page loads
        document.addEventListener('DOMContentLoaded', setupDragAndDrop);
    </script>
    
    <?php include('footer.php'); ?>
</body>
</html>