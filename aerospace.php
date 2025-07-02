<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aerospace & Defence | Sarathi Cooperative</title>
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
  "url": "https://www.sarathicooperative.com"
}
</script>

    <style>
    * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.1;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 8px;
        }

        /* Hero Section */
.hero {
    padding-top: 95px;
    background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.1)), url('img/fighter-jets.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    padding: 120px 0;
    text-align: center;
    border-bottom: 1px solid #e9ecef;
    min-height: 60vh;
    display: flex;
    align-items: center;
    position: relative;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    /*background: linear-gradient(135deg, rgba(44, 62, 80, 0.7) 0%, rgba(52, 152, 219, 0.6) 100%);*/
    z-index: 1;
}

.hero .container {
    padding-top: 95px;
    position: relative;
    z-index: 2;
}

.hero h1 {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    color: #ffffff;
    font-weight: 300;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.hero p {
    font-size: 1.8rem;
    color: #f8f9fa;
    margin-bottom: 1rem;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* Remove the hero-image div since we're using background image */
.hero-image {
    display: none;
}

/* Technologies & Innovation Section */
.technologies-section {
    padding: 30px 0;
}

.technologies-section .container {
    max-width: 1400px; /* Wider container */
}

.technologies-section h2 {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #2c3e50;
    text-align: center;
    font-weight: 300;
}

.technologies-section h3 {
    font-size: 2.2rem;
    margin-bottom: 1.1rem;
    color: #495057;
}

.technologies-section p {
    font-size: 1.2rem;
    line-height: 1.1;
    margin-bottom: 1rem;
    color: #6c757d;
    max-width: 100%; /* Full width for paragraph */
}

/* New layout for image and list side by side */
.tech-content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: start;
    margin-top: 1rem;
}

.tech-image-container {
    margin-top: 40px;
    position: relative;
}

.tech-image-container .image-placeholder {
    width: 650px;
    height: 250px;
    background: #e9ecef;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.tech-list-container {
    padding: 10px 0;
}

.tech-list-container h4 {
    font-size: 1.8rem;
    margin-bottom: 1.1rem;
    color: #2c3e50;
    font-weight: 500;
}

/* Enhanced List Styling */
.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    padding: 1.2rem 0;
    border-bottom: 1px solid #e9ecef;
    position: relative;
    padding-left: 1rem;
    font-size: 1.1rem;
    line-height: 1;
    color: #495057;
    transition: all 0.3s ease;
}

.feature-list li:before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 1.2rem;
    color: #28a745;
    font-weight: bold;
    font-size: 1.2rem;
    width: 20px;
    height: 20px;
    background: rgba(40, 167, 69, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
}

.feature-list li:hover {
    background: rgba(40, 167, 69, 0.05);
    padding-left: 1.5rem;
    border-radius: 8px;
    margin: 0 -10px;
    padding-right: 10px;
}

.feature-list li:last-child {
    border-bottom: none;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .technologies-section .container {
        max-width: 1200px;
    }
    
    .tech-content-grid {
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    .technologies-section .container {
        max-width: 100%;
        padding: 0 20px;
    }
    
    .tech-content-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .technologies-section h2 {
        font-size: 2.2rem;
    }
    
    .technologies-section h3 {
        font-size: 1.8rem;
    }
    
    .technologies-section p {
        font-size: 1.1rem;
    }
    
    .tech-image-container .image-placeholder {
        height: 250px;
    }
    
    .feature-list li {
        font-size: 1rem;
        padding: 1rem 0;
        padding-left: 1.8rem;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero {
        padding: 80px 0;
        min-height: 50vh;
    }
    
    .hero h1 {
        font-size: 2.5rem;
    }

    .hero p {
        font-size: 1.3rem;
        padding: 0 20px;
    }
}

        /* Main Content */
        .main-content {
            padding: 40px 0;
        }

        .section {
            margin-top: -35px;
            margin-bottom: 75px;
            background: white;
            padding: 20px 0;
        }

        .section:nth-child(even) {
            background: #f8f9fa;
        }

        .section h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #2c3e50;
            text-align: center;
            font-weight: 300;
        }

        .section h3 {
            font-size: 2.2rem;
            margin-bottom: 1rem;
            color: #495057;
        }

        .section p {
            font-size: 1.1rem;
            line-height: 1.1;
            margin-bottom: 1rem;
            color: #6c757d;
        }

        /* Grid Layouts */
        .grid {
            display: grid;
            gap: 2rem;
            margin-top: 2rem;
        }

        .grid-2 {
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        }

        .grid-3 {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .card p {
            color: #6c757d;
            font-size: 1rem;
        }

        /* Image Placeholders */
        .image-placeholder {
            width: 100%;
            height: 250px;
            background: #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .large-image {
            height: 400px;
        }

        .small-image {
            height: 200px;
        }

        /* Content with Image */
        .content-with-image {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: center;
            margin-top: 2rem;
        }

        .content-with-image.reverse {
            direction: rtl;
        }

        .content-with-image.reverse > * {
            direction: ltr;
        }

        /* Statistics */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1.2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 300;
            color: #3498db;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 1.1rem;
        }

        /* List Styling */
        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 1rem 0;
            border-bottom: 1px solid #e9ecef;
            position: relative;
            padding-left: 1.5rem;
        }

        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .section h2 {
                font-size: 2rem;
            }

            .content-with-image {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .grid {
                gap: 2rem;
            }

            .card {
                padding: 1.5rem;
            }
        }
        /* Updated Blog Section Styles - Replace the existing blog section styles */
