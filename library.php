<?php
require_once('db.php');

// Handle member authentication for modal
if (isset($_POST['verify_member']) && isset($_POST['member_id']) && isset($_POST['book_id'])) {
    $member_id = trim($_POST['member_id']);
    $book_id = (int)$_POST['book_id'];
    
    // Check if member exists
    $member_check = $pdo->prepare("SELECT id FROM members WHERE username = ? AND status = 1");
    $member_check->execute([$member_id]);
    $member_exists = $member_check->fetch();
    
    if ($member_exists) {
        // Store in session for this book
        if (!isset($_SESSION)) session_start();
        $_SESSION['verified_books'][$book_id] = true;
        $_SESSION['member_id'] = $member_id;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid Member ID. Please check your credentials.']);
    }
    exit;
}

// Get filter parameters
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : null;
$type_filter = isset($_GET['type']) ? $_GET['type'] : 'all';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$title_filter = isset($_GET['title']) ? trim($_GET['title']) : '';

// Fetch all active categories
$category_query = "SELECT id, name FROM categories WHERE status = 1";
$categories = $pdo->query($category_query)->fetchAll(PDO::FETCH_ASSOC);

// Fetch all unique titles for dropdown
$titles_query = "
    SELECT DISTINCT title FROM (
        SELECT title FROM ebooks WHERE status = 1
        UNION
        SELECT title FROM achievements WHERE status = 1
        UNION
        SELECT title FROM memories WHERE status = 1 AND type = 'book'
    ) AS all_titles
    ORDER BY title ASC
";
$all_titles = $pdo->query($titles_query)->fetchAll(PDO::FETCH_COLUMN);

// Build the ebooks query - UPDATED to include book_date and visibility with search functionality
$ebook_query = "SELECT e.*, c.name as category_name 
               FROM ebooks e 
               LEFT JOIN categories c ON e.category_id = c.id 
               WHERE e.status = 1";
$params = [];

if ($category_filter) {
    $ebook_query .= " AND e.category_id = ?";
    $params[] = $category_filter;
}

if ($title_filter) {
    $ebook_query .= " AND e.title = ?";
    $params[] = $title_filter;
}

