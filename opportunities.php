<?php
// Database connection
$servername = "localhost";
$username = "u828878874_sarathi_new";
$password = "#Sarathi@2025";
$dbname = "u828878874_sarathi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_POST && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    // Handle file upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $upload_dir = 'admin/uploads/jobs/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
        $new_filename = $first_name . '_' . $last_name . '_' . time() . '.' . $file_extension;
        $resume_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            // File uploaded successfully
        } else {
            $error_message = "Error uploading resume.";
        }
    }
    
    if (!isset($error_message)) {
        // Insert application into database
        $stmt = $pdo->prepare("INSERT INTO job_applications (job_id, first_name, last_name, mobile, email, address, resume_path, applied_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$job_id, $first_name, $last_name, $mobile, $email, $address, $resume_path]);
        
        // Get job details for email
        $job_stmt = $pdo->prepare("SELECT * FROM opportunities WHERE id = ?");
        $job_stmt->execute([$job_id]);
        $job = $job_stmt->fetch(PDO::FETCH_ASSOC);
        
        // Send email to applicant
        $to_applicant = $email;
        $subject_applicant = "Job Application Confirmation - Sarathi Cooperative";
        $message_applicant = "Dear $first_name $last_name,\n\n";
        $message_applicant .= "Thank you for applying for the position of " . $job['title'] . " at Sarathi Cooperative.\n\n";
        $message_applicant .= "You have successfully applied for the job. Our HR team will contact you soon.\n\n";
        $message_applicant .= "Best regards,\nSarathi Cooperative Team";
        
        $headers_applicant = "From: sarathicooperative@outlook.com\r\n";
        $headers_applicant .= "Reply-To: sarathicooperative@outlook.com\r\n";
        
        mail($to_applicant, $subject_applicant, $message_applicant, $headers_applicant);
        
        // Send email to company with resume attachment
        $to_company = "sarathicooperative@outlook.com";
        $subject_company = "New Job Application - " . $job['title'];
        $message_company = "New job application received:\n\n";
        $message_company .= "Position: " . $job['title'] . "\n";
        $message_company .= "Applicant: $first_name $last_name\n";
        $message_company .= "Email: $email\n";
        $message_company .= "Mobile: $mobile\n";
        $message_company .= "Address: $address\n\n";
        $message_company .= "Please find the attached resume.";
        
        // Email with attachment
        $boundary = md5(time());
        $headers_company = "From: noreply@sarathicooperative.com\r\n";
        $headers_company .= "MIME-Version: 1.0\r\n";
        $headers_company .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
        
        $email_body = "--$boundary\r\n";
        $email_body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $email_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $email_body .= $message_company . "\r\n";
        
        if ($resume_path && file_exists($resume_path)) {
            $file_content = chunk_split(base64_encode(file_get_contents($resume_path)));
            $email_body .= "--$boundary\r\n";
            $email_body .= "Content-Type: application/octet-stream; name=\"" . basename($resume_path) . "\"\r\n";
            $email_body .= "Content-Disposition: attachment; filename=\"" . basename($resume_path) . "\"\r\n";
            $email_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $email_body .= $file_content . "\r\n";
        }
        
        $email_body .= "--$boundary--";
        
        mail($to_company, $subject_company, $email_body, $headers_company);
        
        $success_message = "Your application has been submitted successfully! You will receive a confirmation email shortly.";
    }
}