.blog-section {
    padding: 80px 0;
    background: #ffffff; /* Simple white background */
    position: relative;
}

/* Remove the decorative background pattern */
.blog-section::before {
    display: none;
}

.blog-container {
    position: relative;
    z-index: 2;
}

.blog-header {
    text-align: center;
    margin-bottom: 60px;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
}

.blog-section-title {
    color: black;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    position: relative;
}

.blog-section-subtitle {
    font-size: 1.1rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 20px;
}

/* Remove decorative blog stats */
.blog-stats {
    display: none;
}

/* Blog Carousel Container */
.blog-carousel-container {
    position: relative;
    overflow: hidden;
    margin-bottom: 50px;
}

.blogs-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* Always show 3 columns */
    gap: 30px;
    transition: transform 0.5s ease-in-out;
    width: 100%;
}

/* Blog card styles - simplified */
.blog-card {
    background: var(--white);
    border-radius: 12px; /* Reduced border radius for simpler look */
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* Simpler shadow */
    transition: var(--transition);
    position: relative;
    cursor: pointer;
    border: 1px solid #f0f2f5; /* Add subtle border */
}

.blog-card:hover {
    transform: translateY(-5px); /* Reduced hover effect */
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.blog-image {
    width: 100%;
    height: 200px; /* Reduced height */
    object-fit: cover;
    background: var(--primary-gradient);
    position: relative;
}

.blog-image-placeholder {
    height: 200px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Simpler gradient */
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 2.5rem;
    position: relative;
}

/* Remove decorative pattern from placeholder */
.blog-image-placeholder::before {
    display: none;
}

.blog-content {
    padding: 25px;
}

.blog-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 0.85rem;
    color: var(--text-light);
}

.blog-date {
    display: flex;
    align-items: center;
    gap: 5px;
}

.blog-author {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}

.blog-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 3.6rem; /* Ensure consistent height */
}

.blog-excerpt {
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 4.5rem; /* Ensure consistent height */
}

.blog-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 15px;
}

.blog-tag {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
}

.blog-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f0f2f5;
}

.read-more-btn {
    background: var(--primary-gradient);
    color: var(--white);
    padding: 8px 16px;
    border: none;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.read-more-btn:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Simplified CTA section */
.blog-cta {
    text-align: center;
    background: #f8f9fa; /* Simple light background */
    padding: 40px;
    border-radius: 12px;
    border: 1px solid #e9ecef;
    position: relative;
}

/* Remove decorative elements from CTA */
.blog-cta::before {
    display: none;
}

.blog-cta h3 {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 12px;
    color: var(--text-primary);
}

.blog-cta p {
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 25px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.view-all-btn {
    background: var(--primary-gradient);
    color: var(--white);
    padding: 12px 24px;
    border: none;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
}

.view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

/* Carousel indicators (optional) */
.blog-indicators {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 30px;
}

.indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ddd;
    cursor: pointer;
    transition: var(--transition);
}

.indicator.active {
    background: var(--primary-gradient);
    transform: scale(1.2);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .blogs-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }
}

