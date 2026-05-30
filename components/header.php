<?php
    $is_index = basename($_SERVER['PHP_SELF']) === 'index.php';
    $asset_prefix = $is_index ? '' : '../';
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
                    if ($page === 'visitors_page.php') {
                        echo 'Events List';
                    } elseif ($page === 'contact.php') {
                        echo 'Contact Us';
                    } elseif ($page === 'admin_page.php') {
                        echo 'Request List';
                    } else {
                        echo 'EventHub Portal';
                    }
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
    <?php endif; ?>