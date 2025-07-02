<?php
require_once('db.php');

// Get book ID from URL
$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$book_id) {
    header('Location: library.php');
    exit;
}

// Fetch book details
$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM ebooks e LEFT JOIN categories c ON e.category_id = c.id WHERE e.id = ? AND e.status = 1");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header('Location: library.php');
    exit;
}

// Check if user is trying to access a members-only book
$is_member_verified = false;
$show_member_prompt = false;

if ($book['visibility'] === 'members') {
    if (isset($_POST['member_id'])) {
        $member_id = trim($_POST['member_id']);
        $member_stmt = $pdo->prepare("SELECT username FROM members WHERE username = ? AND status = 1");
        $member_stmt->execute([$member_id]);
        $member = $member_stmt->fetch();
        
        if ($member) {
            $is_member_verified = true;
            // Store verification in session to avoid repeated prompts
            session_start();
            $_SESSION['verified_member'] = $member_id;
            $_SESSION['verified_for_book'] = $book_id;
        } else {
            $member_error = "Invalid Member ID. Please check your credentials or become a member.";
        }
    } else {
        // Check if already verified in session
        session_start();
        if (isset($_SESSION['verified_member']) && isset($_SESSION['verified_for_book']) && $_SESSION['verified_for_book'] == $book_id) {
            $is_member_verified = true;
        } else if (isset($_GET['page']) && $_GET['page'] > 1) {
            $show_member_prompt = true;
        }
    }
}

// Get the actual file path
$file_path = "admin/uploads/ebooks/files/" . $book['file_path'];
$file_exists = file_exists($file_path);

