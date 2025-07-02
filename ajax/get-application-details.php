<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch application details
    $stmt = $conn->prepare("
        SELECT ja.*, o.title as job_title 
        FROM job_applications ja 
        JOIN opportunities o ON ja.job_id = o.id 
        WHERE ja.id = ?
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $application = $result->fetch_assoc();
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h6>Personal Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo htmlspecialchars($application['email']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Mobile:</strong></td>
                            <td><?php echo htmlspecialchars($application['mobile']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Address:</strong></td>
                            <td><?php echo nl2br(htmlspecialchars($application['address'])); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Application Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Job Applied For:</strong></td>
                            <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Applied Date:</strong></td>
                            <td><?php echo date('M d, Y H:i', strtotime($application['applied_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $application['application_status'] == 'pending' ? 'warning' : 
                                        ($application['application_status'] == 'shortlisted' ? 'success' : 
                                        ($application['application_status'] == 'hired' ? 'primary' : 
                                        ($application['application_status'] == 'rejected' ? 'danger' : 'info')));
                                ?>">
                                    <?php echo ucfirst($application['application_status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php if($application['reviewed_date']): ?>
                        <tr>
                            <td><strong>Reviewed Date:</strong></td>
                            <td><?php echo date('M d, Y H:i', strtotime($application['reviewed_date'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <?php if($application['cover_letter']): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Cover Letter</h6>
                    <div class="card">
                        <div class="card-body">
                            <?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($application['resume_path']): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Resume</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        <a href="<?php echo htmlspecialchars($application['resume_path']); ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download"></i> Download Resume
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($application['interview_date']): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Interview Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Interview Date:</strong></td>
                            <td><?php echo date('M d, Y H:i', strtotime($application['interview_date'])); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Interview Status:</strong></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $application['interview_status'] == 'scheduled' ? 'info' : 
                                        ($application['interview_status'] == 'completed' ? 'success' : 'warning');
                                ?>">
                                    <?php echo ucfirst($application['interview_status']); ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if($application['notes']): ?>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Internal Notes</h6>
                    <div class="card">
                        <div class="card-body">
                            <?php echo nl2br(htmlspecialchars($application['notes'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Add Notes Section -->
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Add/Update Notes</h6>
                    <form id="updateNotesForm" data-application-id="<?php echo $application['id']; ?>">
                        <div class="mb-3">
                            <textarea class="form-control" name="notes" rows="3" placeholder="Add internal notes about this application..."><?php echo htmlspecialchars($application['notes']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Update Notes
                        </button>
                    </form>
                </div>
            </div>
            
            <script>
            // Handle notes update
            document.getElementById('updateNotesForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const applicationId = this.getAttribute('data-application-id');
                const notes = this.querySelector('textarea[name="notes"]').value;
                
                fetch('../ajax/update-application-notes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: applicationId,
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notes updated successfully!');
                    } else {
                        alert('Error updating notes: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating notes');
                });
            });
            </script>
            <?php
        } else {
            echo '<div class="alert alert-danger">Application not found</div>';
        }
        
        $stmt->close();
    } else {
        echo '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request</div>';
}

$conn->close();
?>