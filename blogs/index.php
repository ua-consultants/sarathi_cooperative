<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$page = 'blogs';

// Handle status updates
if(isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $blog_id = (int)$_POST['blog_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE blogs SET status = '$status' WHERE id = $blog_id");
}

// Pagination
$page_number = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page_number - 1) * $items_per_page;

// Build query conditions
$where = "1=1";
if(isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (title LIKE '%$search%' OR excerpt LIKE '%$search%')";
}
if(isset($_GET['category'])) {
    $category = (int)$_GET['category'];
    $where .= " AND category_id = $category";
}
if(isset($_GET['status']) && $_GET['status'] !== '') {
    $status_filter = $conn->real_escape_string($_GET['status']);
    $where .= " AND status = '$status_filter'";
}

// Get total records and pages
$total_records = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE $where")->fetch_assoc()['count'];
$total_pages = ceil($total_records / $items_per_page);

// Get blogs with category and author info
$sql = "SELECT b.*, c.name as category_name, u.username as author_name 
        FROM blogs b 
        LEFT JOIN blog_categories c ON b.category_id = c.id 
        LEFT JOIN users u ON b.author_id = u.id 
        WHERE $where 
        ORDER BY b.created_at DESC 
        LIMIT $offset, $items_per_page";
$blogs = $conn->query($sql);

// Get categories for filter
$categories = $conn->query("SELECT id, name FROM blog_categories WHERE status = 1");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Blogs</h1>
            <a href="create.php" class="btn btn-primary">Create New Blog</a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search blogs..." 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo isset($_GET['category']) && $_GET['category'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" <?php echo isset($_GET['status']) && $_GET['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo isset($_GET['status']) && $_GET['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Blogs List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 40%">Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($blog = $blogs->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($blog['featured_image'])): ?>
                                            <img src="<?php echo str_replace('/sarathi/', '../', $blog['featured_image']); ?>" 
                                                alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                                class="me-2 rounded" 
                                                style="width: 50px; height: 50px; object-fit: cover;"
                                                onerror="this.onerror=null; this.src='../assets/images/placeholder.jpg';">
                                        <?php else: ?>
                                            <div class="me-2 rounded bg-light" style="width: 50px; height: 50px;"></div>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0"><?php echo htmlspecialchars($blog['title']); ?></h6>
                                            <small class="text-muted"><?php echo substr(strip_tags($blog['excerpt']), 0, 100); ?>...</small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($blog['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($blog['author_name']); ?></td>
                                <td>
                                    <select class="form-select form-select-sm status-select" data-blog-id="<?php echo $blog['id']; ?>" style="width: 100px;">
                                        <option value="published" <?php echo $blog['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                        <option value="draft" <?php echo $blog['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    </select>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="edit.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="preview.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-info" target="_blank">Preview</a>
                                        <button type="button" class="btn btn-sm <?php echo $blog['is_featured'] ? 'btn-warning' : 'btn-outline-warning'; ?> toggle-feature" 
                                                data-id="<?php echo $blog['id']; ?>" 
                                                data-featured="<?php echo $blog['is_featured']; ?>">
                                            <i class="fas fa-star"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-blog" data-id="<?php echo $blog['id']; ?>">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $page_number == $i ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search='.$_GET['search'] : ''; ?><?php echo isset($_GET['category']) ? '&category='.$_GET['category'] : ''; ?><?php echo isset($_GET['status']) ? '&status='.$_GET['status'] : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const blogId = this.dataset.blogId;
        const status = this.value;
        
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=update_status&blog_id=${blogId}&status=${status}`
        });
    });
});

document.querySelectorAll('.delete-blog').forEach(button => {
    button.addEventListener('click', function() {
        if(confirm('Are you sure you want to delete this blog?')) {
            const blogId = this.dataset.id;
            fetch('delete-blog.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `blog_id=${blogId}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error deleting blog');
                }
            });
        }
    });
});

// Add this before the delete-blog event listeners
document.querySelectorAll('.toggle-feature').forEach(button => {
    button.addEventListener('click', function() {
        const blogId = this.dataset.id;
        const featured = this.dataset.featured === '1' ? 0 : 1;
        
        fetch('toggle-feature.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `blog_id=${blogId}&featured=${featured}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                this.dataset.featured = featured;
                this.classList.toggle('btn-outline-warning');
                this.classList.toggle('btn-warning');
                location.reload(); // Refresh to show updated status
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update featured status');
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>