<?php
    // Get the current file name so we know which menu item should be active
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Fallback to 'Visitor' if the role isn't set
    $user_role = $_SESSION['role'] ?? 'Visitor'; 
?>
<aside class="sidebar">
    <div class="sidebar-brand" style="background: #ffffff; color: #5a0505; border-radius: 8px; margin-bottom: 20px; padding: 15px; text-align: left;">
        <div class="brand-text" style="font-family: 'Shrikhand', cursive; font-size: 16px; line-height: 1.2;">
            <strong>EVENTHUB</strong><br>
            <strong>PORTAL</strong>
        </div>
    </div>
    
    <div class="sidebar-nav">
        
        <?php if ($user_role === 'Admin' || $user_role === 'Staff'): ?>
            <a href="../pages/staff_page.php" class="nav-link <?php echo ($current_page == 'staff_page.php') ? 'active' : ''; ?>">
                <i class="fas fa-tasks"></i> Event List
            </a>
            <a href="../pages/generate_code.php" class="nav-link <?php echo ($current_page == 'generate_code.php') ? 'active' : ''; ?>">
                <i class="fas fa-key"></i> Invitation Code
            </a>
            <a href="../pages/inquiries_page.php" class="nav-link <?php echo ($current_page == 'inquiries_page.php') ? 'active' : ''; ?>">
                <i class="fas fa-question-circle"></i> Inquiries List
            </a>
            
        <?php else: ?>
            <a href="../pages/visitors_page.php" class="nav-link <?php echo ($current_page == 'visitors_page.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Event List
            </a>
            <a href="../pages/contact.php" class="nav-link <?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        <?php endif; ?>
        
        <div class="nav-spacer"></div>
        <a href="#" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        <a href="../index.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </div>
</aside>