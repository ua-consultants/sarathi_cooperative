<?php
// Database connection
$host = 'localhost';
$dbname = 'u828878874_sarathi_db';
$username = 'u828878874_sarathi_new';
$password = '#Sarathi@2025';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch achievements from database (you should create an achievements table)
// For now, using ebooks table as placeholder - replace with proper achievements table
try {
    $stmt = $pdo->prepare("SELECT * FROM ebooks WHERE status = '1' ORDER BY created_at DESC");
    $stmt->execute();
    $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $achievements = [];
    $error_message = "Error fetching achievements: " . $e->getMessage();
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Sarathi Cooperative</title>
    <link rel="icon" href="img/logo-favi-icon.png">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .achievements-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .page-header {
            margin-top: 65px;
            text-align: center;
            margin-bottom: 60px;
            color: #2c3e50;
        }

        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .page-header p {
            font-size: 1.2rem;
            opacity: 0.8;
            color: #7f8c8d;
        }

        .timeline {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.6rem 0;
        }

        /* Timeline center line */
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #4facfe, #00f2fe);
            transform: translateX(-50%);
            border-radius: 2px;
            box-shadow: 0 0 10px rgba(79, 172, 254, 0.5);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 4rem;
            width: 100%;
            display: flex;
            align-items: center;
        }

        /* Left side items (odd) */
        .timeline-item:nth-child(odd) {
            justify-content: flex-end;
        }

        .timeline-item:nth-child(odd) .timeline-content {
            margin-right: calc(50% + 30px);
            text-align: left;
        }

        /* Right side items (even) */
        .timeline-item:nth-child(even) {
            justify-content: flex-start;
        }

        .timeline-item:nth-child(even) .timeline-content {
            margin-left: calc(50% + 30px);
            text-align: left;
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.7rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            max-width: 550px;
            min-width: 450px;
            width: 100%;
            cursor: pointer;
        }

        .achievement-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .achievement-link:hover {
            text-decoration: none;
            color: inherit;
        }

        .timeline-content:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        /* Timeline dots */
        .timeline-dot {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 0 20px rgba(79, 172, 254, 0.6);
            z-index: 2;
            border: 4px solid white;
        }

        .achievement-title {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            line-height: 1;
            word-wrap: break-word;
        }

        .title-divider {
            width: 100%;
            height: 2px;
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            margin: 15px 0;
            border-radius: 1px;
            opacity: 0.3;
        }

        .achievement-date {
            color: #7f8c8d;
            font-size: 1rem;
            margin-bottom: 20px;
            font-weight: 600;
            display: block;
        }

        .achievement-description {
            color: #34495e;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .achievement-category {
            display: inline-block;
            background: linear-gradient(45deg, #4facfe, #00f2fe);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 15px;
        }

        .no-achievements {
            text-align: center;
            color: #2c3e50;
            font-size: 1.2rem;
            padding: 50px;
            background: rgba(255,255,255,0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .error-message {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .timeline-content {
                min-width: 400px;
                max-width: 500px;
            }
            
            .achievements-container {
                max-width: 1200px;
            }
        }

        @media (max-width: 768px) {
            .timeline::before {
                left: 30px;
                transform: none;
            }

            .timeline-item {
                justify-content: flex-start !important;
            }

            .timeline-item:nth-child(odd) .timeline-content,
            .timeline-item:nth-child(even) .timeline-content {
                margin-left: 60px !important;
                margin-right: 0 !important;
                text-align: left !important;
                max-width: calc(100% - 80px);
                min-width: unset;
                width: calc(100% - 80px);
            }

            .timeline-dot {
                left: 30px;
                transform: translateY(-50%);
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .achievements-container {
                padding: 1rem;
            }

            .achievement-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .timeline-content {
                padding: 2rem;
            }

            .achievement-title {
                font-size: 1.4rem;
            }
        }

        /* Animation */
        .timeline-item {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
        "Sarathi Research and Marketing Services",
        "Sarathi Research and Consulting Services"
      ],
      "url": "https://www.sarathicooperative.com"
    }
    </script>
</head>
<body>
    <div class="achievements-container">
        <div class="page-header">
            <h1>Our Achievements</h1>
            <p>Milestones in our journey of excellence and innovation</p>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($achievements)): ?>
            <div class="timeline">
                <?php foreach ($achievements as $index => $achievement): ?>
                    <div class="timeline-item" style="animation-delay: <?php echo ($index * 0.2); ?>s;">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <a href="library.php" class="achievement-link">
                                <h3 class="achievement-title">
                                    <?php echo htmlspecialchars($achievement['title']); ?>
                                </h3>
                                
                                <div class="title-divider"></div>
                                
                                <div class="achievement-date">
                                    <?php 
                                    $date = new DateTime($achievement['book_date']);
                                    echo $date->format('F j, Y'); 
                                    ?>
                                </div>
                                
                                <?php if (!empty($achievement['description'])): ?>
                                    <p class="achievement-description">
                                        <?php 
                                        // Limit description to first 200 characters for timeline view
                                        $description = htmlspecialchars($achievement['description']);
                                        echo strlen($description) > 200 ? substr($description, 0, 200) . '...' : $description;
                                        ?>
                                    </p>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-achievements">
                <h3>🏆 No achievements listed yet</h3>
                <p>We're working hard to bring you our latest accomplishments!</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe all timeline items
            document.querySelectorAll('.timeline-item').forEach((item) => {
                observer.observe(item);
            });

            // Enhanced hover effects
            document.querySelectorAll('.timeline-content').forEach((content) => {
                content.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                    this.style.boxShadow = '0 25px 50px rgba(0,0,0,0.2)';
                });
                
                content.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '0 15px 35px rgba(0,0,0,0.1)';
                });
            });

            // Add smooth scrolling for any internal links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php include 'footer.php'; ?>