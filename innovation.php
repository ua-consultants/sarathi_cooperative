<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innovation Strategy | Sarathi Cooperative</title>
    <link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-gradient: linear-gradient(135deg,rgb(207, 232, 193) 0%,rgb(99, 111, 236) 100%);
            --secondary-gradient: linear-gradient(135deg,rgb(249, 100, 100) 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            --text-primary: #2c3e50;
            --text-secondary: #5d6d7e;
            --text-light: #95a5a6;
            --bg-light: #f8f9fa;
            --white: #ffffff;
            --shadow-light: 0 10px 30px rgba(0,0,0,0.1);
            --shadow-heavy: 0 20px 60px rgba(0,0,0,0.15);
            --border-radius: 20px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.2;
            color: var(--text-primary);
            background: var(--bg-light);
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            transition: var(--transition);
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 40px;
        }
        
        .nav-link {
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 500;
            position: relative;
            transition: var(--transition);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: var(--transition);
        }
        
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        
        /* Hero Section */
        .hero {
            background: var(--primary-gradient);
            color: var(--white);
            padding: 110px 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }
        
        .hero-text h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .hero-text .subtitle {
            font-size: 1.4rem;
            opacity: 0.9;
            margin-bottom: 40px;
            line-height: 1.1;
        }
        
        .hero-stats {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .hero-visual {
            position: relative;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .floating-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--border-radius);
            padding: 30px;
            position: absolute;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-card:nth-child(1) {
            top: 20px;
            left: 20px;
            animation-delay: 0s;
        }
        
        .floating-card:nth-child(2) {
            top: 100px;
            right: 20px;
            animation-delay: 2s;
        }
        
        .floating-card:nth-child(3) {
            bottom: 20px;
            left: 50px;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .btn-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 28px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: var(--white);
            color: var(--text-primary);
            box-shadow: var(--shadow-light);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-heavy);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.5);
        }
        
        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--white);
        }
        
        /* Services Overview */
        .services-overview {
            padding: 80px 0;
            background: var(--white);
        }
        
        .section-header {
            margin-top: -60px;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 40px;
        }
        
        .service-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .service-title {
            margin-left: 15px;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 35px;
            color: var(--text-primary);
        }
        
        .service-icon,
        .service-title {
            display: inline;
        }
        
        
        .service-description {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-top: 25px;
            margin-bottom: 25px;
        }
        
        .service-features {
            list-style: none;
        }
        
        .service-features li {
            padding: 8px 0;
            position: relative;
            padding-left: 25px;
            color: var(--text-secondary);
        }
        
        .service-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        /* Process Section */
        .process-section {
            padding: 80px 0;
            background: var(--bg-light);
        }
        
        .process-container {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 80px;
            align-items: center;
        }
        
        .process-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--text-primary);
        }
        
        .process-content p {
            font-size: 1.1rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: 40px;
        }
        
        .process-steps {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .process-step {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow-light);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }
        
        .process-step:hover {
            transform: translateX(10px);
            box-shadow: var(--shadow-heavy);
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .step-content h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-primary);
        }
        
        .step-content p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        /* Industries Section */
        .industries-section {
            padding: 120px 0;
            background: var(--white);
        }
        
        .industries-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }
        
        .industry-card {
            background: var(--primary-gradient);
            color: var(--white);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .industry-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: var(--transition);
        }
        
        .industry-card:hover::before {
            left: 100%;
        }
        
        .industry-card:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-heavy);
        }
        
        .industry-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
        }
        
        .industry-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .industry-description {
            font-size: 0.95rem;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        /* Testimonials */
        .testimonials-section {
            padding: 120px 0;
            background: var(--bg-light);
        }
        
        .testimonials-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .testimonial-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: 50px;
            box-shadow: var(--shadow-light);
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 50px;
            font-size: 8rem;
            color: var(--primary-gradient);
            opacity: 0.1;
            font-family: serif;
        }
        
        .testimonial-text {
            font-size: 1.2rem;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 30px;
            font-style: italic;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        
        .author-avatar {
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .author-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }
        
        .author-info p {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        /* CTA Section */
        .cta-section {
            background: var(--dark-gradient);
            color: var(--white);
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><pattern id="diagonal" patternUnits="userSpaceOnUse" width="10" height="10"><path d="M-1,1 l2,-2 M0,10 l10,-10 M9,11 l2,-2" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23diagonal)"/></svg>');
        }
        
        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 25px;
        }
        
        .cta-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 50px;
            line-height: 1.6;
        }
        
        /* Footer */
        .footer {
            background: var(--text-primary);
            color: var(--white);
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-section h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--white);
        }
        
        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            line-height: 1.8;
        }
        
        .footer-section a:hover {
            color: var(--white);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .process-container {
                grid-template-columns: 1fr;
            }
            
            .nav-menu {
                display: none;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
.blog-section {
    padding: 40px 0;
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
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    position: relative;
}

.blog-section-title span {
    font-family: 'Dancing Script', cursive;
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
    <!-- Navigation -->
    <!-- <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="logo">🤝 Sarathi Cooperative</div>
                <ul class="nav-menu">
                    <li><a href="#" class="nav-link">Home</a></li>
                    <li><a href="#" class="nav-link active">Services</a></li>
                    <li><a href="#" class="nav-link">Industries</a></li>
                    <li><a href="#" class="nav-link">About</a></li>
                    <li><a href="#" class="nav-link">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav> -->
    <?php include 'header.php'?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Innovation Strategies</h1>
                    <p class="subtitle">Stay ahead of the curve with our Innovation Strategy services. We work closely with you to develop, evaluate, and implement innovative ideas and solutions that drive growth, foster creativity, and enhance competitiveness in an ever-changing market.</p>
                    
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Clients Advised</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">15+</span>
                            <span class="stat-label">Industries</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">95%</span>
                            <span class="stat-label">Success Rate</span>
                        </div>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="floating-card">
                        <h4>📊 Financial Planning</h4>
                        <p>Strategic financial guidance</p>
                    </div>
                    <div class="floating-card">
                        <h4>🎯 Risk Management</h4>
                        <p>Comprehensive risk assessment</p>
                    </div>
                    <div class="floating-card">
                        <h4>📈 Growth Strategy</h4>
                        <p>Scalable business solutions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="services-overview">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Comprehensive Solutions</h2>
                <p class="section-subtitle">From strategic planning to operational excellence, we provide end-to-end services tailored to your unique business needs.</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">💼</div>
                    <h3 class="service-title">Business Strategy</h3>
                    <p class="service-description">Develop comprehensive business strategies that align with your vision and market opportunities. Our experts help you navigate competitive landscapes and identify growth pathways.</p>
                    <ul class="service-features">
                        <li>Strategic planning and roadmap development</li>
                        <li>Market analysis and competitive intelligence</li>
                        <li>Business model optimization</li>
                        <li>Growth strategy formulation</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">💰</div>
                    <h3 class="service-title">Financial Advisory</h3>
                    <p class="service-description">Expert financial guidance to optimize your capital structure, improve cash flow, and make informed investment decisions that drive sustainable growth.</p>
                    <ul class="service-features">
                        <li>Financial planning and analysis</li>
                        <li>Capital structure optimization</li>
                        <li>Investment advisory services</li>
                        <li>Cash flow management</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">⚖️</div>
                    <h3 class="service-title">Risk Management</h3>
                    <p class="service-description">Identify, assess, and mitigate business risks with our comprehensive risk management framework. Protect your business while enabling sustainable growth.</p>
                    <ul class="service-features">
                        <li>Risk assessment and mapping</li>
                        <li>Compliance and regulatory guidance</li>
                        <li>Crisis management planning</li>
                        <li>Insurance and protection strategies</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🚀</div>
                    <h3 class="service-title">Digital Transformation</h3>
                    <p class="service-description">Accelerate your digital journey with strategic technology adoption, process automation, and digital business model innovation.</p>
                    <ul class="service-features">
                        <li>Digital strategy development</li>
                        <li>Technology roadmap planning</li>
                        <li>Process automation consulting</li>
                        <li>Change management support</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">👥</div>
                    <h3 class="service-title">Human Capital</h3>
                    <p class="service-description">Build high-performing teams and organizational capabilities. Our services help you attract, develop, and retain top talent.</p>
                    <ul class="service-features">
                        <li>Organizational design and development</li>
                        <li>Talent acquisition strategies</li>
                        <li>Performance management systems</li>
                        <li>Leadership development programs</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-icon">🌍</div>
                    <h3 class="service-title">Market Expansion</h3>
                    <p class="service-description">Expand into new markets with confidence. We provide market entry strategies, regulatory guidance, and local partnership facilitation.</p>
                    <ul class="service-features">
                        <li>Market entry strategy</li>
                        <li>Regulatory compliance guidance</li>
                        <li>Local partnership development</li>
                        <li>Cultural adaptation consulting</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="process-container">
                <div class="process-content">
                    <h2>Our Process</h2>
                    <p>We follow a structured, collaborative approach to ensure our services deliver maximum value and sustainable results for your organization.</p>
                </div>
                
                <div class="process-steps">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Discovery & Assessment</h3>
                            <p>Comprehensive analysis of your business, challenges, and opportunities</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Strategy Development</h3>
                            <p>Collaborative development of tailored strategies and action plans</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Implementation Support</h3>
                            <p>Hands-on guidance and support throughout the execution phase</p>
                        </div>
                    </div>
                    
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Monitor & Optimize</h3>
                            <p>Continuous monitoring, measurement, and optimization of results</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
/**
 * Reusable Blog Section Component with Auto-Rotation
 * 
 * Usage: 
 * $categoryName = 'Advisory Services'; // Change this for each page
 * include 'components/blog-section.php';
 */

// Get category name from the including page (default to 'General' if not set)
$categoryName = isset($categoryName) ? $categoryName : 'Innovation Strategy';

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