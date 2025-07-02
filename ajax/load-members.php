<?php
require_once '../includes/config.php';

$search = $_GET['search'] ?? '';
$expertise = $_GET['expertise'] ?? '';
$state = $_GET['state'] ?? '';
$seniority = $_GET['seniority'] ?? '';

$where = ["mp.status = 'active'"];
$params = [];
$types = "";

if(!empty($search)) {
    $where[] = "(ma.first_name LIKE ? OR ma.last_name LIKE ? OR mp.expertise LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

if(!empty($expertise)) {
    $where[] = "mp.expertise LIKE ?";
    $params[] = "%$expertise%";
    $types .= "s";
}

if(!empty($state)) {
    $where[] = "mp.state = ?";
    $params[] = $state;
    $types .= "s";
}

if(!empty($seniority)) {
    $where[] = "mp.rating = ?";
    $params[] = $seniority;
    $types .= "i";
}

$whereClause = implode(" AND ", $where);

$stmt = $conn->prepare("
    SELECT mp.*, ma.first_name, ma.last_name, ma.email 
    FROM member_profiles mp 
    JOIN membership_applications ma ON mp.application_id = ma.id 
    WHERE $whereClause
    ORDER BY mp.rating DESC, ma.first_name ASC
");

if(!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$members = $stmt->get_result();

while($member = $members->fetch_assoc()):
?>
    <div class="col-md-6">
        <div class="card member-card">
            <div class="card-body text-center">
                <img src="/sarathi/uploads/members/<?php echo $member['profile_photo']; ?>" 
                     class="member-photo" alt="<?php echo htmlspecialchars($member['first_name']); ?>">
                
                <h5 class="mt-3"><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h5>
                
                <div class="star-rating mb-2">
                    <?php for($i = 0; $i < $member['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>

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

                <button class="btn btn-outline-primary view-member-detail" data-id="<?php echo $member['id']; ?>">
                    Know More
                </button>
            </div>
        </div>
    </div>
<?php endwhile; ?>