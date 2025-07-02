<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--    <title>Enhanced Footer</title>-->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .footer {
            background-color: #ffffff;
            border-top: 1px solid #e0e0e0;
            padding: 40px 20px 20px;
            position: relative;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 40px;
            align-items: start;
        }
        
        .footer-left {
            display: flex;
            flex-direction: column;
        }
        
        .footer-nav {
            list-style: none;
            margin-bottom: 20px;
        }
        
        .footer-nav li {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            color: #333;
            font-size: 16px;
        }
        
        .footer-nav li::before {
            content: '>';
            margin-right: 8px;
            color: #666;
            font-weight: bold;
        }
        
        .footer-nav a {
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }
        
        .footer-nav a:hover {
            color: #007bff;
        }
        
        .social-media {
            margin-left: 16px;
            margin-top: 18px;
            display: flex;
            gap: 15px;
            justify-content: flex-start;
        }
        
        .social-media a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            /*background-color: #f8f9fa;*/
            /*border: 1px solid #e0e0e0;*/
            border-radius: 50%;
            color: #666;
            font-size: 18px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-media a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .social-media a.facebook:hover {
            background-color: #1877f2;
            color: white;
            border-color: #1877f2;
        }
        
        .social-media a.twitter:hover {
            background-color: #1da1f2;
            color: white;
            border-color: #1da1f2;
        }
        
        .social-media a.instagram:hover {
            background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%);
            color: white;
            border-color: #bc1888;
        }
        
        .social-media a.linkedin:hover {
            background-color: #0077b5;
            color: white;
            border-color: #0077b5;
        }
        
        .social-media a.youtube:hover {
            background-color: #ff0000;
            color: white;
            border-color: #ff0000;
        }
        
        .footer-center {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .become-sarathi {
            font-size: 19px;
            font-family: 'Dancing Script', cursive;
            font-weight: bold;
            color: #333;
            /*margin-bottom: 12px;*/
        }
        
        .divider-line {
            width: 160px;
            height: 1px;
            background: black;
            margin-top: 5px;
            margin-bottom: 5px;
            border-radius: 1px;
        }
        
        .engage-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .state-list {
            font-size: 14px;
            color: #666;
            margin-top: 14px;
        }
        .qr-code img {
            width: 120px;
            height: 120px;
            border: 1px solid #ddd;
        }
        
        .footer-right {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .logo-container {
            margin-bottom: 20px;
        }
        
        .logo-container img {
            height: 85px;
            width: 185px;
        }
        
        .contact-info {
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        
        .contact-info div {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        
        .contact-info div::before {
            margin-left: 8px;
            color: #666;
        }
        
        .contact-info .email::before {
            /*content: '✉ ';*/
            size: 10px;
        }
        
        .contact-info .phone::before {
            content: '📞';
        }
        
        .company-name {
            font-size: 16px;
            color: #666;
            /*font-style: italic;*/
            margin-top: 4px;
            text-align: right;
            line-height: 1.4;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
        
        .scroll-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        
        .scroll-top:hover {
            background-color: #555;
        }
        
        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 30px;
                text-align: center;
            }
            
            .footer-right {
                text-align: center;
                align-items: center;
            }
            
            .contact-info div {
                justify-content: center;
            }
            
            .company-name {
                text-align: center;
            }
            
            .social-media {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Main content area -->
    <!--<div class="main-content">-->
    <!--    <h1>Your Page Content Goes Here</h1>-->
    <!--    <p>This is just placeholder content to show the footer positioning.</p>-->
    <!--</div>-->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Left Navigation -->
            <div class="footer-left">
                <ul class="footer-nav">
                    <li><a href="sarathi-in-news.php">Sarathi in News</a></li>
                    <li><a href="library.php">Latest Projects</a></li>
                    <li><a href="library.php">The Library</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="opportunities.php">Opportunities @Sarathi</a></li>
                </ul>
                
                <!-- Social Media Icons -->
                <div class="social-media">
                    <a href="https://www.facebook.com/share/1EpbtuQv1Y/" class="facebook" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://x.com/Sarathicoop" class="twitter" title="Twitter">
                        <i class="fa-brands fa-x-twitter"></i>
                    </a>
                    
                    <a href="https://www.instagram.com/sarathi.cooperative" class="instagram" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/sarathi-cooperative-48b05236b" class="linkedin" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://youtube.com/@sarathicooperative-z9g" class="youtube" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            
            <!-- Center QR Code -->
            <div class="footer-center">
                <div class="become-sarathi">Become a Sarathian</div>
                <div class="divider-line"></div>
                <div class="engage-text">Engage-Participate-Contribute</div>
                <div class="qr-code">
                    <img src="img/qr_code.png" alt="QR Code">
                </div>
                <div class="state-list">Delhi ● Haryana ● Rajasthan ● Uttar Pradesh</div>
            </div>
            
            <!-- Right Logo and Contact -->
            <div class="footer-right">
                <div class="logo-container">
                    <img src="img/logo.png" alt="Sarathi Cooperative">
                </div>
                
                <div class="contact-info">
                    <div class="phone">+91 966 715 3393</div>
                    <div class="phone">+91 921 852 6890</div>
                    <div class="email">Sarathi@SarathiCooperative.Org</div>
                </div>
                
                <div class="company-name">
                    Sarathi Research Consulting And<br>
                    Management Services Cooperative Limited
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            © Copyright Sarathi Cooperative 2025. All rights reserved.
        </div>
    </footer>
    
    <!-- Scroll to Top Button -->
    <button class="scroll-top" onclick="scrollToTop()">↑</button>
    
    <script>
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        // Show/hide scroll button based on scroll position
        window.addEventListener('scroll', function() {
            const scrollButton = document.querySelector('.scroll-top');
            if (window.pageYOffset > 300) {
                scrollButton.style.display = 'flex';
            } else {
                scrollButton.style.display = 'none';
            }
        });
        
        // Initially hide scroll button
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.scroll-top').style.display = 'none';
        });
    </script>
</body>
</html>