// Fetch all active job opportunities
$stmt = $pdo->prepare("SELECT * FROM opportunities WHERE status = 'active' ORDER BY posted_date DESC");
$stmt->execute();
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Career Opportunities - Sarathi Cooperative</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .body{
            padding-top: 95px;
        }
        .job-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .job-title {
            color: #2c5aa0;
            font-weight: bold;
            margin-bottom: 10px;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .job-title:hover {
            color: #1e3a8a;
            text-decoration: underline;
        }
        .job-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .apply-btn {
            background: linear-gradient(45deg, #2c5aa0, #1e3a8a);
            border: none;
            border-radius: 25px;
            padding: 8px 25px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .apply-btn:hover {
            background: linear-gradient(45deg, #1e3a8a, #2c5aa0);
            transform: scale(1.05);
        }
        .modal-header {
            background: linear-gradient(45deg, #2c5aa0, #1e3a8a);
            color: white;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .form-control:focus {
            border-color: #2c5aa0;
            box-shadow: 0 0 0 0.2rem rgba(44, 90, 160, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #2c5aa0, #1e3a8a);
            border: none;
            border-radius: 25px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #1e3a8a, #2c5aa0);
        }
        .alert-success {
            border-radius: 10px;
            border: none;
            background: linear-gradient(45deg, #d4edda, #c3e6cb);
        }
        .alert-danger {
            border-radius: 10px;
            border: none;
            background: linear-gradient(45deg, #f8d7da, #f5c6cb);
        }
        .job-details-section {
            margin-bottom: 20px;
        }
        .job-details-section h6 {
            color: #2c5aa0;
            font-weight: 600;
            margin-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }
        .job-meta-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 10px;
        }
        .job-meta-item i {
            color: #2c5aa0;
            margin-right: 5px;
        }
        .salary-badge {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header Section - Leave space for header -->
    <div id="header-section" style="padding-top: 95px;">
        <?php include('header.php')?>
    </div>
    
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-5" style="color: #2c5aa0;">
                    <i class="fas fa-briefcase me-3"></i>Career Opportunities At Sarathi Cooperative
                </h1>
                
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($jobs)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No job opportunities available at the moment</h3>
                        <p class="text-muted">Please check back later for new openings.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($jobs as $job): ?>
                            <div class="col-lg-6 col-md-8 col-sm-12 mx-auto">
                                <div class="job-card">
                                    <h4 class="job-title" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#jobDetailsModal"
                                        data-job-id="<?php echo $job['id']; ?>"
                                        data-job-title="<?php echo htmlspecialchars($job['title']); ?>"
                                        data-job-location="<?php echo htmlspecialchars($job['location']); ?>"
                                        data-job-type="<?php echo htmlspecialchars($job['job_type']); ?>"
                                        data-job-description="<?php echo htmlspecialchars($job['description']); ?>"
                                        data-job-requirements="<?php echo htmlspecialchars($job['requirements']); ?>"
                                        data-job-salary="<?php echo htmlspecialchars($job['salary_range']); ?>"
                                        data-job-posted="<?php echo date('M d, Y', strtotime($job['posted_date'])); ?>"
                                        data-job-deadline="<?php echo !empty($job['application_deadline']) ? date('M d, Y', strtotime($job['application_deadline'])) : 'Not specified'; ?>"
                                        data-job-experience="<?php echo htmlspecialchars($job['experience_required'] ?? 'Not specified'); ?>"
                                        data-job-benefits="<?php echo htmlspecialchars($job['benefits'] ?? 'Not specified'); ?>">
                                        <i class="fas fa-user-tie me-2"></i><?php echo htmlspecialchars($job['title']); ?>
                                    </h4>
                                    
                                    <div class="job-meta">
                                        <span class="me-3">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($job['location']); ?>
                                        </span>
                                        <span class="me-3">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo htmlspecialchars($job['job_type']); ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Posted: <?php echo date('M d, Y', strtotime($job['posted_date'])); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="job-description">
                                        <?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 200))); ?>
                                        <?php if (strlen($job['description']) > 200): ?>...<?php endif; ?>
                                    </p>
                                    
                                    <?php if (!empty($job['requirements'])): ?>
                                        <div class="requirements mb-3">
                                            <strong>Key Requirements:</strong>
                                            <p class="small text-muted">
                                                <?php echo nl2br(htmlspecialchars(substr($job['requirements'], 0, 150))); ?>
                                                <?php if (strlen($job['requirements']) > 150): ?>...<?php endif; ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <?php if (!empty($job['salary_range'])): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-rupee-sign me-1"></i><?php echo htmlspecialchars($job['salary_range']); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <button class="btn apply-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#applyModal"
                                                data-job-id="<?php echo $job['id']; ?>"
                                                data-job-title="<?php echo htmlspecialchars($job['title']); ?>">
                                            <i class="fas fa-paper-plane me-2"></i>Apply Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobDetailsModal" tabindex="-1" aria-labelledby="jobDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobDetailsModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Job Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="job-details-section">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 id="modalJobTitle" class="text-primary mb-3"></h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <span id="modalJobSalary" class="salary-badge"></span>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="job-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <strong>Location:</strong> <span id="modalJobLocation"></span>
                                </div>
                                <div class="job-meta-item">
                                    <i class="fas fa-clock"></i>
                                    <strong>Type:</strong> <span id="modalJobType"></span>
                                </div>
                                <div class="job-meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <strong>Posted:</strong> <span id="modalJobPosted"></span>
                                </div>
                                <div class="job-meta-item">
                                    <i class="fas fa-calendar-times"></i>
                                    <strong>Deadline:</strong> <span id="modalJobDeadline"></span>
                                </div>
                                <div class="job-meta-item">
                                    <i class="fas fa-chart-line"></i>
                                    <strong>Experience:</strong> <span id="modalJobExperience"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="job-details-section">
                        <h6><i class="fas fa-file-alt me-2"></i>Job Description</h6>
                        <p id="modalJobDescription" class="text-justify"></p>
                    </div>
                    
                    <div class="job-details-section">
                        <h6><i class="fas fa-list-check me-2"></i>Requirements</h6>
                        <div id="modalJobRequirements"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                    <button type="button" class="btn btn-primary" id="modalApplyBtn">
                        <i class="fas fa-paper-plane me-1"></i>Apply for this Position
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Apply for Position
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" id="applicationForm">
                        <input type="hidden" name="job_id" id="jobId">
                        <input type="hidden" name="apply_job" value="1">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="firstName" class="form-label">
                                    <i class="fas fa-user me-1"></i>First Name *
                                </label>
                                <input type="text" class="form-control" id="firstName" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lastName" class="form-label">
                                    <i class="fas fa-user me-1"></i>Last Name *
                                </label>
                                <input type="text" class="form-control" id="lastName" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Mobile Number *
                                </label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email Address *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Address *
                            </label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="resume" class="form-label">
                                <i class="fas fa-file-pdf me-1"></i>Upload Resume *
                            </label>
                            <input type="file" class="form-control" id="resume" name="resume" 
                                   accept=".pdf,.doc,.docx" required>
                            <div class="form-text">Accepted formats: PDF, DOC, DOCX (Max size: 5MB)</div>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section - Leave space for footer -->
    <div id="footer-section" class="mt-5">
        <!-- Footer content will be included here -->
    </div>
    <?php include('footer.php')?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle job details modal
        document.addEventListener('DOMContentLoaded', function() {
            const jobDetailsModal = document.getElementById('jobDetailsModal');
            const applyModal = document.getElementById('applyModal');
            let currentJobId = null;
            let currentJobTitle = null;
            
            jobDetailsModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                
                // Get job data from button attributes
                currentJobId = button.getAttribute('data-job-id');
                currentJobTitle = button.getAttribute('data-job-title');
                const jobLocation = button.getAttribute('data-job-location');
                const jobType = button.getAttribute('data-job-type');
                const jobDescription = button.getAttribute('data-job-description');
                const jobRequirements = button.getAttribute('data-job-requirements');
                const jobSalary = button.getAttribute('data-job-salary');
                const jobPosted = button.getAttribute('data-job-posted');
                const jobDeadline = button.getAttribute('data-job-deadline');
                const jobExperience = button.getAttribute('data-job-experience');
                const jobBenefits = button.getAttribute('data-job-benefits');
                
                // Populate modal content
                document.getElementById('modalJobTitle').textContent = currentJobTitle;
                document.getElementById('modalJobLocation').textContent = jobLocation;
                document.getElementById('modalJobType').textContent = jobType;
                document.getElementById('modalJobPosted').textContent = jobPosted;
                document.getElementById('modalJobDeadline').textContent = jobDeadline;
                document.getElementById('modalJobExperience').textContent = jobExperience;
                document.getElementById('modalJobDescription').innerHTML = jobDescription.replace(/\n/g, '<br>');
                document.getElementById('modalJobRequirements').innerHTML = jobRequirements.replace(/\n/g, '<br>');
                document.getElementById('modalJobBenefits').innerHTML = jobBenefits.replace(/\n/g, '<br>');
                
                // Show/hide salary badge
                const salaryElement = document.getElementById('modalJobSalary');
                if (jobSalary && jobSalary.trim() !== '') {
                    salaryElement.innerHTML = '<i class="fas fa-rupee-sign me-1"></i>' + jobSalary;
                    salaryElement.style.display = 'inline-block';
                } else {
                    salaryElement.style.display = 'none';
                }
            });
            
            // Handle apply button in job details modal
            document.getElementById('modalApplyBtn').addEventListener('click', function() {
                // Close job details modal
                const jobDetailsModalInstance = bootstrap.Modal.getInstance(jobDetailsModal);
                jobDetailsModalInstance.hide();
                
                // Wait for the modal to be hidden, then show apply modal
                jobDetailsModal.addEventListener('hidden.bs.modal', function() {
                    // Set job data for apply modal
                    document.getElementById('jobId').value = currentJobId;
                    document.getElementById('applyModalLabel').innerHTML = 
                        '<i class="fas fa-user-plus me-2"></i>Apply for ' + currentJobTitle;
                    
                    // Show apply modal
                    const applyModalInstance = new bootstrap.Modal(applyModal);
                    applyModalInstance.show();
                }, { once: true });
            });
            
            // Handle apply button click from job cards
            applyModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                
                // Only handle if button has job data (not from modal apply button)
                if (button && button.hasAttribute('data-job-id')) {
                    const jobId = button.getAttribute('data-job-id');
                    const jobTitle = button.getAttribute('data-job-title');
                    
                    document.getElementById('jobId').value = jobId;
                    document.getElementById('applyModalLabel').innerHTML = 
                        '<i class="fas fa-user-plus me-2"></i>Apply for ' + jobTitle;
                }
            });
            
            // File size validation
            document.getElementById('resume').addEventListener('change', function() {
                const file = this.files[0];
                if (file && file.size > 5 * 1024 * 1024) { // 5MB limit
                    alert('File size must be less than 5MB');
                    this.value = '';
                }
            });
            
            // Form validation
            document.getElementById('applicationForm').addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                }
            });
        });
    </script>
</body>
</html>