<?php
    session_start();
    // Path updated to point to components folder
    require_once '../components/config.php';

    // 1. Security Check - Matches your login session key 'id'
    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }

    // 2. Determine User Role
    $role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
    $isAdmin = ($role == 'admin');
    $isManager = ($role == 'manager');
    
    // Admin AND Manager can see edit icons
    $can_edit = ($isAdmin);

    // 3. Fetch Users Data - Using your specific table names
    $sql = "SELECT user.id, user.name, role.role 
            FROM user 
            LEFT JOIN role ON user.id = role.user_id 
            ORDER BY user.id ASC";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">
    <div class="user-hero" aria-hidden="true"></div>
    <header class="user-topbar">
        <button class="topbar-menu" type="button" aria-label="Open menu">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">Members List</h1>
        <a href="../index.php" class="topbar-logout">
            <i class="fas fa-sign-out-alt"></i> Log out
        </a>
    </header>

    <div class="user-container">
        <div class="overlay"></div>
        <?php require_once '../components/sidebar.php'; ?>

        <main class="main-content-wrapper">
            <div class="member-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()):
                        $role_raw = $row['role'] ?? 'Member';
                        $role_lower = strtolower($role_raw);
                        $role_class = in_array($role_lower, ['admin','manager','member']) ? "member-role-$role_lower" : 'member-role-default';
                        $initials = strtoupper(mb_substr($row['name'], 0, 1));
                    ?>
                        <article class="member-card">
                            <div class="member-avatar"><?php echo $initials; ?></div>
                            <div class="member-info">
                                <div class="member-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="member-id">ID <?php echo htmlspecialchars($row['id']); ?></div>
                            </div>
                            <span class="member-role <?php echo $role_class; ?>"><?php echo htmlspecialchars($role_raw); ?></span>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="member-empty">No members found.</div>
                <?php endif; ?>
            </div>
        </main>
    </div>
<script>
    const menuBtn = document.querySelector('.topbar-menu');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    if (menuBtn && sidebar && overlay) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
</script>
</body>
</html>