<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$page = 'blogs';

// Get blog data
$blog_id = (int)$_GET['id'];
$blog = $conn->query("SELECT * FROM blogs WHERE id = $blog_id")->fetch_assoc();

if (!$blog) {
    header('Location: index.php');
    exit;
}

// Get active categories
$categories = $conn->query("SELECT id, name FROM blog_categories WHERE status = 1 ORDER BY name");

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Edit Blog</h1>
            <div>
                <a href="preview.php?id=<?php echo $blog_id; ?>" class="btn btn-info me-2" target="_blank">Preview</a>
                <a href="index.php" class="btn btn-secondary">Back to Blogs</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" id="blogForm">
                    <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea name="content" id="editor"><?php echo htmlspecialchars($blog['content']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Author</label>
                                <input type="text" name="author_name" class="form-control" required>
                            </div>

                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">SEO Metadata</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($blog['meta_title']); ?>" maxlength="60">
                                        <small class="text-muted">Recommended length: 50-60 characters</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Meta Description</label>
                                        <textarea name="meta_description" class="form-control" rows="3" maxlength="160"><?php echo htmlspecialchars($blog['meta_description']); ?></textarea>
                                        <small class="text-muted">Recommended length: 150-160 characters</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($blog['meta_keywords']); ?>">
                                        <small class="text-muted">Separate keywords with commas</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Featured Image</label>
                                        <?php if($blog['featured_image']): ?>
                                            <img src="<?php echo $blog['featured_image']; ?>" class="img-fluid mb-2 rounded">
                                        <?php endif; ?>
                                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                                        <small class="text-muted">Leave empty to keep current image</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            <?php while($cat = $categories->fetch_assoc()): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $blog['category_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($cat['name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Excerpt</label>
                                        <textarea name="excerpt" class="form-control" rows="3" required><?php echo htmlspecialchars($blog['excerpt']); ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="draft" <?php echo $blog['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                            <option value="published" <?php echo $blog['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Update Blog</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'undo', 'redo'],
        ckfinder: {
            uploadUrl: 'https://sarathicooperative.org/admin/blogs/upload-image.php'
        }
    })
    .catch(error => {
        console.error(error);
    });

document.getElementById('blogForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('update-blog.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            window.location.href = 'index.php?success=Blog updated successfully';
        } else {
            alert(data.message || 'Error updating blog');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating blog');
    });
});
</script>

<?php include '../includes/footer.php'; ?>