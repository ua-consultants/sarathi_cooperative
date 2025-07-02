<?php
require_once '../includes/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id) die('Invalid request');

$stmt = $conn->prepare("
    SELECT mp.*, ma.first_name, ma.last_name, mb.position, mb.bio 
    FROM member_profiles mp 
    JOIN membership_applications ma ON mp.application_id = ma.id
    JOIN member_board mb ON mp.id = mb.member_id 
    WHERE mp.id = ? AND mb.status = 'active'
");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if(!$member) die('Member not found');
?>

<div class="modal-header">
    <h5 class="modal-title">Board Member Profile</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="text-center mb-4">
        <img src="/sarathi/uploads/members/<?php echo $member['profile_photo']; ?>" 
             class="board-photo-lg" alt="Profile Photo">
        <h3 class="mt-3"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
        <h5 class="text-muted mb-4"><?php echo htmlspecialchars($member['position']); ?></h5>
    </div>

    <?php if(!empty($member['bio'])): ?>
    <div class="mb-4">
        <h6>About</h6>
        <p class="text-muted"><?php echo nl2br(htmlspecialchars($member['bio'])); ?></p>
    </div>
    <?php endif; ?>

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
            <h6>Journey with Sarathi</h6>
            <p class="text-muted"><?php echo nl2br(htmlspecialchars($member['journey'])); ?></p>
        </div>
    </div>
</div>

<style>
.board-photo-lg {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 50%;
    border: 5px solid #f8f9fa;
}

.expertise-tag {
    font-size: 0.8rem;
    background: #e9ecef;
    padding: 3px 10px;
    border-radius: 15px;
    margin: 2px;
    display: inline-block;
}
</style>