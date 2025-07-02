<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

$blog_id = (int)$_GET['id'];
$blog = $conn->query("
    SELECT b.*, c.name as category_name, u.username as author_name 
    FROM blogs b 
    LEFT JOIN blog_categories c ON b.category_id = c.id 
    LEFT JOIN users u ON b.author_id = u.id 
    WHERE b.id = $blog_id
")->fetch_assoc();

if (!$blog) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .blog-content img {
            max-width: 100%;
            height: auto;
        }
        .preview-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #333;
            color: white;
            padding: 10px;
            z-index: 1000;
        }
        .blog-container {
            margin-top: 60px;
        }
        .featured-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="preview-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-warning me-2">Preview Mode</span>
                <span class="badge bg-<?php echo $blog['status'] == 'published' ? 'success' : 'secondary'; ?>">
                    <?php echo ucfirst($blog['status']); ?>
                </span>
            </div>
            <div>
                <a href="edit.php?id=<?php echo $blog_id; ?>" class="btn btn-sm btn-primary">Edit</a>
                <button onclick="window.close()" class="btn btn-sm btn-secondary">Close Preview</button>
            </div>
        </div>
    </div>

    <div class="blog-container">
        <?php if (!empty($blog['featured_image'])): ?>
            <img src="<?php echo str_replace('/sarathi/', '../', $blog['featured_image']); ?>" 
                alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                class="featured-image"
                onerror="this.onerror=null; this.src='../assets/images/placeholder.jpg';">
        <?php endif; ?>
        
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="mb-4">
                        <h1 class="mb-3"><?php echo htmlspecialchars($blog['title']); ?></h1>
                        
                        <div class="d-flex align-items-center text-muted mb-3">
                            <span class="me-3">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($blog['author_name']); ?>
                            </span>
                            <span class="me-3">
                                <i class="fas fa-folder"></i>
                                <?php echo htmlspecialchars($blog['category_name']); ?>
                            </span>
                            <span>
                                <i class="fas fa-calendar"></i>
                                <?php echo date('F d, Y', strtotime($blog['created_at'])); ?>
                            </span>
                        </div>

                        <div class="bg-light p-3 rounded mb-4">
                            <?php echo htmlspecialchars($blog['excerpt']); ?>
                        </div>
                    </div>

                    <div class="blog-content">
                        <?php echo $blog['content']; ?>
                    </div>

                    <?php if($blog['meta_keywords']): ?>
                    <div class="mt-4">
                        <h5>Tags</h5>
                        <?php
                        $keywords = explode(',', $blog['meta_keywords']);
                        foreach($keywords as $keyword): ?>
                            <span class="badge bg-secondary me-1"><?php echo trim($keyword); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">SEO Preview</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label text-muted">Meta Title</label>
                                <div class="border p-2 rounded">
                                    <?php echo htmlspecialchars($blog['meta_title']); ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Meta Description</label>
                                <div class="border p-2 rounded">
                                    <?php echo htmlspecialchars($blog['meta_description']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>