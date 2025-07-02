<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$page = 'blogs';

// Handle category operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = $conn->real_escape_string($_POST['name']);
                // Check if category exists
                $exists = $conn->query("SELECT id FROM blog_categories WHERE name = '$name'")->num_rows;
                if ($exists) {
                    $_SESSION['error'] = "Category '$name' already exists";
                } else {
                    $conn->query("INSERT INTO blog_categories (name, status) VALUES ('$name', 1)");
                    $_SESSION['success'] = "Category added successfully";
                }
                break;
            
            case 'update':
                $id = (int)$_POST['id'];
                $name = $conn->real_escape_string($_POST['name']);
                $status = (int)$_POST['status'];
                
                // Check if new name exists for other categories
                $exists = $conn->query("SELECT id FROM blog_categories WHERE name = '$name' AND id != $id")->num_rows;
                if ($exists) {
                    $_SESSION['error'] = "Category '$name' already exists";
                } else {
                    $conn->query("UPDATE blog_categories SET name = '$name', status = $status WHERE id = $id");
                    $_SESSION['success'] = "Category updated successfully";
                }
                break;
            case 'delete':
                $id = (int)$_POST['id'];
                $conn->query("DELETE FROM blog_categories WHERE id = $id");
                break;
        }
    }
}

// Add this near the top of the page to display messages
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// Get categories
$categories = $conn->query("SELECT * FROM blog_categories ORDER BY name");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Blog Categories</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                Add Category
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($category = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($category['name']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $category['status'] ? 'success' : 'danger'; ?>">
                                        <?php echo $category['status'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-category" 
                                            data-id="<?php echo $category['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($category['name']); ?>"
                                            data-status="<?php echo $category['status']; ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-category" 
                                            data-id="<?php echo $category['id']; ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-category-id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" id="edit-category-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit-category-status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.edit-category').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const status = this.dataset.status;
        
        document.getElementById('edit-category-id').value = id;
        document.getElementById('edit-category-name').value = name;
        document.getElementById('edit-category-status').value = status;
        
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    });
});

document.querySelectorAll('.delete-category').forEach(button => {
    button.addEventListener('click', function() {
        if(confirm('Are you sure you want to delete this category?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="${this.dataset.id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>