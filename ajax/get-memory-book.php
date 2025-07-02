<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
requireLogin();

// Set proper headers
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');

try {
    // Validate input
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid memory book ID');
    }
    
    $memoryId = (int)$_GET['id'];
    
    // Database connection check
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get memory book details
    $stmt = $conn->prepare("
        SELECT m.*, u.username as creator_name 
        FROM memories m 
        LEFT JOIN users u ON m.created_by = u.id 
        WHERE m.id = ? AND m.type = 'book' AND m.status = 1
    ");
    
    if (!$stmt) {
        throw new Exception('Database prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $memoryId);
    
    if (!$stmt->execute()) {
        throw new Exception('Database execution failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $memory = $result->fetch_assoc();
    $stmt->close();
    
    if (!$memory) {
        throw new Exception('Memory book not found or access denied');
    }
    
    // Get memory book pages
    $pageStmt = $conn->prepare("
        SELECT page_number, image_path 
        FROM memory_pages 
        WHERE memory_id = ? 
        ORDER BY page_number ASC
    ");
    
    if (!$pageStmt) {
        throw new Exception('Failed to prepare pages query: ' . $conn->error);
    }
    
    $pageStmt->bind_param('i', $memoryId);
    
    if (!$pageStmt->execute()) {
        throw new Exception('Failed to execute pages query: ' . $pageStmt->error);
    }
    
    $pagesResult = $pageStmt->get_result();
    $pages = [];
    
    while ($page = $pagesResult->fetch_assoc()) {
        $pages[] = $page;
    }
    
    $pageStmt->close();
    
    // Format date for display
    $eventDate = new DateTime($memory['event_date']);
    $createdDate = new DateTime($memory['created_at']);
    
    ?>
    
    <div class="memory-book-viewer">
        <!-- Memory Book Header -->
        <div class="memory-book-header mb-4">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="mb-2"><?php echo htmlspecialchars($memory['title']); ?></h3>
                    <div class="memory-info text-muted">
                        <p class="mb-1">
                            <i class="fas fa-calendar-alt"></i> 
                            <strong>Event Date:</strong> <?php echo $eventDate->format('F j, Y'); ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-map-marker-alt"></i> 
                            <strong>Venue:</strong> <?php echo htmlspecialchars($memory['venue']); ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-images"></i> 
                            <strong>Total Pages:</strong> <?php echo count($pages); ?>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-user"></i> 
                            <strong>Created by:</strong> <?php echo htmlspecialchars($memory['creator_name'] ?? 'Unknown'); ?>
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-clock"></i> 
                            <strong>Created:</strong> <?php echo $createdDate->format('F j, Y \a\t g:i A'); ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <div class="memory-book-controls">
                        <button type="button" class="btn btn-secondary btn-sm" id="prevPage" disabled>
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>
                        <span class="page-counter mx-2">
                            <span id="currentPage">1</span> / <span id="totalPages"><?php echo count($pages); ?></span>
                        </span>
                        <button type="button" class="btn btn-secondary btn-sm" id="nextPage" <?php echo count($pages) <= 1 ? 'disabled' : ''; ?>>
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (empty($pages)): ?>
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle"></i>
                <h5>No Pages Found</h5>
                <p>This memory book doesn't have any pages yet.</p>
            </div>
        <?php else: ?>
            <!-- Memory Book Pages Container -->
            <div class="memory-book-pages">
                <div class="page-container text-center">
                    <?php foreach ($pages as $index => $page): ?>
                        <div class="memory-page <?php echo $index === 0 ? 'active' : ''; ?>" 
                             data-page="<?php echo $page['page_number']; ?>"
                             style="<?php echo $index === 0 ? '' : 'display: none;'; ?>">
                            <div class="page-image-container">
                                <img src="uploads/memories<?php echo htmlspecialchars($page['image_path']); ?>" 
                                     alt="Page <?php echo $page['page_number']; ?>" 
                                     class="img-fluid memory-page-image"
                                     style="max-height: 600px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                                     onclick="openImageFullscreen(this.src)">
                                <div class="page-number-badge">
                                    Page <?php echo $page['page_number']; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Thumbnail Navigation (for books with multiple pages) -->
                <?php if (count($pages) > 1): ?>
                    <div class="thumbnail-navigation mt-4">
                        <div class="row">
                            <?php foreach ($pages as $index => $page): ?>
                                <div class="col-2 col-md-1 mb-2">
                                    <div class="thumbnail-wrapper">
                                        <img src="uploads/memories<?php echo htmlspecialchars($page['image_path']); ?>" 
                                             alt="Page <?php echo $page['page_number']; ?>" 
                                             class="img-fluid thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                             data-page="<?php echo $page['page_number']; ?>"
                                             onclick="showPage(<?php echo $page['page_number']; ?>)"
                                             style="cursor: pointer; border-radius: 4px; opacity: <?php echo $index === 0 ? '1' : '0.6'; ?>;">
                                        <div class="thumbnail-number"><?php echo $page['page_number']; ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <style>
    .memory-book-viewer {
        max-width: 100%;
    }
    
    .memory-book-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
    }
    
    .memory-info i {
        width: 20px;
        color: #6c757d;
    }
    
    .page-container {
        position: relative;
        min-height: 400px;
    }
    
    .memory-page {
        transition: opacity 0.3s ease-in-out;
    }
    
    .page-image-container {
        position: relative;
        display: inline-block;
    }
    
    .memory-page-image {
        cursor: pointer;
        transition: transform 0.2s ease;
    }
    
    .memory-page-image:hover {
        transform: scale(1.02);
    }
    
    .page-number-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .thumbnail-wrapper {
        position: relative;
    }
    
    .thumbnail {
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    
    .thumbnail.active {
        border-color: #007bff;
        opacity: 1 !important;
    }
    
    .thumbnail:hover {
        opacity: 1 !important;
        transform: scale(1.1);
    }
    
    .thumbnail-number {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    
    .page-counter {
        font-weight: bold;
    }
    </style>
    
    <script>
    let currentPageNum = 1;
    const totalPagesCount = <?php echo count($pages); ?>;
    
    function showPage(pageNumber) {
        // Hide all pages
        $('.memory-page').hide().removeClass('active');
        $('.thumbnail').removeClass('active').css('opacity', '0.6');
        
        // Show selected page
        $(`.memory-page[data-page="${pageNumber}"]`).show().addClass('active');
        $(`.thumbnail[data-page="${pageNumber}"]`).addClass('active').css('opacity', '1');
        
        // Update current page
        currentPageNum = pageNumber;
        $('#currentPage').text(pageNumber);
        
        // Update navigation buttons
        $('#prevPage').prop('disabled', pageNumber <= 1);
        $('#nextPage').prop('disabled', pageNumber >= totalPagesCount);
    }
    
    $('#prevPage').on('click', function() {
        if (currentPageNum > 1) {
            showPage(currentPageNum - 1);
        }
    });
    
    $('#nextPage').on('click', function() {
        if (currentPageNum < totalPagesCount) {
            showPage(currentPageNum + 1);
        }
    });
    
    function openImageFullscreen(imageSrc) {
        // Create fullscreen modal for image
        const modal = $(`
            <div class="modal fade" id="fullscreenImageModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content bg-dark">
                        <div class="modal-header border-0">
                            <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="${imageSrc}" class="img-fluid" style="max-height: 90vh;">
                        </div>
                    </div>
                </div>
            </div>
        `);
        
        $('body').append(modal);
        modal.modal('show');
        
        modal.on('hidden.bs.modal', function() {
            modal.remove();
        });
    }
    
    // Keyboard navigation
    $(document).on('keydown', function(e) {
        if ($('#viewMemoryBookModal').hasClass('show')) {
            if (e.key === 'ArrowLeft' && currentPageNum > 1) {
                showPage(currentPageNum - 1);
            } else if (e.key === 'ArrowRight' && currentPageNum < totalPagesCount) {
                showPage(currentPageNum + 1);
            }
        }
    });
    </script>
    
    <?php
    
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Get Memory Book Error: " . $e->getMessage());
    
    echo '<div class="alert alert-danger text-center">';
    echo '<i class="fas fa-exclamation-triangle"></i>';
    echo '<h5>Error Loading Memory Book</h5>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '</div>';
    
} catch (Error $e) {
    // Catch fatal errors
    error_log("Get Memory Book Fatal Error: " . $e->getMessage());
    
    echo '<div class="alert alert-danger text-center">';
    echo '<i class="fas fa-exclamation-triangle"></i>';
    echo '<h5>Fatal Error</h5>';
    echo '<p>A fatal error occurred while loading the memory book.</p>';
    echo '</div>';
}
?>