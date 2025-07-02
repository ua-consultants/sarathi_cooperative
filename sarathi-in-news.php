<?php
// Database connection
$servername = "localhost";
$username = "u828878874_sarathi_new";
$password = "#Sarathi@2025";
$dbname = "u828878874_sarathi_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Updated query to fetch announcements for news section (instead of news table)
$news_query = "SELECT id, title, content, media_type, media_url, created_at, end_date 
               FROM announcements 
               WHERE status = 'active' 
               AND (end_date IS NULL OR end_date >= CURDATE())
               ORDER BY created_at DESC 
               LIMIT 10";
$news_result = mysqli_query($conn, $news_query);

// Fetch announcements for sidebar (keep existing query but optimize)
$announcements_query = "SELECT id, title, content as description, created_at, end_date 
                       FROM announcements 
                       WHERE status = 'active' 
                       AND (end_date IS NULL OR end_date >= CURDATE())
                       ORDER BY created_at DESC 
                       LIMIT 10";
$announcements_result = mysqli_query($conn, $announcements_query);


// Fetch announcements
$announcements_query = "SELECT * FROM announcements WHERE status = 'active' ORDER BY created_at DESC LIMIT 10";
$announcements_result = mysqli_query($conn, $announcements_query);

// Fetch latest blogs for rotation - Make sure we get featured_image
$blogs_query = "SELECT b.*, bc.name as category_name 
                FROM blogs b 
                LEFT JOIN blog_categories bc ON b.category_id = bc.id 
                WHERE b.status = 'published' 
                ORDER BY b.created_at DESC LIMIT 5";
$blogs_result = mysqli_query($conn, $blogs_query);

// Store blogs in array immediately after query
$blogs = [];
if($blogs_result && mysqli_num_rows($blogs_result) > 0) {
    while($blog = mysqli_fetch_assoc($blogs_result)) {
        $blogs[] = $blog;
    }
}

// Fetch testimonials for rotation
$testimonials_query = "SELECT * FROM testimonials WHERE status = 'active' ORDER BY created_at DESC LIMIT 5";
$testimonials_result = mysqli_query($conn, $testimonials_query);

// Store testimonials in array
$testimonials = [];
if($testimonials_result && mysqli_num_rows($testimonials_result) > 0) {
    while($testimonial = mysqli_fetch_assoc($testimonials_result)) {
        $testimonials[] = $testimonial;
    }
}

// Fetch ebooks for rotation
$ebooks_query = "SELECT * FROM ebooks WHERE status = 1 ORDER BY created_at DESC LIMIT 5";
$ebooks_result = mysqli_query($conn, $ebooks_query);

