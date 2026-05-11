<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logos">
            <img src="../assets/images/unijipng.png" alt="Uni logo">
            <img src="../assets/images/jiclogopng.png" alt="JIC logo">
        </div>
        <div class="brand-divider" aria-hidden="true"></div>
        <div class="brand-text">
            <span>CLUB</span>
            <span>MANAGEMENT</span>
            <span>SYSTEM</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php $current = basename($_SERVER['PHP_SELF']); ?>
        <a href="user_page.php" class="nav-link <?php echo $current === 'user_page.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Members List
        </a>
        <a href="meeting_list.php" class="nav-link <?php echo $current === 'meeting_list.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Meeting List
        </a>

        <a href="#" class="nav-link nav-spacer"><i class="fas fa-gear"></i> Settings</a>
        <a href="../auth/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </nav>
</aside>