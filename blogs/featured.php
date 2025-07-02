<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$page = 'blogs';

// Handle feature/unfeature actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blog_id = (int)$_POST['blog_id'];
    $action = $_POST['action'];
    
    if ($action === 'feature') {
        // Check if we already have 3 featured blogs
        $featured_count = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE is_featured = 1")->fetch_assoc()['count'];
        
        if ($featured_count < 3) {
            $conn->query("UPDATE blogs SET is_featured = 1 WHERE id = $blog_id");
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Maximum 3 blogs can be featured'];
        }
    } else if ($action === 'unfeature') {
        $conn->query("UPDATE blogs SET is_featured = 0 WHERE id = $blog_id");
        $response = ['success' => true];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get featured and non-featured blogs
$featured_blogs = $conn->query("
    SELECT b.*, c.name as category_name 
    FROM blogs b 
    LEFT JOIN blog_categories c ON b.category_id = c.id 
    WHERE b.is_featured = 1 AND b.status = 'published'
    ORDER BY b.created_at DESC
");

$available_blogs = $conn->query("
    SELECT b.*, c.name as category_name 
    FROM blogs b 
    LEFT JOIN blog_categories c ON b.category_id = c.id 
    WHERE b.is_featured = 0 AND b.status = 'published'
    ORDER BY b.created_at DESC
");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Featured Blogs</h1>
            <a href="index.php" class="btn btn-secondary">Back to Blogs</a>
        </div>

        <div class="row">
            <!-- Featured Blogs -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Featured Blogs (<?php echo $featured_blogs->num_rows; ?>/3)</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php while($blog = $featured_blogs->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $blog['featured_image']; ?>" class="me-3 rounded" style="width: 64px; height: 64px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($blog['title']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($blog['category_name']); ?></small>
                                    </div>
                                    <button class="btn btn-sm btn-danger unfeature-blog" data-id="<?php echo $blog['id']; ?>">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Blogs -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Available Blogs</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php while($blog = $available_blogs->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $blog['featured_image']; ?>" class="me-3 rounded" style="width: 64px; height: 64px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($blog['title']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($blog['category_name']); ?></small>
                                    </div>
                                    <button class="btn btn-sm btn-success feature-blog" data-id="<?php echo $blog['id']; ?>">
                                        Feature
                                    </button>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.feature-blog').forEach(button => {
    button.addEventListener('click', function() {
        const blogId = this.dataset.id;
        updateFeaturedStatus(blogId, 'feature');
    });
});

document.querySelectorAll('.unfeature-blog').forEach(button => {
    button.addEventListener('click', function() {
        const blogId = this.dataset.id;
        updateFeaturedStatus(blogId, 'unfeature');
    });
});

function updateFeaturedStatus(blogId, action) {
    fetch('featured.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `blog_id=${blogId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Error updating featured status');
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>