if ($search_query) {
    $ebook_query .= " AND (e.title LIKE ? OR e.author LIKE ? OR e.description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Add ordering
if ($search_query) {
    $ebook_query .= " ORDER BY e.title ASC";
} else {
    $ebook_query .= " ORDER BY e.created_at DESC";
}

// Build the achievements query
$achievement_query = "SELECT * FROM achievements WHERE status = 1";
$achievement_params = [];

if ($title_filter) {
    $achievement_query .= " AND title = ?";
    $achievement_params[] = $title_filter;
}

if ($search_query) {
    $achievement_query .= " AND (title LIKE ? OR description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $achievement_params[] = $search_param;
    $achievement_params[] = $search_param;
}

if ($search_query) {
    $achievement_query .= " ORDER BY title ASC";
} else {
    $achievement_query .= " ORDER BY created_at DESC";
}

// Build the memory books query
$memorybook_query = "SELECT m.id, m.title, m.description, m.cover_image, m.created_at 
                     FROM memories m 
                     WHERE m.status = 1 AND m.type = 'book'";
$memorybook_params = [];

if ($title_filter) {
    $memorybook_query .= " AND m.title = ?";
    $memorybook_params[] = $title_filter;
}

if ($search_query) {
    $memorybook_query .= " AND (m.title LIKE ? OR m.description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $memorybook_params[] = $search_param;
    $memorybook_params[] = $search_param;
}

if ($search_query) {
    $memorybook_query .= " ORDER BY m.title ASC";
} else {
    $memorybook_query .= " ORDER BY m.created_at DESC";
}

// Execute queries
$stmt = $pdo->prepare($ebook_query);
$stmt->execute($params);
$ebooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_achievements = $pdo->prepare($achievement_query);
$stmt_achievements->execute($achievement_params);
$achievements = $stmt_achievements->fetchAll(PDO::FETCH_ASSOC);

$stmt_memorybooks = $pdo->prepare($memorybook_query);
$stmt_memorybooks->execute($memorybook_params);
$memorybooks = $stmt_memorybooks->fetchAll(PDO::FETCH_ASSOC);

// Function to build URL with current filters
function buildFilterUrl($new_params = []) {
    global $category_filter, $type_filter, $search_query, $title_filter;
    
    $params = [
        'type' => $type_filter,
        'category' => $category_filter,
        'search' => $search_query,
        'title' => $title_filter
    ];
    
    // Override with new parameters
    foreach ($new_params as $key => $value) {
        $params[$key] = $value;
    }
    
    // Remove empty parameters
    $params = array_filter($params, function($value) {
        return $value !== '' && $value !== null;
    });
    
    return 'library.php?' . http_build_query($params);
}

// Start session for member verification
if (!isset($_SESSION)) session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library - Sarathi Cooperative</title>        
    <link rel="icon" href="img/logo-favi-icon.png">
    <style>
        .body {
            overflow: scroll;
        }
        .ebook-grid {
            margin-bottom: 150px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 2rem;
            padding: 2rem;
        }
        
        .ebook-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            position: relative;
        }
        
        .ebook-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .ebook-cover {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .ebook-content {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            height: 140px;
        }
        
        .ebook-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            font-size: 0.85rem;
            color: #666;
        }
        
        .ebook-title {
            font-size: 1.2rem;
            margin: 0 0 1rem 0;
            color: #333;
            line-height: 1.3;
            flex-grow: 1;
        }
        
        .ebook-author {
            font-weight: 500;
        }
        
        .ebook-date {
            font-style: italic;
            color: #888;
        }
        
        .ebook-description {
            color: #666;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        /* Member only badge */
        .member-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 2;
        }
        
        .read-indicator {
            background: #28a745;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 0.5rem;
        }
        
        @media (max-width: 1024px) {
            .ebook-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .ebook-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .achievement-card .ebook-cover {
            height: 200px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 1rem;
        }
        
        .achievement-card .ebook-content {
            text-align: center;
            height: auto;
        }
        
        .achievement-card .ebook-title {
            font-size: 1.1rem;
            margin: 1rem 0;
        }

        /* Enhanced Filter Styles */
        .filter-strip {
            margin-top: 125px;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .filter-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: space-between;
        }
        
        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-label {
            color: white;
            font-weight: 500;
            font-size: 0.9rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        .filter-control {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            background: rgba(255,255,255,0.95);
            color: #333;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            min-width: 150px;
        }
        
        .filter-control:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        }
        
        /* Search Bar Styles */
        .search-container {
            position: relative;
            min-width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 8px 40px 8px 15px;
            border: none;
            border-radius: 25px;
            background: rgba(255,255,255,0.95);
            color: #333;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .search-input:focus {
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        }
        
        .search-input::placeholder {
            color: #999;
            font-style: italic;
        }
        
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #667eea;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            background: #5a67d8;
            transform: translateY(-50%) scale(1.1);
        }
        
        .clear-filters-btn {
            padding: 8px 16px;
            background: black;
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .clear-filters-btn:hover {
            background: gold;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Modal Styles */
        .book-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            overflow: auto;
        }
        
        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 2% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 900px;
            height: 90vh;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            margin: 0;
            font-size: 1.2rem;
        }
        
        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        
        .close:hover {
            opacity: 0.7;
        }
        
        .modal-body {
            height: calc(90vh - 70px);
            overflow: hidden;
        }
        
        .pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Member verification form */
        .member-verification {
            padding: 40px;
            text-align: center;
        }
        
        .member-form {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .verify-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.3s ease;
            margin-right: 10px;
        }
        
        .verify-btn:hover {
            transform: translateY(-2px);
        }
        
        .join-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .join-link:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #dc3545;
            margin-top: 10px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 4px;
            display: none;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                height: 95vh;
                margin: 2.5% auto;
            }
            
            .member-verification {
                padding: 20px;
            }
            
            .search-container {
                min-width: 250px;
            }
            
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-group {
                justify-content: center;
            }
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

    <!-- Enhanced Filter Strip -->
    <div class="filter-strip">
        <div class="filter-container">
            <div class="filter-row">
                <!-- Main Filters -->
                <div class="filter-group">
                    <div class="filter-item">
                        <select class="filter-control" onchange="window.location.href='<?php echo buildFilterUrl(['category' => '']); ?>' + this.value">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $category_filter === $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-item">
                        <div class="search-container">
                            <form method="GET" action="library.php" style="margin: 0;">
                                <input type="hidden" name="category" value="<?php echo $category_filter; ?>">
                                <input type="hidden" name="title" value="<?php echo $title_filter; ?>">
                                <input type="text" 
                                       name="search" 
                                       class="search-input" 
                                       placeholder="A-Z" 
                                       value="<?php echo htmlspecialchars($search_query); ?>"
                                       onkeypress="if(event.key==='Enter') this.form.submit();">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="filter-item">
                        <select class="filter-control" onchange="window.location.href='<?php echo buildFilterUrl(['title' => '']); ?>' + encodeURIComponent(this.value)">
                            <option value="">All Titles</option>
                            <?php foreach ($all_titles as $title): ?>
                                <option value="<?php echo htmlspecialchars($title); ?>" <?php echo $title_filter === $title ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Clear Filters -->
                <div class="filter-group">
                    <a href="library.php" class="clear-filters-btn">Clear All Filters</a>
                </div>
            </div>
        </div>
    </div>

    <main>
        <div class="ebook-grid">
            <?php if ($type_filter === 'all' || $type_filter === 'ebooks'): ?>
                <?php foreach ($ebooks as $ebook): ?>
                    <div class="ebook-card" onclick="openBookModal(<?php echo $ebook['id']; ?>, '<?php echo htmlspecialchars($ebook['title'], ENT_QUOTES); ?>', '<?php echo $ebook['visibility']; ?>', '<?php echo htmlspecialchars($ebook['file_path']); ?>')">
                        <!--<?php if ($ebook['visibility'] === 'members'): ?>-->
                        <!--    <div class="member-badge">Members Only</div>-->
                        <!--<?php endif; ?>-->
                        <img src="https://sarathicooperative.org/admin/uploads/ebooks/covers/<?php echo htmlspecialchars($ebook['cover_image']); ?>" 
                             alt="<?php echo htmlspecialchars($ebook['title']); ?>" 
                             class="ebook-cover">
                        <div class="ebook-content">
                            <h2 class="ebook-title"><?php echo htmlspecialchars($ebook['title']); ?></h2>
                            <div class="ebook-meta">
                                <span class="ebook-author"><?php echo htmlspecialchars($ebook['author']); ?></span>
                                <span class="ebook-date"><?php echo htmlspecialchars($ebook['book_date'] ?? 'N/A'); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($type_filter === 'all' || $type_filter === 'achievements'): ?>
                <?php foreach ($achievements as $achievement): ?>
                    <article class="ebook-card achievement-card">
                        <img src="admin/uploads/<?php echo htmlspecialchars($achievement['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($achievement['title']); ?>" 
                             class="ebook-cover">
                        <div class="ebook-content">
                            <h2 class="ebook-title"><?php echo htmlspecialchars($achievement['title']); ?></h2>
                            <p class="ebook-description"><?php echo htmlspecialchars(substr($achievement['description'], 0, 150)) . '...'; ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if ($type_filter === 'all' || $type_filter === 'memorybooks'): ?>
                <?php foreach ($memorybooks as $memorybook): ?>
                    <a href="view-memory-book.php?id=<?php echo $memorybook['id']; ?>" class="ebook-card achievement-card">
                        <img src="admin/uploads/memories<?php echo htmlspecialchars($memorybook['cover_image']); ?>" 
                             alt="<?php echo htmlspecialchars($memorybook['title']); ?>" 
                             class="ebook-cover">
                        <div class="ebook-content">
                            <h2 class="ebook-title"><?php echo htmlspecialchars($memorybook['title']); ?></h2>
                            <p class="ebook-description">
                                <?php echo htmlspecialchars(substr($memorybook['description'], 0, 150)) . '...'; ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if ((count($ebooks) + count($achievements) + count($memorybooks)) === 0): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #666;">
                    <h3>No results found</h3>
                    <p>Try adjusting your search terms or filters.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Book Modal -->
    <div id="bookModal" class="book-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Book Viewer</h3>
                <span class="close" onclick="closeBookModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <script>
        let currentBookId = null;
        let currentBookVisibility = null;
        let currentBookPath = null;

        function openBookModal(bookId, title, visibility, filePath) {
            currentBookId = bookId;
            currentBookVisibility = visibility;
            currentBookPath = filePath;
            
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('bookModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            // Check if member verification is needed
            if (visibility === 'members') {
                // Check if already verified for this book
                <?php if (isset($_SESSION['verified_books'])): ?>
                    const verifiedBooks = <?php echo json_encode($_SESSION['verified_books']); ?>;
                    if (verifiedBooks[bookId]) {
                        loadPDFViewer(filePath);
                        return;
                    }
                <?php endif; ?>
                
                showMemberVerification();
            } else {
                loadPDFViewer(filePath);
            }
        }
        
        function closeBookModal() {
            document.getElementById('bookModal').style.display = 'none';
            document.body.style.overflow = 'scrollbar'; // Restore scrolling
            document.getElementById('modalBody').innerHTML = '';
        }
        
        function showMemberVerification() {
            document.getElementById('modalBody').innerHTML = `
                <div class="member-verification">
                    <h3 style="margin-bottom: 20px; color: #333;">Member Verification Required</h3>
                    <p style="margin-bottom: 30px; color: #666;">This book is available for Sarathi Cooperative members only. Please enter your Member ID to access.</p>
                    
                    <div class="member-form">
                        <div class="form-group">
                            <label for="memberId">Member ID</label>
                            <input type="text" id="memberId" placeholder="Enter your Member ID" required>
                        </div>
                        <div class="error-message" id="errorMessage"></div>
                        <div style="margin-top: 20px;">
                            <button class="verify-btn" onclick="verifyMember()">Verify & Read Book</button>
                        </div>
                        <div class="loading" id="loadingIndicator">
                            <div class="spinner"></div>
                            <p>Verifying membership...</p>
                        </div>
                        <p style="margin-top: 20px; color: #666;">
                            Don't have a Member ID? 
                            <a href="become-a-sarathian.php" class="join-link" target="_blank">Become a Sarathian</a>
                        </p>
                    </div>
                </div>
            `;
        }
        
        function verifyMember() {
            const memberId = document.getElementById('memberId').value.trim();
            const errorDiv = document.getElementById('errorMessage');
            const loadingDiv = document.getElementById('loadingIndicator');
            
            if (!memberId) {
                showError('Please enter your Member ID');
                return;
            }
            
            // Show loading
            loadingDiv.style.display = 'block';
            errorDiv.style.display = 'none';
            
            // Send verification request
            const formData = new FormData();
            formData.append('verify_member', '1');
            formData.append('member_id', memberId);
            formData.append('book_id', currentBookId);
            
            fetch('library.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                
                if (data.success) {
                    loadPDFViewer(currentBookPath);
                } else {
                    showError(data.message || 'Invalid Member ID. Please check your credentials.');
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                showError('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        }
        
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
        
        function loadPDFViewer(filePath) {
            const pdfUrl = `https://sarathicooperative.org/admin/uploads/ebooks/files/${filePath}`;
            
            document.getElementById('modalBody').innerHTML = `
                <iframe class="pdf-viewer" 
                        src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=1&view=FitH" 
                        oncontextmenu="return false;"
                        onload="this.style.pointerEvents='auto';">
                    <p>Your browser does not support PDFs. 
                       <a href="${pdfUrl}" target="_blank">Download the PDF</a>
                    </p>
                </iframe>
            `;
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('bookModal');
            if (event.target === modal) {
                closeBookModal();
            }
        }
        
        // Handle escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeBookModal();
            }
        });
        
        // Prevent right-click context menu on modal
        document.getElementById('bookModal').addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Additional security measures
        document.addEventListener('keydown', function(e) {
            // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if (e.keyCode == 123 || 
                (e.ctrlKey && e.shiftKey && e.keyCode == 73) ||
                (e.ctrlKey && e.shiftKey && e.keyCode == 74) ||
                (e.ctrlKey && e.keyCode == 85)) {
                e.preventDefault();
                return false;
            }
            
            // Disable Ctrl+P (print)
            if (e.ctrlKey && e.keyCode == 80) {
                e.preventDefault();
                return false;
            }
            
            // Disable Ctrl+S (save)
            if (e.ctrlKey && e.keyCode == 83) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable right-click on entire document when modal is open
        document.addEventListener('contextmenu', function(e) {
            if (document.getElementById('bookModal').style.display === 'block') {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable text selection in modal
        document.addEventListener('selectstart', function(e) {
            if (document.getElementById('bookModal').style.display === 'block') {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable drag and drop in modal
        document.addEventListener('dragstart', function(e) {
            if (document.getElementById('bookModal').style.display === 'block') {
                e.preventDefault();
                return false;
            }
        });
        
        // Additional protection against print screen
        document.addEventListener('keyup', function(e) {
            if (e.keyCode == 44) { // Print Screen key
                if (document.getElementById('bookModal').style.display === 'block') {
                    alert('Screenshots are not allowed for protected content.');
                }
            }
        });
        
        // Blur event to detect when window loses focus (potential screenshot)
        let blurTimeout;
        window.addEventListener('blur', function() {
            if (document.getElementById('bookModal').style.display === 'block') {
                blurTimeout = setTimeout(function() {
                    // Optional: Add watermark or blur content when window loses focus
                    console.log('Window lost focus - potential screenshot attempt');
                }, 100);
            }
        });
        
        window.addEventListener('focus', function() {
            if (blurTimeout) {
                clearTimeout(blurTimeout);
            }
        });
    </script>
    
    <!-- Additional CSS for security -->
    <style>
        /* Prevent text selection in modal */
        .book-modal {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Prevent drag and drop */
        .book-modal * {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
        
        /* Hide PDF toolbar and controls */
        .pdf-viewer {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            pointer-events: none;
        }
        
        /* Watermark overlay for additional protection */
        .watermark-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><text x="50%" y="50%" text-anchor="middle" dy=".3em" font-family="Arial" font-size="20" fill="rgba(0,0,0,0.1)" transform="rotate(-45 100 100)">Sarathi Cooperative</text></svg>') repeat;
            pointer-events: none;
            z-index: 10;
        }
        
        /* Print media query to hide content */
        @media print {
            .book-modal {
                display: none !important;
            }
            
            body * {
                visibility: hidden;
            }
            
            body::after {
                content: "Printing is not allowed for protected content.";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 24px;
                color: #333;
                visibility: visible;
            }
        }
        
        /* Additional security styles */
        .no-select {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Hide when developer tools are open */
        @media screen and (max-width: 1200px) and (min-width: 1199px) {
            .book-modal .modal-content {
                filter: blur(5px);
            }
            
            .book-modal::after {
                content: "Developer tools detected. Please close to continue.";
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                z-index: 1001;
                color: #333;
                font-size: 16px;
                text-align: center;
            }
        }
    </style>
        <?php include 'footer.php'; ?>

</body>
</html>