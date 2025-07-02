<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if required files exist before including them
if (!file_exists('db.php')) {
    die("Error: db.php file not found.");
}

require_once 'db.php';

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid memory book ID.");
}

$memoryId = (int)$_GET['id'];

// Check if $pdo is available
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Database connection error: PDO object not available.");
}

try {
    // Fetch memory book details
    $stmt = $pdo->prepare("
        SELECT m.*, u.username AS creator_name 
        FROM memories m 
        LEFT JOIN users u ON m.created_by = u.id 
        WHERE m.id = ? AND m.type = 'book' AND m.status = 1
    ");
    $stmt->execute([$memoryId]);
    $memory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$memory) {
        die("Memory book not found or is not active.");
    }

    // Fetch memory book pages - try multiple possible table structures
    $pages = [];
    
    // First try the memory_pages table
    try {
        $pagesStmt = $pdo->prepare("SELECT page_number, image_path FROM memory_pages WHERE memory_id = ? ORDER BY page_number ASC");
        $pagesStmt->execute([$memoryId]);
        $pages = $pagesStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // If memory_pages doesn't exist, try getting images from memory_images table
        try {
            $pagesStmt = $pdo->prepare("SELECT id as page_number, image_path FROM memory_images WHERE memory_id = ? ORDER BY id ASC");
            $pagesStmt->execute([$memoryId]);
            $pages = $pagesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e2) {
            // If no separate pages table, check if images are stored as JSON in memories table
            if (!empty($memory['images'])) {
                $images = json_decode($memory['images'], true);
                if (is_array($images)) {
                    $pages = [];
                    foreach ($images as $index => $image) {
                        $pages[] = [
                            'page_number' => $index + 1,
                            'image_path' => $image
                        ];
                    }
                }
            }
        }
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("General error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($memory['title'] ?? 'Memory Book'); ?> - Memory Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: grey;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .memory-book-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .memory-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .memory-title {
            background: black;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .memory-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        
        .memory-info-item {
            background: rgba(255, 255, 255, 0.7);
            padding: 1rem;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }
        
        .memory-info i {
            color: #667eea;
            width: 24px;
            margin-right: 8px;
        }
        
        .page-viewer {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 2rem;
        }
        
        .page-image {
            max-height: 75vh;
            max-width: 100%;
            object-fit: contain;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }
        
        .page-image:hover {
            transform: scale(1.02);
        }
        
        .thumbnail-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .thumbnail-nav {
            max-height: 200px;
            overflow-y: auto;
            padding: 0.5rem;
        }
        
        .thumbnail-nav::-webkit-scrollbar {
            width: 8px;
        }
        
        .thumbnail-nav::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        
        .thumbnail-nav::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }
        
        .thumbnail {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border: 3px solid transparent;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .thumbnail.active {
            border-color: #667eea;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .thumbnail:hover {
            transform: scale(1.05);
            border-color: #764ba2;
        }
        
        .navigation-controls {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .nav-btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .nav-btn:hover:not(:disabled) {
            background: linear-gradient(45deg, #764ba2, #667eea);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .nav-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        
        .page-counter {
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            color: #333;
        }
        
        .page-number-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        
        .fullscreen-modal .modal-content {
            background: rgba(0, 0, 0, 0.95);
            border: none;
        }
        
        .fullscreen-modal img {
            max-height: 90vh;
            max-width: 90vw;
            object-fit: contain;
        }
        
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            color: #333;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .back-btn:hover {
            background: white;
            transform: translateY(-2px);
            color: #333;
            text-decoration: none;
        }
        
        .no-pages-message {
            text-align: center;
            padding: 4rem 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .no-pages-message i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .memory-title {
                font-size: 2rem;
            }
            
            .memory-header {
                padding: 1.5rem;
            }
            
            .page-viewer {
                padding: 1rem;
            }
            
            .navigation-controls {
                flex-direction: column;
                gap: 1rem;
                padding: 1.5rem;
            }
            
            .nav-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<a href="library.php" class="back-btn">
    <i class="fas fa-arrow-left"></i> Back to Library
</a>

<div class="container memory-book-container">
    <!-- Memory Book Header -->
    <div class="memory-header">
        <h1 class="memory-title"><?php echo htmlspecialchars($memory['title'] ?? 'Untitled Memory Book'); ?></h1>
        
        <div class="memory-info">
            <?php if (isset($memory['event_date']) && $memory['event_date'] && $memory['event_date'] !== '0000-00-00'): ?>
            <div class="memory-info-item">
                <i class="fas fa-calendar-alt"></i>
                <strong>Event Date:</strong> <?php echo date('F j, Y', strtotime($memory['event_date'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($memory['venue']) && $memory['venue']): ?>
            <div class="memory-info-item">
                <i class="fas fa-map-marker-alt"></i>
                <strong>Venue:</strong> <?php echo htmlspecialchars($memory['venue']); ?>
            </div>
            <?php endif; ?>
            
            <div class="memory-info-item">
                <i class="fas fa-images"></i>
                <strong>Total Photos:</strong> <?php echo count($pages); ?>
            </div>
            
            <div class="memory-info-item">
                <i class="fas fa-user"></i>
                <strong>Created by:</strong> <?php echo htmlspecialchars($memory['creator_name'] ?? 'Unknown'); ?>
            </div>
            
            <?php if (isset($memory['created_at'])): ?>
            <div class="memory-info-item">
                <i class="fas fa-clock"></i>
                <strong>Created:</strong> <?php echo date('F j, Y', strtotime($memory['created_at'])); ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($memory['description']) && $memory['description']): ?>
            <div class="memory-info-item" style="grid-column: 1 / -1;">
                <i class="fas fa-quote-left"></i>
                <strong>Description:</strong> <?php echo htmlspecialchars($memory['description']); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Memory Book Viewer -->
    <?php if (!empty($pages)): ?>
        <!-- Main Image Viewer -->
        <div class="page-viewer">
            <div id="memoryViewer" class="text-center position-relative">
                <?php foreach ($pages as $index => $page): ?>
                    <div class="page-container" style="<?php echo $index !== 0 ? 'display:none;' : ''; ?>" data-page="<?php echo htmlspecialchars($page['page_number']); ?>">
                        <div class="position-relative d-inline-block">
                            <?php 
                            // Try multiple possible image paths
                            $imagePaths = [
                                'admin/uploads/memories/' . $page['image_path'],
                                'admin/uploads/memories/' . ltrim($page['image_path'], '/'),
                                'uploads/memories/' . $page['image_path'],
                                $page['image_path']
                            ];
                            
                            $validImagePath = null;
                            foreach ($imagePaths as $path) {
                                if (file_exists($path)) {
                                    $validImagePath = $path;
                                    break;
                                }
                            }
                            
                            if ($validImagePath): 
                            ?>
                            <img src="<?php echo htmlspecialchars($validImagePath); ?>"
                                 alt="Page <?php echo htmlspecialchars($page['page_number']); ?>"
                                 class="page-image"
                                 onclick="openFullscreen(this.src)">
                            <div class="page-number-badge">Photo <?php echo htmlspecialchars($page['page_number']); ?></div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Image not found: <?php echo htmlspecialchars($page['image_path']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Navigation Controls -->
        <div class="navigation-controls">
            <button class="btn nav-btn" id="prevBtn" <?php echo count($pages) <= 1 ? 'disabled' : ''; ?>>
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            
            <div class="page-counter">
                <span id="currentPage">1</span> of <span id="totalPages"><?php echo count($pages); ?></span>
            </div>
            
            <button class="btn nav-btn" id="nextBtn" <?php echo count($pages) <= 1 ? 'disabled' : ''; ?>>
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Thumbnail Navigation -->
        <div class="thumbnail-container">
            <h5 class="mb-3"><i class="fas fa-th"></i> All Photos</h5>
            <div class="row g-2 thumbnail-nav">
                <?php foreach ($pages as $index => $page): ?>
                    <div class="col-6 col-md-3 col-lg-2">
                        <?php 
                        $imagePaths = [
                            'admin/uploads/memories/' . $page['image_path'],
                            'admin/uploads/memories/' . ltrim($page['image_path'], '/'),
                            'uploads/memories/' . $page['image_path'],
                            $page['image_path']
                        ];
                        
                        $validImagePath = null;
                        foreach ($imagePaths as $path) {
                            if (file_exists($path)) {
                                $validImagePath = $path;
                                break;
                            }
                        }
                        
                        if ($validImagePath): 
                        ?>
                        <img src="<?php echo htmlspecialchars($validImagePath); ?>"
                             alt="Photo <?php echo htmlspecialchars($page['page_number']); ?>"
                             data-page="<?php echo htmlspecialchars($page['page_number']); ?>"
                             class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                             onclick="goToPage(<?php echo (int)$page['page_number']; ?>)">
                        <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center thumbnail" style="border: 2px dashed #ccc;">
                            <small class="text-muted">No Image</small>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="no-pages-message">
            <i class="fas fa-images"></i>
            <h3>No Photos Available</h3>
            <p class="text-muted">This memory book doesn't have any photos yet.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Fullscreen Modal -->
<div class="modal fade fullscreen-modal" id="fullscreenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-body d-flex align-items-center justify-content-center p-0">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" style="z-index: 1001;"></button>
                <img src="" id="fullscreenImage" alt="Full Size View">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentPage = 1;
    const totalPages = <?php echo count($pages); ?>;

    function showPage(pageNumber) {
        // Hide all pages
        document.querySelectorAll('.page-container').forEach(container => {
            container.style.display = 'none';
        });
        
        // Remove active class from all thumbnails
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });

        // Show selected page
        const pageContainer = document.querySelector(`.page-container[data-page="${pageNumber}"]`);
        if (pageContainer) {
            pageContainer.style.display = 'block';
        }
        
        // Activate corresponding thumbnail
        const thumbnail = document.querySelector(`.thumbnail[data-page="${pageNumber}"]`);
        if (thumbnail) {
            thumbnail.classList.add('active');
            // Scroll thumbnail into view
            thumbnail.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Update page counter
        document.getElementById('currentPage').textContent = pageNumber;
        currentPage = pageNumber;

        // Update navigation buttons
        document.getElementById('prevBtn').disabled = (pageNumber === 1);
        document.getElementById('nextBtn').disabled = (pageNumber === totalPages);
    }

    // Navigation button events
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentPage > 1) showPage(currentPage - 1);
    });

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentPage < totalPages) showPage(currentPage + 1);
    });

    function goToPage(pageNum) {
        showPage(pageNum);
    }

    function openFullscreen(imageSrc) {
        document.getElementById('fullscreenImage').src = imageSrc;
        new bootstrap.Modal(document.getElementById('fullscreenModal')).show();
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('fullscreenModal');
        if (modal.classList.contains('show')) return;

        if (e.key === 'ArrowLeft') {
            document.getElementById('prevBtn').click();
        } else if (e.key === 'ArrowRight') {
            document.getElementById('nextBtn').click();
        } else if (e.key === 'Escape') {
            window.location.href = 'library.php';
        }
    });

    // Auto-play functionality (optional)
    let autoPlay = false;
    let autoPlayInterval;

    function toggleAutoPlay() {
        autoPlay = !autoPlay;
        if (autoPlay) {
            autoPlayInterval = setInterval(() => {
                if (currentPage < totalPages) {
                    showPage(currentPage + 1);
                } else {
                    showPage(1); // Loop back to first page
                }
            }, 3000); // Change every 3 seconds
        } else {
            clearInterval(autoPlayInterval);
        }
    }

    // Touch/swipe support for mobile
    let startX = 0;
    let endX = 0;

    document.addEventListener('touchstart', function(e) {
        startX = e.changedTouches[0].screenX;
    });

    document.addEventListener('touchend', function(e) {
        endX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = startX - endX;
        
        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swiped left - next page
                document.getElementById('nextBtn').click();
            } else {
                // Swiped right - previous page
                document.getElementById('prevBtn').click();
            }
        }
    }
</script>

</body>
</html>