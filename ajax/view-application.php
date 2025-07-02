<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Invalid request');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$stmt = $conn->prepare("
    SELECT ma.*, m.first_name, m.last_name, m.email, m.phone,
           m.profile_image, m.highest_qualification, m.area_of_expertise,
           m.zip_code, m.city, m.state, m.journey, m.status as member_status,
           m.id_type1, m.id_proof1, m.id_type2, m.id_proof2
    FROM members m
    JOIN membership_applications ma ON ma.member_id = m.id
    WHERE m.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$application = $stmt->get_result()->fetch_assoc();

if (!$application) {
    http_response_code(404);
    exit('Application not found');
}

// Get achievements with type and description
$stmt = $conn->prepare("
    SELECT * FROM member_achievements 
    WHERE member_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $application['member_id']);
$stmt->execute();
$achievements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="modal-header">
    <h5 class="modal-title">Application Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="application-details">
        <!-- Personal Information -->
        <div class="section mb-4">
            <h6 class="section-title">Personal Information</h6>
            <div class="row">
                <div class="col-md-3 text-center mb-3">
                    <?php if($application['profile_image']): ?>
                        <img src="../uploads/members/<?php echo htmlspecialchars($application['profile_image']); ?>" 
                             alt="Profile Photo" 
                             class="img-fluid rounded-circle profile-image"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    <?php endif; ?>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($application['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($application['phone']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Qualification:</strong> <?php echo htmlspecialchars($application['highest_qualification']); ?></p>
                            <p><strong>Expertise:</strong> <?php echo htmlspecialchars($application['area_of_expertise']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($application['city'] . ', ' . $application['state']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Professional Journey -->
        <div class="section mb-4">
            <h6 class="section-title">Professional Journey</h6>
            <div class="journey-text">
                <?php echo nl2br(htmlspecialchars($application['journey'])); ?>
            </div>
        </div>

            <!-- Achievements -->
            <?php if(!empty($achievements)): ?>
            <div class="section mb-4">
                <h6 class="section-title">Achievements</h6>
                <div class="row">
                    <?php foreach($achievements as $achievement): ?>
                        <div class="col-md-6 mb-3">
                            <div class="achievement-card p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6><?php echo htmlspecialchars($achievement['name']); ?></h6>
                                    <span class="badge bg-info"><?php echo ucfirst($achievement['type']); ?></span>
                                </div>
                                <?php if($achievement['description']): ?>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($achievement['description']); ?></p>
                                <?php endif; ?>
                                <?php if($achievement['image']): ?>
                                    <img src="../uploads/achievements/<?php echo htmlspecialchars($achievement['image']); ?>" 
                                         alt="Achievement" 
                                         class="img-fluid mt-2 achievement-image"
                                         onclick="window.open(this.src, '_blank')"
                                         style="max-height: 200px; cursor: pointer;">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- National ID Proofs -->
            <div class="section mb-4">
                <h6 class="section-title">National ID Proofs</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="id-card p-3 border rounded">
                            <h6><?php echo ucfirst($application['id_type1']); ?></h6>
                            <img src="../uploads/documents/<?php echo htmlspecialchars($application['id_proof1']); ?>" 
                                 alt="<?php echo ucfirst($application['id_type1']); ?>" 
                                 class="img-fluid mt-2 id-image"
                                 onclick="window.open(this.src, '_blank')"
                                 style="max-height: 200px; cursor: pointer;">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="id-card p-3 border rounded">
                            <h6><?php echo ucfirst($application['id_type2']); ?></h6>
                            <img src="../uploads/documents/<?php echo htmlspecialchars($application['id_proof2']); ?>" 
                                 alt="<?php echo ucfirst($application['id_type2']); ?>" 
                                 class="img-fluid mt-2 id-image"
                                 onclick="window.open(this.src, '_blank')"
                                 style="max-height: 200px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Proof -->
            <?php if($application['payment_proof']): ?>
            <div class="section mb-4">
                <h6 class="section-title">Payment Proof</h6>
                <img src="../uploads/payments/<?php echo htmlspecialchars($application['payment_proof']); ?>" 
                     alt="Payment Proof" 
                     class="img-fluid rounded border"
                     style="max-height: 300px; cursor: pointer"
                     onclick="window.open(this.src, '_blank')">
            </div>
            <?php endif; ?>
            
            <?php if($application['status'] === 'pending'): ?>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-danger reject-application" data-id="<?php echo $application['id']; ?>">
                    Reject
                </button>
                <button type="button" class="btn btn-success approve-application" data-id="<?php echo $application['id']; ?>">
                    Approve
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                Application status: <?php echo ucfirst($application['status']); ?>
                <?php if($application['status'] === 'rejected' && isset($application['admin_remarks'])): ?>
                    <br>Remarks: <?php echo htmlspecialchars($application['admin_remarks']); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.section-title {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 15px;
}
.journey-text {
    white-space: pre-line;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}
.achievement-card {
    background: #fff;
    transition: transform 0.2s;
}
.achievement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>