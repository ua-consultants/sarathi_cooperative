<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../../img/logo.png" alt="Sarathi" class="logo" onclick="window.location.href='dashboard.php'">
    </div>
    <ul class="nav flex-column">
        <!-- Dashboard (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <!-- Blogs (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'blogs') ? 'active' : ''; ?>" href="blogs/">
                <i class="fas fa-blog"></i>
                <span>Blogs</span>
            </a>
        </li>
        <!-- Announcements (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'announcements') ? 'active' : ''; ?>" href="announcements/index.php">
                <i class="fas fa-bullhorn"></i>
                <span>Announcements</span>
            </a>
        </li>
        <!-- Opportunities (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'opportunities') ? 'active' : ''; ?>" href="opportunities.php">
                <i class="fas fa-handshake"></i>
                <span>Opportunities</span>
            </a>
        </li>
        <!-- Memories (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'memories') ? 'active' : ''; ?>" href="memories.php">
                <i class="fas fa-images"></i>
                <span>Memories</span>
            </a>
        </li>
        <!-- Achievements (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'achievements') ? 'active' : ''; ?>" href="achievements.php">
                <i class="fas fa-trophy"></i>
                <span>Achievements</span>
            </a>
        </li>
        <!-- Testimonials (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'testimonials') ? 'active' : ''; ?>" href="testimonials.php">
                <i class="fas fa-comments fa-fw"></i>
                <span>Testimonials</span>
            </a>
        </li>
        <!-- FAQ (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'faq') ? 'active' : ''; ?>" href="faqs.php">
                <i class="fas fa-question-circle"></i>
                <span>FAQ</span>
            </a>
        </li>
        <!-- E-Books (All Users) -->
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'ebooks') ? 'active' : ''; ?>" href="ebooks.php">
                <i class="fas fa-book"></i>
                <span>E-Books</span>
            </a>
        </li>
        <!-- Members (Admin Only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'members') ? 'active' : ''; ?>" href="members.php">
                <i class="fas fa-users"></i>
                <span>Members</span>
            </a>
        </li>
        <?php endif; ?>
        <!-- Board Members (Admin Only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'board-members') ? 'active' : ''; ?>" href="board-members.php">
                <i class="fas fa-users"></i>
                <span>Board Members</span>
            </a>
        </li>
        <?php endif; ?>
        <!-- Users Management (Admin Only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo ($page == 'users') ? 'active' : ''; ?>" href="users.php">
                <i class="fas fa-user-cog"></i>
                <span>Users</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</aside>