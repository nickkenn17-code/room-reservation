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
    $sql = "SELECT user.id, user.name, user.major, user.profile_pic, role.role
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

            <div id= "membersView">
                <div class="member-grid">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()):
                            $role_raw = $row['role'] ?? 'Member';
                            $role_lower = strtolower($role_raw);
                            $role_class = in_array($role_lower, ['admin','manager','member']) ? "member-role-$role_lower" : 'member-role-default';
                            $initials = strtoupper(mb_substr($row['name'], 0, 1));
                        ?>
                            <article class="member-card" 
                                    data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                    data-role="<?php echo htmlspecialchars($role_raw); ?>"
                                    data-major="<?php echo htmlspecialchars($row['major'] ?? 'No Major Assigned'); ?>"
                                    data-avatar="<?php echo htmlspecialchars($row['profile_pic'] ?? 'avatar1.jpg'); ?>"
                                    onclick="openMemberModal(this)"
                                    style="cursor: pointer;">
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
            </div>

            <div id="meetingsView" style="display: none;">
                <div class="member-empty">
                    <i class="fas fa-calendar-alt" style="font-size: 24px; margin-bottom: 10px; color: #5a0505;"></i><br>
                    No meetings scheduled yet.
                </div>
            </div>

            <!-- modal structure for member details -->
            <div id="memberModal" class="custom-modal">
                <div class="custom-modal-content">
                    <span class="close-modal" onclick="closeMemberModal()">&times;</span>
                    <div class="modal-body">
                        <div class="modal-body">
                            <div class="modal-avatar-wrapper">
                                <img id="modalAvatar" src="" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <h2 id="modalName" class="modal-name">[Name]</h2>
                        <p id="modalId" class="modal-text">[ID]</p>
                        <p id="modalRole" class="modal-text font-bold">[Role]</p>
                        <p id="modalClub" class="modal-text">[Club/Major]</p>
                    </div>
                </div>
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


    // Function to open and populate the modal
    function openMemberModal(element) {
        // Get data from the clicked card
        const name = element.getAttribute('data-name');
        const id = element.getAttribute('data-id');
        const role = element.getAttribute('data-role');
        const major = element.getAttribute('data-major');
        const avatar = element.getAttribute('data-avatar');

        // Inject data into the modal text elements
        document.getElementById('modalName').innerText = name;
        document.getElementById('modalId').innerText = "ID " + id;
        document.getElementById('modalRole').innerText = role;
        document.getElementById('modalClub').innerText = major;

        // Set the image source (Adjust the folder path if your avatars are stored elsewhere)
        document.getElementById('modalAvatar').src = '../assets/images/' + avatar;

        // Show the modal
        document.getElementById('memberModal').classList.add('show');
    }

    // Function to close the modal
    function closeMemberModal() {
        document.getElementById('memberModal').classList.remove('show');
    }

    // Optional: Close modal when clicking outside the white box
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('memberModal');
        if (event.target === modal) {
            closeMemberModal();
        }
    });


    // Function to switch between Members and Meetings views
    function switchView(viewName) {
        // 1. Remove the 'active' highlight from all sidebar links
        document.getElementById('nav-members').classList.remove('active');
        document.getElementById('nav-meetings').classList.remove('active');

        // 2. Add the 'active' highlight to the link you just clicked
        document.getElementById('nav-' + viewName).classList.add('active');

        // 3. Hide all view containers
        document.getElementById('membersView').style.display = 'none';
        document.getElementById('meetingsView').style.display = 'none';

        // 4. Show only the requested container
        document.getElementById(viewName + 'View').style.display = 'block';

        // 5. Update the text in the Topbar Title to match
        const titleElement = document.querySelector('.page-title');
        if (viewName === 'members') {
            titleElement.innerText = 'MEMBERS LIST';
        } else if (viewName === 'meetings') {
            titleElement.innerText = 'MEETING LIST';
        }
    }
    
</script>
</body>
</html>