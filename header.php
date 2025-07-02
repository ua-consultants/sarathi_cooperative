<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha512-GQGU0fMMi238uA+a/bdWJfpUGKUkBdgfFdgBm72SUQ6BeyWjoY/ton0tEjH+OSH9iP4Dfh+7HM0I9f5eR0L/4w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="img/logo.png"/>
    <style>
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: background-color 0.3s ease;
        }

        .headerContent {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .logo img {
            height: 65px;
            width: auto;
        }
        .menuToggle {
            display: none;
            font-size: 1.5rem;
            color: #0a2b4f;
            cursor: pointer;
            z-index: 1002;
            background: none;
            border: none;
            padding: 0.75rem;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        .menuToggle:hover {
            color: #ffd700;
        }

        .menuToggle:focus {
            outline: 2px solid #0a2b4f;
            outline-offset: 2px;
        }

        .nav {
            display: flex;
            justify-content: center;
            flex: 1;
            margin-right: -475px;
        }

        .navList {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            margin: 0;
            padding: 0;
            list-style: none;
            justify-content: center;
        }

        .navList span {
            font-family: 'Dancing Script', cursive;
            font-size: 1rem;
            font-weight: 700;
            color: #0a2b4f;
        }

        .navList > li {
            position: relative;
            padding: 1rem 0;
            cursor: pointer;
        }

        .megaMenu {
            display: none;
            position: absolute;
            left: 50%;
            top: 100%;
            width: auto;
            min-width: 300px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 1.5rem;
            transform: translateX(-50%);
            border-radius: 4px;
            z-index: 1001;
        }

        .navList > li:hover .megaMenu {
            display: block;
        }

        .aboutList {
            width: 200px;
            list-style: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            padding: 0;
            margin: 0 auto;
        }
        
        .aboutList li a {
            color: #0a2b4f;
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
            transition: all 0.3s ease;
        }

        .aboutList li a:hover {
            color: #ffd700;
            transform: scale(1.1);
        }

        .gridList {
            list-style: none;
            display: grid;
            width: 550px;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            padding: 0;
            margin: 0 auto;
        }
        
        .gridList li a{
            text-decoration: none;
            color: #0a2b4f;
            transition: all 0.3s ease;
        }

        .gridList li a:hover {
            color: #ffd700;
            transform: scale(1.05);
        }

        .iconGroup {
            flex: 0 0 200px;
            display: flex;
            gap: 1.5rem;
            justify-content: flex-end;
            align-items: center;
        }

        .searchContainer {
            position: relative;
            display: flex;
            align-items: center;
        }

        .searchInput {
            position: absolute;
            right: 100%;
            width: 0;
            padding: 0.5rem;
            border: 1px solid #eee;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            opacity: 0;
        }

        .searchInput.active {
            width: 200px;
            opacity: 1;
            margin-right: 0.5rem;
        }

        .icon {
            font-size: 1.25rem;
            color: #0a2b4f;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .icon:hover {
            color: #ffd700;
            transform: scale(1.1);
        }

        /* Login Modal Styles */
        .login-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .login-modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 2rem;
            border: none;
            border-radius: 15px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .login-form {
            text-align: center;
        }

        .login-form h2 {
            color: #0a2b4f;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #0a2b4f;
        }

        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #0a2b4f, #1e4d72);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(10, 43, 79, 0.3);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-align: center;
        }

        .success-message {
            color: #27ae60;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-align: center;
        }

        /* User Profile Dropdown */
        .user-profile {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0a2b4f;
        }

        .user-name {
            color: #0a2b4f;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            min-width: 180px;
            z-index: 1002;
        }

        .profile-dropdown.show {
            display: block;
        }

        .profile-dropdown a {
            display: block;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .profile-dropdown a:hover {
            background-color: #f8f9fa;
            color: #0a2b4f;
        }

        /* Mobile Menu Button - Hidden by default */
        .menuToggle {
            display: none;
            font-size: 1.5rem;
            color: #0a2b4f;
            cursor: pointer;
            margin-right: 1rem;
        }

        /* Responsive Styles */
        @media (max-width: 1200px) {
            .headerContent {
                padding: 0.5rem;
            }

            .navList {
                gap: 2rem;
            }
            
            .nav {
                margin-right: -300px;
            }

            .gridList {
                width: 450px;
            }
        }

        @media (max-width: 1024px) {
            .navList {
                gap: 1.5rem;
            }
            
            .nav {
                margin-right: -200px;
            }

            .gridList {
                grid-template-columns: 1fr;
                width: 300px;
            }
        }

        @media (max-width: 450px) {
            /* Show hamburger menu only on mobile */
            .menuToggle {
                display: block;
            }

            /* Hide desktop navigation */
            .nav {
                position: fixed;
                top: 80px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 80px);
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                flex-direction: column;
                padding: 2rem;
                transition: left 0.3s ease;
                margin-right: 0;
            }

            .nav.active {
                left: 0;
            }

            .navList {
                flex-direction: column;
                width: 100%;
                gap: 2rem;
            }

            .navList > li {
                width: 100%;
                text-align: center;
                cursor: pointer;
            }

            .megaMenu {
                display: none;
                position: static;
                transform: none;
                box-shadow: none;
                width: 100%;
                margin-top: 1rem;
                padding: 1rem 0;
                background: rgba(245, 245, 245, 0.98);
            }

            .megaMenu.active {
                display: block;
            }

            .gridList {
                grid-template-columns: 1fr;
                width: 100%;
            }

            /* Hide desktop icons on mobile */
            .iconGroup {
                display: none;
            }

            /* Show mobile icons in navigation */
            .mobile-icons {
                display: flex;
                width: 100%;
                justify-content: center;
                gap: 2rem;
                margin-top: 2rem;
                padding-top: 2rem;
                border-top: 1px solid #eee;
            }

            .searchContainer {
                width: 100%;
                justify-content: center;
            }

            .searchInput.active {
                position: static;
                width: 100%;
                opacity: 1;
                margin: 0;
            }

            .login-modal-content {
                margin: 5% auto;
                width: 95%;
                max-width: 350px;
            }
        }

        /* Add mobile icons container - hidden on desktop */
        .mobile-icons {
            display: none;
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
      "url": "https://www.sarathicooperative.com"
    }
    </script>
</head>
<body>
    <header class="header">
        <div class="headerContent">
            <div class="logo">
                <a href="/">
                    <img src="img/logo-sarathi.png" alt="Sarathi Cooperative Logo">
                </a>
            </div>
            <button class="menuToggle" onclick="toggleMenu()" aria-label="Toggle navigation menu" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <!--<i class="fas fa-bars menuToggle" onclick="toggleMenu()"></i>-->
            
            <div class="nav">
                <ul class="navList">
                    <li>
                        Sarathi <span>At Glance</span>
                        <div class="megaMenu">
                            <ul class="aboutList">
                                <li><a href="we-sarathians.php">We Sarathians</a></li>
                                <li><a href="leadership.php">Leadership</a></li>
                                <li><a href="become-a-sarathian.php">Become a Sarathian</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        Industries <span>We Cater</span>
                        <div class="megaMenu">
                            <ul class="gridList">
                                <li><a href="aerospace.php">Aerospace & Defence</a></li>
                                <li><a href="agriculture.php">Agriculture</a></li>
                                <li><a href="automotive.php">Automotive</a></li>
                                <li><a href="consumers.php">Consumer Products</a></li>
                                <li><a href="education.php">Education</a></li>
                                <li><a href="energy.php">Energy</a></li>
                                <li><a href="finance.php">Financial Services</a></li>
                                <li><a href="health.php">Health Care Industry</a></li>
                                <li><a href="industrial.php">Industrial Goods</a></li>
                                <li><a href="insurance.php">Insurance Industry</a></li>
                                <li><a href="investors.php">Principal Investors & Private Equity</a></li>
                                <li><a href="public-sector.php">Public Sector</a></li>
                                <li><a href="retail.php">Retail Industry</a></li>
                                <li><a href="tech-media.php">Technology Media and Telecommunication</a></li>
                                <li><a href="transportation.php">Transportation & Logistics</a></li>
                                <li><a href="travel.php">Travel and Tourism</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        Services <span>We Perform</span>
                        <div class="megaMenu">
                            <ul class="gridList">
                                <li><a href="advisory.php">Advisory</a></li>
                                <li><a href="climate-change.php">Climate Change and Sustainability</a></li>
                                <li><a href="cost-management.php">Cost Management</a></li>
                                <li><a href="digital-tech.php">Digital Technology Data</a></li>
                                <li><a href="innovation.php">Innovation Strategy</a></li>
                                <li><a href="marketing.php">Marketing & Sales</a></li>
                                <li><a href="risk-management.php">Risk Management & Compliance</a></li>
                                <li><a href="social-impact.php">Social Impact</a></li>
                                <li><a href="organization.php">Organizational Strategy</a></li>
                                <li><a href="international-business.php">International Business</a></li>
                                <li><a href="manufacturing.php">Manufacturing</a></li>
                                <li><a href="tax.php">Tax</a></li>
                            </ul>
                        </div>
                    </li>

                    <li>
                        Memories <span>We Make</span>
                        <div class="megaMenu">
                            <ul class="aboutList">
                                <li><a href="testimonials.php">Testimonials</a></li>
                                <li><a href="blogs.php">Blogs</a></li>
                                <li><a href="library.php">Library</a></li>
                                <li><a href="achievements.php">Achievements</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
                
                <!-- Mobile Icons - Only visible on mobile inside nav -->
                <div class="mobile-icons">
                    <div class="searchContainer">
                        <input type="text" class="searchInput" placeholder="Search...">
                        <i class="fas fa-search icon" onclick="toggleSearch()"></i>
                    </div>
                    
                    <?php if (isset($_SESSION['member_id'])): ?>
                        <div class="user-profile" onclick="toggleProfileDropdown()">
                            <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'img/default-avatar.png'; ?>" alt="Profile" class="user-avatar">
                            <span class="user-name"><?php echo $_SESSION['first_name']; ?></span>
                            <i class="fas fa-chevron-down"></i>
                            <div class="profile-dropdown" id="profileDropdownMobile">
                                <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <i class="fas fa-user icon" onclick="openLoginModal()"></i>
                    <?php endif; ?>
                </div>
            </div>
                
            <!-- Desktop Icons - Only visible on desktop -->
            <div class="iconGroup">
                <div class="searchContainer">
                    <input type="text" class="searchInput" placeholder="Search...">
                    <i class="fas fa-search icon" onclick="toggleSearch()"></i>
                </div>
                
                <?php if (isset($_SESSION['member_id'])): ?>
                    <div class="user-profile" onclick="toggleProfileDropdown()">
                        <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'img/default-avatar.png'; ?>" alt="Profile" class="user-avatar">
                        <span class="user-name"><?php echo $_SESSION['first_name']; ?></span>
                        <i class="fas fa-chevron-down"></i>
                        <div class="profile-dropdown" id="profileDropdown">
                            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <i class="fas fa-user icon" onclick="openLoginModal()"></i>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Login Modal -->
    <div id="loginModal" class="login-modal">
        <div class="login-modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <form class="login-form" id="loginForm" method="POST" action="login.php">
                <h2>Member Login</h2>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group position-relative">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <button type="button" id="togglePasswordBtn" class="btn btn-link position-absolute" style="top: 35px; right: 0; padding: 0 10px; z-index: 2; background: transparent; border: none;">
                        <i class="fas fa-eye"></i> 
                    </button>
                </div>
                <button type="submit" class="login-btn">Login</button>
                <div id="loginMessage"></div>
            </form>
        </div>
    </div>

    <script>
        function toggleSearch() {
            const searchInput = document.querySelector('.searchInput');
            searchInput.classList.toggle('active');
            if (searchInput.classList.contains('active')) {
                searchInput.focus();
            }
        }

        function toggleMenu() {
            const nav = document.querySelector('.nav');
            nav.classList.toggle('active');
        }

        function openLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            const dropdownMobile = document.getElementById('profileDropdownMobile');
            
            if (dropdown) dropdown.classList.toggle('show');
            if (dropdownMobile) dropdownMobile.classList.toggle('show');
        }

        // Handle mobile menu item clicks
        document.querySelectorAll('.navList > li').forEach(item => {
            item.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    const megaMenu = this.querySelector('.megaMenu');
                    if (megaMenu) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const isActive = megaMenu.classList.contains('active');
                        
                        document.querySelectorAll('.megaMenu').forEach(menu => {
                            menu.classList.remove('active');
                        });
                        
                        if (!isActive) {
                            megaMenu.classList.add('active');
                        }
                    }
                }
            });
        });

        // Close modals and dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.querySelector('.nav');
            const menuToggle = document.querySelector('.menuToggle');
            const loginModal = document.getElementById('loginModal');
            const profileDropdown = document.getElementById('profileDropdown');
            const profileDropdownMobile = document.getElementById('profileDropdownMobile');
            
            // Close mobile menu
            if (!nav.contains(event.target) && !menuToggle.contains(event.target)) {
                nav.classList.remove('active');
                document.querySelectorAll('.megaMenu').forEach(menu => {
                    menu.classList.remove('active');
                });
            }
            
            // Close login modal
            if (event.target === loginModal) {
                closeLoginModal();
            }
            
            // Close profile dropdowns
            if (profileDropdown && !event.target.closest('.user-profile')) {
                profileDropdown.classList.remove('show');
            }
            if (profileDropdownMobile && !event.target.closest('.user-profile')) {
                profileDropdownMobile.classList.remove('show');
            }
        });
        
        document.getElementById('togglePasswordBtn').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
        
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
        
        // Handle login form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageDiv = document.getElementById('loginMessage');
            
            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = '<div class="success-message">Login successful! Redirecting...</div>';
                    setTimeout(() => {
                        window.location.href = 'profile.php';
                    }, 1500);
                } else {
                    messageDiv.innerHTML = '<div class="error-message">' + data.message + '</div>';
                }
            })
            .catch(error => {
                messageDiv.innerHTML = '<div class="error-message">An error occurred. Please try again.</div>';
            });
        });
    </script>
</body>
</html>