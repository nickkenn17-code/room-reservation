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
    <title>Club Management System</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#5a0505">
    
    <link rel="stylesheet" href="<?php echo $asset_prefix; ?>assets/css/style.min.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="manifest" href="<?php echo $asset_prefix; ?>manifest.json">
    <meta name="theme-color" content="#600000">

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
            
            <h1 class="page-title" id="page-title">Members List</h1>

            <div class="topbar-profile">
                <span class="profile-name"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                <div class="profile-avatar">
                    <?php 
                        $raw_pic = $_SESSION['profile_pic'] ?? '';
                        $pic_name = !empty($raw_pic) ? basename($raw_pic) : '';
                        $pic_path = $asset_prefix . 'assets/images/' . $pic_name;

                        if (!empty($pic_name) && file_exists($pic_path)): ?>
                            <img src="<?php echo htmlspecialchars($pic_path); ?>" 
                                 alt="Profile" 
                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: 
                            echo strtoupper(mb_substr($_SESSION['name'], 0, 1)); 
                        endif; 
                    ?>
                </div>
            </div>
        </header>

        <div class="overlay"></div>
    <?php endif; ?>