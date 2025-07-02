<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Sarathi Cooperative</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.1;
            color: #333;
            /*background: linear-gradient(rgba(248, 249, 250, 0.3), rgba(248, 249, 250, 0.4)), url('img/services.png');*/
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
            overflow-x: hidden;
        }

        .privacy-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
        }

        .privacy-header {
            margin-top: 95px;
            background: rgb(223, 225, 228);
            color: white;
            padding: 15px 15px;
            border-radius: 15px;
            margin-bottom: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .privacy-header h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .privacy-header p {
            font-size: 1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Scattered Service Icons */
        .scattered-icon {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 15px;
            background: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s ease;
            border: 2px solid transparent;
            z-index: 10;
            opacity: 0.8;
        }

        .scattered-icon:hover {
            transform: translateY(-5px) scale(1.15);
            box-shadow: 0 12px 30px rgba(44, 82, 130, 0.4);
            border-color: #4299e1;
            opacity: 1;
        }

        .scattered-icon .service-icon {
            font-size: 3.2rem;
            color: grey;
            transition: all 0.3s ease;
        }

        .scattered-icon:hover .service-icon {
            transform: scale(1.2) rotate(10deg);
            color: #ffd700;
        }

        .scattered-icon .service-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #2c5282;
            color: white;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .scattered-icon .service-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #2c5282;
        }

        .scattered-icon:hover .service-tooltip {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 10px);
        }

        /* Floating animations for scattered icons */
        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(2deg); }
        }

        @keyframes gentleFloatReverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-6px) rotate(-2deg); }
        }

        .scattered-icon:nth-child(odd) {
            animation: gentleFloat 6s ease-in-out infinite;
        }

        .scattered-icon:nth-child(even) {
            animation: gentleFloatReverse 7s ease-in-out infinite;
        }

        /* Icon positioning - distributed across sections 1-9, right side only */
        .privacy-content {
            position: relative;
        }
        
        .icon-advisory { top: 280px; right: 280px; }
        .icon-climate { top: 510px; right: 280px; }
        .icon-cost { top: 910px; right: 280px; }
        .icon-digital { top: 1250px; right: 280px; }
        .icon-innovation { top: 1750px; right: 280px; }
        .icon-marketing { top: 2080px; right: 280px; }
        .icon-risk { top: 2400px; right: 280px; }
        .icon-social { top: 2850px; right: 280px; }
        .icon-organization { top: 3250px; right: 280px; }
        .icon-international { top: 3650px; right: 280px; }
        .icon-manufacturing { top: 4000px; right: 280px; }
        .icon-tax { top: 4550px; right: 280px; }

        .last-updated {
            background: #e8f4fd;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #2c5282;
        }

        .last-updated strong {
            color: #2c5282;
        }

        .privacy-content {
            background: rgba(255, 255, 255, 0.75);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            backdrop-filter: blur(3px);
        }

        .section {
            margin-bottom: 25px;
            padding-bottom: 19px;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section h2 {
            color: #2c5282;
            font-size: 1.2rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .section h3 {
            color: #4a5568;
            font-size: 1rem;
            margin: 15px 0 10px 0;
            font-weight: 600;
        }

        .section p {
            margin-bottom: 10px;
            color: #4a5568;
            font-size: 1.05rem;
        }

        .section ul {
            margin: 15px 0 15px 30px;
            color: #4a5568;
        }

        .section ul li {
            margin-bottom: 8px;
            font-size: 1.05rem;
        }

        .highlight-box {
            background: rgba(247, 250, 252, 0.65);
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            border-left: 5px solid #2c5282;
            backdrop-filter: blur(2px);
        }

        .icon {
            font-size: 1.2rem;
            color: #2c5282;
        }

        .table-of-contents {
            background: rgba(248, 249, 250, 0.95);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
            backdrop-filter: blur(10px);
        }

        .table-of-contents h3 {
            text-align: center;
            color: #2c5282;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }

        .toc-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .toc-item {
            padding: 10px;
            background: rgba(248, 249, 250, 0.9);
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }

        .toc-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-color: #2c5282;
        }

        .toc-item a {
            color: #4a5568;
            text-decoration: none;
            font-weight: 600;
            display: block;
            text-align: center;
        }

        .toc-item a:hover {
            color: #2c5282;
        }

        /* Privacy Policy Content Styles */
        .privacy-policy-content {
            margin: 0;
            padding: 0;
            background: transparent;
            border: none;
            box-shadow: none;
            border-radius: 0;
        }

        .privacy-policy-content img {
            width: 100%;
            height: auto;
            display: block;
            margin: 0;
            padding: 0;
            border: none;
            border-radius: 0;
            box-shadow: none;
            background: transparent;
        }

        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }
            
            .toc-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .scattered-icon {
                width: 50px;
                height: 50px;
                position: relative !important;
                display: inline-block;
                margin: 5px;
                top: auto !important;
                right: auto !important;
                left: auto !important;
            }
            
            .mobile-icons-container {
                text-align: center;
                margin: 20px 0;
                padding: 20px;
                background: rgba(248, 249, 250, 0.95);
                border-radius: 10px;
                backdrop-filter: blur(10px);
            }

            .privacy-policy-content {
                margin: 0;
                padding: 0;
            }

            .privacy-policy-content img {
                width: 100%;
                height: auto;
            }
        }

        @media (max-width: 480px) {
            .toc-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .privacy-container {
                padding: 10px;
            }

            .privacy-header {
                padding: 40px 20px;
            }

            .privacy-header h1 {
                font-size: 2rem;
            }

            .privacy-content {
                padding: 25px;
            }

            .section h2 {
                font-size: 1.5rem;
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

    <!-- Scattered Service Icons - positioned relative to privacy-content -->
    <div class="privacy-container">
        <div class="privacy-header">
            <h1><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
        </div>

        <div class="table-of-contents">
            <h3><i class="fas fa-list"></i> Table of Contents</h3>
            <div class="toc-grid">
                <div class="toc-item">
                    <a href="#information-we-collect">1. Information We Collect</a>
                </div>
                <div class="toc-item">
                    <a href="#how-we-use">2. How We Use Your Information</a>
                </div>
                <div class="toc-item">
                    <a href="#information-sharing">3. Information Sharing and Disclosure</a>
                </div>
                <div class="toc-item">
                    <a href="#data-security">4. Data Security</a>
                </div>
                <div class="toc-item">
                    <a href="#data-retention">5. Data Retention</a>
                </div>
                <div class="toc-item">
                    <a href="#your-rights">6. Your Rights and Choices</a>
                </div>
                <div class="toc-item">
                    <a href="#cookies">7. Cookies and Tracking Technologies</a>
                </div>
                <div class="toc-item">
                    <a href="#third-party">8. Third-Party Services</a>
                </div>
                <div class="toc-item">
                    <a href="#updates">9. Updates to This Policy</a>
                </div>
            </div>
        </div>

        <!-- Mobile Icons Container (visible only on mobile) -->
        <div class="mobile-icons-container" style="display: none;">
            <h4>Our Services</h4>
            <div class="scattered-icon">
                <i class="service-icon fas fa-briefcase"></i>
                <span class="service-tooltip">Advisory</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-leaf"></i>
                <span class="service-tooltip">Climate Change</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-calculator"></i>
                <span class="service-tooltip">Cost Management</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-microchip"></i>
                <span class="service-tooltip">Digital Technology</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-lightbulb"></i>
                <span class="service-tooltip">Innovation Strategy</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-bullhorn"></i>
                <span class="service-tooltip">Marketing & Sales</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-shield-alt"></i>
                <span class="service-tooltip">Risk Management</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-globe"></i>
                <span class="service-tooltip">Social Impact</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-sitemap"></i>
                <span class="service-tooltip">Organizational Strategy</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-globe-americas"></i>
                <span class="service-tooltip">International Business</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-industry"></i>
                <span class="service-tooltip">Manufacturing</span>
            </div>
            <div class="scattered-icon">
                <i class="service-icon fas fa-file-invoice-dollar"></i>
                <span class="service-tooltip">Tax</span>
            </div>
        </div>

        <div class="privacy-content">

            <!-- Privacy Policy Content Section -->
            <div class="privacy-policy-content">
                <!-- Replace 'privacy-policy.png' with your actual image file name and path -->
                <img src="img/sarathi-privacy-policy.jpeg" alt="Sarathi Cooperative Privacy Policy" title="Privacy Policy - Sarathi Cooperative">
            </div>

        </div>
    </div>

    <script>
        // Show mobile icons container on mobile devices
        function handleMobileIcons() {
            const mobileContainer = document.querySelector('.mobile-icons-container');
            if (window.innerWidth <= 768) {
                mobileContainer.style.display = 'block';
            } else {
                mobileContainer.style.display = 'none';
            }
        }

        // Call on load and resize
        window.addEventListener('load', handleMobileIcons);
        window.addEventListener('resize', handleMobileIcons);

        // Add scroll-based animation to scattered icons
        window.addEventListener('scroll', function() {
            const icons = document.querySelectorAll('.scattered-icon');
            const scrollTop = window.pageYOffset;
            
            icons.forEach(function(icon, index) {
                const speed = 0.5 + (index * 0.1);
                const yPos = scrollTop * speed;
                icon.style.transform = `translateY(${yPos}px)`;
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>