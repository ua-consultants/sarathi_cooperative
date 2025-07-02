<?php
// Database connection setup
$host = 'localhost';
$dbname = 'u828878874_sarathi_db';
$username = 'u828878874_sarathi_new';
$password = '#Sarathi@2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get blog ID from URL
$blog_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($blog_id <= 0) {
    header('Location: blogs.php');
    exit;
}

$blog = $pdo->prepare("SELECT b.*, c.name as category_name 
                      FROM blogs b 
                      LEFT JOIN blog_categories c ON b.category_id = c.id 
                      WHERE b.id = ? AND b.status = 'published'");
$blog->execute([$blog_id]);
$blog = $blog->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    header('Location: blogs.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Sarathi</title>
    <style>
        .blog-content img {
            margin-top: 85px;
            max-width: 100%;
            height: auto;
        }
        .blog-container {
            margin-top: 105px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .featured-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .blog-header {
            margin-top: 25px;
            margin-bottom: 2rem;
        }
        .blog-meta {
            display: flex;
            gap: 1.5rem;
            color: #666;
            margin: 1rem 0;
            font-size: 0.9rem;
        }
        .blog-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .blog-excerpt {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-style: italic;
            color: #555;
        }
        .blog-content {
            line-height: 1.8;
            color: #444;
        }
        .blog-tags {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        .tag {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            background: #eee;
            border-radius: 20px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Sarathi Cooperative",
      "alternateName": [
        "Sarathi Research Consulting and Management Services",
        "Sarathi Research Services",
        "Sarathi Consulting Services",
        "Sarathi Marketing Services",
        "Sarathi Services",
        "Sarathi Research and Marketing Services",
        "Sarathi Research and Consulting Services",
        "Sarathi Consulting and Marketing Services",
        "Research and Consulting Services",
        "Marketing Services",
        "Marketing and Consulting Services",
        "Sarathi Consultants",
        "Sarathi Consultancy"
      ],
      "url": "https://sarathicooperative.org"
    }
</script>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="blog-container">
        <?php if (!empty($blog['featured_image'])): ?>
            <img src="admin/uploads/blogs/<?php echo htmlspecialchars($blog['featured_image']); ?>" 
                alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                class="featured-image">
        <?php endif; ?>
        
        <div class="blog-header">
            <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
            
            <div class="blog-meta">
                <span>
                    <i class="fas fa-folder"></i>
                    <?php echo htmlspecialchars($blog['category_name']); ?>
                </span>
                <span>
                    <i class="fas fa-calendar"></i>
                    <?php echo date('F d, Y', strtotime($blog['created_at'])); ?>
                </span>
                <span>
                    <i class="fas fa-user"></i>
                    <?php echo htmlspecialchars($blog['author_name']); ?>
                </span>
            </div>

            <?php if($blog['excerpt']): ?>
                <div class="blog-excerpt">
                    <?php echo htmlspecialchars($blog['excerpt']); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="blog-content">
            <?php echo $blog['content']; ?>
        </div>

        <?php if($blog['meta_keywords']): ?>
        <div class="blog-tags">
            <h3>Tags</h3>
            <?php
            $keywords = explode(',', $blog['meta_keywords']);
            foreach($keywords as $keyword): ?>
                <span class="tag"><?php echo trim($keyword); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>