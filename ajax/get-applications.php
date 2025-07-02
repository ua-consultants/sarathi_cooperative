<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit;
}

if (isset($_GET['job_id'])) {
    $job_id = intval($_GET['job_id']);
    
    // Fetch applications for this job
    $stmt = $conn->prepare("
        SELECT ja.*, o.title as job_title 
        FROM job_applications ja 
        JOIN opportunities o ON ja.job_id = o.id 
        WHERE ja.job_id = ? 
        ORDER BY ja.applied_date DESC
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $job_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($application = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($application['email']); ?></td>
                            <td><?php echo htmlspecialchars($application['mobile']); ?></td>
                            <td>
                                <select class="form-select form-select-sm application-status" 
                                        data-id="<?php echo $application['id']; ?>">
                                    <option value="pending" <?php echo $application['application_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="reviewed" <?php echo $application['application_status'] == 'reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                    <option value="shortlisted" <?php echo $application['application_status'] == 'shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                    <option value="rejected" <?php echo $application['application_status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="hired" <?php echo $application['application_status'] == 'hired' ? 'selected' : ''; ?>>Hired</option>
                                </select>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($application['applied_date'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info view-application-details" 
                                            data-id="<?php echo $application['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($application['resume_path']): ?>
                                        <?php 
                                            // Build full path to the resume
                                            $resume_url = '../admin/uploads/jobs/' . htmlspecialchars(basename($application['resume_path'])); 
                                        ?>
                                        <a href="<?php echo $resume_url; ?>" class="btn btn-sm btn-success" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <script>
            // Update application status
            document.querySelectorAll('.application-status').forEach(select => {
                select.addEventListener('change', function() {
                    const applicationId = this.getAttribute('data-id');
                    const newStatus = this.value;
                    
                    fetch('../ajax/update-application-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: applicationId,
                            status: newStatus
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Status updated successfully
                            console.log('Status updated');
                        } else {
                            alert('Error updating status');
                            // Revert the select to previous value
                            this.value = this.getAttribute('data-original-value');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating status');
                    });
                });
            });
            
            // View application details
            document.querySelectorAll('.view-application-details').forEach(button => {
                button.addEventListener('click', function() {
                    const applicationId = this.getAttribute('data-id');
                    
                    fetch(`../ajax/get-application-details.php?id=${applicationId}`)
                    .then(response => response.text())
                    .then(data => {
                        // Create a new modal for application details
                        const modalHtml = `
                            <div class="modal fade" id="applicationDetailsModal" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Application Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            ${data}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remove existing modal if any
                        const existingModal = document.getElementById('applicationDetailsModal');
                        if (existingModal) {
                            existingModal.remove();
                        }
                        
                        // Add new modal to body
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        
                        // Show the modal
                        const modal = new bootstrap.Modal(document.getElementById('applicationDetailsModal'));
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading application details');
                    });
                });
            });
            </script>
            <?php
        } else {
            echo '<div class="alert alert-info">No applications found for this job opportunity.</div>';
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