// For PDF files, we'll use PDF.js
$is_pdf = strtolower(pathinfo($book['file_path'], PATHINFO_EXTENSION)) === 'pdf';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Sarathi Cooperative Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
        }
        
        .book-reader {
            max-width: 100%;
            
            background: white;
            min-height: 100vh;
        }
        
        .book-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1rem;
            position: sticky;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
        }
        
        .book-header.header-hidden {
            transform: translateY(-100%);
        }
        
        .book-cover {
            width: 100px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .book-info h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .book-meta {
            opacity: 0.9;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .book-meta span {
            margin-right: 1.5rem;
        }
        
        .pdf-container {
            width: 100%;
            height: 100vh; /* Adjust height calculation */
            border: none;
            position: relative;
        }
        
        .pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .member-prompt {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 3rem;
            margin: 2rem;
            text-align: center;
            position: fixed; /* Changed to fixed */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 500px;
            width: 90%;
        }
        
        .member-form {
            max-width: 400px;
            margin: 1.5rem auto 0;
        }
        
        .back-to-library {
            position: fixed;
            top: 90px; /* Adjust position to be below header */
            left: 20px;
            z-index: 1001;
        }
        
        .back-btn {
            background: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .back-btn:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }
        
        .blur-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(5px);
            z-index: 999;
        }
        
        .loading-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 400px;
            flex-direction: column;
        }
        
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error-container {
            text-align: center;
            padding: 3rem;
            color: #dc3545;
        }
        
        .pdf-controls {
            position: sticky;
            bottom: 0;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            z-index: 100;
        }
        
        .control-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .control-btn:hover {
            background: #0056b3;
        }
        
        .control-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .book-reader {
                padding-top: 60px; /* Adjust for mobile header */
            }
            
            .book-header {
                padding: 1rem;
                position: relative;
                top: 0;
                position: sticky;
                top: 60px; /* Adjust for mobile header height */
            }
            
            .book-header .row {
                text-align: center;
            }
            
            .book-cover {
                width: 80px;
                height: 100px;
                margin-bottom: 1rem;
            }
            
            .book-info h1 {
                font-size: 1.2rem;
            }
            
            .back-to-library {
                position: fixed;
                top: 70px; /* Adjust for mobile */
                left: 10px;
            }
            
            .back-btn {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            
            .pdf-container {
                height: calc(100vh - 200px); /* Adjust for mobile */
            }
            
            .member-prompt {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                margin: 0;
                max-height: 80vh;
                overflow-y: auto;
                width: 95%;
                padding: 2rem;
            }
        }
        
        /* Additional fix for very small screens */
        @media (max-width: 480px) {
            .book-reader {
                padding-top: 50px;
            }
            
            .book-header {
                top: 50px;
                padding: 0.8rem;
            }
            
            .back-to-library {
                top: 60px;
                left: 5px;
            }
            
            .back-btn {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .pdf-container {
                height: calc(100vh - 180px);
            }
        }
    </style>
</head>
<body>

    <div class="back-to-library">
        <a href="library.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Library
        </a>
    </div>

    <div class="book-reader">
        <!--<div class="book-header">-->
        <!--    <div class="row align-items-center">-->
        <!--        <div class="col-md-1 text-center">-->
        <!--            <img src="https://sarathicooperative.org/admin/uploads/ebooks/covers/<?php echo htmlspecialchars($book['cover_image']); ?>" -->
        <!--                 alt="<?php echo htmlspecialchars($book['title']); ?>" -->
        <!--                 class="book-cover">-->
        <!--        </div>-->
        <!--        <div class="col-md-10">-->
        <!--            <div class="book-info">-->
        <!--                <h1><?php echo htmlspecialchars($book['title']); ?></h1>-->
        <!--                <div class="book-meta">-->
        <!--                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($book['author']); ?></span>-->
        <!--                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($book['category_name']); ?></span>-->
        <!--                    <?php if ($book['visibility'] === 'members'): ?>-->
        <!--                        <span><i class="fas fa-lock"></i> Members Only</span>-->
        <!--                    <?php endif; ?>-->
        <!--                    <span><i class="fas fa-calendar"></i> <?php echo date('M Y', strtotime($book['created_at'])); ?></span>-->
        <!--                </div>-->
        <!--                <p style="font-size: 0.9rem; margin: 0;"><?php echo htmlspecialchars($book['description']); ?></p>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <?php if (!$file_exists): ?>
            <div class="error-container">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h3>File Not Found</h3>
                <p>The book file is currently unavailable. Please contact the administrator.</p>
            </div>
        <?php elseif ($book['visibility'] === 'members' && $show_member_prompt && !$is_member_verified): ?>
            <div class="blur-overlay"></div>
            <div class="member-prompt">
                <h4><i class="fas fa-lock"></i> Members Only Content</h4>
                <p>This book is available exclusively for Sarathi Cooperative members. Please enter your Member ID to continue reading.</p>
                
                <?php if (isset($member_error)): ?>
                    <div class="alert alert-danger"><?php echo $member_error; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="member-form">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="member_id" placeholder="Enter your Member ID" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-unlock"></i> Verify
                        </button>
                    </div>
                </form>
                
                <p class="mt-3">
                    <small>Not a member yet? <a href="become-a-sarathian.php" class="fw-bold">Become a Sarathian</a></small>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($file_exists && ($book['visibility'] === 'public' || $is_member_verified || !$show_member_prompt)): ?>
            <?php if ($is_pdf): ?>
                <div class="pdf-container">
                    <iframe src="<?php echo $file_path; ?>#toolbar=1&navpanes=1&scrollbar=1&view=FitH" 
                            class="pdf-viewer"
                            frameborder="0">
                        <p>Your browser does not support PDF viewing. 
                           <a href="<?php echo $file_path; ?>" target="_blank">Click here to download the PDF</a>
                        </p>
                    </iframe>
                </div>
            <?php else: ?>
                <!-- For other file types like EPUB, DOCX, etc. -->
                <div class="pdf-container">
                    <div class="loading-container">
                        <div class="loading-spinner"></div>
                        <p>Loading book content...</p>
                        <a href="<?php echo $file_path; ?>" class="btn btn-primary" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Open in New Tab
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        <?php elseif ($book['visibility'] === 'members' && !$is_member_verified): ?>
            <!-- Show preview for members-only books -->
            <div class="pdf-container" style="position: relative;">
                <?php if ($is_pdf): ?>
                    <iframe src="<?php echo $file_path; ?>#toolbar=0&navpanes=0&scrollbar=0&view=FitH&page=1" 
                            class="pdf-viewer"
                            frameborder="0"
                            style="pointer-events: none;">
                    </iframe>
                    <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); 
                                background: rgba(0,0,0,0.8); color: white; padding: 1rem 2rem; 
                                border-radius: 25px; text-align: center;">
                        <i class="fas fa-lock"></i> 
                        <strong>Members Only Content</strong><br>
                        <small>Scroll down or navigate to continue reading</small>
                    </div>
                <?php else: ?>
                    <div class="loading-container">
                        <i class="fas fa-lock fa-3x mb-3 text-warning"></i>
                        <h4>Members Only Content</h4>
                        <p>This book requires membership to access.</p>
                        <button class="btn btn-primary" onclick="window.location.href='?id=<?php echo $book_id; ?>&page=2'">
                            <i class="fas fa-unlock"></i> Verify Membership
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Header hide/show on scroll
        let lastScrollTop = 0;
        let scrollThreshold = 10; // Minimum scroll distance to trigger
        
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Check if we've scrolled enough to trigger the effect
            if (Math.abs(scrollTop - lastScrollTop) > scrollThreshold) {
                const bookHeader = document.querySelector('.book-header');
                
                if (scrollTop > lastScrollTop && scrollTop > 200) {
                    // Scrolling down - hide header
                    bookHeader.classList.add('header-hidden');
                } else {
                    // Scrolling up - show header
                    bookHeader.classList.remove('header-hidden');
                }
                
                lastScrollTop = scrollTop;
            }
        });
        
        // Scroll detection for members-only books
        <?php if ($book['visibility'] === 'members' && !$is_member_verified && !$show_member_prompt): ?>
        let memberScrollThreshold = false;
        window.addEventListener('scroll', function() {
            if (!memberScrollThreshold && (window.scrollY > 300 || document.documentElement.scrollTop > 300)) {
                memberScrollThreshold = true;
                window.location.href = '?id=<?php echo $book_id; ?>&page=2';
            }
        });
        
        // Also trigger on PDF iframe interaction
        setTimeout(function() {
            const iframe = document.querySelector('.pdf-viewer');
            if (iframe) {
                iframe.addEventListener('load', function() {
                    try {
                        iframe.contentWindow.addEventListener('scroll', function() {
                            if (!memberScrollThreshold) {
                                memberScrollThreshold = true;
                                window.location.href = '?id=<?php echo $book_id; ?>&page=2';
                            }
                        });
                    } catch(e) {
                        // Cross-origin restrictions, fallback to time-based trigger
                        setTimeout(function() {
                            if (!memberScrollThreshold) {
                                memberScrollThreshold = true;
                                window.location.href = '?id=<?php echo $book_id; ?>&page=2';
                            }
                        }, 10000); // 10 seconds
                    }
                });
            }
        }, 1000);
        <?php endif; ?>
        
        // Loading state management
        window.addEventListener('load', function() {
            const loadingContainers = document.querySelectorAll('.loading-container');
            loadingContainers.forEach(container => {
                if (container.querySelector('.loading-spinner')) {
                    // Hide loading after 3 seconds if still showing
                    setTimeout(() => {
                        const spinner = container.querySelector('.loading-spinner');
                        if (spinner) {
                            spinner.style.display = 'none';
                        }
                    }, 3000);
                }
            });
        });
    </script>
</body>
</html>