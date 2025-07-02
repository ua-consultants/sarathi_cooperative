<?php
// Database connection
$mysqli = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

include 'header.php';

// Fetch active testimonials
$testimonials = [];
$stmt = $mysqli->prepare("SELECT * FROM testimonials WHERE status = 'active' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $testimonials[] = $row;
}
$stmt->close();
?>
<link rel="icon" href="img/logo-favi-icon.png">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <h1 class="mb-5" style="margin-top: 75px;">Testimonials</h1>
        </div>
    </div>

    <div class="row">
        <?php foreach ($testimonials as $testimonial): ?>
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Quote Icon -->
                        <div class="text-primary mb-3">
                            <i class="fas fa-quote-left fa-2x"></i>
                        </div>
                        
                        <!-- Testimonial Text -->
                        <p class="lead fst-italic mb-4">
                            "<?php echo htmlspecialchars($testimonial['description']); ?>"
                        </p>
                        
                        <div class="text-primary mb-3">
                            <i class="fas fa-quote-right fa-2x"></i>
                        </div>
                        
                        <!-- Author Info -->
                        <div class="border-top pt-3">
                            <h5 class="mb-1"><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                            <p class="text-muted mb-1"><?php echo htmlspecialchars($testimonial['designation']); ?></p>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($testimonial['company_name']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>