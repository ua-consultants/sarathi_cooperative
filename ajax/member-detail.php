<?php
require_once '../includes/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) die('Invalid request');

$stmt = $conn->prepare("
    SELECT mp.*, ma.first_name, ma.last_name, ma.email 
    FROM member_profiles mp 
    JOIN membership_applications ma ON mp.application_id = ma.id 
    WHERE mp.id = ? AND mp.status = 'active'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if(!$member) die('Member not found');

// Fetch achievements
$stmt = $conn->prepare("SELECT * FROM member_achievements WHERE member_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$achievements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="modal-header">
    <h5 class="modal-title">Member Profile</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="text-center mb-4">
        <img src="../uploads/members/<?php echo $member['profile_photo']; ?>" 
             class="member-photo-lg" alt="Profile Photo">
        <h4 class="mt-3"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h4>
        <div class="star-rating mb-2">
            <?php for($i = 0; $i < $member['rating']; $i++): ?>
                <i class="fas fa-star"></i>
            <?php endfor; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h6>Expertise</h6>
            <div class="expertise-tags mb-3">
                <?php 
                $areas = explode(',', $member['expertise']);
                foreach($areas as $area): 
                    $area = trim($area);
                    if(!empty($area)):
                ?>
                    <span class="expertise-tag"><?php echo htmlspecialchars($area); ?></span>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
        <div class="col-md-6">
            <h6>Location</h6>
            <p><?php echo htmlspecialchars($member['city'] . ', ' . $member['state']); ?></p>
        </div>
    </div>

    <div class="mb-4">
        <h6>Professional Journey</h6>
        <p><?php echo nl2br(htmlspecialchars($member['journey'])); ?></p>
    </div>

    <?php if(!empty($achievements)): ?>
    <div class="mb-4">
        <h6>Achievements</h6>
        <div class="row">
            <?php foreach($achievements as $achievement): ?>
            <div class="col-md-4 mb-3">
                <div class="achievement-card">
                    <img src="/sarathi/uploads/members/<?php echo $achievement['image']; ?>" 
                         class="img-fluid" alt="Achievement">
                    <h6 class="mt-2"><?php echo htmlspecialchars($achievement['name']); ?></h6>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal-footer justify-content-center">
    <a href="mailto:<?php echo $member['email']; ?>" class="btn btn-primary">
        <i class="fas fa-envelope me-2"></i> Connect with <?php echo htmlspecialchars($member['first_name']); ?>
    </a>
</div>

<style>
.member-photo-lg {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
}

.achievement-card {
    border: 1px solid #dee2e6;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
}

.achievement-card img {
    max-height: 150px;
    object-fit: contain;
}
</style>