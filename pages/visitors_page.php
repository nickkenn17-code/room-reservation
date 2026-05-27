<?php
    session_start();
    // Step out of the pages/ folder, then look for components/config.php
    require_once '../components/config.php'; 

    // Mock session identifier defaults if testing outside active login pipeline
    if (!isset($_SESSION['id'])) {
        $_SESSION['id'] = 1;
        $_SESSION['name'] = "Visitor";
        $_SESSION['role'] = "Visitor";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub Portal</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="user-body">
    <div class="user-hero" aria-hidden="true"></div>

    <header class="user-topbar">
        <h1 class="page-title">Events List</h1>
    </header>

    <div class="user-container" style="padding-top: 68px;">
        
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-text">
                    <strong>EVENTHUB</strong>
                    <strong>PORTAL</strong>
                </div>
            </div>
            
            <div class="sidebar-nav">
                <a href="#" class="nav-link active"><i class="fas fa-home"></i> Events List</a>
                <a href="../pages/contact.php" class="nav-link"><i class="fas fa-envelope"></i> Contact Us</a>
                <div class="nav-spacer"></div>
                <a href="#" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
                <a href="../auth/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Log out</a>
            </div>
        </aside>

        <main class="main-content-wrapper">
            <div style="text-align: center; margin-top: 100px; color: #666;">
                <h2 style="font-family: 'Poppins', sans-serif; font-weight: 500; color: #333;">Welcome to EventHub</h2>
                <p style="font-size: 14px; margin-top: 10px;">Select an option or click the FAQ Assistant on the bottom right to begin.</p>
            </div>
        </main>
    </div>

    <?php include '../components/faq_modal.php'; ?>

</body>
</html>