@media (max-width: 768px) {
    .blog-section {
        padding: 60px 0;
    }
    
    .blog-section-title {
        font-size: 2rem;
    }
    
    .blogs-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .blog-content {
        padding: 20px;
    }
    
    .blog-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
    </style>
</head>
<body>
    <?php include 'header.php'?>

    <section class="hero">
        <div class="container">
            <h1>Aerospace & Defense Industry</h1>
            <p>Leading innovation in aerospace technology, defense systems, and mission-critical solutions that protect and advance global security interests</p>
            
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Industry Overview -->
        <section class="section">
            <div class="container">
                <h2>Industry Overview</h2>
                <p>The aerospace and defense industry represents one of the most technologically advanced and strategically important sectors in the global economy. This industry encompasses the design, development, manufacturing, and maintenance of aircraft, spacecraft, missiles, and defense systems that serve both civilian and military applications.</p>
                
                <div class="content-with-image">
                    <div>
                        <h3>Market Leadership</h3>
                        <p>Our industry drives innovation across multiple domains, from commercial aviation that connects the world to advanced defense systems that ensure national security. The sector continues to evolve with emerging technologies including artificial intelligence, autonomous systems, and next-generation propulsion technologies.</p>
                        <p>With a global market value exceeding $900 billion, the aerospace and defense industry employs millions worldwide and represents a cornerstone of economic stability and technological advancement in developed nations.</p>
                    </div>
                    <div class="image-placeholder">
                        <img src="img/satellite.png">
                    </div>
                </div>
            </div>
        </section>

        <!-- Key Sectors -->
        <section class="section">
            <div class="container">
                <h2>Key Industry Sectors</h2>
                <div class="grid grid-3">
                    <div class="card">
                        <div class="image-placeholder small-image">
                            <img src="img/commercial-plane.png">
                        </div>
                        <h3>Commercial Aviation</h3>
                        <p>Development and manufacturing of passenger aircraft, cargo planes, and related systems. This sector focuses on fuel efficiency, passenger safety, and advanced avionics systems that enable global connectivity.</p>
                    </div>
                    <div class="card">
                        <div class="image-placeholder small-image">
                            <img src="img/iaf-fighter.png">
                        </div>
                        <h3>Defense Systems</h3>
                        <p>Advanced military aircraft, missile systems, and defense technologies designed to protect national interests. Includes fighter jets, transport aircraft, and unmanned aerial vehicles.</p>
                    </div>
                    <div class="card">
                        <div class="image-placeholder small-image">
                            <img src="img/indian-rocket.png">
                        </div>
                        <h3>Space & Satellites</h3>
                        <p>Spacecraft development, satellite technology, and launch vehicles that support communications, navigation, Earth observation, and scientific exploration missions.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technologies & Innovation -->
<section class="section technologies-section">
    <div class="container">
        <h2>Advanced Technologies</h2>
        <div>
            <h3>Cutting-Edge Innovation</h3>
            <p>The aerospace and defense industry is at the forefront of technological innovation, developing solutions that often find applications across multiple industries. Our comprehensive approach to research and development ensures that we remain leaders in emerging technologies while maintaining the highest standards of reliability and performance. From advanced materials that reduce weight while increasing strength, to artificial intelligence systems that enhance decision-making capabilities, our focus areas represent the future of aerospace and defense technology.</p>
        </div>
        
        <div class="tech-content-grid">
            <div class="tech-image-container">
                <div class="image-placeholder">
                    <img src="img/cockpit.png" alt="Advanced Cockpit Technology">
                </div>
            </div>
            <div class="tech-list-container">
                <h4>Our Technology Focus Areas:</h4>
                <ul class="feature-list">
                    <li>Advanced Materials and Composites</li>
                    <li>Artificial Intelligence and Machine Learning</li>
                    <li>Autonomous Flight Systems</li>
                    <li>Next-Generation Propulsion</li>
                    <li>Cyber Security Solutions</li>
                    <li>Quantum Computing Applications</li>
                </ul>
            </div>
        </div>
    </div>
</section>

        <!-- Industry Statistics -->

        <!-- Applications & Solutions -->
        <section class="section">
            <div class="container">
                <h2>Critical Applications</h2>
                <div class="grid grid-2">
                    <div class="card">
                        <div class="image-placeholder">
                            <img src="img/radar-defense.png">
                        </div>
                        <h3>Defense & Security</h3>
                        <p>Advanced radar systems, missile defense networks, and surveillance technologies that protect national borders and maintain global security. Our solutions integrate seamlessly with existing defense infrastructure while providing enhanced threat detection and response capabilities.</p>
                    </div>
                    <div class="card">
                        <div class="image-placeholder">
                            <img src="img/traffic-control.png">
                        </div>
                        <h3>Air Traffic Management</h3>
                        <p>Sophisticated air traffic control systems, navigation aids, and communication networks that ensure safe and efficient movement of aircraft worldwide. These systems handle millions of flights annually with exceptional safety records.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Manufacturing Excellence -->
        <section class="section">
            <div class="container">
                <h2>Manufacturing Excellence</h2>
                <div class="content-with-image">
                    <div>
                        <h3>Precision Engineering</h3>
                        <p>Our manufacturing processes represent the pinnacle of precision engineering, utilizing advanced robotics, quality control systems, and lean manufacturing principles to deliver products that meet the most stringent requirements.</p>
                        <p>Every component undergoes rigorous testing and validation to ensure reliability in the most demanding environments. Our facilities maintain the highest industry certifications and employ cutting-edge technologies to maintain our competitive edge.</p>
                        <ul class="feature-list">
                            <li>ISO 9001 Quality Management</li>
                            <li>AS9100 Aerospace Standards</li>
                            <li>Lean Six Sigma Processes</li>
                            <li>Advanced Materials Testing</li>
                        </ul>
                    </div>
                    <div class="image-placeholder">
                        <img src="img/manufacturing-floor.png">
                    </div>
                </div>
            </div>
        </section>

    </main>
<?php
/**
 * Reusable Blog Section Component with Auto-Rotation
 * 
 * Usage: 
 * $categoryName = 'Advisory Services'; // Change this for each page
 * include 'components/blog-section.php';
 */

// Get category name from the including page (default to 'General' if not set)
$categoryName = isset($categoryName) ? $categoryName : 'Aerospace & Defence';

// Database connection (adjust as per your database configuration)
try {
    // Replace with your actual database connection details
    $pdo = new PDO("mysql:host=localhost;dbname=u828878874_sarathi_db", "u828878874_sarathi_new", "#Sarathi@2025");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch category ID
    $categoryStmt = $pdo->prepare("SELECT id, description FROM blog_categories WHERE name = ? AND status = 1");
    $categoryStmt->execute([$categoryName]);
    $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        // Fetch all published blogs for this category
        $blogsStmt = $pdo->prepare("
            SELECT id, title, slug, excerpt, featured_image, published_at, author_name, meta_keywords, created_at
            FROM blogs 
            WHERE category_id = ? AND status = 'published' 
            ORDER BY created_at DESC
        ");
        $blogsStmt->execute([$category['id']]);
        $blogs = $blogsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Count total blogs in this category
        $totalBlogs = count($blogs);
    } else {
        $blogs = [];
        $totalBlogs = 0;
    }
    
} catch(PDOException $e) {
    // Handle database error gracefully
    $blogs = [];
    $totalBlogs = 0;
    error_log("Database error in blog section: " . $e->getMessage());
}

// Only display the section if we have blogs
if (!empty($blogs)):
    // Calculate how many sets of 3 blogs we have
    $blogsPerSet = 3;
    $totalSets = ceil($totalBlogs / $blogsPerSet);
    $showCarousel = $totalBlogs > $blogsPerSet;
?>
<!-- Blog Section -->
<section class="blog-section">
    <div class="container blog-container">
        <div class="blog-header">
            <h2 class="blog-section-title">Blogs: <span>Subject Expert Speaks</span></h2>
            <p class="blog-section-subtitle">
                Stay updated with the latest trends, strategies, and insights in <?php echo htmlspecialchars($categoryName); ?>. 
                Our expert content helps you make informed decisions and stay ahead of the curve.
            </p>
        </div>
        
        <div class="blog-carousel-container">
            <div class="blogs-grid" id="blogsGrid">
                <?php foreach ($blogs as $index => $blog): ?>
                    <article class="blog-card" onclick="window.location.href='/blog/<?php echo htmlspecialchars($blog['slug']); ?>'" 
                             data-set="<?php echo floor($index / $blogsPerSet); ?>">
                        <?php if (!empty($blog['featured_image'])): ?>
                            <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                                 class="blog-image">
                        <?php else: ?>
                            <div class="blog-image-placeholder">
                                📄
                            </div>
                        <?php endif; ?>
                        
                        <div class="blog-content">
                            <div class="blog-meta">
                                <span class="blog-date">
                                    📅 <?php echo date('M j, Y', strtotime($blog['created_at'])); ?>
                                </span>
                                <?php if (!empty($blog['author_name'])): ?>
                                    <span class="blog-author">
                                        👤 <?php echo htmlspecialchars($blog['author_name']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h3>
                            
                            <?php if (!empty($blog['excerpt'])): ?>
                                <p class="blog-excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($blog['meta_keywords'])): ?>
                                <div class="blog-tags">
                                    <?php 
                                    $keywords = explode(',', $blog['meta_keywords']);
                                    foreach (array_slice($keywords, 0, 3) as $keyword): 
                                        $keyword = trim($keyword);
                                        if (!empty($keyword)):
                                    ?>
                                        <span class="blog-tag"><?php echo htmlspecialchars($keyword); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="blog-footer">
                                <a href="/blog/<?php echo htmlspecialchars($blog['slug']); ?>" class="read-more-btn">
                                    Read More →
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($showCarousel && $totalSets > 1): ?>
                <div class="blog-indicators" id="blogIndicators">
                    <?php for ($i = 0; $i < $totalSets; $i++): ?>
                        <span class="indicator <?php echo $i === 0 ? 'active' : ''; ?>" 
                              data-set="<?php echo $i; ?>"></span>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($totalBlogs > 6): ?>
            <div class="blog-cta">
                <h3>Explore More Insights</h3>
                <p>Discover more expert content and in-depth analysis on <?php echo htmlspecialchars($categoryName); ?>.</p>
                <a href="/blog/category/<?php echo urlencode(strtolower(str_replace(' ', '-', $categoryName))); ?>" class="view-all-btn">
                    View All Articles →
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const blogsGrid = document.getElementById('blogsGrid');
    const indicators = document.querySelectorAll('.indicator');
    const blogCards = document.querySelectorAll('.blog-card');
    const totalBlogs = <?php echo $totalBlogs; ?>;
    const blogsPerSet = <?php echo $blogsPerSet; ?>;
    const totalSets = <?php echo $totalSets; ?>;
    const showCarousel = <?php echo $showCarousel ? 'true' : 'false'; ?>;
    
    let currentSet = 0;
    let autoRotateInterval;
    
    // Function to show specific set of blogs
    function showBlogSet(setIndex) {
        // Hide all blog cards
        blogCards.forEach(card => {
            card.style.display = 'none';
        });
        
        // Show blogs for the current set
        const startIndex = setIndex * blogsPerSet;
        const endIndex = Math.min(startIndex + blogsPerSet, totalBlogs);
        
        for (let i = startIndex; i < endIndex; i++) {
            if (blogCards[i]) {
                blogCards[i].style.display = 'block';
            }
        }
        
        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === setIndex);
        });
        
        currentSet = setIndex;
    }
    
    // Function to go to next set
    function nextSet() {
        const nextIndex = (currentSet + 1) % totalSets;
        showBlogSet(nextIndex);
    }
    
    // Initialize carousel if needed
    if (showCarousel && totalSets > 1) {
        // Show first set
        showBlogSet(0);
        
        // Add click handlers to indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                showBlogSet(index);
                // Restart auto-rotation
                clearInterval(autoRotateInterval);
                startAutoRotation();
            });
        });
        
        // Start auto-rotation
        function startAutoRotation() {
            autoRotateInterval = setInterval(nextSet, 5000); // 5 seconds
        }
        
        startAutoRotation();
        
        // Pause auto-rotation on hover
        const blogSection = document.querySelector('.blog-section');
        blogSection.addEventListener('mouseenter', () => {
            clearInterval(autoRotateInterval);
        });
        
        blogSection.addEventListener('mouseleave', () => {
            startAutoRotation();
        });
    } else {
        // Show first 3 blogs if no carousel needed
        showBlogSet(0);
    }
    
    // Add hover effects to blog cards
    blogCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Add smooth scroll to blog section if accessed via anchor
    if (window.location.hash === '#blog') {
        document.querySelector('.blog-section').scrollIntoView({
            behavior: 'smooth'
        });
    }
});
</script>

<?php endif; // End if blogs exist ?>

<?php include('footer.php')?>