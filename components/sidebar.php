<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logos">
            <img src="../assets/images/unijipng.png" alt="Uni logo">
            <img src="../assets/images/jiclogopng.png" alt="JIC logo">
        </div>
        <div class="brand-divider" aria-hidden="true"></div>
        <div class="brand-text">
            <span>ROOM</span>
            <span>RESERVATION</span>
            <span>SYSTEM</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="javascript:void(0);" onclick="switchView('members')" id="nav-members" class="nav-link active">
            <i class="fas fa-users"></i> Members List
        </a>
        
        <a href="javascript:void(0);" onclick="switchView('meetings')" id="nav-meetings" class="nav-link">
            <i class="fas fa-calendar-alt"></i> Meeting List
        </a>

        <?php if ($isAdmin || $isManager): ?>
            <a href="javascript:void(0);" onclick="switchView('requests')" id="nav-requests" class="nav-link">
                <i class="fas fa-envelope-open-text"></i> Requests
            </a>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
            <a href="javascript:void(0);" onclick="switchView('logs')" id="nav-logs" class="nav-link">
                <i class="fas fa-file-alt"></i> Activity Logs
            </a>
        <?php endif; ?>

        <a href="#" class="nav-link nav-spacer"><i class="fas fa-gear"></i> Settings</a>
        <a href="../index.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </nav>
</aside>