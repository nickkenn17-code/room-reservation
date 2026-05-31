<?php
    //  30 MINUTE SESSION TIMEOUT LOGIC 
    $timeout_duration = 1800; // 1800 seconds = 30 minutes
    $is_index = basename($_SERVER['PHP_SELF']) === 'index.php';
    $asset_prefix = $is_index ? '' : '../';

    if (isset($_SESSION['last_activity'])) {
        // Calculate the time difference
        $elapsed_time = time() - $_SESSION['last_activity'];
        
        if ($elapsed_time > $timeout_duration) {
            session_unset();
            session_destroy();
            // Send them back to the login page with a timeout flag
            header("Location: " . $asset_prefix . "index.php?timeout=1");
            exit();
        }
    }
    // Update last activity time on every single page load
    $_SESSION['last_activity'] = time(); 
    // ---------------------------------------

    $body_class = $is_index ? '' : 'user-body';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub Portal</title>

    <link rel="manifest" href="<?php echo $asset_prefix; ?>manifest.json">
    <meta name="theme-color" content="#5a0505">
    
    <link rel="stylesheet" href="<?php echo $asset_prefix; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Shrikhand&display=swap" rel="stylesheet">

    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('<?php echo $asset_prefix; ?>sw.js')
            .then(registration => console.log('SW registered'))
            .catch(err => console.log('SW registration failed: ', err));
        });
      }
    </script>
</head>
<body<?php echo $body_class ? ' class="' . $body_class . '"' : ''; ?>>
    <?php if ($is_index): ?>
        <header class="login-header-bar" aria-hidden="true"></header>
    <?php else: ?>
        <div class="user-hero" aria-hidden="true"></div>
        
        <header class="user-topbar">
            <button class="topbar-menu" type="button" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
            
            <h1 class="page-title" id="page-title" style="text-transform: uppercase;">
                <?php
                    // Dynamic page title based on the active file
                    $page = basename($_SERVER['PHP_SELF']);
                    $header_title = match($page) {
                        'visitors_page.php', 'staff_page.php' => 'EVENT LIST',
                        'inquiries_page.php' => 'INQUIRIES LIST',
                        'generate_code.php' => 'INVITATION CODE',
                        'contact.php' => 'CONTACT US',
                        default => 'EVENTHUB PORTAL'
                    };
                    echo htmlspecialchars($header_title);
                ?>
            </h1>

            <div class="topbar-profile">
                <span class="profile-name">
                    <?php 
                        $displayName = htmlspecialchars($_SESSION['name'] ?? 'Visitor');
                        $role = htmlspecialchars($_SESSION['role'] ?? '');
                        echo $displayName . ($role ? ' ' . $role : '');
                    ?>
                </span>
                <div class="profile-avatar">
                    <?php 
                        echo strtoupper(mb_substr($_SESSION['name'] ?? 'V', 0, 1)); 
                    ?>
                </div>
            </div>
        </header>

        <div class="overlay"></div>

        <div id="timeoutModal" class="custom-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center;">
            <div class="custom-modal-content" style="background: #ffffff; padding: 30px; border-radius: 12px; max-width: 400px; text-align: center; border-top: 5px solid #5a0505;">
                <h3 style="color: #5a0505; margin-top: 0; font-family: 'Shrikhand', cursive;">Session Timeout</h3>
                <p style="color: #333; font-size: 14px; font-family: 'Montserrat', sans-serif; margin-bottom: 20px;">
                    This website's policy enforces automatic signout after a period of inactivity.<br><br>
                    Do you want to stay signed in?
                </p>
                <button onclick="staySignedIn()" style="padding: 10px 20px; background: transparent; border: 2px solid #5a0505; color: #5a0505; border-radius: 8px; font-weight: bold; cursor: pointer; font-family: 'Montserrat', sans-serif; transition: 0.3s;">Stay signed in</button>
            </div>
        </div>

        <script>
            let warningTimer;
            let logoutTimer;

            function startTimers() {
                // Show the warning modal at 29 minutes (1,740,000 milliseconds)
                warningTimer = setTimeout(() => {
                    let modal = document.getElementById('timeoutModal');
                    modal.style.display = 'flex';
                }, 1740000);

                // Force logout via JS at 30 minutes (1,800,000 milliseconds)
                logoutTimer = setTimeout(() => {
                    window.location.href = '<?php echo $asset_prefix; ?>auth/logout.php';
                }, 1800000);
            }

            function resetTimers() {
                clearTimeout(warningTimer);
                clearTimeout(logoutTimer);
                startTimers();
            }

            function staySignedIn() {
                let modal = document.getElementById('timeoutModal');
                modal.style.display = 'none';
                
                // Invisibly ping the server to reset the PHP $_SESSION['last_activity']
                fetch('<?php echo $asset_prefix; ?>actions/ping_session.php')
                    .then(response => console.log('Session refreshed.'))
                    .catch(error => console.error('Error refreshing session:', error));
                
                resetTimers();
            }

            // Start the timers when the page loads, and reset them if the user moves their mouse or types
            window.onload = startTimers;
            document.onmousemove = resetTimers;
            document.onkeypress = resetTimers;
            document.onclick = resetTimers;
        </script>
    <?php endif; ?>