// Store ebooks in array
$ebooks = [];
if($ebooks_result && mysqli_num_rows($ebooks_result) > 0) {
    while($ebook = mysqli_fetch_assoc($ebooks_result)) {
        $ebooks[] = $ebook;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarathi in News - Sarathi Cooperative</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            line-height: 1.6;
            padding-top: 95px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Main Content Section */
        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 50px;
        }

        /* Memories We Make Section */
        .memories-section {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .memories-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .memories-title h2 {
            font-size: 36px;
            color: #2c3e50;
        }

        .memories-title .we-make {
            font-family: 'Dancing Script', cursive;
            color: #3498db;
        }

        .memories-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .memory-block {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            min-height: 280px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .memory-block:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        .memory-block .icon {
            font-size: 40px;
            color: #3498db;
            margin-bottom: 15px;
            text-align: center;
        }

        /* Image styling for memory blocks */
        .memory-block .image-container {
            width: 120px;
            height: 160px;
            margin: 0 auto 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        .memory-block .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s ease;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .memory-block .image-container img.active {
            opacity: 1;
        }

        .memory-block:hover .image-container img.active {
            transform: scale(1.05);
        }

        .memory-block h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            text-align: center;
        }

        /* Testimonials block - keep original content */
        .memory-block.testimonials {
            min-height: 250px;
        }

        .memory-block.testimonials .memory-content {
            position: relative;
            height: 120px;
            overflow: hidden;
            text-align: left;
        }

        .memory-block.testimonials .content-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .memory-block.testimonials .content-slide.active {
            opacity: 1;
        }

        .memory-block.testimonials .content-slide h4 {
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .memory-block.testimonials .content-slide p {
            color: #666;
            font-size: 12px;
            line-height: 1.4;
        }

        .memory-block.testimonials .content-slide .meta {
            color: #3498db;
            font-size: 10px;
            font-weight: 500;
            margin-top: 8px;
        }

        /* Simple content for image blocks */
        .memory-block .simple-content {
            position: relative;
            height: 60px;
            overflow: hidden;
        }

        .memory-block .simple-content .content-title {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            transition: opacity 0.5s ease;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
            text-align: center;
        }

        .memory-block .simple-content .content-title.active {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .memories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 968px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .memories-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 10px;
            }
            
            .memories-grid {
                grid-template-columns: 1fr;
            }
            
            .news-section,
            .calendar-section,
            .announcements-section,
            .memory-block {
                padding: 15px;
            }
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
            }
            to {
                opacity: 1;
                max-height: 200px;
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .announcement-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .announcement-image,
            .announcement-video {
                max-height: 200px;
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

    <div class="container">
        <!-- Main Content Section -->
        <div class="container-fluid announcements py-2">
    <div class="container py-5">
        <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px; margin-top: -25px">
            <h2 class="heading_text">Sarathi News</h2>
            <p class="mb-0">Stay updated with our latest news, events, and important information</p>
        </div>
        
        <div class="row g-5">
            <!-- Announcements Grid Display (Left Side) -->
            <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="announcements-grid-container bg-light rounded p-4" style="min-height: 630px;">
                    
                    <!-- Grid Header -->
                    <div class="announcements-header d-flex justify-content-between align-items-center mb-4">
                        <!--<div class="grid-info">-->
                        <!--    <h5 class="mb-0 text-primary">Recent Announcements</h5>-->
                        <!--    <small class="text-muted">Click on any card to view details</small>-->
                        <!--</div>-->
                        <div class="announcements-count">
                            <span class="badge bg-primary" id="announcementsCount"></span>
                            <span class="text-muted"></span>
                        </div>
                    </div>

                    <!-- Announcements Grid -->
                    <div class="announcements-grid" id="announcementsGrid">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Calendar and Subscribe (Right Side) -->
            <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.3s">
                <!-- Calendar -->
                <div class="calendar-container bg-white rounded shadow-sm p-4 mb-4">
                    <div id="announcement-calendar"></div>
                </div>
                
                <!-- Subscribe Section -->
                <!--<div class="subscribe-container bg-white rounded shadow-sm p-4">-->
                <!--    <div class="text-center mb-3">-->
                <!--        <i class="fas fa-bell text-primary fs-2 mb-2"></i>-->
                <!--        <h5 style="color: #2800bb;">Stay Updated</h5>-->
                <!--        <p class="text-muted mb-0">Subscribe to receive email notifications about new announcements</p>-->
                <!--    </div>-->
                    
                <!--    <form id="subscribeForm" class="mt-3">-->
                <!--        <div class="input-group mb-3">-->
                <!--            <input type="email" class="form-control" id="subscriberEmail" placeholder="Enter your email" required>-->
                <!--            <button class="btn btn-primary" type="submit" id="subscribeBtn">-->
                <!--                <i class="fas fa-paper-plane me-1"></i>Subscribe-->
                <!--            </button>-->
                <!--        </div>-->
                <!--    </form>-->
                    
                <!--    <div id="subscribeMessage" class="mt-3" style="display: none;"></div>-->
                <!--</div>-->
            </div>
        </div>
    </div>
</div>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2800bb;">
                <h5 class="modal-title text-white" id="announcementModalLabel">Announcement Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Date Details Modal -->
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #2800bb, #4a20d6); padding: 0.9rem;">
                <h5 class="modal-title text-white fw-bold" id="calendarEventModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Events Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="calendarModalContent">
                <!-- Content will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Interest Registration Modal -->
<div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="interestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="modal-title text-white fw-bold" id="interestModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Register Your Interest
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-handshake text-success fs-1 mb-3"></i>
                    <h4 class="text-success mb-2">Join Our Next Meeting!</h4>
                    <p class="text-muted">Please provide your contact details to receive seat confirmation for the upcoming meeting.</p>
                </div>
                
                <form id="interestForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="interestEmail" class="form-label fw-semibold">
                                <i class="fas fa-envelope text-primary me-2"></i>Email Address *
                            </label>
                            <input type="email" class="form-control form-control-lg" id="interestEmail" required 
                                   placeholder="your.email@example.com">
                        </div>
                        <div class="col-md-6">
                            <label for="interestPhone" class="form-label fw-semibold">
                                <i class="fas fa-phone text-primary me-2"></i>Phone Number *
                            </label>
                            <input type="tel" class="form-control form-control-lg" id="interestPhone" required 
                                   placeholder="+91 9876543210">
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="registerInterestBtn">
                            <i class="fas fa-paper-plane me-2"></i>Register Interest
                        </button>
                    </div>
                </form>
                
                <div id="interestMessage" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
.announcements-grid-container {
    border: 1px solid #e9ecef;
}

.announcements-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    min-height: 480px;
}

.announcement-card {
    background: white;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 180px;
    position: relative;
    overflow: hidden;
}

.announcement-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2800bb, #4a20d6);
}

.announcement-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(40, 0, 187, 0.15);
    border-color: #2800bb;
}

.announcement-card:hover .card-title {
    color: #2800bb;
}
.announcements-count {
    display: none;
}

.card-title {
    color: #333;
    font-weight: 600;
    font-size: 1rem;
    line-height: 1.4;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
    flex-grow: 1;
}

.card-date {
    display: flex;
    align-items: center;
    color: #666;
    font-size: 0.9rem;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.card-date i {
    margin-right: 8px;
    color: #2800bb;
    font-size: 0.85rem;
}

.no-announcements {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    text-align: center;
    color: #999;
}

.no-announcements i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

.announcements-header h5 {
    color: #2800bb !important;
}

/* Calendar styles */
.calendar-container {
    border: 1px solid #e9ecef;
}

.subscribe-container {
    margin-top: 45px;
    border: 1px solid #e9ecef;
    background-image: url('img/logo.png');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    position: relative;
}

.subscribe-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: inherit;
}

.subscribe-container > * {
    position: relative;
    z-index: 1;
}

.calendar-day {
    padding-top: calc(100% * 1 / 7); /* Square cells based on available width */
    position: relative;
    font-size: 1.1rem;
    color: #555;
    cursor: default;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.has-event {
    background-color: pink;
    color: white;
    font-weight: bold;
}

.calendar-day.has-event:hover {
    background-color: #1e0088;
}

.calendar-day.has-fixed-event {
    background-color: #28a745;
    color: white;
    font-weight: bold;
}

.calendar-day.has-fixed-event:hover {
    background-color: #218838;
}

.calendar-day.has-multiple-events {
    background: linear-gradient(45deg, #2800bb 50%, #28a745 50%);
    color: white;
    font-weight: bold;
}

.calendar-day.has-multiple-events:hover {
    background: linear-gradient(45deg, #1e0088 50%, #218838 50%);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.calendar-nav-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #2800bb;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.calendar-nav-btn:hover {
    background-color: #f8f9fa;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    text-align: center;
}

.calendar-weekday {
    font-weight: bold;
    color: #666;
    padding: 8px 0;
    font-size: 0.9rem;
}

.event-item {
    background: #f8f9fa;
    border-left: 4px solid #2800bb;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 0 8px 8px 0;
}

.event-item.fixed-event {
    border-left-color: #28a745;
}

.event-item h6 {
    color: #2800bb;
    margin-bottom: 8px;
}

.event-item.fixed-event h6 {
    color: #28a745;
}

.event-badge {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    margin-right: 8px;
}

.announcement-badge {
    background-color: #2800bb;
    color: white;
}

.fixed-event-badge {
    background-color: #28a745;
    color: white;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .announcements-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 991px) {
    .announcements-grid-container {
        min-height: 500px;
        margin-bottom: 30px;
    }
    
    .announcements-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

@media (max-width: 768px) {
    .announcements-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .announcement-card {
        padding: 10px;
        min-height: 150px;
    }
    
    .card-title {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .announcement-card {
        padding: 10px;
    }
    
    .card-title {
        font-size: 0.95rem;
    }
}
.event-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: none;
}

.date-section {
    background: linear-gradient(135deg, #2800bb, #4a20d6);
    color: white;
    padding: 1rem;
    text-align: center;
    position: relative;
}

.date-section::after {
    content: '';
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 15px solid #4a20d6;
    border-top: 15px solid transparent;
    border-bottom: 15px solid transparent;
}

.date-month {
    font-size: 1.2rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.date-day {
    font-size: 1.9rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.date-year {
    font-size: 1rem;
    font-weight: 500;
    opacity: 0.8;
}

.content-section {
    padding: 0.7rem;
    flex: 1;
}

.event-title {
    color: #2800bb;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.event-content {
    color: #555;
    line-height: 1.;
    margin-bottom: 1.5rem;
}

.meeting-details {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.1rem;
    margin: 1.1rem 0;
    border-left: 4px solid #28a745;
}

.meeting-detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.8rem;
}

.meeting-detail-item:last-child {
    margin-bottom: 0;
}

.meeting-detail-item i {
    width: 20px;
    color: #28a745;
    margin-right: 0.8rem;
}

.cta-button {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    padding: 0.8rem 1.2rem;
    border-radius: 50px;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    color: white;
}

.fixed-event-card {
    border-left: 5px solid #28a745;
}

.announcement-card {
    border-left: 5px solid #2800bb;
}

@media (max-width: 768px) {
    .date-section::after {
        display: none;
    }
    
    .date-day {
        font-size: 2.5rem;
    }
    
    .content-section {
        padding: 1.5rem;
    }
    
    .event-title {
        font-size: 1.3rem;
    }
}
</style>

<!-- JavaScript -->

<script>
// Fetch announcements from database (using your existing PHP structure)
let announcements = [
    <?php
    // Fetch announcements from database
    $sql = "SELECT id, title, content, media_type, media_url, created_at, end_date FROM announcements WHERE status = 'active' ORDER BY created_at DESC LIMIT 6";
    $result = $conn->query($sql);
    
    $announcements_array = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $announcements_array[] = $row;
        }
    }
    
    // Convert to JavaScript array
    foreach ($announcements_array as $index => $announcement) {
        echo json_encode($announcement);
        if ($index < count($announcements_array) - 1) {
            echo ',';
        }
    }
    ?>
];

// Initialize announcements grid
function initializeAnnouncementsGrid() {
    const gridContainer = document.getElementById('announcementsGrid');
    const countElement = document.getElementById('announcementsCount');
    
    countElement.textContent = announcements.length;
    
    if (announcements.length === 0) {
        gridContainer.innerHTML = `
            <div class="no-announcements">
                <i class="fas fa-info-circle"></i>
                <h5>No announcements available</h5>
                <p>Check back later for updates!</p>
            </div>
        `;
        return;
    }
    
    createAnnouncementCards();
}

function createAnnouncementCards() {
    const gridContainer = document.getElementById('announcementsGrid');
    gridContainer.innerHTML = '';
    
    announcements.forEach((announcement, index) => {
        const card = document.createElement('div');
        card.className = 'announcement-card';
        card.setAttribute('data-id', announcement.id);
        card.setAttribute('data-bs-toggle', 'modal');
        card.setAttribute('data-bs-target', '#announcementModal');
        
        const endDate = new Date(announcement.end_date);
        const formattedDate = endDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        
        card.innerHTML = `
            <div class="card-title">${announcement.title}</div>
            <div class="card-date">
                <i class="fas fa-calendar-alt"></i>
                ${formattedDate}
            </div>
        `;
        
        // Add click event listener
        card.addEventListener('click', function() {
            showAnnouncementModal(announcement.id);
        });
        
        gridContainer.appendChild(card);
    });
}

// Show announcement modal with full details
function showAnnouncementModal(announcementId) {
    fetch('get_announcement.php?id=' + announcementId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const announcement = data.announcement;
            let modalContent = `
                <div class="announcement-detail">
                    <div class="mb-3">
                        <span class="badge bg-primary">${new Date(announcement.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</span>
                    </div>
            `;
            
            if (announcement.media_type === 'image' && announcement.media_url) {
                modalContent += `
                    <div class="mb-4">
                        <img src="${announcement.media_url}" class="img-fluid rounded w-100" style="max-height: 380px; object-fit: cover;" alt="Announcement Image">
                    </div>
                `;
            }
            
            modalContent += `
                    <h4 class="mb-1.5" style="color: #2800bb;">${announcement.title}</h4>
                    <div class="announcement-full-content">
                        <p style="line-height: 1.6; white-space: pre-wrap;">${announcement.content}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('announcementModalLabel').textContent = announcement.title;
        }
    })
    .catch(error => {
        document.getElementById('modalContent').innerHTML = '<div class="alert alert-danger">Error loading announcement details.</div>';
    });
}

// Calendar functionality with enhanced features
let currentDate = new Date();
let announcementDates = [];

// Fetch announcement dates from database (using your existing PHP structure)
<?php
$date_sql = "SELECT DISTINCT end_date as announcement_date FROM announcements WHERE status = 'active'";
$date_result = $conn->query($date_sql);
$dates = [];
if ($date_result->num_rows > 0) {
    while($date_row = $date_result->fetch_assoc()) {
        $dates[] = $date_row['announcement_date'];
    }
}
echo "announcementDates = " . json_encode($dates) . ";";
?>

// Function to get the nth weekday of a month
function getNthWeekdayOfMonth(year, month, weekday, n) {
    const firstDay = new Date(year, month, 1);
    const firstWeekday = firstDay.getDay();
    let daysToAdd = (weekday - firstWeekday + 7) % 7;
    daysToAdd += (n - 1) * 7;
    return new Date(year, month, 1 + daysToAdd);
}

// Function to get the last weekday of a month
function getLastWeekdayOfMonth(year, month, weekday) {
    const lastDay = new Date(year, month + 1, 0);
    const lastWeekday = lastDay.getDay();
    let daysToSubtract = (lastWeekday - weekday + 7) % 7;
    return new Date(year, month + 1, 0 - daysToSubtract);
}

// Function to check if it's the last month of a quarter
function isLastMonthOfQuarter(month) {
    return month === 2 || month === 5 || month === 8 || month === 11; // March, June, September, December
}

// Function to get fixed events for a specific date
function getFixedEventsForDate(date) {
    const events = [];
    const year = date.getFullYear();
    const month = date.getMonth(); // 0-based (0 = January, ..., 5 = June)
    const day = date.getDate();
    const dayOfWeek = date.getDay();

    // Special case: June 2025
    if (year === 2025 && month === 5) { // month is 0-based, so 5 = June
        if (day === 22 && (dayOfWeek === 6 || dayOfWeek === 0)) {
            // Add BOD Meeting
            events.push({
                title: 'Board of Directors Meeting',
                type: 'BOD Meeting',
                description: 'Monthly Board of Directors meeting for cooperative governance and decision making.'
            });

            // Add Online Webinar
            events.push({
                title: 'Monthly Online Webinar',
                type: 'Webinar',
                description: 'Members are encouraged to engage and Participate in Sarathi Cooperative Progress as accomplished in past quarter, as well as discuss-plan the activities of next quarter.'
            });
        }
    } else {
        // Regular logic for other months/years

        // 2nd Saturday of every month - BOD Meet
        const secondSaturday = getNthWeekdayOfMonth(year, month, 6, 2);
        if (day === secondSaturday.getDate() && dayOfWeek === 6) {
            events.push({
                title: 'Board of Directors Meeting',
                type: 'BOD Meeting',
                description: 'Monthly Board of Directors meeting for cooperative governance and decision making.'
            });
        }

        // Last Saturday of every month - Online Webinar
        const lastSaturday = getLastWeekdayOfMonth(year, month, 6);
        if (day === lastSaturday.getDate() && dayOfWeek === 6) {
            events.push({
                title: 'Monthly Online Webinar',
                type: 'Webinar',
                description: 'Members are encouraged to engage and Participate in Sarathi Cooperative Progress as accomplished in past quarter, as well as discuss-plan the activities of next quarter.'
            });
        }
    }

    // Quarterly General Body Meeting (last month of each quarter)
    if (!isLastMonthOfQuarter(month) || (year === 2025 && month === 5)) {
        // Skip general body meet in June 2025 if needed, or apply normally
    }

    if (isLastMonthOfQuarter(month)) {
        const secondSunday = getNthWeekdayOfMonth(year, month, 0, 2);
        if (day === secondSunday.getDate() && dayOfWeek === 0) {
            events.push({
                title: 'Quarterly General Body Meeting',
                type: 'General Body Meeting',
                description: 'Quarterly General Body meeting for all members to discuss cooperative affairs and progress.'
            });
        }
    }

    return events;
}
// Function to get announcements for a specific date
function getAnnouncementsForDate(dateString) {
    return announcements.filter(announcement => {
        const endDate = new Date(announcement.end_date);
        const checkDate = new Date(dateString);
        return endDate.toDateString() === checkDate.toDateString();
    });
}

function renderCalendar() {
    const calendar = document.getElementById('announcement-calendar');
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    
    let calendarHTML = `
        <div class="calendar-header">
            <button class="calendar-nav-btn" onclick="changeMonth(-1)">‹</button>
            <h6 class="mb-0">${monthNames[month]} ${year}</h6>
            <button class="calendar-nav-btn" onclick="changeMonth(1)">›</button>
        </div>
        <div class="calendar-grid">
            <div class="calendar-weekday">Sun</div>
            <div class="calendar-weekday">Mon</div>
            <div class="calendar-weekday">Tue</div>
            <div class="calendar-weekday">Wed</div>
            <div class="calendar-weekday">Thu</div>
            <div class="calendar-weekday">Fri</div>
            <div class="calendar-weekday">Sat</div>
    `;
    
    for (let i = 0; i < 35; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const isCurrentMonth = date.getMonth() === month;
        const dateString = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

        // Check for announcements - fix the date comparison
        const hasAnnouncements = announcementDates.includes(dateString);
        
        // Check for fixed events
        const fixedEvents = getFixedEventsForDate(date);
        const hasFixedEvents = fixedEvents.length > 0;
        
        let classes = 'calendar-day';
        let title = '';
        
        if (!isCurrentMonth) {
            classes += ' text-muted';
        }
        
        if (hasAnnouncements && hasFixedEvents) {
            classes += ' has-multiple-events';
            title = 'Has announcements and scheduled events';
        } else if (hasAnnouncements) {
            classes += ' has-event';
            title = 'Has announcements';
        } else if (hasFixedEvents) {
            classes += ' has-fixed-event';
            title = fixedEvents.map(e => e.title).join(', ');
        }
        
        calendarHTML += `<div class="${classes}" 
                               title="${title}" 
                               onclick="showDateEvents('${dateString}')"
                               style="cursor: ${(hasAnnouncements || hasFixedEvents) ? 'pointer' : 'default'}">
                           ${date.getDate()}
                         </div>`;
    }
    
    calendarHTML += '</div>';
    calendar.innerHTML = calendarHTML;
}

// Enhanced showDateEvents function
function showDateEvents(dateString) {
    const date = new Date(dateString);
    const announcements = getAnnouncementsForDate(dateString);
    const fixedEvents = getFixedEventsForDate(date);
    
    if (announcements.length === 0 && fixedEvents.length === 0) {
        return; // No events to show
    }
    
    const modalTitle = document.getElementById('calendarEventModalLabel');
    const modalContent = document.getElementById('calendarModalContent');
    
    modalTitle.innerHTML = `<i class="fas fa-calendar-alt me-2"></i>Events on ${date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })}`;
    
    let contentHTML = '<div class="p-4">';
    
    // Show fixed events first
    fixedEvents.forEach(event => {
        const month = date.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        const day = date.getDate();
        const year = date.getFullYear();
        
        // Check if it's June for venue information
        const isJune = date.getMonth() === 5; // June is month 5 (0-indexed)
        const venue = isJune ? 'Eros' : 'TBA';
        const time = '8:00 AM - 11:00 AM';
        
        contentHTML += `
            <div class="event-card fixed-event-card">
                <div class="row g-0">
                    <div class="col-md-3">
                        <div class="date-section">
                            <div class="date-month">${month}</div>
                            <div class="date-day">${day}</div>
                            <div class="date-year">${year}</div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="content-section">
                            <h3 class="event-title">${event.title}</h3>
                            <p class="event-content">${event.description}</p>
                            
                            <div class="meeting-details">
                                <div class="meeting-detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><strong>Time:</strong> ${time}</span>
                                </div>
                                <div class="meeting-detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><strong>Venue:</strong> ${venue}</span>
                                </div>
                                <div class="meeting-detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><strong>Type:</strong> ${event.type}</span>
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button class="btn cta-button" onclick="showInterestModal('${event.title}', '${dateString}')">
                                    <i class="fas fa-hand-point-up me-2"></i>Interested?
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Show announcements
    announcements.forEach(announcement => {
        const month = date.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        const day = date.getDate();
        const year = date.getFullYear();
        
        const mediaHTML = announcement.media_type === 'image' && announcement.media_url 
            ? `<div class="mb-3">
                 <img src="${announcement.media_url}" class="img-fluid rounded" style="max-height: 350px; width: 100%; object-fit: cover;" alt="Announcement Image">
               </div>` 
            : '';
            
        contentHTML += `
            <div class="event-card announcement-card">
                <div class="row g-0">
                    <div class="col-md-3">
                        <div class="date-section" style="background: linear-gradient(135deg, #2800bb, #4a20d6);">
                            <div class="date-month">${month}</div>
                            <div class="date-day">${day}</div>
                            <div class="date-year">${year}</div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="content-section">
                            <h3 class="event-title">${announcement.title}</h3>
                            ${mediaHTML}
                            <p class="event-content">${announcement.content}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    contentHTML += '</div>';
    modalContent.innerHTML = contentHTML;
    
    // Show the modal
    const modalElement = document.getElementById('calendarEventModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Function to show interest registration modal
function showInterestModal(eventTitle, eventDate) {
    document.getElementById('interestModalLabel').innerHTML = `
        <i class="fas fa-user-plus me-2"></i>Register for ${eventTitle}
    `;
    
    // Hide the calendar modal first
    const calendarModal = bootstrap.Modal.getInstance(document.getElementById('calendarEventModal'));
    if (calendarModal) {
        calendarModal.hide();
    }
    
    // Show interest modal after a short delay
    setTimeout(() => {
        const interestModal = new bootstrap.Modal(document.getElementById('interestModal'));
        interestModal.show();
        
        // Store event details for form submission
        document.getElementById('interestForm').setAttribute('data-event-title', eventTitle);
        document.getElementById('interestForm').setAttribute('data-event-date', eventDate);
    }, 300);
}

// Handle interest form submission
document.getElementById('interestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('interestEmail').value;
    const phone = document.getElementById('interestPhone').value;
    const eventTitle = this.getAttribute('data-event-title');
    const eventDate = this.getAttribute('data-event-date');
    const submitBtn = document.getElementById('registerInterestBtn');
    const messageDiv = document.getElementById('interestMessage');
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('event_title', eventTitle);
    formData.append('event_date', eventDate);
    
    // Send request to server
    fetch('register_interest.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.style.display = 'block';
        if (data.success) {
            messageDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message || 'Registration successful! You will receive a confirmation email shortly.'}
                </div>
            `;
            // Clear form
            document.getElementById('interestEmail').value = '';
            document.getElementById('interestPhone').value = '';
            
            // Close modal after 3 seconds
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('interestModal'));
                modal.hide();
            }, 3000);
        } else {
            messageDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${data.message || 'Registration failed. Please try again.'}
                </div>
            `;
        }
    })
    .catch(error => {
        messageDiv.style.display = 'block';
        messageDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                An error occurred. Please try again later.
            </div>
        `;
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Register Interest';
        
        // Hide message after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    });
});
// Initialize everything on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeAnnouncementsGrid();
    renderCalendar();
});
</script>   

                <!-- Calendar Section -->
                <?php
// Calendar functionality - Add this to your existing PHP section
$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Validate month and year
if ($current_month < 1 || $current_month > 12) {
    $current_month = date('n');
}
if ($current_year < 2020 || $current_year > 2030) {
    $current_year = date('Y');
}

// Fetch announcements for the current month being viewed
$announcements_query = "SELECT *, DATE(end_date) as announcement_date FROM announcements 
                       WHERE status = 'active' 
                       AND MONTH(created_at) = ? 
                       AND YEAR(created_at) = ?
                       ORDER BY created_at DESC";
$announcements_stmt = mysqli_prepare($conn, $announcements_query);
mysqli_stmt_bind_param($announcements_stmt, "ii", $current_month, $current_year);
mysqli_stmt_execute($announcements_stmt);
$announcements_result = mysqli_stmt_get_result($announcements_stmt);

// Store announcements in array and create date mapping
$announcements = [];
$announcement_dates = [];
if($announcements_result && mysqli_num_rows($announcements_result) > 0) {
    while($announcement = mysqli_fetch_assoc($announcements_result)) {
        $announcements[] = $announcement;
        $day = date('j', strtotime($announcement['announcement_date']));
        if (!isset($announcement_dates[$day])) {
            $announcement_dates[$day] = [];
        }
        $announcement_dates[$day][] = $announcement;
    }
}
?>

<!-- Memories We Make Section -->
<div class="memories-section">
    <div class="memories-title">
        <h2>Memories <span class="we-make">We Make</span></h2>
    </div>
    <div class="memories-grid">
        <!-- Achievements Block -->
        
        <!-- Testimonials Block -->
        <div class="memory-block testimonials" onclick="window.location.href='testimonials.php'">
            <h3>Testimonials</h3>
            <div class="memory-content" id="testimonialsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>

        <!-- Library Block -->
        <div class="memory-block library" onclick="window.location.href='library.php'">
            <h3>Library</h3>
            <div class="image-container" id="libraryImages">
                <!-- Images will be loaded here -->
            </div>
            <div class="simple-content" id="libraryContent">
                <!-- Content will be loaded here -->
            </div>
        </div>

        <!-- Blogs Block -->
        <div class="memory-block blogs" onclick="window.location.href='blogs.php'">
            
            <h3>Blogs</h3>
            <div class="image-container" id="blogsImages">
                <!-- Images will be loaded here -->
            </div>
            <div class="simple-content" id="blogsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
/* Memories We Make Section Styles */
.memories-section {
    margin-top: 50px;
    margin-bottom: 50px;
}

.memories-title {
    text-align: center;
    margin-bottom: 40px;
}

.memories-title h2 {
    font-size: 36px;
    color: #2c3e50;
}

.memories-title .we-make {
    font-family: 'Dancing Script', cursive;
    color: #3498db;
}

.memories-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.memory-block {
    background: white;
    border-radius: 15px;
    padding: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 280px;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.memory-block:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

.memory-block .icon {
    font-size: 40px;
    color: #3498db;
    margin-bottom: 15px;
    text-align: center;
}

/* Image styling for memory blocks */
.memory-block .image-container {
    width: 150px;
    height: 220px;
    margin: 0 auto 15px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    position: relative;
}

.memory-block .image-container img {
    width: 100%;
    height: 100%;
    transition: all 0.5s ease;
    position: absolute;
    top: 0;
    left: 0;
    opacity: 0;
}

.memory-block .image-container img.active {
    opacity: 1;
}

.memory-block:hover .image-container img.active {
    transform: scale(1.05);
}

.memory-block h3 {
    color: #2c3e50;
    margin-bottom: 15px;
    font-size: 18px;
    text-align: center;
}

/* Testimonials block - keep original content */
.memory-block.testimonials {
    min-height: 250px;
}

.memory-block.testimonials .memory-content {
    position: relative;
    height: 320px;
    overflow: hidden;
    align-content: center;
    justify-content: center;
    text-align: left;
}

.memory-block.testimonials .content-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    opacity: 0;
    transition: opacity 0.5s ease;
}

.memory-block.testimonials .content-slide.active {
    opacity: 1;
}

.memory-block.testimonials .content-slide h4 {
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.memory-block.testimonials .content-slide p {
    color: #666;
    margin-top: 55px;
    font-size: 12px;
    line-height: 1.4;
}

.memory-block.testimonials .content-slide .meta {
    color: #3498db;
    font-size: 10px;
    font-weight: 500;
    margin-top: 28px;
}

/* Additional CSS for 2 testimonials per slide - ADD TO YOUR EXISTING CSS */

.memory-block.testimonials .testimonials-pair {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%;
    height: 100%;
    justify-content: center;
}

.memory-block.testimonials .testimonial-item {
    padding: 12px 15px;
    /*background: rgba(52, 152, 219, 0.08);*/
    border-radius: 6px;
    /*border-left: 2px solid #3498db;*/
    height: auto;
    min-height: 120px;
}

.memory-block.testimonials .testimonial-item h4 {
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
    font-style: italic;
    line-height: 1.4;
}

.memory-block.testimonials .testimonial-item .meta {
    color: #3498db;
    font-size: 10px;
    font-weight: 500;
    margin-top: 12px;
    text-align: right;
}

/* Responsive design for smaller screens */
@media (max-width: 768px) {
    .memory-block.testimonials .testimonials-pair {
        gap: 12px;
    }
    
    .memory-block.testimonials .testimonial-item {
        min-height: 100px;
        padding: 10px 12px;
    }
}

/* Simple content for image blocks */
.memory-block .simple-content {
    position: relative;
    height: 60px;
    overflow: hidden;
}

.memory-block .simple-content .content-title {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    opacity: 0;
    transition: opacity 0.5s ease;
    color: #2c3e50;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.3;
    text-align: center;
}

.memory-block .simple-content .content-title.active {
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .memories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 968px) {
    .memories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 600px) {
    .memories-grid {
        grid-template-columns: 1fr;
    }
    
    .memory-block {
        padding: 15px;
    }
}
</style>

<script>
// Initialize memory blocks data
let testimonialsData = <?php echo json_encode($testimonials); ?>;
let ebooksData = <?php echo json_encode($ebooks); ?>;
let blogsData = <?php echo json_encode($blogs); ?>;

let currentIndexes = {
    testimonials: 0,
    library: 0,
    blogs: 0
};

// Function to initialize memory blocks
function initializeMemoryBlocks() {
    initializeTestimonials();
    initializeLibrary();
    initializeBlogs();
    
    // Start rotation timers
    setInterval(rotateTestimonials, 5000);
    setInterval(rotateLibrary, 5000);
    setInterval(rotateBlogs, 5000);
}

// Achievements Block Functions
function initializeAchievements() {
    const imagesContainer = document.getElementById('achievementsImages');
    const contentContainer = document.getElementById('achievementsContent');
    
    if (achievementsData.length === 0) {
        imagesContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;"><i class="fas fa-trophy" style="font-size: 30px;"></i></div>';
        contentContainer.innerHTML = '<div class="content-title active">No achievements yet</div>';
        return;
    }
    
    // Add images
    achievementsData.forEach((achievement, index) => {
        const img = document.createElement('img');
        img.src = achievement.image_path || 'img/default-achievement.jpg';
        img.alt = achievement.title;
        img.className = index === 0 ? 'active' : '';
        imagesContainer.appendChild(img);
    });
    
    // Add content
    achievementsData.forEach((achievement, index) => {
        const titleDiv = document.createElement('div');
        titleDiv.className = index === 0 ? 'content-title active' : 'content-title';
        titleDiv.textContent = achievement.title;
        contentContainer.appendChild(titleDiv);
    });
}

function rotateAchievements() {
    if (achievementsData.length <= 1) return;
    
    const images = document.querySelectorAll('#achievementsImages img');
    const titles = document.querySelectorAll('#achievementsContent .content-title');
    
    // Remove active class from current
    images[currentIndexes.achievements].classList.remove('active');
    titles[currentIndexes.achievements].classList.remove('active');
    
    // Move to next
    currentIndexes.achievements = (currentIndexes.achievements + 1) % achievementsData.length;
    
    // Add active class to new
    images[currentIndexes.achievements].classList.add('active');
    titles[currentIndexes.achievements].classList.add('active');
}

// Testimonials Block Functions
function initializeTestimonials() {
    const contentContainer = document.getElementById('testimonialsContent');
    
    if (testimonialsData.length === 0) {
        contentContainer.innerHTML = '<div class="content-slide active"><h4>No testimonials yet</h4><p>Be the first to share your experience!</p></div>';
        return;
    }
    
    // Clear existing content
    contentContainer.innerHTML = '';
    
    // Group testimonials in pairs (2 per slide)
    for (let i = 0; i < testimonialsData.length; i += 2) {
        const slideDiv = document.createElement('div');
        slideDiv.className = i === 0 ? 'content-slide active' : 'content-slide';
        
        // Create testimonials container for this slide
        let slideContent = '<div class="testimonials-pair">';
        
        // Add first testimonial
        const testimonial1 = testimonialsData[i];
        const description1 = testimonial1.description.length > 80 
            ? testimonial1.description.substring(0, 80) + '...' 
            : testimonial1.description;
        
        slideContent += `
            <div class="testimonial-item">
                <h4>"${description1}"</h4>
                <div class="meta">- ${testimonial1.name}, ${testimonial1.designation}<br>${testimonial1.company_name}</div>
            </div>
        `;
        
        // Add second testimonial if it exists
        if (i + 1 < testimonialsData.length) {
            const testimonial2 = testimonialsData[i + 1];
            const description2 = testimonial2.description.length > 80 
                ? testimonial2.description.substring(0, 80) + '...' 
                : testimonial2.description;
            
            slideContent += `
                <div class="testimonial-item">
                    <h4>"${description2}"</h4>
                    <div class="meta">- ${testimonial2.name}, ${testimonial2.designation}<br>${testimonial2.company_name}</div>
                </div>
            `;
        }
        
        slideContent += '</div>';
        slideDiv.innerHTML = slideContent;
        contentContainer.appendChild(slideDiv);
    }
}

function rotateTestimonials() {
    const slides = document.querySelectorAll('#testimonialsContent .content-slide');
    
    if (slides.length <= 1) return;
    
    // Remove active class from current
    slides[currentIndexes.testimonials].classList.remove('active');
    
    // Move to next slide
    currentIndexes.testimonials = (currentIndexes.testimonials + 1) % slides.length;
    
    // Add active class to new
    slides[currentIndexes.testimonials].classList.add('active');
}

// Library Block Functions
function initializeLibrary() {
    const imagesContainer = document.getElementById('libraryImages');
    const contentContainer = document.getElementById('libraryContent');
    
    if (ebooksData.length === 0) {
        imagesContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;"><i class="fas fa-book" style="font-size: 30px;"></i></div>';
        contentContainer.innerHTML = '<div class="content-title active">No books available</div>';
        return;
    }
    
    // Add images
    ebooksData.forEach((ebook, index) => {
        const img = document.createElement('img');
        // Fix: Construct the correct path for cover images
        if (ebook.cover_image) {
            img.src = 'https://sarathicooperative.org/admin/uploads/ebooks/covers/' + ebook.cover_image;
        } else {
            img.src = 'img/default-book.jpg';
        }
        img.alt = ebook.title;
        img.className = index === 0 ? 'active' : '';
        imagesContainer.appendChild(img);
    });
    
    // Add content
    ebooksData.forEach((ebook, index) => {
        const titleDiv = document.createElement('div');
        titleDiv.className = index === 0 ? 'content-title active' : 'content-title';
        titleDiv.textContent = ebook.title;
        contentContainer.appendChild(titleDiv);
    });
}

function rotateLibrary() {
    if (ebooksData.length <= 1) return;
    
    const images = document.querySelectorAll('#libraryImages img');
    const titles = document.querySelectorAll('#libraryContent .content-title');
    
    // Remove active class from current
    images[currentIndexes.library].classList.remove('active');
    titles[currentIndexes.library].classList.remove('active');
    
    // Move to next
    currentIndexes.library = (currentIndexes.library + 1) % ebooksData.length;
    
    // Add active class to new
    images[currentIndexes.library].classList.add('active');
    titles[currentIndexes.library].classList.add('active');
}

// Blogs Block Functions
function initializeBlogs() {
    const imagesContainer = document.getElementById('blogsImages');
    const contentContainer = document.getElementById('blogsContent');
    
    if (blogsData.length === 0) {
        imagesContainer.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;"><i class="fas fa-pen-fancy" style="font-size: 30px;"></i></div>';
        contentContainer.innerHTML = '<div class="content-title active">No blogs available</div>';
        return;
    }
    
    // Add images
    blogsData.forEach((blog, index) => {
        const img = document.createElement('img');
        img.src = blog.featured_image || 'img/default-blog.jpg';
        img.alt = blog.title;
        img.className = index === 0 ? 'active' : '';
        imagesContainer.appendChild(img);
    });
    
    // Add content
    blogsData.forEach((blog, index) => {
        const titleDiv = document.createElement('div');
        titleDiv.className = index === 0 ? 'content-title active' : 'content-title';
        titleDiv.textContent = blog.title;
        contentContainer.appendChild(titleDiv);
    });
}

function rotateBlogs() {
    if (blogsData.length <= 1) return;
    
    const images = document.querySelectorAll('#blogsImages img');
    const titles = document.querySelectorAll('#blogsContent .content-title');
    
    // Remove active class from current
    images[currentIndexes.blogs].classList.remove('active');
    titles[currentIndexes.blogs].classList.remove('active');
    
    // Move to next
    currentIndexes.blogs = (currentIndexes.blogs + 1) % blogsData.length;
    
    // Add active class to new
    images[currentIndexes.blogs].classList.add('active');
    titles[currentIndexes.blogs].classList.add('active');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeMemoryBlocks();
});
</script>

    <?php include 'footer.php'; ?>
</body>
</html>